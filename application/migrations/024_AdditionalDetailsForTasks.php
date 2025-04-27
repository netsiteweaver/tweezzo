<?php

class Migration_AdditionalDetailsForTasks extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `scope_client_expectation` TEXT NOT NULL");        
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `scope_not_included` TEXT NULL");        
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `scope_when_done` TEXT NULL");        

    }

    function down()
    {
        $this->db->query("ALTER TABLE `tasks` DROP `scope_client_expectation`");
        $this->db->query("ALTER TABLE `tasks` DROP `scope_not_included`");
        $this->db->query("ALTER TABLE `tasks` DROP `scope_when_done`");
    }
}