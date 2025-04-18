<?php

class Migration_UploadImages extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `task_images` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `uuid` varchar(40) NOT NULL,
                            `task_id` int DEFAULT NULL,
                            `created_on` datetime NOT NULL,
                            `created_by` int NOT NULL,
                            `file_name` varchar(100) NOT NULL,
                            `thumb_name` varchar(100) NOT NULL,
                            `file_ext` varchar(10) NOT NULL,
                            `file_size` float NOT NULL,
                            `image_width` int NOT NULL,
                            `image_height` int NOT NULL,
                            `image_type` varchar(25) NOT NULL,
                            `status` int NOT NULL DEFAULT '1',
                            PRIMARY KEY (`id`)) ENGINE=InnoDB ");
        $this->db->query("ALTER TABLE `task_images` ADD CONSTRAINT `fk_image_task` FOREIGN KEY (`task_id`) REFERENCES `tasks`(`id`)");
        $this->db->query("ALTER TABLE `task_images` ADD CONSTRAINT `fk_image_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)");
    }

    function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `task_images`");
    }
}