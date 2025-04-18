<?php

class Migration_StageHistory extends CI_Migration
{
    function up()
    {
        //create trigger
        $trigger = "
        CREATE TRIGGER stage_change
        AFTER UPDATE ON tasks
        FOR EACH ROW
        BEGIN
            IF NOT (OLD.stage <=> NEW.stage) THEN
                INSERT INTO stage_change_history (created_on, task_id, old_stage, new_stage, created_by, created_by_email, created_by_ip, created_by_user_agent)
                VALUES (NOW(), OLD.id, OLD.stage, NEW.stage, @current_user_id, @current_user_email, @current_user_ip, @current_user_agent);
            END IF;
        END";
        $this->db->query($trigger);        
        $this->db->query("CREATE TABLE `stage_change_history` (
                                `id` INT NOT NULL AUTO_INCREMENT ,
                                `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
                                `task_id` INT NOT NULL ,
                                `old_stage` VARCHAR(20) NOT NULL ,
                                `new_stage` VARCHAR(20) NOT NULL ,
                                `created_by` INT NULL ,
                                `created_by_email` VARCHAR(100) NULL ,
                                `created_by_ip` VARCHAR(100) NULL ,
                                `created_by_user_agent` VARCHAR(255) NULL ,
                                PRIMARY KEY (`id`)) ENGINE = InnoDB");
        $this->db->query("ALTER TABLE `stage_change_history` ADD CONSTRAINT `fk_change_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)");
        $this->db->query("ALTER TABLE `stage_change_history` ADD CONSTRAINT `fk_change_task` FOREIGN KEY (`task_id`) REFERENCES `tasks`(`id`)");

        $this->db->query("ALTER TABLE `tasks` DROP COLUMN `sprint`, DROP COLUMN `assigned_to`, ADD COLUMN `due_date` DATE NULL, ADD COLUMN `estimated_hours` FLOAT NULL");
    }

    function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `stage_change_history`");
        $this->db->query("ALTER TABLE tasks ADD COLUMN `sprint` VARCHAR(20) NOT NULL, ADD COLUMN `assigned_to` VARCHAR(200) NOT NULL DEFAULT '[]', DROP COLUMN `due_date`, DROP COLUMN `estimated_hours`");
        $this->db->query("DROP TRIGGER IF EXISTS `stage_change`");
    }
}