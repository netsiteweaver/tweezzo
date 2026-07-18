<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Which task stages the overdue-task reminder cron includes is now configurable
 * from Settings > System Params > Reminders. The default reproduces the previous
 * hardcoded behaviour: every stage except 'completed' and 'on_hold'.
 */
class Migration_OverdueReminderStages extends CI_Migration
{
    public function up()
    {
        $chk = $this->db->select('count(id) as ct')->from('params')
                    ->where('title', 'overdue_reminder_stages')->get()->row('ct');
        if ($chk == "0") {
            $stages = json_encode(['new','in_progress','testing','staging','validated','stopped']);
            $this->db->insert('params', [
                'title'  => 'overdue_reminder_stages',
                'value'  => $stages,
                'status' => '1'
            ]);
        }
    }

    public function down()
    {
        $this->db->where('title', 'overdue_reminder_stages')->delete('params');
    }
}
