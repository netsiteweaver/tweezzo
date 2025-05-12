<?php

class Migration_TaskValidation extends CI_Migration
{
    function up()
    {
        $this->db->query("INSERT INTO `params` (`title`, `value`, `status`) VALUES('logo-light', 'LOGO-TWEEZZO-HORIZONTAL-TRANSPARENT-50PX-TextLight.png', '1')");
        $this->db->query("INSERT INTO `params` (`title`, `value`, `status`) VALUES('logo-dark', 'LOGO-TWEEZZO-HORIZONTAL-TRANSPARENT-50PX-TextDark.png', '1')");

        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `validated_on` DATETIME NULL");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `validated_by` INT NULL");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `rejected_on` DATETIME NULL");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `rejected_by` int NULL");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `rejected_reason` VARCHAR(100) NULL");

        $this->db->query("DROP TRIGGER IF EXISTS `stage_change`;
                CREATE TRIGGER `stage_change` 
                AFTER UPDATE ON `tasks` 
                FOR EACH ROW 
                    BEGIN 
                        IF NOT (OLD.stage <=> NEW.stage) THEN 
                            INSERT INTO stage_change_history (created_on, task_id, old_stage, new_stage, created_by, created_by_email, created_by_ip, created_by_user_agent,user_type) VALUES (NOW(), OLD.id, OLD.stage, NEW.stage, @current_user_id, @current_user_email, @current_user_ip, @current_user_agent, @current_user_type); 
                        END IF; 
                    END ");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `tasks` DROP `validated_on`, DROP `validated_by`, DROP `rejected_on`, DROP `rejected_by`, DROP `rejected_reason`");

        $this->db->query("DELETE FROM `params` WHERE `title` LIKE 'logo-%'");
    }
}