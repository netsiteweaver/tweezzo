<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_SubmittedTasksImages extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE `submitted_tasks_images` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `uuid` varchar(40) NOT NULL,
                            `submitted_task_id` int DEFAULT NULL,
                            `created_on` datetime NOT NULL,
                            `created_by_customer` INT NULL,
                            `uploaded_by_user_type` ENUM('admin','developer','customer') NOT NULL DEFAULT 'customer',
                            `file_name` varchar(100) NOT NULL,
                            `thumb_name` varchar(100) NOT NULL,
                            `file_ext` varchar(10) NOT NULL,
                            `file_size` float NOT NULL,
                            `image_width` int NOT NULL,
                            `image_height` int NOT NULL,
                            `image_type` varchar(25) NOT NULL,
                            `status` int NOT NULL DEFAULT '1',
                            PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB ");

        $this->db->query("ALTER TABLE `submitted_tasks_images`
                            ADD CONSTRAINT `fk_submitted_task_images_task`
                            FOREIGN KEY (`submitted_task_id`) REFERENCES `submitted_tasks`(`id`)");

        $this->db->query("ALTER TABLE `submitted_tasks_images`
                            ADD CONSTRAINT `fk_submitted_task_images_customer`
                            FOREIGN KEY (`created_by_customer`) REFERENCES `customers`(`customer_id`)");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `submitted_tasks_images`");
    }
}

