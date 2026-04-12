<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stage history is recorded in application code (see Tasks_model::record_stage_change_history).
 * The MySQL trigger relied on session @vars which are unreliable with CodeIgniter's DB layer.
 * Drops the trigger if it exists (legacy installs may still have it from older migrations).
 */
class Migration_StageChangeHistoryDeveloper extends CI_Migration
{
    public function up()
    {
        $this->db->query('DROP TRIGGER IF EXISTS `stage_change`');
    }

    public function down()
    {
        $this->db->query("
            CREATE TRIGGER `stage_change`
            AFTER UPDATE ON `tasks`
            FOR EACH ROW
            BEGIN
                IF NOT (OLD.stage <=> NEW.stage) THEN
                    INSERT INTO stage_change_history (created_on, task_id, old_stage, new_stage, created_by, created_by_email, created_by_ip, created_by_user_agent, user_type)
                    VALUES (NOW(), OLD.id, OLD.stage, NEW.stage, @current_user_id, @current_user_email, @current_user_ip, @current_user_agent, @current_user_type);
                END IF;
            END
        ");
    }
}
