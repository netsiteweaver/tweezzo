<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_SprintValidationReminders extends CI_Migration {

    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `sprint_validation_reminders` (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `sprint_id` INT(11) NOT NULL,
                `ready_for_validation` TINYINT(1) NOT NULL DEFAULT 0,
                `ready_set_on` DATETIME NULL DEFAULT NULL,
                `ready_set_by` INT(11) NULL DEFAULT NULL,
                `last_sent_on` DATETIME NULL DEFAULT NULL,
                `created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_sprint_validation_reminders_sprint` (`sprint_id`),
                KEY `idx_sprint_validation_reminders_ready` (`ready_for_validation`),
                KEY `idx_sprint_validation_reminders_last_sent_on` (`last_sent_on`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `sprint_validation_reminders`");
    }
}
