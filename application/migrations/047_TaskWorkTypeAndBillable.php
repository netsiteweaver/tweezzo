<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_TaskWorkTypeAndBillable extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `work_type` ENUM('development','maintenance','support','other') NULL DEFAULT 'development' AFTER `estimated_hours`");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `billable` TINYINT(1) NULL DEFAULT NULL AFTER `work_type`");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `tasks` DROP COLUMN `work_type`, DROP COLUMN `billable`");
    }
}
