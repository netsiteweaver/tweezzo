<?php

class Migration_ActiveProjects extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `projects` ADD COLUMN `active` INT NOT NULL DEFAULT '1'");
        $this->db->query("ALTER TABLE `sprints` ADD COLUMN `active` INT NOT NULL DEFAULT '1'");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `projects` DROP `active`");
        $this->db->query("ALTER TABLE `sprints` DROP `active`");
    }
}