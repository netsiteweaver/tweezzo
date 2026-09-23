<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_DashboardPreferences extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `dashboard_preferences` (
                            `id` INT NOT NULL AUTO_INCREMENT,
                            `user_id` INT NOT NULL,
                            `block_key` VARCHAR(64) NOT NULL,
                            `position` INT NOT NULL DEFAULT '0',
                            `visible` TINYINT(1) NOT NULL DEFAULT '1',
                            `updated_on` DATETIME NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `uq_dashboard_pref_user_block` (`user_id`,`block_key`),
                            KEY `fk_dashboard_pref_user` (`user_id`),
                            CONSTRAINT `fk_dashboard_pref_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    function down()
    {
        $this->db->query("DROP TABLE `dashboard_preferences`");
    }
}
