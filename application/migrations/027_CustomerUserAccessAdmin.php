<?php

class Migration_CustomerUserAccessAdmin extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `customer_access` ADD COLUMN `admin` int NOT NULL");
        $this->db->query("UPDATE `customer_access` SET admin = 1 WHERE 1");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `sprint_id` int NULL AFTER `name`");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `customer_access` DROP `admin`");
    }
}