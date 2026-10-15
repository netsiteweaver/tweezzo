<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Portal users (customer_access) need `can_validate` = 1 to validate/reject staging tasks.
 * Existing users keep the ability they had before the flag existed.
 */
class Migration_CustomerAccessCanValidate extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `customer_access` ADD COLUMN `can_validate` TINYINT(1) NOT NULL DEFAULT 0 AFTER `admin`");
        $this->db->query("UPDATE `customer_access` SET `can_validate` = 1");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `customer_access` DROP COLUMN `can_validate`");
    }
}
