<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_TaskSettledOn extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `settled_on` DATETIME NULL DEFAULT NULL COMMENT 'Date settled' AFTER `settled`");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `tasks` DROP COLUMN `settled_on`");
    }
}
