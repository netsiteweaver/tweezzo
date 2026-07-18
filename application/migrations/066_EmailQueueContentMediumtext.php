<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Task-reminder emails (overdue / due) can exceed 64KB of HTML when a developer
 * has many tasks. email_queue.content stored as TEXT (max 65,535 bytes) truncated
 * those emails mid-row, breaking the layout at the end. Widen it to MEDIUMTEXT (16MB).
 *
 * Nullability of the existing column is detected and preserved.
 */
class Migration_EmailQueueContentMediumtext extends CI_Migration
{
    public function up()
    {
        if ( ! $this->db->table_exists('email_queue') || ! $this->db->field_exists('content', 'email_queue')) {
            return;
        }
        $this->db->query('ALTER TABLE `email_queue` MODIFY `content` MEDIUMTEXT ' . $this->contentNullability());
    }

    public function down()
    {
        if ( ! $this->db->table_exists('email_queue') || ! $this->db->field_exists('content', 'email_queue')) {
            return;
        }
        // NOTE: reverting to TEXT will truncate any stored content larger than 64KB.
        $this->db->query('ALTER TABLE `email_queue` MODIFY `content` TEXT ' . $this->contentNullability());
    }

    /**
     * Returns 'NOT NULL' or 'NULL' matching the column's current definition,
     * so the ALTER preserves it instead of silently flipping nullability.
     */
    private function contentNullability()
    {
        $col = $this->db->query(
            "SELECT IS_NULLABLE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'email_queue'
               AND COLUMN_NAME = 'content'"
        )->row();

        return ($col && $col->IS_NULLABLE === 'NO') ? 'NOT NULL' : 'NULL';
    }
}
