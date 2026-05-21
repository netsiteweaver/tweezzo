<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_MeetingNotesNextMeetingTime extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `meeting_notes` MODIFY COLUMN `next_meeting_date` DATETIME NULL DEFAULT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `meeting_notes` MODIFY COLUMN `next_meeting_date` DATE NULL DEFAULT NULL");
    }
}
