<?php
class Attachment_model extends CI_Model
{
    public function insert_attachments($attachments)
    {
        return $this->db->insert_batch('attachments', $attachments);
    }

    public function get_attachments($note_id)
    {
        return $this->db->get_where('attachments', ['meeting_note_id' => $note_id])->result();
    }

    public function delete_attachment($id)
    {
        return $this->db->delete('attachments', ['id' => $id]);
    }
}
