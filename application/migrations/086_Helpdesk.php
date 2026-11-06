<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helpdesk: customer support tickets with threaded replies, attachments, SLA tracking
 * and conversion to tasks. Tickets come from the customer portal, a public form or staff.
 */
class Migration_Helpdesk extends CI_Migration {

    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `ticket_categories` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(100) NOT NULL,
            `display_order` INT NOT NULL DEFAULT 0,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("INSERT INTO `ticket_categories` (`name`, `display_order`) VALUES
            ('General', 1), ('Bug / Issue', 2), ('Question', 3), ('Billing', 4), ('Feature request', 5)");

        $this->db->query("CREATE TABLE IF NOT EXISTS `tickets` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `uuid` CHAR(36) NOT NULL,
            `ticket_number` VARCHAR(20) NULL DEFAULT NULL,
            `public_ref` VARCHAR(16) NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `body` MEDIUMTEXT NOT NULL,
            `customer_id` INT NULL DEFAULT NULL,
            `customer_access_id` INT NULL DEFAULT NULL,
            `requester_name` VARCHAR(100) NULL DEFAULT NULL,
            `requester_email` VARCHAR(150) NULL DEFAULT NULL,
            `requester_company` VARCHAR(150) NULL DEFAULT NULL,
            `project_id` INT NULL DEFAULT NULL,
            `task_id` INT NULL DEFAULT NULL,
            `category_id` INT UNSIGNED NULL DEFAULT NULL,
            `priority` ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
            `status` ENUM('new','open','pending_customer','on_hold','resolved','closed') NOT NULL DEFAULT 'new',
            `assigned_to` INT NULL DEFAULT NULL,
            `source` ENUM('portal','public','staff') NOT NULL DEFAULT 'portal',
            `public_token` CHAR(64) NULL DEFAULT NULL,
            `ip_address` VARCHAR(45) NULL DEFAULT NULL,
            `first_response_due` DATETIME NULL DEFAULT NULL,
            `resolution_due` DATETIME NULL DEFAULT NULL,
            `first_responded_at` DATETIME NULL DEFAULT NULL,
            `resolved_at` DATETIME NULL DEFAULT NULL,
            `closed_at` DATETIME NULL DEFAULT NULL,
            `first_response_breach_notified_at` DATETIME NULL DEFAULT NULL,
            `sla_breach_notified_at` DATETIME NULL DEFAULT NULL,
            `last_activity_at` DATETIME NULL DEFAULT NULL,
            `created_on` DATETIME NOT NULL,
            `created_by` INT NULL DEFAULT NULL,
            `created_by_customer` INT NULL DEFAULT NULL,
            `updated_on` DATETIME NULL DEFAULT NULL,
            `deleted` TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_tickets_uuid` (`uuid`),
            UNIQUE KEY `uq_tickets_public_token` (`public_token`),
            UNIQUE KEY `uq_tickets_public_ref` (`public_ref`),
            KEY `idx_tickets_status` (`status`),
            KEY `idx_tickets_assigned` (`assigned_to`),
            KEY `idx_tickets_customer` (`customer_id`),
            KEY `idx_tickets_resolution_due` (`resolution_due`),
            KEY `idx_tickets_requester_email` (`requester_email`),
            KEY `idx_tickets_ip_created` (`ip_address`, `created_on`),
            CONSTRAINT `fk_tickets_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
            CONSTRAINT `fk_tickets_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
            CONSTRAINT `fk_tickets_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
            CONSTRAINT `fk_tickets_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
            CONSTRAINT `fk_tickets_category` FOREIGN KEY (`category_id`) REFERENCES `ticket_categories` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `ticket_replies` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ticket_id` INT UNSIGNED NOT NULL,
            `author_type` ENUM('admin','developer','customer','guest','system') NOT NULL,
            `author_id` INT NULL DEFAULT NULL,
            `body` MEDIUMTEXT NOT NULL,
            `is_internal` TINYINT(1) NOT NULL DEFAULT 0,
            `created_on` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_ticket_replies` (`ticket_id`, `id`),
            CONSTRAINT `fk_ticket_replies_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `ticket_attachments` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ticket_id` INT UNSIGNED NOT NULL,
            `reply_id` INT UNSIGNED NULL DEFAULT NULL,
            `file_name` VARCHAR(255) NOT NULL,
            `original_name` VARCHAR(255) NOT NULL,
            `mime` VARCHAR(100) NULL DEFAULT NULL,
            `size` INT NOT NULL DEFAULT 0,
            `is_internal` TINYINT(1) NOT NULL DEFAULT 0,
            `uploaded_by_type` ENUM('admin','developer','customer','guest') NOT NULL,
            `uploaded_by_id` INT NULL DEFAULT NULL,
            `created_on` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_ticket_attachments` (`ticket_id`),
            CONSTRAINT `fk_ticket_attachments_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `ticket_history` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ticket_id` INT UNSIGNED NOT NULL,
            `field` VARCHAR(50) NOT NULL,
            `old_value` VARCHAR(255) NULL DEFAULT NULL,
            `new_value` VARCHAR(255) NULL DEFAULT NULL,
            `changed_by_type` ENUM('admin','developer','customer','guest','system') NOT NULL,
            `changed_by_id` INT NULL DEFAULT NULL,
            `created_on` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_ticket_history` (`ticket_id`, `id`),
            CONSTRAINT `fk_ticket_history_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Converted tickets are tagged as coming from the helpdesk.
        $this->db->query("ALTER TABLE `tasks` MODIFY `source` ENUM('admin','whatsapp','email','phone','bulk','meeting','hms','helpdesk','other') NOT NULL DEFAULT 'admin'");

        // SLA targets in hours, per priority.
        $this->db->insert('params', [
            'title' => 'helpdesk_sla',
            'value' => json_encode([
                'urgent' => ['first_response' => 1,  'resolution' => 8],
                'high'   => ['first_response' => 4,  'resolution' => 24],
                'normal' => ['first_response' => 8,  'resolution' => 72],
                'low'    => ['first_response' => 24, 'resolution' => 120],
            ]),
        ]);
        // Users notified of new tickets and SLA breaches; defaults to whoever gets new-task alerts.
        $taskNotify = $this->db->select('value')->from('params')->where('title', 'notification_create_tasks')->get()->row();
        $this->db->insert('params', [
            'title' => 'notification_create_tickets',
            'value' => $taskNotify ? $taskNotify->value : '[]',
        ]);

        // Admin sidebar menu + permissions (same pattern as 031_SubmittedTasks).
        $maxId = intval($this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct);
        $maxDisplayOrder = intval($this->db->query("SELECT MAX(display_order) AS displayOrder FROM menu WHERE `parent_menu` = '0' AND `visible` = '1' AND `status` = '1'")->row()->displayOrder);
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            ($maxId+1, 'menu', 'Helpdesk', 'helpdesk', '', '#e83e8c', NULL, 'fa-life-ring', $maxDisplayOrder+1, 0, 1, 1, 1, 1, 0, 1, 0),
            ($maxId+2, 'menu', 'Tickets', 'helpdesk', 'listing', '', NULL, 'fa-list-ul', 1, $maxId+1, 1, 1, 1, 1, 0, 1, 0),
            ($maxId+3, 'menu', 'New Ticket', 'helpdesk', 'add', '', NULL, 'fa-plus-square', 2, $maxId+1, 1, 1, 1, 1, 0, 1, 0),
            ($maxId+4, 'menu', 'View', 'helpdesk', 'view', '', NULL, '', 999, 0, 0, 1, 1, 1, 0, 1, 0),
            ($maxId+5, 'menu', 'Edit', 'helpdesk', 'edit', '', NULL, '', 999, 0, 0, 1, 1, 1, 0, 1, 0),
            ($maxId+6, 'menu', 'Delete', 'helpdesk', 'delete', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0)");

        $users = $this->db->select("id")->from("users")->where("user_type", "regular")->get()->result();
        foreach ($users as $user) {
            for ($i = $maxId + 1; $i <= $maxId + 6; $i++) {
                $this->db->insert("permissions", [
                    'user_id' => $user->id,
                    'menu_id' => $i,
                    'create'  => 1,
                    'read'    => 1,
                    'update'  => 1,
                    'delete'  => ($i == $maxId + 6) ? 1 : 0,
                ]);
            }
        }

        // Attachments are streamed through access-checked endpoints, never served directly.
        $dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'tickets';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($dir . DIRECTORY_SEPARATOR . '.htaccess',
            "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n    Order deny,allow\n    Deny from all\n</IfModule>\n");
        @file_put_contents($dir . DIRECTORY_SEPARATOR . 'index.html', '');
    }

    public function down()
    {
        $menuIds = array_map(function ($r) { return (int) $r->id; },
            $this->db->select('id')->from('menu')->where('controller', 'helpdesk')->get()->result());
        if (!empty($menuIds)) {
            $this->db->where_in('menu_id', $menuIds)->delete('permissions');
            $this->db->where_in('id', $menuIds)->delete('menu');
        }
        $this->db->where_in('title', ['helpdesk_sla', 'notification_create_tickets'])->delete('params');
        $this->db->query("UPDATE `tasks` SET `source` = 'other' WHERE `source` = 'helpdesk'");
        $this->db->query("ALTER TABLE `tasks` MODIFY `source` ENUM('admin','whatsapp','email','phone','bulk','meeting','hms','other') NOT NULL DEFAULT 'admin'");
        $this->db->query("DROP TABLE IF EXISTS `ticket_history`");
        $this->db->query("DROP TABLE IF EXISTS `ticket_attachments`");
        $this->db->query("DROP TABLE IF EXISTS `ticket_replies`");
        $this->db->query("DROP TABLE IF EXISTS `tickets`");
        $this->db->query("DROP TABLE IF EXISTS `ticket_categories`");
    }
}
