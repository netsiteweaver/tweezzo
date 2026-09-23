<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_DashboardPreferencesRowBreak extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `dashboard_preferences`
                            ADD COLUMN `row_break` TINYINT(1) NOT NULL DEFAULT '0' AFTER `visible`");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `dashboard_preferences` DROP COLUMN `row_break`");
    }
}
