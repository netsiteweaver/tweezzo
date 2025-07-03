<?php

class Migration_MarkTasksAsCompleted extends CI_Migration
{
    function up()
    {
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `closed` INT NOT NULL");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `mark_closed_by` INT NULL");
        $this->db->query("ALTER TABLE `tasks` ADD COLUMN `mark_closed_on` datetime NULL");
    }

    function down()
    {
        $this->db->query("ALTER TABLE `tasks` DROP `closed`, DROP `mark_closed_by`, DROP `mark_closed_on`");
    }
}