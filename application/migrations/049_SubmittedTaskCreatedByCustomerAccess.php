<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_SubmittedTaskCreatedByCustomerAccess extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `created_by_customer_access` INT NULL AFTER `created_by_customer`");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD CONSTRAINT `fk_st_customer_access` FOREIGN KEY (`created_by_customer_access`) REFERENCES `customer_access`(`id`) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `submitted_tasks` DROP FOREIGN KEY `fk_st_customer_access`");
        $this->db->query("ALTER TABLE `submitted_tasks` DROP COLUMN `created_by_customer_access`");
    }
}
