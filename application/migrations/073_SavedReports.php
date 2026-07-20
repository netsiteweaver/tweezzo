<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Persist generated timesheet reports (developer + client) as snapshots.
 */
class Migration_SavedReports extends CI_Migration
{
    public function up()
    {
        if (!$this->db->table_exists('saved_reports')) {
            $this->db->query("CREATE TABLE `saved_reports` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `uuid` CHAR(36) NOT NULL,
                `report_type` ENUM('developer','client') NOT NULL,
                `title` VARCHAR(255) NULL DEFAULT NULL,
                `date_from` DATE NOT NULL,
                `date_to` DATE NOT NULL,
                `developer_id` INT NULL DEFAULT NULL,
                `customer_id` INT NULL DEFAULT NULL,
                `project_id` INT NULL DEFAULT NULL,
                `sprint_id` INT NULL DEFAULT NULL,
                `billable_only` TINYINT(1) NOT NULL DEFAULT 0,
                `rate` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `currency` VARCHAR(8) NOT NULL DEFAULT 'MUR',
                `subject_name` VARCHAR(255) NULL DEFAULT NULL,
                `subject_email` VARCHAR(255) NULL DEFAULT NULL,
                `total_minutes` INT NOT NULL DEFAULT 0,
                `total_hours` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `entry_count` INT NOT NULL DEFAULT 0,
                `created_by` INT NULL DEFAULT NULL,
                `created_on` DATETIME NOT NULL,
                `status` TINYINT(1) NOT NULL DEFAULT 1,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_saved_reports_uuid` (`uuid`),
                KEY `idx_saved_reports_type` (`report_type`),
                KEY `idx_saved_reports_created` (`created_on`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        if (!$this->db->table_exists('saved_report_lines')) {
            $this->db->query("CREATE TABLE `saved_report_lines` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `report_id` INT NOT NULL,
                `line_date` DATE NULL DEFAULT NULL,
                `task_ref` VARCHAR(100) NULL DEFAULT NULL,
                `task_name` VARCHAR(255) NULL DEFAULT NULL,
                `task_uuid` CHAR(36) NULL DEFAULT NULL,
                `customer_name` VARCHAR(255) NULL DEFAULT NULL,
                `project_name` VARCHAR(255) NULL DEFAULT NULL,
                `sprint_name` VARCHAR(255) NULL DEFAULT NULL,
                `notes` TEXT NULL,
                `start_time` DATETIME NULL DEFAULT NULL,
                `finish_time` DATETIME NULL DEFAULT NULL,
                `work_type` VARCHAR(50) NULL DEFAULT NULL,
                `entry_count` INT NOT NULL DEFAULT 1,
                `duration_minutes` INT NOT NULL DEFAULT 0,
                `hours` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `sort_order` INT NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `idx_saved_report_lines_report` (`report_id`),
                CONSTRAINT `fk_saved_report_lines_report`
                    FOREIGN KEY (`report_id`) REFERENCES `saved_reports` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        $existing = $this->db->select('id')->from('menu')
            ->where(['controller' => 'reports', 'action' => 'saved'])->get()->row();
        if (!empty($existing)) {
            return;
        }

        $parent = $this->db->select('id')->from('menu')
            ->where(['controller' => 'reports', 'action' => '', 'parent_menu' => '0'])->get()->row();
        if (empty($parent)) {
            return;
        }

        $maxId = (int) $this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct;
        $savedId = $maxId + 1;
        $viewId = $maxId + 2;
        $deleteId = $maxId + 3;

        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            ({$savedId}, 'menu', 'Saved', 'reports', 'saved', '', NULL, 'fa-save', 3, {$parent->id}, 1, 0, 1, 1, 0, 1, 0),
            ({$viewId}, 'menu', 'View Saved', 'reports', 'view_saved', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0),
            ({$deleteId}, 'menu', 'Delete Saved', 'reports', 'delete_saved', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0)");

        // Also allow POST save action under developer/client perms via save method menu (hidden)
        $saveId = $maxId + 4;
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            ({$saveId}, 'menu', 'Save Report', 'reports', 'save', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0)");

        $users = $this->db->select('user_id')->from('permissions')
            ->where(['menu_id' => $parent->id, 'read' => 1])->get()->result();
        foreach ($users as $user) {
            foreach ([$savedId, $viewId, $deleteId, $saveId] as $menuId) {
                $this->db->insert('permissions', [
                    'user_id' => $user->user_id,
                    'menu_id' => $menuId,
                    'create'  => 0,
                    'read'    => 1,
                    'update'  => 0,
                    'delete'  => 0,
                ]);
            }
        }
    }

    public function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'reports' AND action IN ('saved','view_saved','delete_saved','save'))");
        $this->db->query("DELETE FROM menu WHERE controller = 'reports' AND action IN ('saved','view_saved','delete_saved','save')");
        if ($this->db->table_exists('saved_report_lines')) {
            $this->db->query("DROP TABLE `saved_report_lines`");
        }
        if ($this->db->table_exists('saved_reports')) {
            $this->db->query("DROP TABLE `saved_reports`");
        }
    }
}
