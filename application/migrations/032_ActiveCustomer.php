<?php

class Migration_ActiveCustomer extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `customers` ADD COLUMN `active` INT NOT NULL DEFAULT '1'");
        $this->db->query("ALTER TABLE `customers` ADD COLUMN `short_code` VARCHAR(5) NOT NULL");
        $this->db->query("ALTER TABLE `customer_access` ADD COLUMN `country_code` VARCHAR(2) NOT NULL DEFAULT 'mu'");

        $this->db->query("ALTER TABLE `users` ADD COLUMN `country_code` VARCHAR(2) NOT NULL DEFAULT 'mu'");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `customers` DROP `active`");
        $this->db->query("ALTER TABLE `customers` DROP `short_code`");
        $this->db->query("ALTER TABLE `customer_access` DROP `country_code`");

        $this->db->query("ALTER TABLE `users` DROP `country_code`");
    }
}