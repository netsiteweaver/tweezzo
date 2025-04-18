<?php

class Migration_NotesPrivate extends CI_Migration
{
    function up()
    {
        $query = "ALTER TABLE `task_notes` ADD COLUMN `display_type` ENUM('private','public') NOT NULL DEFAULT 'public'";
        $this->db->query($query);
    }

    function down()
    {
        $query = "ALTER TABLE `task_notes` DROP `display_type`";
        $this->db->query($query);
    }
}