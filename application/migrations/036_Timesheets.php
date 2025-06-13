<?php

class Migration_Timesheets extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `timesheet` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `task_id` int NOT NULL,
                            `developer_id` int NOT NULL,
                            `start_time` datetime NOT NULL,
                            `finish_time` int NOT NULL,
                            `notes` text NOT NULL,
                            `status` int NOT NULL DEFAULT '1',
                            PRIMARY KEY (`id`),
                            KEY `fk_timesheet_task` (`task_id`),
                            KEY `fk_timesheet_developer` (`developer_id`),
                            CONSTRAINT `fk_timesheet_developer2` FOREIGN KEY (`developer_id`) REFERENCES `users` (`id`),
                            CONSTRAINT `fk_timesheet_task2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    function down()
    {
        $this->db->query("DROP TABLE `timesheet`");
    }
}