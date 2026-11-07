<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/Api_Controller.php';

/**
 * Helpdesk tickets for the mobile app. What each principal sees mirrors the web portals:
 *   staff     -> every ticket, gated by the helpdesk menu permissions (listing/view/add/edit/delete)
 *   developer -> tickets assigned to them, internal notes included (developer portal)
 *   customer  -> their company's tickets, never internal notes/attachments (customer portal)
 *
 * GET  api/tickets/index?status=&priority=&category_id=&assigned_to=me|unassigned|<id>&overdue=1&search=&page=&per_page=
 * GET  api/tickets/view/<uuid>
 * POST api/tickets/create           subject, body, priority, category_id, [staff: customer_id, customer_access_id,
 *                                   requester_name, requester_email, assigned_to], [customer: project_id], attachments[]
 * POST api/tickets/reply/<uuid>     body, [staff/developer: is_internal, status], attachments[]
 * POST api/tickets/update/<uuid>    field=status|priority|category_id|assigned_to, value   (staff)
 * POST api/tickets/resolve/<uuid>   (customer)
 * POST api/tickets/delete/<uuid>    (staff)
 * POST api/tickets/convertToTask/<uuid>  sprint_id, user_ids[], estimated_hours, work_type, billable, ref (staff)
 * GET  api/tickets/file/<attachment id>
 * GET  api/tickets/meta
 */
