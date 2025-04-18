<?php

class Migration_EmailCustomerUnique extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `customers` ADD UNIQUE `idx_email` (`email`,`status`)");        
    }

    function down()
    {
        $this->db->query("ALTER TABLE `customers` DROP INDEX `idx_email`");
    }
}