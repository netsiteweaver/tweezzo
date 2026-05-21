<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_MeetingNotesNextMeetingDate extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `meeting_notes` ADD COLUMN `next_meeting_date` DATE NULL DEFAULT NULL AFTER `meeting_datetime`");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `meeting_notes` DROP COLUMN `next_meeting_date`");
    }
}
