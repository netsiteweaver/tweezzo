<?php

class Migration_LandingPage extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `users` ADD COLUMN `landing_page` VARCHAR(100) NOT NULL DEFAULT 'dashboard/index'");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `users` DROP `landing_page`");
    }
}