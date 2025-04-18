<?php

class Migration_NotificationCustomers extends CI_Migration
{
    function up()
    {
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_add_customers','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_delete_customers','[]','1')");        
    }

    function down()
    {
        $this->db->query("DELETE FROM `params` WHERE title IN ('notification_add_customers','notification_delete_customers')");
    }
}