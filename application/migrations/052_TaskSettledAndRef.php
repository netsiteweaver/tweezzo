<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_TaskSettledAndRef extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `settled` TINYINT(1) NULL DEFAULT NULL AFTER `billable`");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `ref` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Invoice or quote number' AFTER `settled`");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `tasks` DROP COLUMN `settled`, DROP COLUMN `ref`");
    }
}
