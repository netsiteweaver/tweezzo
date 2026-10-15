<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * When every task of a sprint is validated, the related HMS quotations/invoices are checked once
 * (client asked to settle before go-live). Cleared when a task leaves validation, so it runs again.
 */
class Migration_SprintSettlementCheck extends CI_Migration {

    public function up()
    {
        $this->db->query("ALTER TABLE `sprints` ADD COLUMN `settlement_checked_on` DATETIME NULL DEFAULT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `sprints` DROP COLUMN `settlement_checked_on`");
    }
}
