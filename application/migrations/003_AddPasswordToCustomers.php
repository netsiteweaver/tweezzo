<?php

class Migration_AddPasswordToCustomers extends CI_Migration
{
    function up()
    {
        $query = "ALTER TABLE `customers` ADD `password` VARCHAR(100) NOT NULL";
        $this->db->query($query);
        $query = "UPDATE customers SET password = md5(8888)";
        $this->db->query($query);
    }

    function down()
    {
        $query = "ALTER TABLE `customers` DROP `password`";
        $this->db->query($query);
    }
}