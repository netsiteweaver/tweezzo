<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_CompanyLogo extends CI_Migration {

    public function up()
    {
        if (!$this->db->field_exists('logo', 'company')) {
            $this->db->query("ALTER TABLE `company` ADD COLUMN `logo` VARCHAR(255) NULL DEFAULT NULL AFTER `name`");
        }
    }

    public function down()
    {
        if ($this->db->field_exists('logo', 'company')) {
            $this->db->query("ALTER TABLE `company` DROP COLUMN `logo`");
        }
    }
}
