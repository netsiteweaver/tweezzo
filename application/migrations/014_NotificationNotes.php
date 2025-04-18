<?php

class Migration_NotificationNotes extends CI_Migration
{
    function up()
    {
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_add_notes','[]','1')");        
        $this->db->query("INSERT INTO `params` (`title`,`value`,`status`) VALUES ('notification_delete_notes','[]','1')");        
    }

    function down()
    {
        $this->db->query("DELETE FROM `params` WHERE title IN ('notification_add_notes','notification_delete_notes')");
    }
}