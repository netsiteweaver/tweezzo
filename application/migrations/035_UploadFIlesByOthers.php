<?php

class Migration_UploadFilesByOthers extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `task_images` ADD COLUMN `created_by_customer` INT NULL");
        $this->db->query("ALTER TABLE `task_images` ADD CONSTRAINT `fk_task_images_created_by_customer` FOREIGN KEY (created_by_customer) REFERENCES customers(customer_id)");

        $this->db->query("ALTER TABLE `task_images` ADD COLUMN `uploaded_by_user_type` ENUM('admin','developer','customer') NOT NULL DEFAULT 'admin'");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `task_images` DROP `created_by_customer`, DROP `uploaded_by_user_type`");
    }
}