<?php

class Migration_TaskNotesCustomer extends CI_Migration
{
    function up()
    {
        $query = "ALTER TABLE `task_notes` CHANGE `created_by` `created_by` INT NULL";
        $this->db->query($query);
        $query = "ALTER TABLE `task_notes` ADD COLUMN `created_by_customer` INT NULL";
        $this->db->query($query);
    }

    function down()
    {
        $query = "ALTER TABLE `task_notes` CHANGE `created_by` `created_by` INT NOT NULL";
        $this->db->query($query);
        $query = "ALTER TABLE `task_notes` DROP `created_by_customer`";
        $this->db->query($query);
    }
}