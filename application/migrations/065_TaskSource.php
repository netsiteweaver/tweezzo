<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_TaskSource extends CI_Migration {

    public function up()
    {
        if (!$this->db->field_exists('source', 'tasks')) {
            $this->db->query("ALTER TABLE `tasks` ADD COLUMN `source` ENUM('admin','whatsapp','email','phone','bulk','meeting','other') NOT NULL DEFAULT 'admin' AFTER `ref`");
        }
    }

    public function down()
    {
        if ($this->db->field_exists('source', 'tasks')) {
            $this->db->query("ALTER TABLE `tasks` DROP COLUMN `source`");
        }
    }
}
