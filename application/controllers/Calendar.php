<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public calendar downloads, reached from email links (no login).
 * Access is by the per-participant secret token in meeting_note_shares.token.
 */
class Calendar extends CI_Controller
{
    public function meeting($token = '')
    {
        $this->load->model("Meeting_note_model");
        $share = $this->Meeting_note_model->share_for_token($token);
        $note = $share ? $this->Meeting_note_model->get_note($share->meeting_note_id) : null;
        if (empty($note)) {
            show_404();
        }
        $this->output
            ->set_content_type('text/calendar', 'utf-8')
            ->set_header('Content-Disposition: attachment; filename="meeting.ics"')
            ->set_output($this->Meeting_note_model->ics($note, $share));
    }
}
