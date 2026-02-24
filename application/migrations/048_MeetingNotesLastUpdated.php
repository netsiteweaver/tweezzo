<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_MeetingNotesLastUpdated extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `meeting_notes` ADD COLUMN `last_updated` DATETIME NULL DEFAULT NULL");
        $this->db->query("ALTER TABLE `meeting_notes` ADD COLUMN `last_updated_by` INT NULL DEFAULT NULL");
        $this->db->query("UPDATE `meeting_notes` SET `last_updated` = `created_at`");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `meeting_notes` DROP COLUMN `last_updated`, DROP COLUMN `last_updated_by`");
    }
}
