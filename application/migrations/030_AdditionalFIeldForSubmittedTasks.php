<?php

class Migration_AdditionalFIeldForSubmittedTasks extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `created_by_customer` int NOT NULL");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `submitted_tasks` DROP `created_by_customer`");
    }
}