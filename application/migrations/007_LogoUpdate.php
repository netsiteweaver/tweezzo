<?php

class Migration_LogoUpdate extends CI_Migration
{
    function up()
    {
        $this->db->query("UPDATE params SET value = 'TASK-MANAGER-LOGO v1.0.png' WHERE title = 'logo'");
    }

    function down()
    {
        $this->db->query("UPDATE params SET value = 'TASK-MANAGER-LOGO.png' WHERE title = 'logo'");
    }
}