<?php

class Migration_StageHistoryUserType extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `stage_change_history` ADD `user_type` ENUM('user','developer','customer') NOT NULL AFTER `created_by_user_agent`");        
        $this->db->query("ALTER TABLE `stage_change_history` DROP FOREIGN KEY fk_change_user");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `stage_change_history` DROP `user_type`");
        $this->db->query("ALTER TABLE `stage_change_history` ADD CONSTRAINT `fk_change_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)");        
    }
}