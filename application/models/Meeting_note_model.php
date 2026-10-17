<?php
class Meeting_note_model extends CI_Model
{
    public function parseNextMeetingDate($dateRaw, $timeRaw = null)
    {
        $date = trim((string) $dateRaw);
        if ($date === '') {
            return null;
        }
        $time = trim((string) ($timeRaw ?? $this->input->post('next_meeting_time')));
        if ($time === '') {
            return null;
        }
        if (strlen($time) === 5) {
            $time .= ':00';
        }
        return $date . ' ' . $time;
    }

    public function formatMeetingDatetime($value)
    {
        if (empty($value)) {
            return '';
        }
        $dt = date_create($value);
        if (!$dt) {
            return htmlspecialchars((string) $value);
        }
        return date_format($dt, 'Y-m-d H:i');
    }

    public function formatNextMeeting($value)
    {
        if (empty($value)) {
            return '';
        }
        $dt = date_create($value);
        if (!$dt) {
            return htmlspecialchars((string) $value);
        }
        $format = date_format($dt, 'H:i:s') === '00:00:00' ? 'd M Y' : 'd M Y H:i';
        return date_format($dt, $format);
    }

    public function get_all_notes($customer_id = '', $start_date = '', $end_date = '')
    {
        $this->db
            ->select('mn.*, u.name updatedBy')
            ->from('meeting_notes mn')
            ->join('users u', 'u.id = mn.last_updated_by', 'left');

        if ($customer_id !== '' && $customer_id !== null) {
            $this->db->where('mn.customer_id', (int) $customer_id);
        }
        if (!empty($start_date)) {
            $this->db->where('DATE(mn.meeting_datetime) >=', $start_date);
        }
        if (!empty($end_date)) {
            $this->db->where('DATE(mn.meeting_datetime) <=', $end_date);
        }

        return $this->db->order_by('mn.meeting_datetime', 'DESC')->get()->result();
    }

    public function get_note($id)
    {
        return $this->db->get_where('meeting_notes', ['id' => $id])->row();
    }

