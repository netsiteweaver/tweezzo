<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shared meeting links (portal pages, emails, calendar) use an opaque uuid instead of the numeric id.
 */
class Migration_MeetingNotesUuid extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `meeting_notes` ADD COLUMN `uuid` CHAR(36) NULL DEFAULT NULL AFTER `id`");
        $this->db->query("UPDATE `meeting_notes` SET `uuid` = UUID() WHERE `uuid` IS NULL");
        $this->db->query("ALTER TABLE `meeting_notes` ADD UNIQUE KEY `uq_meeting_notes_uuid` (`uuid`)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `meeting_notes` DROP INDEX `uq_meeting_notes_uuid`, DROP COLUMN `uuid`");
    }
}
