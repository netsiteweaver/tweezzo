<?php

class Migration_AddDurationInTimesheet extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE timesheet ADD COLUMN `duration_minutes` INT");
        $this->db->query("UPDATE timesheet
                            SET 
                                finish_time = NOW(),
                                duration_minutes = TIMESTAMPDIFF(MINUTE, start_time, finish_time)
                            WHERE 
                                finish_time IS NOT NULL");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `timesheet` DROP `duration_minutes`");
    }
}