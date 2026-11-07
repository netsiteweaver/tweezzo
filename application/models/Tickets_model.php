<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helpdesk tickets. Shared by the admin Helpdesk controller, the customer and developer
 * portals and the public support form.
 *
 * An "actor" is ['type' => admin|developer|customer|guest|system, 'id' => int|null].
 * Customer actors use customer_access.id; admin/developer actors use users.id.
 */
class Tickets_model extends CI_Model
{
    const UPLOAD_DIR = 'uploads/tickets/';
    const MAX_FILES = 5;
    const MAX_FILE_KB = 10240;
    const ALLOWED_TYPES = 'jpg|jpeg|png|gif|pdf|doc|docx|xls|xlsx|csv|txt|zip';
    const PUBLIC_RATE_LIMIT_PER_HOUR = 5;

    /** Statuses where the ticket no longer needs work. */
    const CLOSED_STATUSES = ['resolved', 'closed'];
    /** Statuses where the SLA clock is paused (no overdue flag, no breach alert). */
    const PAUSED_STATUSES = ['pending_customer', 'on_hold', 'resolved', 'closed'];

    public static function priorities()
    {
        return [
            'low'    => ['label' => 'Low',    'badge' => 'secondary'],
            'normal' => ['label' => 'Normal', 'badge' => 'info'],
            'high'   => ['label' => 'High',   'badge' => 'warning'],
            'urgent' => ['label' => 'Urgent', 'badge' => 'danger'],
        ];
    }

    public static function statuses()
    {
        return [
            'new'              => ['label' => 'New',               'badge' => 'primary'],
            'open'             => ['label' => 'Open',              'badge' => 'info'],
            'pending_customer' => ['label' => 'Awaiting customer', 'badge' => 'warning'],
            'on_hold'          => ['label' => 'On hold',           'badge' => 'secondary'],
            'resolved'         => ['label' => 'Resolved',          'badge' => 'success'],
            'closed'           => ['label' => 'Closed',            'badge' => 'dark'],
        ];
    }

    public static function statusLabel($status)
    {
        $s = self::statuses();
        return isset($s[$status]) ? $s[$status]['label'] : ucfirst((string) $status);
    }

    public static function priorityLabel($priority)
    {
        $p = self::priorities();
        return isset($p[$priority]) ? $p[$priority]['label'] : ucfirst((string) $priority);
    }

    /**
     * Badge HTML. $bs5 switches between Bootstrap 4 (admin) and 5 (portals) class names.
     */
    public static function statusBadge($status, $bs5 = false)
    {
        $s = self::statuses();
        $badge = isset($s[$status]) ? $s[$status]['badge'] : 'secondary';
        $cls = $bs5 ? "badge bg-{$badge}" . ($badge === 'warning' || $badge === 'info' ? ' text-dark' : '') : "badge badge-{$badge}";
        return '<span class="' . $cls . '">' . htmlspecialchars(self::statusLabel($status)) . '</span>';
    }

    public static function priorityBadge($priority, $bs5 = false)
    {
        $p = self::priorities();
        $badge = isset($p[$priority]) ? $p[$priority]['badge'] : 'secondary';
        $cls = $bs5 ? "badge bg-{$badge}" . ($badge === 'warning' || $badge === 'info' ? ' text-dark' : '') : "badge badge-{$badge}";
        return '<span class="' . $cls . '">' . htmlspecialchars(self::priorityLabel($priority)) . '</span>';
    }

    /** Bodies are stored as sanitized HTML; sanitize again on output in case of older or edited rows. */
    public static function formatBody($html)
    {
        return self::sanitizeHtml($html);
    }

    /** Plain text → HTML with line breaks and clickable links (used to migrate pre-rich-text bodies). */
    public static function textToHtml($text)
    {
        $html = htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
        $html = preg_replace('~(https?://[^\s<]+)~i', '<a href="$1" target="_blank" rel="noopener noreferrer nofollow">$1</a>', $html);
        return nl2br($html);
    }

    /** Max stored body size (pasted content can be large; images go in attachments). */
    const MAX_BODY_BYTES = 200000;

    /** Tags kept with their content (attributes are all dropped except a safe href). */
    const ALLOWED_TAGS = ['p', 'br', 'b', 'strong', 'i', 'em', 'u', 's', 'strike', 'del', 'sub', 'sup',
        'ul', 'ol', 'li', 'a', 'blockquote', 'pre', 'code', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'div', 'span'];
    /** Tags removed together with everything inside them. */
    const DROPPED_TAGS = ['script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet', 'noscript',
        'template', 'svg', 'math', 'head', 'title', 'meta', 'link', 'base', 'form', 'input', 'button', 'select',
        'textarea', 'option', 'img', 'video', 'audio', 'source', 'track', 'picture', 'canvas', 'map', 'area'];

    /**
     * Whitelist HTML sanitizer for ticket bodies, which can come from anonymous visitors.
     * Parses with DOMDocument (no regex), keeps only ALLOWED_TAGS, strips every attribute, and keeps
     * links only when they are http(s) or mailto. Unknown tags are unwrapped so their text survives.
     */
    public static function sanitizeHtml($html)
    {
        $html = (string) $html;
        if (trim($html) === '') return '';

        $doc = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>'
            . $html . '</body></html>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) return '';
        self::sanitizeChildren($body);

        $out = '';
        foreach ($body->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }
        return trim($out);
    }

