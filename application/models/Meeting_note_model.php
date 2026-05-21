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
    }

    public function update_note($id)
    {
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
            'attendees' => $this->input->post('attendees', true),
            'lieu' => $this->input->post('lieu', true),
            'next_meeting_date' => $this->parseNextMeetingDate(
                $this->input->post('next_meeting_date'),
                $this->input->post('next_meeting_time')
            ),
            'last_updated'  =>  date("Y-m-d H:i:s"),
            'last_updated_by'   =>  $_SESSION['user_id']
        ];
        return $this->db->where('id', $id)->update('meeting_notes', $data);
    }

    public function delete_note($id)
    {
        return $this->db->delete('meeting_notes', ['id' => $id]);
    }
}
