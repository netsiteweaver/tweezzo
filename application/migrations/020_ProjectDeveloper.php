

<?php

class Migration_ProjectDeveloper extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `project_user` (
                            `id` int NOT NULL,
                            `project_id` int NOT NULL,
                            `user_id` int NOT NULL,
                            KEY `fk_pu_developer` (`user_id`),
                            KEY `fk_pu_project` (`project_id`),
                            CONSTRAINT `fk_pu_developer` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
                            CONSTRAINT `fk_pu_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");
    }

    function down()
    {
        $this->db->query("DROP TABLE `project_user`");
    }
}