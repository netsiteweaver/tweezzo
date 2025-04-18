<?php

class Migration_PortalLoginHistory extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `portal_login_history` (
                                        `id` int NOT NULL AUTO_INCREMENT,
                                        `email` text,
                                        `datetime` datetime NOT NULL,
                                        `result` enum('SUCCESS','FAILED') NOT NULL,
                                        `ip` text NOT NULL,
                                        `result_other` text NOT NULL,
                                        `type` ENUM('customer','developer') NOT NULL,
                                        `os` varchar(100) NOT NULL,
                                        `browser` varchar(100) NOT NULL,
                                        PRIMARY KEY (`id`)
                                        ) ENGINE=InnoDB;");
        $this->db->query("ALTER TABLE `customers` ADD COLUMN `last_login` DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL");
    }

    function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `portal_login_history`");
        $this->db->query("ALTER TABLE customers DROP COLUMN last_login");
    }
}