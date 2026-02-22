<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_ProjectAndSprintCode extends CI_Migration {

    public function up()
    {
        // Project code: unique across all projects (nullable for existing rows)
        $this->db->query("ALTER TABLE `projects` ADD COLUMN `code` VARCHAR(20) NULL DEFAULT NULL AFTER `name`");
        $this->db->query("ALTER TABLE `projects` ADD UNIQUE KEY `uk_projects_code` (`code`)");

        // Sprint code: unique per project (nullable for existing rows)
        $this->db->query("ALTER TABLE `sprints` ADD COLUMN `code` VARCHAR(20) NULL DEFAULT NULL AFTER `name`");
        $this->db->query("ALTER TABLE `sprints` ADD UNIQUE KEY `uk_sprints_project_code` (`project_id`, `code`)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `projects` DROP INDEX `uk_projects_code`, DROP COLUMN `code`");
        $this->db->query("ALTER TABLE `sprints` DROP INDEX `uk_sprints_project_code`, DROP COLUMN `code`");
    }
}
