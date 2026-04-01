<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_TaskWorkTypeBugfix extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `tasks` MODIFY COLUMN `work_type` ENUM('development','maintenance','support','bugfix','other') NULL DEFAULT 'development'");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `tasks` MODIFY COLUMN `work_type` ENUM('development','maintenance','support','other') NULL DEFAULT 'development'");
    }
}
