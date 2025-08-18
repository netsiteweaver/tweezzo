<?php

class Migration_CustomerAccessToken extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `customer_access` ADD COLUMN `token` VARCHAR(100) NOT NULL");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `customer_access` DROP `token`");
    }
}