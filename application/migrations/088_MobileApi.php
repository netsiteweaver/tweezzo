<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mobile app API: bearer tokens, FCM device registrations and a push notification queue.
 *
 * A "principal" is who a token belongs to:
 *   staff     -> users.id (user_type = 'regular')
 *   developer -> users.id (user_type = 'developer')
 *   customer  -> customer_access.id
 */
class Migration_MobileApi extends CI_Migration {

    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `api_tokens` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `token_hash` CHAR(64) NOT NULL,
            `principal_type` ENUM('staff','developer','customer') NOT NULL,
            `principal_id` INT NOT NULL,
            `device_name` VARCHAR(100) NULL DEFAULT NULL,
            `ip_address` VARCHAR(45) NULL DEFAULT NULL,
            `created_at` DATETIME NOT NULL,
            `last_used_at` DATETIME NULL DEFAULT NULL,
            `expires_at` DATETIME NOT NULL,
            `revoked` TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_token_hash` (`token_hash`),
            KEY `idx_principal` (`principal_type`, `principal_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `api_login_attempts` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ip_address` VARCHAR(45) NOT NULL,
            `email` VARCHAR(150) NOT NULL,
            `success` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_ip_time` (`ip_address`, `created_at`),
            KEY `idx_email_time` (`email`, `created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `device_tokens` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `api_token_id` INT UNSIGNED NULL DEFAULT NULL,
            `principal_type` ENUM('staff','developer','customer') NOT NULL,
            `principal_id` INT NOT NULL,
            `fcm_token` VARCHAR(255) NOT NULL,
            `platform` VARCHAR(20) NOT NULL DEFAULT 'android',
            `push_enabled` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_fcm_token` (`fcm_token`),
            KEY `idx_principal` (`principal_type`, `principal_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->db->query("CREATE TABLE IF NOT EXISTS `push_queue` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `principal_type` ENUM('staff','developer','customer') NOT NULL,
            `principal_id` INT NOT NULL,
            `title` VARCHAR(200) NOT NULL,
            `body` VARCHAR(500) NOT NULL DEFAULT '',
            `data_json` TEXT NULL,
            `status` ENUM('pending','sending','sent','failed','skipped') NOT NULL DEFAULT 'pending',
            `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `last_error` VARCHAR(500) NULL DEFAULT NULL,
            `created_at` DATETIME NOT NULL,
            `sent_at` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_status` (`status`, `id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `push_queue`");
        $this->db->query("DROP TABLE IF EXISTS `device_tokens`");
        $this->db->query("DROP TABLE IF EXISTS `api_login_attempts`");
        $this->db->query("DROP TABLE IF EXISTS `api_tokens`");
    }
}
