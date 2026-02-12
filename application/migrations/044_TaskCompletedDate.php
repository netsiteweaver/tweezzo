<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_TaskCompletedDate extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `completed_date` DATETIME NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `tasks` DROP COLUMN `completed_date`");
    }
}

