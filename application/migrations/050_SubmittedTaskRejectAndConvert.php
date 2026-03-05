<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_SubmittedTaskRejectAndConvert extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `rejection_reason` TEXT NULL AFTER `rejected_by`");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `converted_task_id` INT NULL AFTER `validated_by`");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD CONSTRAINT `fk_st_converted_task` FOREIGN KEY (`converted_task_id`) REFERENCES `tasks`(`id`) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `submitted_tasks` DROP FOREIGN KEY `fk_st_converted_task`");
        $this->db->query("ALTER TABLE `submitted_tasks` DROP COLUMN `converted_task_id`");
        $this->db->query("ALTER TABLE `submitted_tasks` DROP COLUMN `rejection_reason`");
    }
}
