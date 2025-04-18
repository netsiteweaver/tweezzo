<?php

class Migration_AdditionalTaskStages extends CI_Migration
{
    function up()
    {
        $query = "ALTER TABLE `tasks` CHANGE `stage` `stage` ENUM('new','in_progress','testing','staging','validated','completed','on_hold','stopped') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'new'";
        $this->db->query($query);
    }

    function down()
    {
        $query = "ALTER TABLE `tasks` CHANGE `stage` `stage` ENUM('new','in_progress','completed','on_hold','stopped') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'new'";
        $this->db->query($query);
    }
}