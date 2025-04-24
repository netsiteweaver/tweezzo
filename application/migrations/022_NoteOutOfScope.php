<?php

class Migration_NoteOutOfScope extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `task_notes` ADD COLUMN `out_of_scope` INT NOT NULL");        
        $this->db->query("ALTER TABLE `task_notes` ADD COLUMN `out_of_scope_by` INT NULL");        
        $this->db->query("ALTER TABLE `task_notes` ADD COLUMN `out_of_scope_on` datetime NULL");        

    }

    function down()
    {
        $this->db->query("ALTER TABLE `task_notes` DROP `out_of_scope`");
        $this->db->query("ALTER TABLE `task_notes` DROP `out_of_scope_by`");
        $this->db->query("ALTER TABLE `task_notes` DROP `out_of_scope_on`");
    }
}