    public function insert_note($data)
    {
        $data['last_updated'] = $data['created_at'] = date('Y-m-d H:i:s');
        $data['uuid'] = gen_uuid();
        $data['customer_id'] = ($this->input->post('customer_id') == 'other') ? null : $this->input->post('customer_id');
        $this->db->insert('meeting_notes', $data);
        $note_id = $this->db->insert_id();

        // Ensure upload folder exists
        $upload_dir = './uploads/meetings/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (!empty($_FILES['attachments']['name'][0])) {
            $files = $_FILES;
            $count = count($files['attachments']['name']);
            $attachment_data = [];

            for ($i = 0; $i < $count; $i++) {
                $_FILES['attachment']['name']     = $files['attachments']['name'][$i];
                $_FILES['attachment']['type']     = $files['attachments']['type'][$i];
                $_FILES['attachment']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                $_FILES['attachment']['error']    = $files['attachments']['error'][$i];
                $_FILES['attachment']['size']     = $files['attachments']['size'][$i];

                $config['upload_path']   = './uploads/meetings/';
                $config['allowed_types'] = '*';
                $config['max_size']      = 2048;
                $config['encrypt_name']  = TRUE;

                $this->upload->initialize($config);

                if ($this->upload->do_upload('attachment')) {
                    $upload_data = $this->upload->data();
                    $attachment_data[] = [
                        'uuid'              =>  gen_uuid(),
                        'uploaded_at'       =>  date("Y-m-d H:i:s"),
                        'uploaded_by'       =>  $_SESSION['user_id'],
                        'meeting_note_id'   => $note_id,
                        'file_name'         => $upload_data['orig_name'],
                        'file_path'         => 'uploads/meetings/' . $upload_data['file_name'],
                    ];
                }
            }

            if (!empty($attachment_data)) {
                $this->Attachment_model->insert_attachments($attachment_data);
            }
        }
        return $note_id;
    }

    public function update_note($id)
    {
        $before = $this->get_note($id);

        // Ensure upload folder exists
        $upload_dir = './uploads/meetings/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (!empty($_FILES['attachments']['name'][0])) {
            $files = $_FILES;
            $count = count($files['attachments']['name']);
            $attachment_data = [];

            for ($i = 0; $i < $count; $i++) {
                $_FILES['attachment']['name']     = $files['attachments']['name'][$i];
                $_FILES['attachment']['type']     = $files['attachments']['type'][$i];
                $_FILES['attachment']['tmp_name'] = $files['attachments']['tmp_name'][$i];
                $_FILES['attachment']['error']    = $files['attachments']['error'][$i];
                $_FILES['attachment']['size']     = $files['attachments']['size'][$i];

                $config['upload_path']   = './uploads/meetings';
                $config['allowed_types'] = '*';
                $config['max_size']      = 4096;
                $config['encrypt_name']  = TRUE;

                $this->upload->initialize($config);

                if ($this->upload->do_upload('attachment')) {
                    $upload_data = $this->upload->data();
                    $attachment_data[] = [
                        'uuid'              =>  gen_uuid(),
                        'uploaded_at'       =>  date("Y-m-d H:i:s"),
                        'uploaded_by'       =>  $_SESSION['user_id'],
                        'meeting_note_id'   =>  $id,
                        'file_name'         =>  $upload_data['orig_name'],
                        'file_path'         =>  'uploads/meetings/' . $upload_data['file_name'],
                    ];
                }
            }

            if (!empty($attachment_data)) {
                $this->Attachment_model->insert_attachments($attachment_data);
            }
        }

        $data = [
            'customer_id' => ($this->input->post('customer_id') == 'other') ? null : $this->input->post('customer_id'),
            'customer_name' => $this->input->post('customer_name'),
            'meeting_datetime' => $this->input->post('meeting_date') . ' ' . $this->input->post('meeting_time'),
            'notes' => $this->input->post('notes'),
            'agenda' => (string) $this->input->post('agenda'),
            'attendees' => $this->input->post('attendees', true),
            'lieu' => $this->input->post('lieu', true),
            'next_meeting_date' => $this->parseNextMeetingDate(
                $this->input->post('next_meeting_date'),
                $this->input->post('next_meeting_time')
            ),
            'last_updated'  =>  date("Y-m-d H:i:s"),
            'last_updated_by'   =>  $_SESSION['user_id']
        ];
        // A planned meeting moved to another time gets its reminder again.
        if (!empty($before) && $before->status === 'planned' && $data['meeting_datetime'] . ':00' !== $before->meeting_datetime && $data['meeting_datetime'] !== $before->meeting_datetime) {
            $data['reminder_sent_on'] = null;
        }
        // Followers of a live note refresh when the version moves.
        $this->db->set('notes_version', 'notes_version + 1', false);
        return $this->db->where('id', $id)->update('meeting_notes', $data);
    }

    public function delete_note($id)
    {
        $this->db->delete('meeting_note_entries', ['meeting_note_id' => $id]);
        $this->db->delete('meeting_note_shares', ['meeting_note_id' => $id]);
        $this->db->delete('meeting_note_admin_presence', ['meeting_note_id' => $id]);
        return $this->db->delete('meeting_notes', ['id' => $id]);
    }

    // ---------------------------------------------------------------------
    // Live sharing: participants (customer portal users, developers) follow
    // a live note by polling and, when allowed, add their own entries.
    // ---------------------------------------------------------------------

    /** Seen within this many seconds = viewing the page now (live pages poll every 2s, planned every 10s). */
    const ONLINE_WINDOW = 15;
    /** Seen within this many seconds = page still open, but browsers throttle background tabs to ~1 poll/minute. */
    const AWAY_WINDOW = 90;

    /**
     * Keeps basic formatting from Summernote and drops scripts, event handlers and javascript: links.
     * Used for entries, which can come from portal users.
     */
    public function sanitize_entry_html($html)
    {
        $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1\s*>#is', '', (string) $html);
        $html = strip_tags($html, '<p><br><b><strong><i><em><u><s><strike><ul><ol><li><a><blockquote><pre><code><span><div><h3><h4><h5><h6>');
        $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
        $html = preg_replace('/(href|src)\s*=\s*("|\')?\s*(javascript|data|vbscript):[^"\'>\s]*("|\')?/i', '$1="#"', $html);
        return trim($html);
    }

    /** True when the HTML has visible text. */
    public function html_has_text($html)
    {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8'), " \t\n\r\0\x0B\xC2\xA0") !== '';
    }

    public function get_shares($noteId)
    {
        $noteId = (int) $noteId;
        $since = $this->db->escape(date('Y-m-d H:i:s', time() - self::ONLINE_WINDOW));
        $awaySince = $this->db->escape(date('Y-m-d H:i:s', time() - self::AWAY_WINDOW));
        return $this->db->query("
            SELECT s.*,
                   CASE WHEN s.participant_type = 'customer' THEN ca.name ELSE u.name END AS name,
                   CASE WHEN s.participant_type = 'customer' THEN ca.email ELSE u.email END AS email,
                   (s.last_seen IS NOT NULL AND s.last_seen >= {$since}) AS online,
                   (s.last_seen IS NOT NULL AND s.last_seen < {$since} AND s.last_seen >= {$awaySince}) AS away
            FROM meeting_note_shares s
            LEFT JOIN customer_access ca ON s.participant_type = 'customer' AND ca.id = s.participant_id
            LEFT JOIN users u ON s.participant_type = 'developer' AND u.id = s.participant_id AND u.user_type = 'developer'
            WHERE s.meeting_note_id = {$noteId}
            ORDER BY s.participant_type, name
        ")->result();
    }

    public function get_share($noteId, $type, $participantId)
    {
        return $this->db->get_where('meeting_note_shares', [
            'meeting_note_id'   => (int) $noteId,
            'participant_type'  => $type,
            'participant_id'    => (int) $participantId,
        ])->row();
    }

    /**
     * People who can still be invited to the note: the note customer's active portal users and all active developers.
     */
    public function shareable_people($note)
    {
        $noteId = (int) $note->id;
        $customers = [];
        if (!empty($note->customer_id)) {
            $customers = $this->db->query("
                SELECT ca.id, ca.name, ca.email
                FROM customer_access ca
                WHERE ca.customer_id = ? AND ca.status = 1
                  AND ca.id NOT IN (SELECT participant_id FROM meeting_note_shares WHERE meeting_note_id = {$noteId} AND participant_type = 'customer')
                ORDER BY ca.name
            ", [(int) $note->customer_id])->result();
        }
        $developers = $this->db->query("
            SELECT u.id, u.name, u.email
            FROM users u
            WHERE u.user_type = 'developer' AND u.status = '1'
              AND u.id NOT IN (SELECT participant_id FROM meeting_note_shares WHERE meeting_note_id = {$noteId} AND participant_type = 'developer')
            ORDER BY u.name
        ")->result();
        return ['customers' => $customers, 'developers' => $developers];
    }

    /**
     * @return array ['result' => bool, 'reason' => string, 'share_id' => int, 'person' => object]
     */
    public function add_share($note, $type, $participantId, $canContribute)
    {
        $participantId = (int) $participantId;
        if ($type === 'customer') {
            $person = $this->db->select('id, name, email')->from('customer_access')->where([
                'id' => $participantId, 'status' => 1, 'customer_id' => (int) $note->customer_id,
            ])->get()->row();
            if (empty($note->customer_id) || empty($person)) {
                return ['result' => false, 'reason' => "This portal user doesn't belong to the note's customer."];
            }
        } elseif ($type === 'developer') {
            $person = $this->db->select('id, name, email')->from('users')->where([
                'id' => $participantId, 'status' => '1', 'user_type' => 'developer',
            ])->get()->row();
            if (empty($person)) {
                return ['result' => false, 'reason' => 'Developer not found or inactive.'];
            }
        } else {
            return ['result' => false, 'reason' => 'Invalid participant type.'];
        }

        if ($this->get_share($note->id, $type, $participantId)) {
            return ['result' => false, 'reason' => 'Already shared with this person.'];
        }

        $this->db->insert('meeting_note_shares', [
            'meeting_note_id'   => (int) $note->id,
            'participant_type'  => $type,
            'participant_id'    => $participantId,
            'can_contribute'    => $canContribute ? 1 : 0,
            'token'             => bin2hex(random_bytes(16)),
            'created_on'        => date('Y-m-d H:i:s'),
            'created_by'        => isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null,
        ]);
        return ['result' => true, 'share_id' => (int) $this->db->insert_id(), 'person' => $person];
    }

    public function update_share($noteId, $shareId, $canContribute)
    {
        $this->db->set('can_contribute', $canContribute ? 1 : 0)
            ->where(['id' => (int) $shareId, 'meeting_note_id' => (int) $noteId])
            ->update('meeting_note_shares');
        return $this->db->affected_rows() >= 0;
    }

    public function remove_share($noteId, $shareId)
    {
        $this->db->where(['id' => (int) $shareId, 'meeting_note_id' => (int) $noteId])->delete('meeting_note_shares');
        return $this->db->affected_rows() > 0;
    }

    public function touch_share($shareId)
    {
        $this->db->set('last_seen', date('Y-m-d H:i:s'))->where('id', (int) $shareId)->update('meeting_note_shares');
    }

    public function get_entries($noteId, $sinceId = 0)
    {
        return $this->db->query("
            SELECT e.id, e.author_type, e.author_id, e.body, e.created_on,
                   CASE WHEN e.author_type = 'customer' THEN ca.name ELSE u.name END AS author_name
            FROM meeting_note_entries e
            LEFT JOIN customer_access ca ON e.author_type = 'customer' AND ca.id = e.author_id
            LEFT JOIN users u ON e.author_type IN ('admin','developer') AND u.id = e.author_id
            WHERE e.meeting_note_id = ? AND e.id > ?
            ORDER BY e.id
        ", [(int) $noteId, (int) $sinceId])->result();
    }

    /** @return int|false new entry id */
    public function add_entry($noteId, $authorType, $authorId, $body)
    {
        $body = $this->sanitize_entry_html($body);
        if (!$this->html_has_text($body)) {
            return false;
        }
        $this->db->insert('meeting_note_entries', [
            'meeting_note_id'   => (int) $noteId,
            'author_type'       => $authorType,
            'author_id'         => (int) $authorId,
            'body'              => $body,
            'created_on'        => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->insert_id();
    }

    /** Autosave of the main notes while live; bumps notes_version so followers refresh. */
    public function live_save($noteId, $html)
    {
        $this->db->set('notes', (string) $html)
            ->set('notes_version', 'notes_version + 1', false)
            ->set('last_updated', date('Y-m-d H:i:s'))
            ->set('last_updated_by', (int) $_SESSION['user_id'])
            ->where(['id' => (int) $noteId, 'status' => 'live'])
            ->update('meeting_notes');
        if ($this->db->affected_rows() < 1) {
            return false;
        }
        return (int) $this->db->select('notes_version')->from('meeting_notes')->where('id', (int) $noteId)->get()->row()->notes_version;
    }

    public function set_status($noteId, $status)
    {
        $this->db->set('status', $status);
        $this->db->set('closed_on', $status === 'closed' ? date('Y-m-d H:i:s') : null);
        $this->db->where('id', (int) $noteId)->update('meeting_notes');
        if ($status === 'live') {
            $this->notify_live($noteId);
        }
    }

    /**
     * Everything a follower needs since its last poll. Notes HTML is only sent when the version moved.
     */
    public function poll_payload($note, $sinceVersion, $sinceEntryId)
    {
        $payload = [
            'result'        => true,
            'status'        => $note->status,
            'closed_on'     => $note->closed_on,
            'notes_version' => (int) $note->notes_version,
            'entries'       => $this->get_entries($note->id, $sinceEntryId),
        ];
        if ((int) $sinceVersion !== (int) $note->notes_version) {
            $payload['notes'] = $note->notes;
        }
        return $payload;
    }

    /** Notes shared with a portal participant, live ones first. */
    public function notes_shared_with($type, $participantId)
    {
        $this->promote_due();
        return $this->db->select('mn.id, mn.uuid, mn.customer_name, mn.meeting_datetime, mn.lieu, mn.status, mn.closed_on, s.can_contribute')
            ->from('meeting_note_shares s')
            ->join('meeting_notes mn', 'mn.id = s.meeting_note_id')
            ->where(['s.participant_type' => $type, 's.participant_id' => (int) $participantId])
            ->where_in('mn.status', ['planned', 'live', 'closed'])
            ->order_by("FIELD(mn.status, 'live', 'planned', 'closed')", '', false)
            ->order_by('mn.meeting_datetime', 'DESC')
            ->get()->result();
    }

    /** Note id for a shared-link uuid, or 0. Portal URLs never carry the numeric id. */
    public function note_id_for_uuid($uuid)
    {
        if (!preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', (string) $uuid)) {
            return 0;
        }
        $row = $this->db->select('id')->from('meeting_notes')->where('uuid', strtolower($uuid))->get()->row();
        return $row ? (int) $row->id : 0;
    }

    /**
     * Note + share for a portal participant, or null when the note isn't shared with them.
     * Drafts are never visible in portals.
     */
    public function portal_note($noteId, $type, $participantId)
    {
        $share = $this->get_share($noteId, $type, $participantId);
        if (empty($share)) {
            return null;
        }
        $this->promote_due((int) $noteId);
        $note = $this->get_note((int) $noteId);
        if (empty($note) || !in_array($note->status, ['planned', 'live', 'closed'], true)) {
            return null;
        }
        return ['note' => $note, 'share' => $share];
    }

    /** Poll answer for a portal participant; also records that they're watching. */
    public function portal_poll($noteId, $type, $participantId, $sinceVersion, $sinceEntryId)
    {
        $access = $this->portal_note($noteId, $type, $participantId);
        if (empty($access)) {
            return ['result' => false, 'removed' => true, 'reason' => 'This meeting is no longer shared with you.'];
        }
        $this->touch_share($access['share']->id);
        $participants = $this->get_participants($noteId, $type, $participantId);
        if ($access['note']->status === 'planned') {
            return ['result' => true, 'status' => 'planned', 'meeting_datetime' => $access['note']->meeting_datetime, 'participants' => $participants];
        }
        $payload = $this->poll_payload($access['note'], $sinceVersion, $sinceEntryId);
        $payload['can_contribute'] = ($access['share']->can_contribute == 1 && $access['note']->status === 'live');
        $payload['participants'] = $participants;
        return $payload;
    }

    public function portal_add_entry($noteId, $type, $participantId, $body)
    {
        $access = $this->portal_note($noteId, $type, $participantId);
        if (empty($access)) {
            return ['result' => false, 'reason' => 'This meeting is no longer shared with you.'];
        }
        if ($access['note']->status !== 'live') {
            return ['result' => false, 'reason' => $access['note']->status === 'planned' ? "This meeting hasn't started yet." : 'This meeting has ended.'];
        }
        if ($access['share']->can_contribute != 1) {
            return ['result' => false, 'reason' => "You don't have permission to add entries."];
        }
        $entryId = $this->add_entry($noteId, $type, $participantId, $body);
        return $entryId === false
            ? ['result' => false, 'reason' => 'Please write something first.']
            : ['result' => true, 'entry_id' => $entryId];
    }

    // ---------------------------------------------------------------------
    // Planned meetings: auto go-live, reminders, notification emails, calendar links
    // ---------------------------------------------------------------------

    /** Meetings have no end time; calendar entries use this length. */
    const DEFAULT_DURATION_MINUTES = 60;
    const REMINDER_MINUTES_BEFORE = 60;

    public function lieu_label($lieu)
    {
        $labels = ['our' => 'Our office', 'client' => 'Client office', 'online' => 'Online', 'other' => 'Other'];
        return isset($labels[$lieu]) ? $labels[$lieu] : ucfirst((string) $lieu);
    }

    /**
     * Turns planned meetings whose start time has passed into live ones and emails their participants.
     * Called by cron every minute and, as a fallback, from pages that show meetings.
     * Times are compared in PHP's time zone, which is what meeting_datetime is entered in.
     */
    public function promote_due($noteId = null)
    {
        $this->db->select('id')->from('meeting_notes')
            ->where('status', 'planned')
            ->where('meeting_datetime <=', date('Y-m-d H:i:s'));
        if ($noteId !== null) {
            $this->db->where('id', (int) $noteId);
        }
        $promoted = 0;
        foreach ($this->db->get()->result() as $row) {
            $this->db->set('status', 'live')->where(['id' => $row->id, 'status' => 'planned'])->update('meeting_notes');
            if ($this->db->affected_rows() === 1) {
                $this->notify_live($row->id);
                $promoted++;
            }
        }
        return $promoted;
    }

    /** "Now live" email, sent once per meeting (claimed atomically). */
    public function notify_live($noteId)
    {
        $this->db->set('live_notified_on', date('Y-m-d H:i:s'))
            ->where('id', (int) $noteId)->where('live_notified_on IS NULL', null, false)
            ->update('meeting_notes');
        if ($this->db->affected_rows() !== 1) {
            return 0;
        }
        return $this->email_participants($this->get_note($noteId), 'live');
    }

    /** Reminder email for planned meetings starting within the next hour, once per meeting. */
    public function send_due_reminders()
    {
        $now = date('Y-m-d H:i:s');
        $rows = $this->db->select('id')->from('meeting_notes')
            ->where('status', 'planned')
            ->where('reminder_sent_on IS NULL', null, false)
            ->where('meeting_datetime >', $now)
            ->where('meeting_datetime <=', date('Y-m-d H:i:s', time() + self::REMINDER_MINUTES_BEFORE * 60))
            ->get()->result();
        $sent = 0;
        foreach ($rows as $row) {
            $this->db->set('reminder_sent_on', $now)
                ->where('id', $row->id)->where('reminder_sent_on IS NULL', null, false)
                ->update('meeting_notes');
            if ($this->db->affected_rows() === 1) {
                $sent += $this->email_participants($this->get_note($row->id), 'reminder');
            }
        }
        return $sent;
    }

    /**
     * Emails participants of a note.
     * @param string $kind invite | reminder | live
     * @param array|null $shareIds only these shares (e.g. the people just invited); null = everyone
     * @return int emails queued
     */
    public function email_participants($note, $kind, $shareIds = null)
    {
        if (empty($note)) {
            return 0;
        }
        $this->load->model("Email_model3");
        $this->load->model("system_model");
        $logo = $this->system_model->getParam("logo");
        $subjects = [
            'invite'    => ($note->status === 'planned' ? "Meeting invitation: " : "Live meeting notes: ") . $note->customer_name,
            'reminder'  => "Reminder: meeting at " . date('H:i', strtotime($note->meeting_datetime)) . " - " . $note->customer_name,
            'live'      => "Meeting started - follow live: " . $note->customer_name,
            'update'    => ($note->status === 'planned' ? "Meeting updated: " : "Meeting notes updated: ") . $note->customer_name,
        ];
        $count = 0;
        foreach ($this->get_shares($note->id) as $share) {
            if ($shareIds !== null && !in_array((int) $share->id, array_map('intval', $shareIds), true)) {
                continue;
            }
            if (empty($share->email) || !filter_var($share->email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $portal = ($share->participant_type === 'customer') ? 'customers' : 'developers';
            $emailData = [
                'logo'      => $logo,
                'kind'      => $kind,
                'name'      => $share->name,
                'meeting'   => $note,
                'lieu'      => $this->lieu_label($note->lieu),
                'link'      => base_url("portal/{$portal}/meeting/" . $note->uuid),
                'calendar'  => ($note->status === 'planned') ? $this->calendar_links($note, $share) : null,
            ];
            $content = $this->load->view("_email/header", $emailData, true);
            $content .= $this->load->view("_email/meetingNotification", $emailData, true);
            $content .= $this->load->view("_email/footer", [], true);
            $this->Email_model3->save($share->email, $subjects[$kind], $content);
            $count++;
        }
        return $count;
    }

    /** Start/end as DateTime in PHP's zone (meeting_datetime is entered in local time). */
    private function meeting_times($note)
    {
        $start = new DateTime($note->meeting_datetime);
        $end = (clone $start)->modify('+' . self::DEFAULT_DURATION_MINUTES . ' minutes');
        return [$start, $end];
    }

    private function calendar_text($note, $share)
    {
        $portal = ($share->participant_type === 'customer') ? 'customers' : 'developers';
        $text = "Meeting notes (live during the meeting): " . base_url("portal/{$portal}/meeting/" . $note->uuid);
        $agenda = trim(html_entity_decode(strip_tags(str_replace(['</li>', '</p>', '<br>', '<br/>', '<br />'], "\n", (string) $note->agenda)), ENT_QUOTES, 'UTF-8'));
        if ($agenda !== '') {
            $text .= "\n\nAgenda:\n" . preg_replace("/\n{2,}/", "\n", $agenda);
        }
        return $text;
    }

    /** Add-to-calendar links for one participant: Google, Outlook and a tokenised .ics download. */
    public function calendar_links($note, $share)
    {
        list($start, $end) = $this->meeting_times($note);
        $utc = new DateTimeZone('UTC');
        $title = "Meeting: " . $note->customer_name;
        $text = $this->calendar_text($note, $share);
        $location = $this->lieu_label($note->lieu);
        $gStart = (clone $start)->setTimezone($utc)->format('Ymd\THis\Z');
        $gEnd = (clone $end)->setTimezone($utc)->format('Ymd\THis\Z');
        return [
            'google'  => 'https://calendar.google.com/calendar/render?' . http_build_query([
                'action' => 'TEMPLATE', 'text' => $title, 'dates' => $gStart . '/' . $gEnd, 'details' => $text, 'location' => $location,
            ]),
            'outlook' => 'https://outlook.live.com/calendar/0/deeplink/compose?' . http_build_query([
                'path' => '/calendar/action/compose', 'rru' => 'addevent', 'subject' => $title,
                'startdt' => (clone $start)->setTimezone($utc)->format('Y-m-d\TH:i:s\Z'),
                'enddt' => (clone $end)->setTimezone($utc)->format('Y-m-d\TH:i:s\Z'),
                'body' => $text, 'location' => $location,
            ]),
            'ics'     => empty($share->token) ? null : base_url('calendar/meeting/' . $share->token),
        ];
    }

    /** Share row (with participant name/email) for a calendar token. */
    public function share_for_token($token)
    {
        if (!preg_match('/^[a-f0-9]{32}$/', (string) $token)) {
            return null;
        }
        $row = $this->db->get_where('meeting_note_shares', ['token' => $token])->row();
        if (empty($row)) {
            return null;
        }
        foreach ($this->get_shares($row->meeting_note_id) as $share) {
            if ((int) $share->id === (int) $row->id) {
                return $share;
            }
        }
        return null;
    }

    /** iCalendar (RFC 5545) document for one participant. */
    public function ics($note, $share)
    {
        list($start, $end) = $this->meeting_times($note);
        $utc = new DateTimeZone('UTC');
        $esc = function ($v) {
            return str_replace(["\\", ";", ",", "\r\n", "\n"], ["\\\\", "\;", "\\,", "\\n", "\\n"], (string) $v);
        };
        $host = parse_url(base_url(), PHP_URL_HOST) ?: 'tweezzo';
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Tweezzo//Meeting Notes//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:meeting-' . $note->uuid . '@' . $host,
            'DTSTAMP:' . (new DateTime('now', $utc))->format('Ymd\THis\Z'),
            'DTSTART:' . (clone $start)->setTimezone($utc)->format('Ymd\THis\Z'),
            'DTEND:' . (clone $end)->setTimezone($utc)->format('Ymd\THis\Z'),
            'SUMMARY:' . $esc("Meeting: " . $note->customer_name),
            'LOCATION:' . $esc($this->lieu_label($note->lieu)),
            'DESCRIPTION:' . $esc($this->calendar_text($note, $share)),
            'STATUS:CONFIRMED',
            'BEGIN:VALARM',
            'ACTION:DISPLAY',
            'DESCRIPTION:' . $esc("Meeting: " . $note->customer_name),
            'TRIGGER:-PT15M',
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ];
        // Fold lines longer than 75 octets.
        $out = '';
        foreach ($lines as $line) {
            while (strlen($line) > 75) {
                $cut = 75;
                while ($cut > 1 && (ord($line[$cut]) & 0xC0) === 0x80) $cut--; // don't split UTF-8 sequences
                $out .= substr($line, 0, $cut) . "\r\n";
                $line = ' ' . substr($line, $cut);
            }
            $out .= $line . "\r\n";
        }
        return $out;
    }

    // ---------------------------------------------------------------------
    // Presence: who is on the meeting page right now
    // ---------------------------------------------------------------------

    /** Records that a back-office user is viewing the note. */
    public function touch_admin($noteId, $userId)
    {
        $this->db->query(
            "INSERT INTO meeting_note_admin_presence (meeting_note_id, user_id, last_seen) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE last_seen = VALUES(last_seen)",
            [(int) $noteId, (int) $userId, date('Y-m-d H:i:s')]
        );
    }

    /**
     * Everyone on the meeting, for display to any participant: back-office users currently viewing
     * ("Team", listed only while online) plus every invited participant with an online flag.
     * Only names and roles, no emails.
     */
    public function get_participants($noteId, $viewerType, $viewerId)
    {
        $since = date('Y-m-d H:i:s', time() - self::ONLINE_WINDOW);
        $awaySince = date('Y-m-d H:i:s', time() - self::AWAY_WINDOW);
        $list = [];
        $team = $this->db->select('u.id, u.name, p.last_seen')
            ->from('meeting_note_admin_presence p')
            ->join('users u', 'u.id = p.user_id')
            ->where(['p.meeting_note_id' => (int) $noteId, 'p.last_seen >=' => $awaySince])
            ->order_by('u.name')
            ->get()->result();
        foreach ($team as $t) {
            $online = $t->last_seen >= $since;
            $list[] = ['name' => $t->name, 'role' => 'team', 'online' => $online, 'away' => !$online,
                       'me' => ($viewerType === 'admin' && (int) $t->id === (int) $viewerId)];
        }
        foreach ($this->get_shares($noteId) as $sh) {
            $list[] = ['name' => $sh->name ?: '(removed user)', 'role' => $sh->participant_type, 'online' => $sh->online == 1, 'away' => $sh->away == 1,
                       'me' => ($viewerType === $sh->participant_type && (int) $sh->participant_id === (int) $viewerId)];
        }
        // Online first, then away, then by role and name.
        $order = ['team' => 0, 'customer' => 1, 'developer' => 2];
        $rank = function ($p) { return $p['online'] ? 0 : ($p['away'] ? 1 : 2); };
        usort($list, function ($a, $b) use ($order, $rank) {
            return [$rank($a), $order[$a['role']], $a['name']] <=> [$rank($b), $order[$b['role']], $b['name']];
        });
        return $list;
    }
}
