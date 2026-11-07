<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/Api_Controller.php';

/**
 * GET api/dashboard/index  Counters for the app home screen, scoped to the signed-in principal.
 */
class Dashboard extends Api_Controller
{
    public function index()
    {
        $this->load->model('Tickets_model');
        $today = date('Y-m-d');

        if ($this->isStaff()) {
            $data = ['tickets' => null, 'tasks' => null];
            if ($this->staffCan('helpdesk', 'listing')) {
                $data['tickets'] = $this->Tickets_model->summaryCounts((int) $this->principal['id']);
                $data['attention'] = array_map([$this, 'ticketBrief'], $this->Tickets_model->attentionList(10));
            }
            if ($this->staffCan('tasks', 'listing')) {
                $data['tasks'] = $this->taskCounts(null, null);
                $data['tasks']['mine'] = $this->taskCounts((int) $this->principal['id'], null)['open'];
            }
            $this->ok($data);
        }

        if ($this->isDeveloper()) {
            $id = (int) $this->principal['id'];
            $mine = $this->Tickets_model->listForDeveloper($id, 'active');
            $this->ok([
                'tickets' => [
                    'active'  => count($mine),
                    'overdue' => count(array_filter($mine, ['Tickets_model', 'isOverdue'])),
                    'urgent'  => count(array_filter($mine, function ($t) { return $t->priority === 'urgent'; })),
                ],
                'attention' => array_map([$this, 'ticketBrief'], array_slice($mine, 0, 10)),
                'tasks'     => $this->taskCounts($id, null),
            ]);
        }

        // Customer
        $cid = (int) $this->principal['customer_id'];
        $tickets = $this->Tickets_model->listForCustomer($cid, 'active');
        $this->ok([
            'tickets' => [
                'active'            => count($tickets),
                'awaiting_your_reply' => count(array_filter($tickets, function ($t) { return $t->status === 'pending_customer'; })),
            ],
            'attention' => array_map([$this, 'ticketBrief'], array_slice($tickets, 0, 10)),
            'tasks'     => $this->taskCounts(null, $cid),
        ]);
    }

    /** Open-task counters, optionally for one assignee or one customer company. */
    private function taskCounts($userId, $customerId)
    {
        $this->db->select('t.stage, COUNT(DISTINCT t.id) AS ct,
                COUNT(DISTINCT CASE WHEN t.due_date < CURDATE() AND t.stage NOT IN (\'completed\',\'validated\',\'stopped\') THEN t.id END) AS overdue,
                COUNT(DISTINCT CASE WHEN t.due_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 3 DAY AND t.stage NOT IN (\'completed\',\'validated\',\'stopped\') THEN t.id END) AS due_soon', false)
            ->from('tasks t')
            ->join('sprints s', 's.id = t.sprint_id')
            ->join('projects p', 'p.id = s.project_id')
            ->join('customers c', 'c.customer_id = p.customer_id')
            ->where(['t.status' => 1, 't.closed' => 0, 's.status' => 1, 's.active' => 1, 'p.active' => 1, 'c.status' => 1, 'c.active' => 1]);
        if ($userId) {
            $this->db->join('task_user tu', 'tu.task_id = t.id')->where('tu.user_id', (int) $userId);
        }
        if ($customerId) {
            $this->db->where('c.customer_id', (int) $customerId);
        }
        $rows = $this->db->group_by('t.stage')->get()->result();

        $byStage = [];
        $open = $overdue = $dueSoon = 0;
        foreach ($rows as $r) {
            $byStage[$r->stage] = (int) $r->ct;
            if (!in_array($r->stage, ['completed', 'stopped'], true)) $open += (int) $r->ct;
            $overdue += (int) $r->overdue;
            $dueSoon += (int) $r->due_soon;
        }
        return ['open' => $open, 'overdue' => $overdue, 'due_soon' => $dueSoon, 'by_stage' => (object) $byStage];
    }

    private function ticketBrief($t)
    {
        $out = [
            'uuid'           => $t->uuid,
            'ref'            => $this->isCustomer() ? $t->public_ref : $t->ticket_number,
            'subject'        => $t->subject,
            'status'         => $t->status,
            'status_label'   => Tickets_model::statusLabel($t->status),
            'priority'       => $t->priority,
            'priority_label' => Tickets_model::priorityLabel($t->priority),
            'company_name'   => $t->company_name,
            'last_activity_at' => $t->last_activity_at,
        ];
        if (!$this->isCustomer()) {
            $out['assignee_name'] = $t->assignee_name;
            $out['overdue'] = Tickets_model::isOverdue($t);
        }
        return $out;
    }
}
