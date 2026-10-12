<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tasks created from an approved HMS quotation carry the source 'hms' (and the quotation number in `ref`).
 */
class Migration_TaskSourceHms extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `tasks` MODIFY COLUMN `source` ENUM('admin','whatsapp','email','phone','bulk','meeting','hms','other') NOT NULL DEFAULT 'admin'");
    }

    public function down()
    {
        $this->db->query("UPDATE `tasks` SET `source` = 'other' WHERE `source` = 'hms'");
        $this->db->query("ALTER TABLE `tasks` MODIFY COLUMN `source` ENUM('admin','whatsapp','email','phone','bulk','meeting','other') NOT NULL DEFAULT 'admin'");
    }
}
