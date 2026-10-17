<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Meetings planned ahead: a 'planned' note is shared before it starts, shows its agenda to invitees,
 * gets a reminder, and goes live automatically at meeting_datetime.
 * Each share gets a secret token for its calendar (.ics) link.
 */
class Migration_MeetingNotesPlanned extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `meeting_notes`
            MODIFY COLUMN `status` ENUM('draft','planned','live','closed') NOT NULL DEFAULT 'closed',
            ADD COLUMN `agenda` TEXT NULL DEFAULT NULL,
            ADD COLUMN `reminder_sent_on` DATETIME NULL DEFAULT NULL,
            ADD COLUMN `live_notified_on` DATETIME NULL DEFAULT NULL");

        $this->db->query("ALTER TABLE `meeting_note_shares` ADD COLUMN `token` CHAR(32) NULL DEFAULT NULL, ADD UNIQUE KEY `uq_share_token` (`token`)");
        $this->db->query("UPDATE `meeting_note_shares` SET `token` = MD5(CONCAT(id, '-', RAND(), '-', UUID())) WHERE `token` IS NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `meeting_note_shares` DROP INDEX `uq_share_token`, DROP COLUMN `token`");
        $this->db->query("UPDATE `meeting_notes` SET `status` = 'closed' WHERE `status` = 'planned'");
        $this->db->query("ALTER TABLE `meeting_notes`
            MODIFY COLUMN `status` ENUM('draft','live','closed') NOT NULL DEFAULT 'closed',
            DROP COLUMN `agenda`, DROP COLUMN `reminder_sent_on`, DROP COLUMN `live_notified_on`");
    }
}
