<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_RemoveMeetingNotesGoogleCalendar extends CI_Migration
{
    public function up()
    {
        if ($this->db->field_exists('google_calendar_event_id', 'meeting_notes')) {
            $this->dbforge->drop_column('meeting_notes', 'google_calendar_event_id');
        }

        $this->db->where_in('title', [
            'google_calendar_enabled',
            'google_calendar_id',
            'google_calendar_credentials_path',
            'google_calendar_default_time',
            'google_calendar_timezone',
        ])->delete('params');
    }

    public function down()
    {
    }
}
