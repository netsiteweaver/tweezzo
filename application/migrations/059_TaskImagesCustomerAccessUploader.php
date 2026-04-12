<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Store which portal user (customer_access) uploaded an image for accurate captions.
 */
class Migration_TaskImagesCustomerAccessUploader extends CI_Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `task_images` ADD COLUMN `uploaded_by_customer_access_id` INT NULL AFTER `created_by_customer`');
        $this->db->query('ALTER TABLE `task_images` ADD CONSTRAINT `fk_task_images_customer_access` FOREIGN KEY (`uploaded_by_customer_access_id`) REFERENCES `customer_access`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `task_images` DROP FOREIGN KEY `fk_task_images_customer_access`');
        $this->db->query('ALTER TABLE `task_images` DROP COLUMN `uploaded_by_customer_access_id`');
    }
}
