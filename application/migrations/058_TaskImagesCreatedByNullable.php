<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Customer portal uploads attach to task_images with created_by_customer only;
 * there is no users.id for portal customers, so created_by must be nullable.
 */
class Migration_TaskImagesCreatedByNullable extends CI_Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `task_images` DROP FOREIGN KEY `fk_image_user`');
        $this->db->query('ALTER TABLE `task_images` MODIFY `created_by` INT NULL');
        $this->db->query('ALTER TABLE `task_images` ADD CONSTRAINT `fk_image_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('UPDATE `task_images` SET `created_by` = (SELECT `id` FROM `users` WHERE `status` = 1 ORDER BY `id` ASC LIMIT 1) WHERE `created_by` IS NULL');
        $this->db->query('ALTER TABLE `task_images` DROP FOREIGN KEY `fk_image_user`');
        $this->db->query('ALTER TABLE `task_images` MODIFY `created_by` INT NOT NULL');
        $this->db->query('ALTER TABLE `task_images` ADD CONSTRAINT `fk_image_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)');
    }
}
