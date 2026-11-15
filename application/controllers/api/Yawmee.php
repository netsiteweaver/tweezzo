<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/Api_Controller.php';

/**
 * Feed for the Yawmee desktop app (see Yawmee's API_CONTRACT.md): the signed-in team member's open
 * work, as {"items": [...]} rather than the mobile API's {"ok", "data"} envelope.
 *
 * GET  api/yawmee/items                  (route) open tasks and active tickets:
 *        staff     -> every open task (tasks/listing permission), tickets assigned to them
 *        developer -> tasks and tickets assigned to them (all the developer portal lets them see)
 * POST api/yawmee/items/<id>/complete    (route) move a task to Completed (staff with tasks/add permission)
 *
 * Auth: "Authorization: Bearer <token>": a personal token from Settings > Tokens (staff), or from
 * POST api/account/login (portal=team) like the mobile app (developers).
 * Customer tokens are refused. Ids are "task-<uuid>" and "ticket-<uuid>".
 * Tickets and developers' tasks are read-only here (can_complete false): a ticket is resolved with a
 * reply, and only staff close a task.
 */
class Yawmee extends Api_Controller
{
    const MAX_ITEMS = 200;
    /** Tasks still waiting on the assignee. Developers don't see Validated: it is out of their hands. */
    const OPEN_STAGES = ['new', 'in_progress', 'testing', 'staging', 'on_hold', 'validated'];
    const STAGE_LABELS = [
        'new' => 'New', 'in_progress' => 'In progress', 'testing' => 'Testing', 'staging' => 'Staging',
        'on_hold' => 'On hold', 'validated' => 'Validated', 'completed' => 'Completed', 'stopped' => 'Stopped',
    ];

    public function __construct()
    {
        parent::__construct();
        if ($this->isCustomer()) {
            $this->forbidden('Yawmee is for team accounts.');
        }
    }

    public function items()
    {
        $this->requireMethod('get');
        $items = array_merge($this->taskItems(), $this->ticketItems());
        usort($items, function ($a, $b) {
            // Dated items first, soonest first
            if ($a['due'] === $b['due']) return 0;
            if ($a['due'] === null) return 1;
            if ($b['due'] === null) return -1;
            return strcmp($a['due'], $b['due']);
        });
        $this->json(['items' => array_slice($items, 0, self::MAX_ITEMS)]);
    }

    public function complete($id = '')
    {
        $this->requireMethod('post');
        if (!preg_match('/^task-(.+)$/', (string) $id, $m) || !self::isUuid($m[1])) {
            $this->fail('read_only', 'Only tasks can be completed from Yawmee.', 409);
        }
        if (!$this->canCompleteTasks()) {
            $this->forbidden('You cannot complete tasks.');
        }
        $this->load->model('Tasks_model');
        $task = $this->Tasks_model->fetchSingle($m[1]);
        if (empty($task)) {
            $this->notFound('Task');
        }
        if ($task->stage !== 'completed') {
            $result = $this->Tasks_model->move_stage(['task_uuid' => $task->uuid, 'stage' => 'completed', 'progress' => 100]);
            if (!$result['result']) {
                $this->fail('validation', $result['reason'], 422);
            }
        }
        $this->json(['ok' => true]);
    }

    // ------------------------------------------------------------------

    private function taskItems()
    {
        $me = (int) $this->principal['id'];
        if ($this->isStaff()) {
            if (!$this->staffCan('tasks', 'listing')) return [];
            $this->load->model('Tasks_model');
            $rows = $this->Tasks_model->fetchAll('', '', '', self::OPEN_STAGES, '', 't.due_date', 'asc',
                1, self::MAX_ITEMS, '', '', '', false, '', '', '', 'open');
            $canComplete = $this->canCompleteTasks();
            $url = 'tasks/view?task_uuid=';
            $nameField = 'name';
            $stageField = 'stage';
        } else {
            $this->load->model('Developersportal_model');
            $stages = array_values(array_diff(self::OPEN_STAGES, ['validated']));
            $rows = $this->Developersportal_model->getMyTasks($me, '', '', '', json_encode($stages),
                't.due_date IS NULL, t.due_date', 'asc', 1, self::MAX_ITEMS);
            $canComplete = false;
            $url = 'portal/developers/view?task_uuid=';
            $nameField = 'task_name';
            $stageField = 'task_stage';
        }

        $items = [];
        foreach ((array) $rows as $t) {
            $stage = (string) $t->{$stageField};
            $context = implode(' / ', array_filter([$t->company_name ?? null, $t->project_name ?? null]));
            // Staff see everyone's tasks: say whose it is
            if (isset($t->users)) {
                $names = array_map(function ($u) { return $u->display_name ?: $u->name; }, (array) $t->users);
                $context .= ($context !== '' ? ' · ' : '') . ($names ? implode(', ', $names) : 'unassigned');
            }
            $items[] = [
                'id'           => 'task-' . $t->uuid,
                'title'        => (string) $t->{$nameField} . ($context !== '' ? ' (' . $context . ')' : ''),
                'type'         => 'task',
                'status'       => $stage,
                'status_label' => self::STAGE_LABELS[$stage] ?? $stage,
                'priority'     => 'normal',
                'due'          => !empty($t->due_date) ? substr($t->due_date, 0, 10) : null,
                'url'          => base_url($url . rawurlencode($t->uuid)),
                'can_complete' => $canComplete,
            ];
        }
        return $items;
    }

    private function ticketItems()
    {
        $me = (int) $this->principal['id'];
        $this->load->model('Tickets_model');
        if ($this->isStaff()) {
            if (!$this->staffCan('helpdesk', 'listing')) return [];
            $rows = $this->Tickets_model->listing(['status' => 'active', 'assigned_to' => $me], 1, self::MAX_ITEMS);
            $url = function ($uuid) { return base_url('helpdesk/view?uuid=' . rawurlencode($uuid)); };
        } else {
            $rows = $this->Tickets_model->listForDeveloper($me, 'active');
            $url = function ($uuid) { return base_url('portal/developers/ticket/' . rawurlencode($uuid)); };
        }

        $items = [];
        foreach ((array) $rows as $t) {
            // SLA target: first response until someone replied, then resolution
            $due = empty($t->first_responded_at) && !empty($t->first_response_due) ? $t->first_response_due : ($t->resolution_due ?? null);
            $items[] = [
                'id'           => 'ticket-' . $t->uuid,
                'title'        => trim(Tickets_model::staffRef($t) . ' ' . $t->subject) . (!empty($t->company_name) ? ' (' . $t->company_name . ')' : ''),
                'type'         => 'ticket',
                'status'       => (string) $t->status,
                'status_label' => Tickets_model::statusLabel($t->status),
                'priority'     => in_array($t->priority, ['urgent', 'high'], true) ? 'high' : ($t->priority === 'low' ? 'low' : 'normal'),
                'due'          => $due ? self::iso($due) : null,
                'url'          => $url($t->uuid),
                'can_complete' => false,
            ];
        }
        return $items;
    }

    private function canCompleteTasks()
    {
        return $this->isStaff() && $this->staffCan('tasks', 'add');
    }

    /** MySQL DATETIME (Mauritius time, see Api_Controller) to ISO 8601. */
    private static function iso($datetime)
    {
        $ts = strtotime($datetime);
        return $ts ? date('c', $ts) : null;
    }

    private function json($body, $http = 200)
    {
        http_response_code($http);
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
        exit;
    }
}