class Tickets extends Api_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tickets_model');
    }

    public function index()
    {
        $page = $this->page();
        $perPage = $this->perPage();

        if ($this->isStaff()) {
            $this->requireStaffPermission('helpdesk', 'listing');
            $assigned = $this->param('assigned_to');
            if ($assigned === 'me') $assigned = (int) $this->principal['id'];
            $filters = [
                'status'      => $this->param('status', 'active'),
                'priority'    => $this->param('priority'),
                'category_id' => $this->param('category_id'),
                'customer_id' => $this->param('customer_id'),
                'assigned_to' => $assigned,
                'source'      => $this->param('source'),
                'overdue'     => $this->param('overdue'),
                'search_text' => $this->param('search'),
            ];
            $rows = $this->Tickets_model->listing($filters, $page, $perPage);
            $total = $this->Tickets_model->count($filters);
        } else {
            if ($this->isDeveloper()) {
                $status = $this->param('status') === 'all' ? 'all' : 'active';
                $all = $this->Tickets_model->listForDeveloper((int) $this->principal['id'], $status);
            } else {
                $status = in_array($this->param('status'), ['active', 'done', 'all'], true) ? $this->param('status') : 'all';
                $all = $this->Tickets_model->listForCustomer($this->principal['customer_id'], $status);
            }
            $search = trim((string) $this->param('search', ''));
            if ($search !== '') {
                $all = array_values(array_filter($all, function ($t) use ($search) {
                    return stripos($t->subject, $search) !== false
                        || stripos((string) $t->public_ref, $search) !== false
                        || ($this->isDeveloper() && stripos((string) $t->ticket_number, $search) !== false);
                }));
            }
            $total = count($all);
            $rows = array_slice($all, ($page - 1) * $perPage, $perPage);
        }

        $this->ok([
            'items'    => array_map([$this, 'ticketPayload'], $rows),
            'page'     => $page,
            'per_page' => $perPage,
            'total'    => $total,
        ]);
    }

    public function view($uuid = '')
    {
        if ($this->isStaff()) $this->requireStaffPermission('helpdesk', 'view');
        $ticket = $this->ticketOr404($uuid);
        $internal = !$this->isCustomer();
        $thread = $this->Tickets_model->getThread($ticket, $internal);

        $data = [
            'ticket'      => $this->ticketPayload($ticket, true),
            'attachments' => array_map([$this, 'attachmentPayload'], $thread['ticket_attachments']),
            'replies'     => array_map([$this, 'replyPayload'], $thread['replies']),
            'can'         => $this->capabilities($ticket),
        ];
        if (!$this->isCustomer()) {
            $data['history'] = array_map(function ($h) {
                return [
                    'field'      => $h->field,
                    'old_value'  => $h->old_value,
                    'new_value'  => $h->new_value,
                    'changed_by' => $h->changed_by_name,
                    'created_on' => $h->created_on,
                ];
            }, $this->Tickets_model->getHistory((int) $ticket->id));
        }
        $this->ok($data);
    }

    public function create()
    {
        $this->requireMethod('post');
        $data = [
            'subject'     => $this->param('subject'),
            'body'        => $this->param('body'),
            'priority'    => $this->param('priority'),
            'category_id' => $this->param('category_id'),
            'ip_address'  => $this->input->ip_address(),
        ];
        if ($this->isStaff()) {
            $this->requireStaffPermission('helpdesk', 'add');
            $data += [
                'customer_id'        => $this->param('customer_id'),
                'customer_access_id' => $this->param('customer_access_id'),
                'requester_name'     => $this->param('requester_name'),
                'requester_email'    => $this->param('requester_email'),
                'project_id'         => $this->param('project_id'),
                'assigned_to'        => $this->param('assigned_to'),
            ];
            $source = 'staff';
        } elseif ($this->isCustomer()) {
            $data += [
                'customer_id'        => $this->principal['customer_id'],
                'customer_access_id' => $this->principal['id'],
                'project_id'         => $this->param('project_id'),
            ];
            $source = 'portal';
        } else {
            $this->forbidden('Developers cannot create tickets.');
        }

        $result = $this->Tickets_model->create($data, $source, $this->actor());
        if (!$result['result']) {
            $this->fail('validation', $result['reason'], 422);
        }
        $this->ok([
            'ticket'        => $this->ticketPayload($result['ticket']),
            'upload_errors' => $result['upload_errors'],
        ], 201);
    }

    public function reply($uuid = '')
    {
        $this->requireMethod('post');
        if ($this->isStaff()) $this->requireStaffPermission('helpdesk', 'edit');
        $ticket = $this->ticketOr404($uuid);

        $isInternal = !$this->isCustomer() && in_array((string) $this->param('is_internal'), ['1', 'true'], true);
        $status = !$this->isCustomer() ? $this->param('status') : null;
        if ($status !== null && $status !== '' && !isset(Tickets_model::statuses()[$status])) {
            $this->fail('validation', 'Invalid status.', 422);
        }

        $result = $this->Tickets_model->addReply($ticket, $this->actor(), (string) $this->param('body', ''), $isInternal, $status ?: null);
        if (!$result['result']) {
            $this->fail('validation', $result['reason'], 422);
        }
        $this->ok(['reply_id' => $result['reply_id'], 'upload_errors' => $result['upload_errors']], 201);
    }

    public function update($uuid = '')
    {
        $this->requireMethod('post');
        $this->requireStaffPermission('helpdesk', 'edit');
        $ticket = $this->ticketOr404($uuid);
        $field = (string) $this->param('field');
        if (!in_array($field, ['status', 'priority', 'category_id', 'assigned_to'], true)) {
            $this->fail('validation', 'Invalid field.', 422);
        }
        $value = $this->param('value');
        if ($field === 'assigned_to' && $value === 'me') {
            $value = (int) $this->principal['id'];
        }
        $result = $this->Tickets_model->changeField($ticket, $field, $value, $this->actor());
        if (!$result['result']) {
            $this->fail('validation', $result['reason'], 422);
        }
        $fresh = $this->Tickets_model->getById((int) $ticket->id);
        $this->ok(['changed' => !empty($result['changed']), 'ticket' => $this->ticketPayload($fresh, true)]);
    }

    public function resolve($uuid = '')
    {
        $this->requireMethod('post');
        if (!$this->isCustomer()) $this->forbidden();
        $ticket = $this->ticketOr404($uuid);
        if (in_array($ticket->status, Tickets_model::CLOSED_STATUSES, true)) {
            $this->fail('invalid_state', 'This ticket is already resolved or closed.', 409);
        }
        $this->Tickets_model->changeField($ticket, 'status', 'resolved', $this->actor());
        $this->ok(['ticket' => $this->ticketPayload($this->Tickets_model->getById((int) $ticket->id), true)]);
    }

    public function delete($uuid = '')
    {
        $this->requireMethod('post');
        $this->requireStaffPermission('helpdesk', 'delete');
        $ticket = $this->ticketOr404($uuid);
        $this->Tickets_model->softDelete($ticket, $this->actor());
        $this->ok(['deleted' => true]);
    }

    public function convertToTask($uuid = '')
    {
        $this->requireMethod('post');
        $this->requireStaffPermission('helpdesk', 'edit');
        $ticket = $this->ticketOr404($uuid);
        $userIds = $this->param('user_ids', []);
        if (is_string($userIds)) {
            $decoded = json_decode($userIds, true);
            $userIds = is_array($decoded) ? $decoded : explode(',', $userIds);
        }
        $result = $this->Tickets_model->convertToTask(
            $ticket,
            $this->intParam('sprint_id'),
            is_array($userIds) ? $userIds : [],
            $this->param('estimated_hours'),
            $this->param('work_type'),
            $this->param('billable'),
            trim((string) $this->param('ref', '')),
            $this->actor()
        );
        if (!$result['result']) {
            $this->fail('validation', $result['reason'], 422);
        }
        $this->ok(['task_uuid' => $result['task_uuid'], 'task_number' => $result['task_number']], 201);
    }

    /** Streams an attachment after the same access checks as the portals. */
    public function file($id = 0)
    {
        $attachment = $this->Tickets_model->getAttachment((int) $id);
        if (empty($attachment)) $this->notFound('File');
        $ticket = $this->Tickets_model->getById((int) $attachment->ticket_id);
        if (empty($ticket)) $this->notFound('File');

        if ($this->isStaff()) {
            $this->requireStaffPermission('helpdesk', 'view');
        } elseif ($this->isDeveloper()) {
            if ((int) $ticket->assigned_to !== (int) $this->principal['id']) $this->notFound('File');
        } else {
            if ($attachment->is_internal || (int) $ticket->customer_id !== (int) $this->principal['customer_id']) {
                $this->notFound('File');
            }
        }
        header_remove('Content-Type');
        header_remove('Cache-Control');
        $this->Tickets_model->streamAttachment($attachment);
    }

    /** Lookup values for filters and forms. */
    public function meta()
    {
        $statuses = [];
        foreach (Tickets_model::statuses() as $k => $s) $statuses[] = ['value' => $k, 'label' => $s['label']];
        $priorities = [];
        foreach (Tickets_model::priorities() as $k => $p) $priorities[] = ['value' => $k, 'label' => $p['label']];

        $data = [
            'statuses'   => $statuses,
            'priorities' => $priorities,
            'categories' => array_map(function ($c) {
                return ['id' => (int) $c->id, 'name' => $c->name];
            }, $this->Tickets_model->categories()),
            'upload' => [
                'max_files'     => Tickets_model::MAX_FILES,
                'max_file_kb'   => Tickets_model::MAX_FILE_KB,
                'allowed_types' => explode('|', Tickets_model::ALLOWED_TYPES),
            ],
        ];

        if ($this->isStaff()) {
            $data['assignees'] = array_map(function ($u) {
                return ['id' => (int) $u->id, 'name' => $u->name, 'user_type' => $u->user_type];
            }, $this->Tickets_model->assignableUsers());
            $data['customers'] = array_map(function ($c) {
                return ['customer_id' => (int) $c->customer_id, 'company_name' => $c->company_name];
            }, $this->db->select('customer_id, company_name')->from('customers')
                ->where(['status' => 1, 'active' => 1])->order_by('company_name')->get()->result());
            $data['contacts'] = array_map(function ($c) {
                return ['id' => (int) $c->id, 'customer_id' => (int) $c->customer_id, 'name' => $c->name, 'email' => $c->email];
            }, $this->db->select('id, customer_id, name, email')->from('customer_access')
                ->where('status', 1)->order_by('name')->get()->result());
        }
        if ($this->isCustomer()) {
            $data['projects'] = array_map(function ($p) {
                return ['id' => (int) $p->id, 'name' => $p->name];
            }, $this->db->select('id, name')->from('projects')
                ->where(['customer_id' => $this->principal['customer_id'], 'status' => 1, 'active' => 1])
                ->order_by('name')->get()->result());
        }
        $this->ok($data);
    }

    // ------------------------------------------------------------------

    private function ticketOr404($uuid)
    {
        if (!self::isUuid($uuid)) $this->notFound('Ticket');
        if ($this->isStaff()) {
            $ticket = $this->Tickets_model->getByUuid($uuid);
        } elseif ($this->isDeveloper()) {
            $ticket = $this->Tickets_model->getForDeveloper($uuid, (int) $this->principal['id']);
        } else {
            $ticket = $this->Tickets_model->getForCustomer($uuid, (int) $this->principal['customer_id']);
        }
        if (empty($ticket)) $this->notFound('Ticket');
        return $ticket;
    }

    private function capabilities($ticket)
    {
        $closed = $ticket->status === 'closed';
        if ($this->isStaff()) {
            return [
                'reply'           => $this->staffCan('helpdesk', 'edit'),
                'internal_note'   => $this->staffCan('helpdesk', 'edit'),
                'edit'            => $this->staffCan('helpdesk', 'edit'),
                'delete'          => $this->staffCan('helpdesk', 'delete'),
                'convert_to_task' => $this->staffCan('helpdesk', 'edit') && empty($ticket->task_id),
                'resolve'         => false,
            ];
        }
        if ($this->isDeveloper()) {
            return ['reply' => true, 'internal_note' => true, 'edit' => false, 'delete' => false, 'convert_to_task' => false, 'resolve' => false];
        }
        return [
            'reply'           => !$closed,
            'internal_note'   => false,
            'edit'            => false,
            'delete'          => false,
            'convert_to_task' => false,
            'resolve'         => !in_array($ticket->status, Tickets_model::CLOSED_STATUSES, true),
        ];
    }

    /** Ticket fields per role: customers never get internal refs, SLA data or staff contact details. */
    private function ticketPayload($t, $withBody = false)
    {
        $out = [
            'uuid'             => $t->uuid,
            'public_ref'       => $t->public_ref,
            'subject'          => $t->subject,
            'status'           => $t->status,
            'status_label'     => Tickets_model::statusLabel($t->status),
            'priority'         => $t->priority,
            'priority_label'   => Tickets_model::priorityLabel($t->priority),
            'category_id'      => $t->category_id !== null ? (int) $t->category_id : null,
            'category_name'    => $t->category_name,
            'company_name'     => $t->company_name,
            'project_name'     => $t->project_name,
            'assignee_name'    => $t->assignee_name,
            'contact_name'     => $t->contact_name,
            'created_on'       => $t->created_on,
            'last_activity_at' => $t->last_activity_at,
            'resolved_at'      => $t->resolved_at,
            'reply_count'      => isset($t->reply_count) ? (int) $t->reply_count : null,
            'attachment_count' => isset($t->attachment_count) ? (int) $t->attachment_count : null,
        ];
        if (!$this->isCustomer()) {
            $out += [
                'ticket_number'      => $t->ticket_number,
                'ref'                => Tickets_model::staffRef($t),
                'source'             => $t->source,
                'customer_id'        => $t->customer_id !== null ? (int) $t->customer_id : null,
                'contact_email'      => $t->contact_email,
                'assigned_to'        => $t->assigned_to !== null ? (int) $t->assigned_to : null,
                'assignee_type'      => $t->assignee_type,
                'first_response_due' => $t->first_response_due,
                'first_responded_at' => $t->first_responded_at,
                'resolution_due'     => $t->resolution_due,
                'overdue'            => Tickets_model::isOverdue($t),
                'task_uuid'          => $t->task_uuid,
                'task_number'        => $t->task_number,
            ];
        } else {
            $out['ref'] = $t->public_ref;
        }
        if ($withBody) {
            $out['body_html'] = Tickets_model::formatBody($t->body);
        }
        return $out;
    }

    private function replyPayload($r)
    {
        return [
            'id'          => (int) $r->id,
            'author_type' => $r->author_type,
            'author_name' => $r->author_name,
            'is_staff'    => in_array($r->author_type, ['admin', 'developer', 'system'], true),
            'is_internal' => (bool) $r->is_internal,
            'body_html'   => Tickets_model::formatBody($r->body),
            'created_on'  => $r->created_on,
            'attachments' => array_map([$this, 'attachmentPayload'], $r->attachments),
        ];
    }

    private function attachmentPayload($a)
    {
        return [
            'id'          => (int) $a->id,
            'name'        => $a->original_name,
            'mime'        => $a->mime,
            'size'        => (int) $a->size,
            'is_image'    => Tickets_model::isImageAttachment($a),
            'is_internal' => (bool) $a->is_internal,
            'url'         => base_url('api/tickets/file/' . (int) $a->id),
        ];
    }
}
