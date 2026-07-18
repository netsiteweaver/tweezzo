<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Companion to 067_OverdueReminderStages: the two due-soon reminder crons
 * (cron/sendDueTaskReminders and cron/sendDueTodayReminders) get their own
 * stage lists, so each reminder can be scoped independently of the others.
 * Defaults reproduce the previous hardcoded behaviour: every stage except
 * 'completed' and 'on_hold'.
 */
class Migration_DueReminderStages extends CI_Migration
{
    private $params = ['due_reminder_stages', 'due_today_reminder_stages'];

    public function up()
    {
        $stages = json_encode(['new','in_progress','testing','staging','validated','stopped']);
        foreach ($this->params as $title) {
            $chk = $this->db->select('count(id) as ct')->from('params')
                        ->where('title', $title)->get()->row('ct');
            if ($chk == "0") {
                $this->db->insert('params', [
                    'title'  => $title,
                    'value'  => $stages,
                    'status' => '1'
                ]);
            }
        }
    }

    public function down()
    {
        $this->db->where_in('title', $this->params)->delete('params');
    }
}
