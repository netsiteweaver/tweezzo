<?php

class Migration_UsersAndCustomerForceChangePassword extends CI_Migration
{
    function up()
    {
        $query = "ALTER TABLE `users` ADD COLUMN `force_change` INT NULL";
        $this->db->query($query);
        $query = "ALTER TABLE `users` ADD COLUMN `token` VARCHAR(100) NOT NULL";
        $this->db->query($query);
        $query = "ALTER TABLE `customers` ADD COLUMN `force_change` INT NULL";
        $this->db->query($query);
        $query = "ALTER TABLE `customers` ADD COLUMN `token` VARCHAR(100) NOT NULL";
        $this->db->query($query);
    }

    function down()
    {
        $query = "ALTER TABLE `users` DROP `force_change`";
        $this->db->query($query);
        $query = "ALTER TABLE `users` DROP `token`";
        $this->db->query($query);
        $query = "ALTER TABLE `customers` DROP `force_change`";
        $this->db->query($query);
        $query = "ALTER TABLE `customers` DROP `token`";
        $this->db->query($query);
    }
}