    private static function sanitizeChildren(DOMNode $node)
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMCdataSection) {
                $node->replaceChild($node->ownerDocument->createTextNode($child->data), $child);
                continue;
            }
            if ($child instanceof DOMText) {
                continue; // saveHTML escapes text
            }
            if (!($child instanceof DOMElement)) {
                $node->removeChild($child); // comments, processing instructions
                continue;
            }
            $tag = strtolower($child->nodeName);
            if (in_array($tag, self::DROPPED_TAGS, true)) {
                $node->removeChild($child);
                continue;
            }
            self::sanitizeChildren($child);
            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            $href = $tag === 'a' ? trim($child->getAttribute('href')) : '';
            foreach (iterator_to_array($child->attributes) as $attr) {
                $child->removeAttribute($attr->nodeName);
            }
            if ($tag === 'a') {
                // getAttribute() returns the entity-decoded value; reject control chars hiding a scheme.
                if ($href !== '' && !preg_match('/[\x00-\x20]/', $href) && preg_match('~^(https?://|mailto:)~i', $href)) {
                    $child->setAttribute('href', $href);
                    $child->setAttribute('target', '_blank');
                    $child->setAttribute('rel', 'noopener noreferrer nofollow');
                }
            }
        }
    }

    /** True when the HTML has visible text. */
    public static function htmlHasText($html)
    {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8'), " \t\n\r\0\x0B\xC2\xA0") !== '';
    }

    /** Sanitized body, or '' when there's no visible text (e.g. an empty editor's "<p><br></p>"). */
    public static function cleanBody($html)
    {
        $html = (string) $html;
        // No markup at all (editor didn't load, API/email input): keep line breaks and links.
        if (!preg_match('~<[a-z/!]~i', $html)) {
            $html = self::textToHtml(trim($html));
        }
        $clean = self::sanitizeHtml($html);
        return self::htmlHasText($clean) ? $clean : '';
    }

    public static function isOverdue($ticket)
    {
        if (in_array($ticket->status, self::PAUSED_STATUSES, true)) {
            return false;
        }
        $now = date('Y-m-d H:i:s');
        if (!empty($ticket->resolution_due) && $ticket->resolution_due < $now) {
            return true;
        }
        return empty($ticket->first_responded_at) && !empty($ticket->first_response_due) && $ticket->first_response_due < $now;
    }

    /** The logged-in admin or developer as an actor (admin session takes priority). */
    public function staffActorFromSession()
    {
        if (!empty($_SESSION['user_id'])) {
            $type = $this->db->select('user_type')->from('users')->where('id', (int) $_SESSION['user_id'])->get()->row('user_type');
            return ['type' => $type === 'developer' ? 'developer' : 'admin', 'id' => (int) $_SESSION['user_id']];
        }
        if (!empty($_SESSION['developer_id'])) {
            return ['type' => 'developer', 'id' => (int) $_SESSION['developer_id']];
        }
        return null;
    }

    public static function isStaff($actor)
    {
        return in_array($actor['type'], ['admin', 'developer'], true);
    }

    /**
     * Customer-facing reference, e.g. HD-7KQ4-X9MP. Random so it reveals nothing about ticket
     * volume and can't be guessed; staff keep the sequential ticket_number internally.
     */
    public function generatePublicRef()
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // no 0/O/1/I
        do {
            $chars = '';
            for ($i = 0; $i < 8; $i++) {
                $chars .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $ref = 'HD-' . substr($chars, 0, 4) . '-' . substr($chars, 4);
        } while ($this->db->where('public_ref', $ref)->count_all_results('tickets') > 0);
        return $ref;
    }

    /** Internal number plus customer ref, for staff screens and staff emails. */
    public static function staffRef($ticket)
    {
        return $ticket->ticket_number . (!empty($ticket->public_ref) ? " ({$ticket->public_ref})" : '');
    }

    /**
     * Name the requester gave. A public-form ticket matched to a portal account keeps the name typed
     * in the form, which may differ from the account's name (e.g. a shared "Admin" login).
     */
    public static function requesterName($ticket)
    {
        if (!empty($ticket->requester_name)
            && (empty($ticket->customer_access_id) || strcasecmp((string) $ticket->requester_email, (string) $ticket->contact_email) === 0)) {
            return $ticket->requester_name;
        }
        return $ticket->contact_name;
    }

    // ------------------------------------------------------------------
    // Lookups
    // ------------------------------------------------------------------

    public function categories()
    {
        return $this->db->select('id, name')->from('ticket_categories')->where('status', 1)->order_by('display_order, name')->get()->result();
    }

    /** Staff who can be assigned tickets: active regular (admin) and developer users. */
    public function assignableUsers()
    {
        return $this->db->select('id, name, email, user_type')
            ->from('users')
            ->where('status', 1)
            ->where_in('user_type', ['regular', 'developer'])
            ->order_by('user_type DESC, name')
            ->get()->result();
    }

    public function customerIdForAccess($customer_access_id)
    {
        return $this->db->select('customer_id')->from('customer_access')->where('id', (int) $customer_access_id)->get()->row('customer_id');
    }

    // ------------------------------------------------------------------
    // Fetching
    // ------------------------------------------------------------------

    private function baseSelect()
    {
        $this->db->select("t.*, c.company_name, cat.name AS category_name,
                u.name AS assignee_name, u.email AS assignee_email, u.user_type AS assignee_type,
                p.name AS project_name,
                tk.uuid AS task_uuid, tk.task_number AS task_number,
                COALESCE(ca.name, t.requester_name) AS contact_name,
                COALESCE(ca.email, t.requester_email) AS contact_email", false)
            ->from('tickets t')
            ->join('customers c', 'c.customer_id = t.customer_id', 'left')
            ->join('customer_access ca', 'ca.id = t.customer_access_id', 'left')
            ->join('ticket_categories cat', 'cat.id = t.category_id', 'left')
            ->join('users u', 'u.id = t.assigned_to', 'left')
            ->join('projects p', 'p.id = t.project_id', 'left')
            ->join('tasks tk', 'tk.id = t.task_id', 'left')
            ->where('t.deleted', 0);
    }

    public function getByUuid($uuid)
    {
        if (empty($uuid)) return null;
        $this->baseSelect();
        return $this->db->where('t.uuid', (string) $uuid)->get()->row();
    }

    public function getById($id)
    {
        $this->baseSelect();
        return $this->db->where('t.id', (int) $id)->get()->row();
    }

    /** Ticket visible to a customer company (any colleague in the same company). */
    public function getForCustomer($uuid, $customer_id)
    {
        if (empty($uuid) || empty($customer_id)) return null;
        $this->baseSelect();
        return $this->db->where('t.uuid', (string) $uuid)->where('t.customer_id', (int) $customer_id)->get()->row();
    }

    /** Ticket visible to a developer: only tickets assigned to them. */
    public function getForDeveloper($uuid, $developer_id)
    {
        if (empty($uuid) || empty($developer_id)) return null;
        $this->baseSelect();
        return $this->db->where('t.uuid', (string) $uuid)->where('t.assigned_to', (int) $developer_id)->get()->row();
    }

    /** Public tracking link for guest tickets. */
    public function getByToken($token)
    {
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) return null;
        $this->baseSelect();
        return $this->db->where('t.public_token', $token)->get()->row();
    }

    private function applyFilters($f)
    {
        $now = date('Y-m-d H:i:s');
        $status = isset($f['status']) ? $f['status'] : 'active';
        if ($status === 'active') {
            $this->db->where_not_in('t.status', self::CLOSED_STATUSES);
        } elseif (!empty($status) && $status !== 'all' && isset(self::statuses()[$status])) {
            $this->db->where('t.status', $status);
        }
        if (!empty($f['priority']) && isset(self::priorities()[$f['priority']])) {
            $this->db->where('t.priority', $f['priority']);
        }
        if (!empty($f['category_id'])) {
            $this->db->where('t.category_id', (int) $f['category_id']);
        }
        if (!empty($f['customer_id'])) {
            $this->db->where('t.customer_id', (int) $f['customer_id']);
        }
        if (!empty($f['assigned_to'])) {
            if ($f['assigned_to'] === 'unassigned') {
                $this->db->where('t.assigned_to IS NULL', null, false);
            } else {
                $this->db->where('t.assigned_to', (int) $f['assigned_to']);
            }
        }
        if (!empty($f['source'])) {
            $this->db->where('t.source', $f['source']);
        }
        if (!empty($f['overdue'])) {
            $paused = "'" . implode("','", self::PAUSED_STATUSES) . "'";
            $nowEsc = $this->db->escape($now);
            $this->db->where("t.status NOT IN ($paused) AND (t.resolution_due < $nowEsc OR (t.first_responded_at IS NULL AND t.first_response_due < $nowEsc))", null, false);
        }
        if (!empty($f['search_text'])) {
            $s = trim($f['search_text']);
            $this->db->group_start()
                ->like('t.ticket_number', $s)
                ->or_like('t.public_ref', $s)
                ->or_like('t.subject', $s)
                ->or_like('t.requester_email', $s)
                ->or_like('t.requester_name', $s)
                ->or_like('ca.email', $s)
                ->or_like('ca.name', $s)
                ->or_like('c.company_name', $s)
                ->group_end();
        }
    }

    public function listing($filters, $page = 1, $per_page = 25)
    {
        $page = max(1, (int) $page);
        $per_page = max(1, (int) $per_page);
        $this->baseSelect();
        $this->db->select('(SELECT COUNT(1) FROM ticket_replies r WHERE r.ticket_id = t.id AND r.is_internal = 0) AS reply_count', false);
        $this->db->select('(SELECT COUNT(1) FROM ticket_attachments a WHERE a.ticket_id = t.id) AS attachment_count', false);
        $this->applyFilters($filters);
        $this->db->order_by("FIELD(t.status,'new','open','pending_customer','on_hold','resolved','closed')", '', false);
        $this->db->order_by("FIELD(t.priority,'urgent','high','normal','low')", '', false);
        $this->db->order_by('t.last_activity_at', 'DESC');
        $this->db->limit($per_page, ($page - 1) * $per_page);
        return $this->db->get()->result();
    }

    public function count($filters)
    {
        $this->db->select('COUNT(1) AS ct', false)
            ->from('tickets t')
            ->join('customers c', 'c.customer_id = t.customer_id', 'left')
            ->join('customer_access ca', 'ca.id = t.customer_access_id', 'left')
            ->where('t.deleted', 0);
        $this->applyFilters($filters);
        return (int) $this->db->get()->row('ct');
    }

    /** Quick counters for the admin listing header. */
    public function summaryCounts($user_id)
    {
        return [
            'new'        => $this->count(['status' => 'new']),
            'active'     => $this->count(['status' => 'active']),
            'unassigned' => $this->count(['status' => 'active', 'assigned_to' => 'unassigned']),
            'overdue'    => $this->count(['status' => 'active', 'overdue' => 1]),
            'mine'       => $this->count(['status' => 'active', 'assigned_to' => (int) $user_id]),
        ];
    }

    /** Customers that have at least one ticket (for the listing filter). */
    public function customersWithTickets()
    {
        return $this->db->query("SELECT c.customer_id, c.company_name, COUNT(t.id) AS ticket_count
            FROM customers c JOIN tickets t ON t.customer_id = c.customer_id AND t.deleted = 0
            GROUP BY c.customer_id, c.company_name ORDER BY c.company_name")->result();
    }

    /**
     * Open tickets that need staff attention, for the dashboard: overdue first, then unassigned,
     * then by priority and nearest SLA deadline. Paused tickets (waiting on customer/on hold) are left out.
     */
    public function attentionList($limit = 8)
    {
        $paused = "'" . implode("','", self::PAUSED_STATUSES) . "'";
        $now = $this->db->escape(date('Y-m-d H:i:s'));
        $this->baseSelect();
        $this->db->where("t.status NOT IN ($paused)", null, false);
        $this->db->order_by("CASE WHEN t.resolution_due < $now OR (t.first_responded_at IS NULL AND t.first_response_due < $now) THEN 0 ELSE 1 END", '', false);
        $this->db->order_by('CASE WHEN t.assigned_to IS NULL THEN 0 ELSE 1 END', '', false);
        $this->db->order_by("FIELD(t.priority,'urgent','high','normal','low')", '', false);
        $this->db->order_by('IF(t.first_responded_at IS NULL, t.first_response_due, t.resolution_due)', 'ASC', false);
        $this->db->limit((int) $limit);
        return $this->db->get()->result();
    }

    public function listForCustomer($customer_id, $status = 'all')
    {
        $this->baseSelect();
        $this->db->select('(SELECT COUNT(1) FROM ticket_replies r WHERE r.ticket_id = t.id AND r.is_internal = 0) AS reply_count', false);
        $this->db->select('(SELECT COUNT(1) FROM ticket_attachments a WHERE a.ticket_id = t.id AND a.is_internal = 0) AS attachment_count', false);
        $this->db->where('t.customer_id', (int) $customer_id);
        if ($status === 'active') {
            $this->db->where_not_in('t.status', self::CLOSED_STATUSES);
        } elseif ($status === 'done') {
            $this->db->where_in('t.status', self::CLOSED_STATUSES);
        }
        return $this->db->order_by('t.last_activity_at', 'DESC')->get()->result();
    }

    public function listForDeveloper($developer_id, $status = 'active')
    {
        $this->baseSelect();
        $this->db->select('(SELECT COUNT(1) FROM ticket_replies r WHERE r.ticket_id = t.id) AS reply_count', false);
        $this->db->select('(SELECT COUNT(1) FROM ticket_attachments a WHERE a.ticket_id = t.id) AS attachment_count', false);
        $this->db->where('t.assigned_to', (int) $developer_id);
        if ($status === 'active') {
            $this->db->where_not_in('t.status', self::CLOSED_STATUSES);
        }
        $this->db->order_by("FIELD(t.priority,'urgent','high','normal','low')", '', false);
        return $this->db->order_by('t.last_activity_at', 'DESC')->get()->result();
    }

    /**
     * Replies with author names, oldest first. Attachments are attached to each reply;
     * attachments on the original ticket are returned under the key 0.
     */
    public function getThread($ticket, $includeInternal)
    {
        $this->db->select("r.*, CASE
                WHEN r.author_type IN ('admin','developer') THEN u.name
                WHEN r.author_type = 'customer' THEN ca.name
                ELSE NULL END AS author_name", false)
            ->from('ticket_replies r')
            ->join('users u', "u.id = r.author_id AND r.author_type IN ('admin','developer')", 'left')
            ->join('customer_access ca', "ca.id = r.author_id AND r.author_type = 'customer'", 'left')
            ->where('r.ticket_id', (int) $ticket->id);
        if (!$includeInternal) {
            $this->db->where('r.is_internal', 0);
        }
        $replies = $this->db->order_by('r.id', 'ASC')->get()->result();

        $this->db->from('ticket_attachments')->where('ticket_id', (int) $ticket->id);
        if (!$includeInternal) {
            $this->db->where('is_internal', 0);
        }
        $attachments = [];
        foreach ($this->db->order_by('id')->get()->result() as $a) {
            $attachments[(int) $a->reply_id][] = $a;
        }

        foreach ($replies as $r) {
            if ($r->author_type === 'guest') {
                $r->author_name = $ticket->requester_name ?: 'Guest';
            } elseif ($r->author_type === 'system') {
                $r->author_name = 'System';
            }
            $r->author_name = $r->author_name ?: 'Unknown';
            $r->attachments = isset($attachments[(int) $r->id]) ? $attachments[(int) $r->id] : [];
        }

        return ['replies' => $replies, 'ticket_attachments' => isset($attachments[0]) ? $attachments[0] : []];
    }

    public function getHistory($ticket_id)
    {
        return $this->db->select("h.*, CASE
                WHEN h.changed_by_type IN ('admin','developer') THEN u.name
                WHEN h.changed_by_type = 'customer' THEN ca.name
                WHEN h.changed_by_type = 'guest' THEN 'Requester'
                ELSE 'System' END AS changed_by_name", false)
            ->from('ticket_history h')
            ->join('users u', "u.id = h.changed_by_id AND h.changed_by_type IN ('admin','developer')", 'left')
            ->join('customer_access ca', "ca.id = h.changed_by_id AND h.changed_by_type = 'customer'", 'left')
            ->where('h.ticket_id', (int) $ticket_id)
            ->order_by('h.id', 'DESC')
            ->get()->result();
    }

    // ------------------------------------------------------------------
    // Create / reply / update
    // ------------------------------------------------------------------

    /**
     * @param array  $data   subject, body, priority, category_id, project_id, customer_id,
     *                       customer_access_id, requester_name, requester_email, requester_company,
     *                       assigned_to, ip_address
     * @param string $source portal|public|staff
     * @param array  $actor
     */
    public function create($data, $source, $actor)
    {
        $subject = trim((string) ($data['subject'] ?? ''));
        if (strlen((string) ($data['body'] ?? '')) > self::MAX_BODY_BYTES) {
            return ['result' => false, 'reason' => 'The description is too long. Please shorten it or attach a file instead.'];
        }
        $body = self::cleanBody($data['body'] ?? '');
        if ($subject === '' || $body === '') {
            return ['result' => false, 'reason' => 'Please enter a subject and a description.'];
        }
        if (mb_strlen($subject) > 255) {
            return ['result' => false, 'reason' => 'The subject is too long (255 characters max).'];
        }
        $priority = isset(self::priorities()[$data['priority'] ?? '']) ? $data['priority'] : 'normal';
        $category_id = !empty($data['category_id']) ? (int) $data['category_id'] : null;
        if ($category_id && !$this->db->where(['id' => $category_id, 'status' => 1])->count_all_results('ticket_categories')) {
            $category_id = null;
        }

        $customer_id = !empty($data['customer_id']) ? (int) $data['customer_id'] : null;
        $customer_access_id = !empty($data['customer_access_id']) ? (int) $data['customer_access_id'] : null;
        if ($customer_access_id) {
            // The contact must belong to the customer.
            $access = $this->db->select('customer_id')->from('customer_access')->where(['id' => $customer_access_id, 'status' => 1])->get()->row();
            if (!$access || ($customer_id && (int) $access->customer_id !== $customer_id)) {
                return ['result' => false, 'reason' => 'Invalid customer contact.'];
            }
            $customer_id = (int) $access->customer_id;
        }

        $requester_email = trim((string) ($data['requester_email'] ?? ''));
        if ($requester_email !== '' && !filter_var($requester_email, FILTER_VALIDATE_EMAIL)) {
            return ['result' => false, 'reason' => 'Please enter a valid email address.'];
        }
        if (!$customer_access_id && $requester_email === '' && $source !== 'staff') {
            return ['result' => false, 'reason' => 'Please enter your email address.'];
        }

        $project_id = !empty($data['project_id']) ? (int) $data['project_id'] : null;
        if ($project_id) {
            $projectCustomer = $this->db->select('customer_id')->from('projects')->where(['id' => $project_id, 'status' => 1])->get()->row('customer_id');
            if (!$projectCustomer || ($customer_id && (int) $projectCustomer !== $customer_id)) {
                $project_id = null;
            }
        }

        $assigned_to = null;
        if (!empty($data['assigned_to']) && $this->isAssignable((int) $data['assigned_to'])) {
            $assigned_to = (int) $data['assigned_to'];
        }

        $now = date('Y-m-d H:i:s');
        $due = $this->computeDueDates($priority, $now);
        $row = [
            'uuid'               => gen_uuid(),
            'public_ref'         => $this->generatePublicRef(),
            'subject'            => $subject,
            'body'               => $body,
            'customer_id'        => $customer_id,
            'customer_access_id' => $customer_access_id,
            // Public-form tickets keep what was typed even when matched to a portal account.
            'requester_name'     => $customer_access_id && $source !== 'public' ? null : (trim((string) ($data['requester_name'] ?? '')) ?: null),
            'requester_email'    => $customer_access_id && $source !== 'public' ? null : ($requester_email ?: null),
            'requester_company'  => trim((string) ($data['requester_company'] ?? '')) ?: null,
            'project_id'         => $project_id,
            'category_id'        => $category_id,
            'priority'           => $priority,
            'status'             => $assigned_to ? 'open' : 'new',
            'assigned_to'        => $assigned_to,
            'source'             => $source,
            'public_token'       => bin2hex(random_bytes(32)),
            'ip_address'         => $data['ip_address'] ?? null,
            'first_response_due' => $due['first_response'],
            'resolution_due'     => $due['resolution'],
            'last_activity_at'   => $now,
            'created_on'         => $now,
            'created_by'         => self::isStaff($actor) ? $actor['id'] : null,
            'created_by_customer'=> $actor['type'] === 'customer' ? $actor['id'] : null,
        ];

        $this->db->trans_start();
        $this->db->insert('tickets', $row);
        $ticket_id = (int) $this->db->insert_id();
        $this->db->where('id', $ticket_id)->update('tickets', ['ticket_number' => sprintf('TKT-%06d', $ticket_id)]);
        $this->logHistory($ticket_id, 'created', null, $source, $actor);
        if ($assigned_to) {
            $this->logHistory($ticket_id, 'assigned_to', null, $this->userName($assigned_to), $actor);
        }
        $this->db->trans_complete();
        if (!$this->db->trans_status()) {
            return ['result' => false, 'reason' => 'Unable to create the ticket. Please try again.'];
        }

        $upload = $this->saveUploads($ticket_id, null, $actor, false);
        $ticket = $this->getById($ticket_id);

        $this->notifyNewTicket($ticket);
        if ($assigned_to && !(self::isStaff($actor) && $actor['id'] === $assigned_to)) {
            $this->notifyAssigned($ticket);
        }

        return ['result' => true, 'ticket' => $ticket, 'upload_errors' => $upload['errors']];
    }

    /**
     * Adds a reply. $newStatus (staff only) optionally moves the ticket in the same step.
     */
    public function addReply($ticket, $actor, $body, $isInternal = false, $newStatus = null)
    {
        if (strlen((string) $body) > self::MAX_BODY_BYTES) {
            return ['result' => false, 'reason' => 'The message is too long. Please shorten it or attach a file instead.'];
        }
        $body = self::cleanBody($body);
        $isStaff = self::isStaff($actor);
        $isInternal = $isStaff && $isInternal;
        if ($body === '' && empty($this->pendingUploadNames())) {
            return ['result' => false, 'reason' => 'Please write a message.'];
        }
        if (!$isStaff && $ticket->status === 'closed') {
            return ['result' => false, 'reason' => 'This ticket is closed. Please open a new ticket.'];
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert('ticket_replies', [
            'ticket_id'   => (int) $ticket->id,
            'author_type' => $actor['type'],
            'author_id'   => $actor['id'],
            'body'        => $body,
            'is_internal' => $isInternal ? 1 : 0,
            'created_on'  => $now,
        ]);
        $reply_id = (int) $this->db->insert_id();
        $upload = $this->saveUploads((int) $ticket->id, $reply_id, $actor, $isInternal);

        $update = ['last_activity_at' => $now, 'updated_on' => $now];
        if ($isStaff && !$isInternal && empty($ticket->first_responded_at)) {
            $update['first_responded_at'] = $now;
        }
        $this->db->where('id', (int) $ticket->id)->update('tickets', $update);

        // Status follow-up.
        if ($isStaff && !empty($newStatus) && $newStatus !== $ticket->status) {
            $this->changeField($ticket, 'status', $newStatus, $actor, false);
        } elseif ($isStaff && !$isInternal && $ticket->status === 'new') {
            $this->changeField($ticket, 'status', 'open', $actor, false);
        } elseif (!$isStaff && in_array($ticket->status, ['pending_customer', 'resolved'], true)) {
            // A customer reply reopens the conversation.
            $this->changeField($ticket, 'status', 'open', $actor, false);
        }

        $ticket = $this->getById((int) $ticket->id);
        $reply = $this->db->get_where('ticket_replies', ['id' => $reply_id])->row();
        $this->notifyReply($ticket, $reply, $actor);

        return ['result' => true, 'reply_id' => $reply_id, 'upload_errors' => $upload['errors']];
    }

    /**
     * Updates status, priority, category_id or assigned_to and logs it.
     */
    public function changeField($ticket, $field, $value, $actor, $notify = true)
    {
        $now = date('Y-m-d H:i:s');
        $update = ['updated_on' => $now, 'last_activity_at' => $now];
        $old = $ticket->$field;
        $oldLabel = $old;
        $newLabel = $value;

        switch ($field) {
            case 'status':
                if (!isset(self::statuses()[$value])) return ['result' => false, 'reason' => 'Invalid status.'];
                $update['status'] = $value;
                if ($value === 'resolved') $update['resolved_at'] = $now;
                if ($value === 'closed') {
                    $update['closed_at'] = $now;
                    if (empty($ticket->resolved_at)) $update['resolved_at'] = $now;
                }
                if (!in_array($value, self::CLOSED_STATUSES, true)) {
                    $update['resolved_at'] = null;
                    $update['closed_at'] = null;
                }
                $oldLabel = self::statusLabel($old);
                $newLabel = self::statusLabel($value);
                break;

            case 'priority':
                if (!isset(self::priorities()[$value])) return ['result' => false, 'reason' => 'Invalid priority.'];
                $update['priority'] = $value;
                // SLA targets follow the new priority, still measured from creation.
                $due = $this->computeDueDates($value, $ticket->created_on);
                $update['first_response_due'] = $due['first_response'];
                $update['resolution_due'] = $due['resolution'];
                if ($due['first_response'] > $now) $update['first_response_breach_notified_at'] = null;
                if ($due['resolution'] > $now) $update['sla_breach_notified_at'] = null;
                $oldLabel = self::priorityLabel($old);
                $newLabel = self::priorityLabel($value);
                break;

            case 'category_id':
                $value = !empty($value) ? (int) $value : null;
                if ($value && !$this->db->where(['id' => $value, 'status' => 1])->count_all_results('ticket_categories')) {
                    return ['result' => false, 'reason' => 'Invalid category.'];
                }
                $update['category_id'] = $value;
                $oldLabel = $ticket->category_name;
                $newLabel = $value ? $this->db->select('name')->get_where('ticket_categories', ['id' => $value])->row('name') : null;
                break;

            case 'assigned_to':
                $value = !empty($value) ? (int) $value : null;
                if ($value && !$this->isAssignable($value)) return ['result' => false, 'reason' => 'Invalid assignee.'];
                $update['assigned_to'] = $value;
                if ($value && $ticket->status === 'new') $update['status'] = 'open';
                $oldLabel = $ticket->assignee_name;
                $newLabel = $value ? $this->userName($value) : null;
                break;

            default:
                return ['result' => false, 'reason' => 'Invalid field.'];
        }

        if ((string) $old === (string) $value) {
            return ['result' => true, 'changed' => false];
        }

        $this->db->where('id', (int) $ticket->id)->update('tickets', $update);
        $this->logHistory((int) $ticket->id, $field, $oldLabel, $newLabel, $actor);
        if ($field === 'assigned_to' && isset($update['status'])) {
            $this->logHistory((int) $ticket->id, 'status', self::statusLabel($ticket->status), self::statusLabel('open'), $actor);
        }

        $fresh = $this->getById((int) $ticket->id);
        if ($field === 'assigned_to' && $value && !(self::isStaff($actor) && $actor['id'] === $value)) {
            $this->notifyAssigned($fresh);
        }
        if ($field === 'status' && $notify && in_array($value, self::CLOSED_STATUSES, true) && self::isStaff($actor)) {
            $this->notifyStatusChanged($fresh);
        }

        return ['result' => true, 'changed' => true, 'ticket' => $fresh];
    }

    /**
     * Attaches a ticket (typically from the public form) to a customer company, optionally to one
     * of its portal contacts.
     * - Company only: the company sees it in their portal; the original requester keeps receiving
     *   emails and their tracking link keeps working.
     * - With a contact: the contact becomes the requester and follows it in the portal.
     * Passing an empty customer detaches it again (only possible when there is a requester email).
     */
    public function linkCustomer($ticket, $customer_id, $customer_access_id, $actor)
    {
        $customer_id = !empty($customer_id) ? (int) $customer_id : null;
        $customer_access_id = !empty($customer_access_id) ? (int) $customer_access_id : null;

        if (!$customer_id) {
            $customer_access_id = null;
        }
        if (!$customer_access_id && empty($ticket->requester_email)) {
            return ['result' => false, 'reason' => 'This ticket has no requester email, so please choose a contact to keep someone informed.'];
        }
        if ($customer_id) {
            $customer = $this->db->select('company_name')->get_where('customers', ['customer_id' => $customer_id, 'status' => 1])->row();
            if (!$customer) {
                return ['result' => false, 'reason' => 'Invalid customer.'];
            }
        }
        if ($customer_access_id) {
            $ok = $this->db->where(['id' => $customer_access_id, 'customer_id' => $customer_id, 'status' => 1])->count_all_results('customer_access');
            if (!$ok) {
                return ['result' => false, 'reason' => "The contact doesn't belong to this customer."];
            }
        }
        if ((int) $ticket->customer_id === (int) $customer_id && (int) $ticket->customer_access_id === (int) $customer_access_id) {
            return ['result' => true, 'changed' => false];
        }

        $now = date('Y-m-d H:i:s');
        $update = [
            'customer_id'        => $customer_id,
            'customer_access_id' => $customer_access_id,
            'updated_on'         => $now,
            'last_activity_at'   => $now,
        ];
        // A project from another company no longer makes sense.
        if (!empty($ticket->project_id)) {
            $projectCustomer = $this->db->select('customer_id')->get_where('projects', ['id' => (int) $ticket->project_id])->row('customer_id');
            if ((int) $projectCustomer !== (int) $customer_id) {
                $update['project_id'] = null;
            }
        }
        $this->db->where('id', (int) $ticket->id)->update('tickets', $update);

        $newCompany = $customer_id ? $customer->company_name : null;
        if ((int) $ticket->customer_id !== (int) $customer_id) {
            $this->logHistory((int) $ticket->id, 'customer', $ticket->company_name, $newCompany, $actor);
        }
        if ((int) $ticket->customer_access_id !== (int) $customer_access_id) {
            $contactName = $customer_access_id ? $this->db->select('name')->get_where('customer_access', ['id' => $customer_access_id])->row('name') : null;
            $oldContact = !empty($ticket->customer_access_id) ? $ticket->contact_name : null;
            $this->logHistory((int) $ticket->id, 'contact', $oldContact, $contactName, $actor);
        }

        return ['result' => true, 'changed' => true];
    }

    public function softDelete($ticket, $actor)
    {
        $this->db->where('id', (int) $ticket->id)->update('tickets', ['deleted' => 1, 'updated_on' => date('Y-m-d H:i:s')]);
        $this->logHistory((int) $ticket->id, 'deleted', null, '1', $actor);
        return $this->db->affected_rows();
    }

    /**
     * Creates a task from the ticket (same rules as Submitted_tasks_model::approveAndConvertToTask)
     * and links it back to the ticket. Image attachments are copied to the task gallery.
     */
    public function convertToTask($ticket, $sprint_id, $user_ids, $estimated_hours, $work_type, $billable, $ref, $actor)
    {
        if (!empty($ticket->task_id)) {
            return ['result' => false, 'reason' => 'This ticket is already linked to a task.'];
        }
        $sprint = $this->db->select('s.id, s.name, p.id project_id, p.customer_id')
            ->from('sprints s')
            ->join('projects p', 'p.id = s.project_id')
            ->where(['s.id' => (int) $sprint_id, 's.status' => 1, 's.active' => 1])
            ->get()->row();
        if (empty($sprint)) {
            return ['result' => false, 'reason' => 'Please select a valid sprint.'];
        }
        if (!empty($ticket->customer_id) && (int) $sprint->customer_id !== (int) $ticket->customer_id) {
            return ['result' => false, 'reason' => "The sprint doesn't belong to this ticket's customer."];
        }
        $this->load->model('Sprints_model');
        $check = $this->Sprints_model->assertCanAddTaskToSprint((int) $sprint_id);
        if (!$check['result']) {
            return $check;
        }

        $tn = $this->db->query("SELECT MAX(task_number) AS tn FROM tasks WHERE sprint_id = ? AND status = 1", [(int) $sprint_id])->row()->tn;
        $task_number = incrementTaskNumber($tn);
        $allowedWorkTypes = ['development', 'maintenance', 'support', 'bugfix', 'other'];

        $now = date('Y-m-d H:i:s');
        $task_uuid = gen_uuid();
        $this->db->insert('tasks', [
            'uuid'                     => $task_uuid,
            'name'                     => $ticket->subject,
            'section'                  => $ticket->category_name ?: 'Helpdesk',
            // Task descriptions are rendered as raw HTML; ticket bodies come from untrusted users.
            'description'              => self::sanitizeHtml($ticket->body)
                . '<br><br><em>(From helpdesk ticket ' . htmlspecialchars($ticket->ticket_number . ' / ' . $ticket->public_ref) . ')</em>',
            'sprint_id'                => (int) $sprint_id,
            'task_number'              => $task_number,
            'stage'                    => 'new',
            'progress'                 => 0,
            'status'                   => 1,
            'closed'                   => 0,
            'created_by'               => (int) $actor['id'],
            'created_on'               => $now,
            'estimated_hours'          => ($estimated_hours !== null && $estimated_hours !== '') ? (float) $estimated_hours : null,
            'work_type'                => in_array($work_type, $allowedWorkTypes, true) ? $work_type : 'support',
            'billable'                 => ($billable !== null && $billable !== '') ? (int) $billable : null,
            'ref'                      => ($ref !== null && $ref !== '') ? (string) $ref : null,
            'source'                   => 'helpdesk',
            'scope_client_expectation' => '',
            'scope_not_included'       => '',
            'scope_when_done'          => '',
        ]);
        $task_id = (int) $this->db->insert_id();
        if (!$task_id) {
            return ['result' => false, 'reason' => 'Unable to create the task.'];
        }

        $this->copyImagesToTask($ticket, $task_id, $actor);

        $tUpdate = ['task_id' => $task_id, 'updated_on' => $now, 'last_activity_at' => $now];
        if (empty($ticket->project_id)) $tUpdate['project_id'] = (int) $sprint->project_id;
        if (empty($ticket->customer_id)) $tUpdate['customer_id'] = (int) $sprint->customer_id;
        $this->db->where('id', (int) $ticket->id)->update('tickets', $tUpdate);

        $this->db->insert('ticket_replies', [
            'ticket_id'   => (int) $ticket->id,
            'author_type' => 'system',
            'author_id'   => null,
            'body'        => "Converted to task {$task_number} in sprint \"{$sprint->name}\".",
            'is_internal' => 1,
            'created_on'  => $now,
        ]);
        $this->logHistory((int) $ticket->id, 'task', null, $task_number, $actor);

        if (!empty($user_ids) && is_array($user_ids)) {
            $user_ids = array_values(array_filter(array_map('intval', $user_ids)));
            if (!empty($user_ids)) {
                $this->load->model('Tasks_model');
                $this->Tasks_model->assignUsers($user_ids, [$task_id], (int) $sprint->customer_id, (int) $sprint->project_id, (int) $sprint_id);
            }
        }

        return ['result' => true, 'task_uuid' => $task_uuid, 'task_number' => $task_number];
    }

    /** Public form: max N tickets per hour per IP or email. */
    public function publicRateLimited($ip, $email)
    {
        $since = date('Y-m-d H:i:s', time() - 3600);
        $this->db->from('tickets')->where('source', 'public')->where('created_on >', $since)
            ->group_start()->where('ip_address', (string) $ip)->or_where('requester_email', (string) $email)->group_end();
        return $this->db->count_all_results() >= self::PUBLIC_RATE_LIMIT_PER_HOUR;
    }

    // ------------------------------------------------------------------
    // Attachments
    // ------------------------------------------------------------------

    private function pendingUploadNames()
    {
        if (empty($_FILES['attachments']['name']) || !is_array($_FILES['attachments']['name'])) {
            return [];
        }
        return array_values(array_filter($_FILES['attachments']['name']));
    }

    /**
     * Saves files posted as attachments[] (max 5). Returns ['saved' => n, 'errors' => [..]].
     */
    public function saveUploads($ticket_id, $reply_id, $actor, $isInternal)
    {
        $result = ['saved' => 0, 'errors' => []];
        if (empty($this->pendingUploadNames())) {
            return $result;
        }

        $dir = FCPATH . self::UPLOAD_DIR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $files = $_FILES['attachments'];
        $this->load->library('upload');
        $count = 0;
        foreach ($files['name'] as $i => $name) {
            if ($name === '' || $name === null) continue;
            if (++$count > self::MAX_FILES) {
                $result['errors'][] = 'Only ' . self::MAX_FILES . ' files can be attached at a time.';
                break;
            }
            $_FILES['ticket_upload'] = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
            $this->upload->initialize([
                'upload_path'      => $dir,
                'allowed_types'    => self::ALLOWED_TYPES,
                'max_size'         => self::MAX_FILE_KB,
                'encrypt_name'     => true,
                'file_ext_tolower' => true,
            ]);
            if (!$this->upload->do_upload('ticket_upload')) {
                $result['errors'][] = $name . ': ' . strip_tags($this->upload->display_errors('', ''));
                continue;
            }
            $up = $this->upload->data();
            $this->db->insert('ticket_attachments', [
                'ticket_id'        => (int) $ticket_id,
                'reply_id'         => $reply_id ? (int) $reply_id : null,
                'file_name'        => $up['file_name'],
                'original_name'    => mb_substr($up['orig_name'] ?: $name, 0, 255),
                'mime'             => $up['file_type'],
                'size'             => (int) round($up['file_size'] * 1024),
                'is_internal'      => $isInternal ? 1 : 0,
                'uploaded_by_type' => $actor['type'] === 'system' ? 'admin' : $actor['type'],
                'uploaded_by_id'   => $actor['id'],
                'created_on'       => date('Y-m-d H:i:s'),
            ]);
            $result['saved']++;
        }
        unset($_FILES['ticket_upload']);
        return $result;
    }

    public function getAttachment($id)
    {
        return $this->db->get_where('ticket_attachments', ['id' => (int) $id])->row();
    }

    /** Streams an attachment. Callers must check access first. */
    public function streamAttachment($attachment)
    {
        $path = FCPATH . self::UPLOAD_DIR . basename($attachment->file_name);
        if (!is_file($path)) {
            show_404();
            return;
        }
        $isImage = preg_match('~^image/(png|jpe?g|gif)$~', (string) $attachment->mime);
        $disposition = $isImage || $attachment->mime === 'application/pdf' ? 'inline' : 'attachment';
        $filename = str_replace(['"', "\r", "\n"], '', $attachment->original_name);
        header('Content-Type: ' . ($attachment->mime ?: 'application/octet-stream'));
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=3600');
        readfile($path);
        exit;
    }

    public static function isImageAttachment($a)
    {
        return (bool) preg_match('~^image/(png|jpe?g|gif)$~', (string) $a->mime);
    }

    public static function humanSize($bytes)
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024) . ' KB';
        return (int) $bytes . ' B';
    }

    private function copyImagesToTask($ticket, $task_id, $actor)
    {
        $images = $this->db->from('ticket_attachments')->where('ticket_id', (int) $ticket->id)->get()->result();
        if (empty($images)) return;

        $this->load->model('Files_model');
        $taskDir = FCPATH . 'uploads/tasks/';
        foreach ($images as $a) {
            if (!self::isImageAttachment($a)) continue;
            $src = FCPATH . self::UPLOAD_DIR . $a->file_name;
            if (!is_file($src)) continue;
            $ext = strtolower(pathinfo($a->file_name, PATHINFO_EXTENSION));
            $raw = md5(uniqid('', true));
            $fileName = $raw . '.' . $ext;
            if (!@copy($src, $taskDir . $fileName)) continue;
            $this->Files_model->resizeImage($taskDir . $fileName, 200, 200, 'resized');
            $thumb = $raw . '_resized.' . $ext;
            $info = @getimagesize($taskDir . $fileName);
            $this->db->insert('task_images', [
                'uuid'                  => gen_uuid(),
                'task_id'               => (int) $task_id,
                'created_on'            => date('Y-m-d H:i:s'),
                'created_by'            => (int) $actor['id'],
                'file_name'             => $fileName,
                'thumb_name'            => is_file($taskDir . $thumb) ? $thumb : $fileName,
                'file_ext'              => '.' . $ext,
                'file_size'             => round($a->size / 1024, 2),
                'image_width'           => $info ? (int) $info[0] : 0,
                'image_height'          => $info ? (int) $info[1] : 0,
                'image_type'            => (string) $a->mime,
                'status'                => 1,
                'uploaded_by_user_type' => $a->uploaded_by_type === 'customer' ? 'customer' : ($a->uploaded_by_type === 'developer' ? 'developer' : 'admin'),
            ]);
        }
    }

    // ------------------------------------------------------------------
    // SLA
    // ------------------------------------------------------------------

    public function slaSettings()
    {
        $defaults = [
            'urgent' => ['first_response' => 1,  'resolution' => 8],
            'high'   => ['first_response' => 4,  'resolution' => 24],
            'normal' => ['first_response' => 8,  'resolution' => 72],
            'low'    => ['first_response' => 24, 'resolution' => 120],
        ];
        $this->load->model('system_model');
        $param = $this->system_model->getParam('helpdesk_sla', true);
        if (is_object($param)) {
            foreach ($defaults as $p => $v) {
                if (isset($param->$p->first_response)) $defaults[$p]['first_response'] = (float) $param->$p->first_response;
                if (isset($param->$p->resolution)) $defaults[$p]['resolution'] = (float) $param->$p->resolution;
            }
        }
        return $defaults;
    }

    public function computeDueDates($priority, $from)
    {
        $sla = $this->slaSettings();
        $hours = isset($sla[$priority]) ? $sla[$priority] : $sla['normal'];
        $base = strtotime($from);
        return [
            'first_response' => date('Y-m-d H:i:s', $base + (int) round($hours['first_response'] * 3600)),
            'resolution'     => date('Y-m-d H:i:s', $base + (int) round($hours['resolution'] * 3600)),
        ];
    }

    /**
     * Sends one alert per breach (first response and resolution). Returns the number of alerts sent.
     */
    public function processSlaBreaches()
    {
        $now = date('Y-m-d H:i:s');
        $sent = 0;

        $firstResponse = $this->db->select('id')->from('tickets')
            ->where('deleted', 0)
            ->where_not_in('status', self::PAUSED_STATUSES)
            ->where('first_responded_at IS NULL', null, false)
            ->where('first_response_breach_notified_at IS NULL', null, false)
            ->where('first_response_due <', $now)
            ->get()->result();
        foreach ($firstResponse as $row) {
            $this->db->where('id', $row->id)->update('tickets', ['first_response_breach_notified_at' => $now]);
            $this->notifySlaBreach($this->getById($row->id), 'first response');
            $sent++;
        }

        $resolution = $this->db->select('id')->from('tickets')
            ->where('deleted', 0)
            ->where_not_in('status', self::PAUSED_STATUSES)
            ->where('sla_breach_notified_at IS NULL', null, false)
            ->where('resolution_due <', $now)
            ->get()->result();
        foreach ($resolution as $row) {
            $this->db->where('id', $row->id)->update('tickets', ['sla_breach_notified_at' => $now]);
            $this->notifySlaBreach($this->getById($row->id), 'resolution');
            $sent++;
        }

        return $sent;
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function isAssignable($user_id)
    {
        return (bool) $this->db->from('users')
            ->where(['id' => (int) $user_id, 'status' => 1])
            ->where_in('user_type', ['regular', 'developer'])
            ->count_all_results();
    }

    private function userName($user_id)
    {
        return $this->db->select('name')->get_where('users', ['id' => (int) $user_id])->row('name');
    }

    private function logHistory($ticket_id, $field, $old, $new, $actor)
    {
        $this->db->insert('ticket_history', [
            'ticket_id'       => (int) $ticket_id,
            'field'           => $field,
            'old_value'       => $old !== null ? mb_substr((string) $old, 0, 255) : null,
            'new_value'       => $new !== null ? mb_substr((string) $new, 0, 255) : null,
            'changed_by_type' => $actor['type'],
            'changed_by_id'   => $actor['id'],
            'created_on'      => date('Y-m-d H:i:s'),
        ]);
    }

    // ------------------------------------------------------------------
    // Notifications
    // ------------------------------------------------------------------

    /** Link for the requester: portal page for customer contacts, tracking link for guests. */
    public function requesterLink($ticket)
    {
        if (!empty($ticket->customer_access_id)) {
            return base_url('portal/customers/ticket/' . $ticket->uuid);
        }
        return base_url('support/track?t=' . $ticket->public_token);
    }

    private function staffLink($ticket, $user_type)
    {
        return $user_type === 'developer'
            ? base_url('portal/developers/ticket/' . $ticket->uuid)
            : base_url('helpdesk/view?uuid=' . $ticket->uuid);
    }

    /** Users subscribed to helpdesk alerts (notification_create_tickets param). */
    private function notificationUsers()
    {
        $this->load->model('system_model');
        $ids = $this->system_model->getParam('notification_create_tickets', true);
        if (empty($ids) || !is_array($ids)) return [];
        return $this->db->select('id, name, email, user_type')->from('users')
            ->where_in('id', array_map('intval', $ids))->where('status', 1)->get()->result();
    }

    private function assignee($ticket)
    {
        if (empty($ticket->assigned_to)) return null;
        return $this->db->select('id, name, email, user_type')->get_where('users', ['id' => (int) $ticket->assigned_to, 'status' => 1])->row();
    }

    private function sendEmail($to, $subject, $data)
    {
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) return;
        $this->load->model('Email_model3');
        $this->load->model('system_model');
        $data['logo'] = $this->system_model->getParam('logo');
        if (!isset($data['ref'])) {
            $data['ref'] = self::staffRef($data['ticket']);
        }
        $content = $this->load->view('_email/header', $data, true);
        $content .= $this->load->view('_email/helpdesk/ticket', $data, true);
        $content .= $this->load->view('_email/footer', [], true);
        $this->Email_model3->save($to, $subject, $content);
    }

    /** Mobile push to a staff/developer user (users.id); queued, sent by Cron::sendPush. */
    private function pushUser($user_id, $title, $body, $ticket)
    {
        $this->load->model('Push_model');
        $this->Push_model->toUser((int) $user_id, $title, $body, self::pushData($ticket));
    }

    /** Mobile push to the ticket's portal contact, if it has one. */
    private function pushRequester($ticket, $title, $body)
    {
        if (empty($ticket->customer_access_id)) return;
        $this->load->model('Push_model');
        $this->Push_model->toCustomerAccess((int) $ticket->customer_access_id, $title, $body, self::pushData($ticket));
    }

    private static function pushData($ticket)
    {
        return ['type' => 'ticket', 'uuid' => $ticket->uuid, 'tag' => 'ticket-' . $ticket->uuid];
    }

    private function notifyNewTicket($ticket)
    {
        $subject = "[{$ticket->public_ref}] {$ticket->subject}";

        // Acknowledge the requester (not for staff-created tickets without a contact).
        if (!empty($ticket->contact_email)) {
            $this->sendEmail($ticket->contact_email, "We received your request: [{$ticket->public_ref}] {$ticket->subject}", [
                'ref'          => $ticket->public_ref,
                'title'        => 'Support request received',
                'intro'        => 'Hi ' . (self::requesterName($ticket) ?: 'there') . ", thanks for contacting us. Your request has been logged and our team will get back to you. Just reply on the ticket page to add more information.",
                'ticket'       => $ticket,
                'message'      => $ticket->body,
                'link'         => $this->requesterLink($ticket),
                'link_label'   => 'View your ticket',
                'show_details' => true,
            ]);
            if ($ticket->source === 'staff') {
                $this->pushRequester($ticket, "New support ticket {$ticket->public_ref}", $ticket->subject);
            }
        }

        $sentTo = [];
        $assignee = $this->assignee($ticket);
        foreach ($this->notificationUsers() as $u) {
            if ($assignee && (int) $u->id === (int) $assignee->id) continue; // they get the assignment email
            if (isset($sentTo[$u->email])) continue;
            $sentTo[$u->email] = true;
            $this->pushUser($u->id, "New ticket {$ticket->ticket_number}" . ($ticket->company_name ? " · {$ticket->company_name}" : ''), $ticket->subject, $ticket);
            $this->sendEmail($u->email, "New ticket " . $subject, [
                'title'        => 'New support ticket',
                'intro'        => 'A new ticket was submitted by ' . (self::requesterName($ticket) ?: 'a customer') . ($ticket->company_name ? " ({$ticket->company_name})" : '') . '.',
                'ticket'       => $ticket,
                'message'      => $ticket->body,
                'link'         => $this->staffLink($ticket, $u->user_type),
                'link_label'   => 'Open ticket',
                'show_details' => true,
            ]);
        }
    }

    private function notifyAssigned($ticket)
    {
        $assignee = $this->assignee($ticket);
        if (!$assignee) return;
        $this->pushUser($assignee->id, "Ticket assigned to you: {$ticket->ticket_number}", $ticket->subject, $ticket);
        $this->sendEmail($assignee->email, "Ticket assigned to you: [{$ticket->public_ref}] {$ticket->subject}", [
            'title'        => 'Ticket assigned to you',
            'intro'        => "Hi {$assignee->name}, the following ticket has been assigned to you.",
            'ticket'       => $ticket,
            'message'      => $ticket->body,
            'link'         => $this->staffLink($ticket, $assignee->user_type),
            'link_label'   => 'Open ticket',
            'show_details' => true,
        ]);
    }

    private function notifyReply($ticket, $reply, $actor)
    {
        $subject = "Re: [{$ticket->public_ref}] {$ticket->subject}";
        $author = $this->actorName($actor, $ticket);

        if (self::isStaff($actor)) {
            if (!$reply->is_internal) {
                $this->pushRequester($ticket, "{$author} replied · {$ticket->public_ref}", $reply->body);
            }
            if (!$reply->is_internal && !empty($ticket->contact_email)) {
                $this->sendEmail($ticket->contact_email, $subject, [
                    'ref'        => $ticket->public_ref,
                    'title'      => 'New reply on your ticket',
                    'intro'      => "{$author} replied to your support ticket.",
                    'ticket'     => $ticket,
                    'message'    => $reply->body,
                    'link'       => $this->requesterLink($ticket),
                    'link_label' => 'View and reply',
                ]);
            }
            // Internal notes (or replies) by someone else notify the assignee.
            $assignee = $this->assignee($ticket);
            if ($assignee && (int) $assignee->id !== (int) $actor['id'] && $reply->is_internal) {
                $this->pushUser($assignee->id, "Internal note · {$ticket->ticket_number}", "{$author}: " . strip_tags($reply->body), $ticket);
                $this->sendEmail($assignee->email, "Internal note: [{$ticket->public_ref}] {$ticket->subject}", [
                    'title'      => 'Internal note added',
                    'intro'      => "{$author} added an internal note.",
                    'ticket'     => $ticket,
                    'message'    => $reply->body,
                    'link'       => $this->staffLink($ticket, $assignee->user_type),
                    'link_label' => 'Open ticket',
                ]);
            }
            return;
        }

        // Customer/guest reply → assignee, or the notification list when unassigned.
        $assignee = $this->assignee($ticket);
        $recipients = $assignee ? [$assignee] : $this->notificationUsers();
        foreach ($recipients as $u) {
            $this->pushUser($u->id, "{$author} replied · {$ticket->ticket_number}", $reply->body, $ticket);
            $this->sendEmail($u->email, $subject, [
                'title'      => 'Customer replied',
                'intro'      => "{$author} replied on ticket {$ticket->public_ref}.",
                'ticket'     => $ticket,
                'message'    => $reply->body,
                'link'       => $this->staffLink($ticket, $u->user_type),
                'link_label' => 'Open ticket',
            ]);
        }
    }

    private function notifyStatusChanged($ticket)
    {
        $label = self::statusLabel($ticket->status);
        $this->pushRequester($ticket, "Ticket {$ticket->public_ref} " . strtolower($label), $ticket->subject);
        if (empty($ticket->contact_email)) return;
        $this->sendEmail($ticket->contact_email, "[{$ticket->public_ref}] {$ticket->subject} — {$label}", [
            'ref'        => $ticket->public_ref,
            'title'      => "Ticket {$label}",
            'intro'      => $ticket->status === 'resolved'
                ? "We've marked your ticket as resolved. If anything is still not working, just reply on the ticket page and it will be reopened."
                : "Your ticket has been closed. If you need further help, please open a new ticket.",
            'ticket'     => $ticket,
            'message'    => null,
            'link'       => $this->requesterLink($ticket),
            'link_label' => 'View ticket',
        ]);
    }

    private function notifySlaBreach($ticket, $kind)
    {
        $recipients = [];
        if ($a = $this->assignee($ticket)) $recipients[$a->email] = $a;
        foreach ($this->notificationUsers() as $u) $recipients[$u->email] = $u;
        $due = $kind === 'first response' ? $ticket->first_response_due : $ticket->resolution_due;
        foreach ($recipients as $u) {
            $this->pushUser($u->id, "SLA breached ({$kind}) · {$ticket->ticket_number}", $ticket->subject, $ticket);
            $this->sendEmail($u->email, "SLA breached ({$kind}): [{$ticket->public_ref}] {$ticket->subject}", [
                'title'        => 'SLA breached',
                'intro'        => "The {$kind} target for this " . strtolower(self::priorityLabel($ticket->priority)) . "-priority ticket was due on " . date('d M Y H:i', strtotime($due)) . '.',
                'ticket'       => $ticket,
                'message'      => null,
                'link'         => $this->staffLink($ticket, $u->user_type),
                'link_label'   => 'Open ticket',
                'show_details' => true,
            ]);
        }
    }

    private function actorName($actor, $ticket)
    {
        if (self::isStaff($actor)) return $this->userName($actor['id']) ?: 'Support';
        if ($actor['type'] === 'customer') {
            return $this->db->select('name')->get_where('customer_access', ['id' => (int) $actor['id']])->row('name') ?: 'Customer';
        }
        return $ticket->requester_name ?: ($ticket->requester_email ?: 'Customer');
    }
}
