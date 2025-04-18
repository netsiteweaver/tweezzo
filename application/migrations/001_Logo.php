<?php

class Migration_Logo extends CI_Migration
{
    function up()
    {
        $this->db->query("UPDATE params SET value = 'TASK-MANAGER-LOGO.png' WHERE title = 'logo'");
    }

    function down()
    {
        $this->db->query("UPDATE params SET value = '' WHERE title = 'logo'");
    }
}