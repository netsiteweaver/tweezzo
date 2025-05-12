<?php

class Migration_MoreNotifications extends CI_Migration
{
    function up()
    {
        //rename the two *add* to *create*
        $this->db->query("UPDATE params SET title = 'notification_create_customers' WHERE title = 'notification_add_customers'");
        $this->db->query("UPDATE params SET title = 'notification_create_notes' WHERE title = 'notification_add_notes'");

        $this->db->query("UPDATE params SET title = 'notification_create_sprints' WHERE title = 'notification_create_sprint'");
        $this->db->query("UPDATE params SET title = 'notification_create_projects' WHERE title = 'notification_create_project'");
        $this->db->query("UPDATE params SET title = 'notification_create_tasks' WHERE title = 'notification_create_task'");
        $this->db->query("UPDATE params SET title = 'notification_update_sprints' WHERE title = 'notification_update_sprint'");
        $this->db->query("UPDATE params SET title = 'notification_update_projects' WHERE title = 'notification_update_project'");
        $this->db->query("UPDATE params SET title = 'notification_update_tasks' WHERE title = 'notification_update_task'");

        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_delete_projects','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_delete_sprints','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_delete_tasks','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_delete_developers','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_delete_users','[]','1')");        

        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_create_developers','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_create_users','[]','1')");        

        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_update_notes','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_update_customers','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_update_developers','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_update_users','[]','1')");        
    }

    function down()
    {
        $this->db->query("UPDATE params SET title = 'notification_add_customers' WHERE title = 'notification_create_customers'");
        $this->db->query("UPDATE params SET title = 'notification_add_notes' WHERE title = 'notification_create_notes'");

        $this->db->query("DELETE FROM `params` WHERE title IN (
            'notification_delete_projects',
            'notification_delete_sprints',
            'notification_delete_tasks',
            'notification_delete_developers',
            'notification_delete_users',
            'notification_create_developers',
            'notification_create_users',
            'notification_update_notes',
            'notification_update_customers',
            'notification_update_developers',
            'notification_update_users'
        )");
    }
}