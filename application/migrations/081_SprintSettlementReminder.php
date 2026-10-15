<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Outcome of the sprint payment check (due, paid, none, error) and when HMS last reminded the client,
 * so the payment reminder cron knows which sprints to follow up.
 */
class Migration_SprintSettlementReminder extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `sprints` ADD COLUMN `settlement_status` VARCHAR(10) NULL DEFAULT NULL AFTER `settlement_checked_on`");
        $this->db->query("ALTER TABLE `sprints` ADD COLUMN `settlement_reminded_on` DATETIME NULL DEFAULT NULL AFTER `settlement_status`");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `sprints` DROP COLUMN `settlement_reminded_on`");
        $this->db->query("ALTER TABLE `sprints` DROP COLUMN `settlement_status`");
    }
}
