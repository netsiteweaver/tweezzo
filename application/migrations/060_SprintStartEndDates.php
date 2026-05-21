<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_SprintStartEndDates extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `sprints` ADD COLUMN `start_date` DATE NULL DEFAULT NULL AFTER `name`");
        $this->db->query("ALTER TABLE `sprints` ADD COLUMN `end_date` DATE NULL DEFAULT NULL AFTER `start_date`");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `sprints` DROP COLUMN `end_date`, DROP COLUMN `start_date`");
    }
}
