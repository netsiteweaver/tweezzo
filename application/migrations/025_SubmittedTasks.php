

<?php

class Migration_SubmittedTasks extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `submitted_tasks` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `uuid` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
                            `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                            `section` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                            `description` text COLLATE utf8mb4_general_ci NOT NULL,
                            `created_on` datetime NOT NULL,
                            `created_by` int NOT NULL,
                            `scope_client_expectation` text COLLATE utf8mb4_general_ci NOT NULL,
                            `scope_not_included` text COLLATE utf8mb4_general_ci,
                            `scope_when_done` text COLLATE utf8mb4_general_ci,
                            `stage` enum('new','validated','rejected') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'new',
                            `status` int NOT NULL DEFAULT '1',
                            PRIMARY KEY (`id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");        
    }

    function down()
    {
        $this->db->query("DROP TABLE `submitted_tasks`");
    }
}