<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Live meeting notes: a note can be shared with customer portal users and developers
 * who follow it in real time (polling) and, when allowed, add their own entries.
 */
class Migration_MeetingNotesLiveSharing extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `meeting_notes`
            ADD COLUMN `status` ENUM('draft','live','closed') NOT NULL DEFAULT 'closed',
            ADD COLUMN `notes_version` INT NOT NULL DEFAULT 0,
            ADD COLUMN `closed_on` DATETIME NULL DEFAULT NULL");

        $this->db->query("CREATE TABLE IF NOT EXISTS `meeting_note_shares` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `meeting_note_id` INT UNSIGNED NOT NULL,
            `participant_type` ENUM('customer','developer') NOT NULL,
            `participant_id` INT NOT NULL,
            `can_contribute` TINYINT(1) NOT NULL DEFAULT 0,
            `last_seen` DATETIME NULL DEFAULT NULL,
            `created_on` DATETIME NOT NULL,
            `created_by` INT NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_note_participant` (`meeting_note_id`, `participant_type`, `participant_id`),
            KEY `idx_participant` (`participant_type`, `participant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `meeting_note_entries` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `meeting_note_id` INT UNSIGNED NOT NULL,
            `author_type` ENUM('admin','customer','developer') NOT NULL,
            `author_id` INT NOT NULL,
            `body` TEXT NOT NULL,
            `created_on` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_note_entry` (`meeting_note_id`, `id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `meeting_note_entries`");
        $this->db->query("DROP TABLE IF EXISTS `meeting_note_shares`");
        $this->db->query("ALTER TABLE `meeting_notes` DROP COLUMN `status`, DROP COLUMN `notes_version`, DROP COLUMN `closed_on`");
    }
}
