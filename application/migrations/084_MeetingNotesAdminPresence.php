<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Back-office users viewing a shared meeting note, so every participant can see who is online.
 * (Portal participants' presence is meeting_note_shares.last_seen.)
 */
class Migration_MeetingNotesAdminPresence extends CI_Migration {

    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `meeting_note_admin_presence` (
            `meeting_note_id` INT UNSIGNED NOT NULL,
            `user_id` INT NOT NULL,
            `last_seen` DATETIME NOT NULL,
            PRIMARY KEY (`meeting_note_id`, `user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `meeting_note_admin_presence`");
    }
}
