<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/Api_Controller.php';

/**
 * Existing project/sprint tasks for the mobile app (no new task concept).
 *   staff     -> all tasks, gated by the tasks menu permissions (listing/view/add)
 *   developer -> tasks assigned to them (developer portal rules: can't set "validated")
 *   customer  -> read-only: their company's tasks and public notes (customer portal)
 *
 * GET  api/tasks/index?stage=new,in_progress&customer_id=&project_id=&sprint_id=&assigned_to=me|<id>&due_in_days=&search=&page=&per_page=
 * GET  api/tasks/view/<uuid>
 * POST api/tasks/moveStage/<uuid>   stage, [note, note_public]
 * POST api/tasks/addNote/<uuid>     notes, [public]
 * POST api/tasks/assign/<uuid>      user_id      (staff)
 * POST api/tasks/unassign/<uuid>    user_id      (staff)
 * GET  api/tasks/meta               stages + filter lookups
 * GET  api/tasks/sprints?customer_id=   active sprints (staff, for ticket -> task conversion)
 */
class Tasks extends Api_Controller
{
    const STAGES = [
        'new'         => ['label' => 'New',         'progress' => 0],
        'in_progress' => ['label' => 'In progress', 'progress' => 20],
        'testing'     => ['label' => 'Testing',     'progress' => 40],
        'staging'     => ['label' => 'Staging',     'progress' => 60],
        'validated'   => ['label' => 'Validated',   'progress' => 80],
        'completed'   => ['label' => 'Completed',   'progress' => 100],
        'on_hold'     => ['label' => 'On hold',     'progress' => 20],
        'stopped'     => ['label' => 'Stopped',     'progress' => 0],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tasks_model');
    }

    public function index()
    {
        $page = $this->page();
        $perPage = $this->perPage();
        $stages = $this->stageFilter();
        $search = trim((string) $this->param('search', ''));

        if ($this->isStaff()) {
            $this->requireStaffPermission('tasks', 'listing');
            $assigned = $this->param('assigned_to');
            $assigned = $assigned === 'me' ? (int) $this->principal['id'] : (int) $assigned;
            $args = [
                $this->intParam('customer_id') ?: '', $this->intParam('project_id') ?: '', $this->intParam('sprint_id') ?: '',
                $stages, $assigned ?: '',
            ];
            $rows = $this->Tasks_model->fetchAll($args[0], $args[1], $args[2], $args[3], $args[4], 't.due_date', 'asc',
                $page, $perPage, '', '', $search, false, '', '', '', 'open');
            $total = (int) $this->Tasks_model->totalRows($args[0], $args[1], $args[2], $args[3], $args[4], '', 'asc', '', $search, '', '', '', 'open');
            $items = array_map(function ($t) {
                return $this->taskSummary($t, $t->name, $t->stage, array_map(function ($u) {
                    return $u->display_name ?: $u->name;
                }, $t->users));
            }, $rows);
        } elseif ($this->isDeveloper()) {
            $this->load->model('Developersportal_model');
            $all = $this->Developersportal_model->getMyTasks(
                (int) $this->principal['id'],
                $this->intParam('customer_id') ?: '', $this->intParam('project_id') ?: '', $this->intParam('sprint_id') ?: '',
                empty($stages) ? '' : json_encode($stages),
                't.due_date IS NULL, t.due_date', 'asc', 1, 9999, '',
                $this->intParam('due_in_days') ?: ''
            );
            $all = $this->searchFilter($all, $search, 'task_name');
            $total = count($all);
            $items = array_map(function ($t) {
                return $this->taskSummary($t, $t->task_name, $t->task_stage, null);
            }, array_slice($all, ($page - 1) * $perPage, $perPage));
        } else {
            $this->load->model('Customersportal_model');
            $sprintId = $this->intParam('sprint_id');
            if ($sprintId && !$this->customerOwnsSprint($sprintId)) {
                $this->notFound('Sprint');
            }
            $all = $this->Customersportal_model->getTasks($sprintId ?: '', 'task_number', 'asc', implode(',', $stages), '');
            $all = $this->searchFilter($all, $search, 'name');
            $total = count($all);
            $items = array_map(function ($t) {
                return $this->taskSummary($t, $t->name, $t->stage, null);
            }, array_slice($all, ($page - 1) * $perPage, $perPage));
        }

        $this->ok(['items' => $items, 'page' => $page, 'per_page' => $perPage, 'total' => $total]);
    }

    public function view($uuid = '')
    {
        $task = $this->taskOr404($uuid);

        if ($this->isStaff()) {
            $ids = array_filter(array_map('intval', (array) $task->assigned_users));
            $assignees = empty($ids) ? [] : $this->db->select('id, name, display_name, user_type')->from('users')
                ->where_in('id', $ids)->get()->result();
            $assignees = array_map(function ($u) {
                return ['id' => (int) $u->id, 'name' => $u->display_name ?: $u->name, 'user_type' => $u->user_type];
            }, $assignees);
            $notes = $this->db->select('tn.id, tn.notes, tn.created_on, tn.display_type, tn.out_of_scope, u.name AS staff_author, ca.name AS customer')
                ->from('task_notes tn')
                ->join('users u', 'u.id = tn.created_by', 'left')
                ->join('customer_access ca', 'ca.id = tn.created_by_customer', 'left')
                ->where(['tn.task_id' => (int) $task->id, 'tn.status' => 1])
                ->order_by('tn.created_on', 'desc')->get()->result();
        } elseif ($this->isDeveloper()) {
            $assignees = array_map(function ($u) {
                return ['id' => null, 'name' => $u->name, 'user_type' => $u->user_type];
            }, $task->assigned_to);
            $notes = $task->notes;
        } else {
            $assignees = [];
            $notes = $task->notes; // public notes only (Customersportal_model::getTask)
        }

        $this->ok([
            'task' => [
                'uuid'            => $task->uuid,
                'task_ref'        => $task->task_ref ?? $this->taskRef($task),
                'task_number'     => $task->task_number,
                'name'            => $task->name,
                'section'         => $task->section,
                'description_html'=> $this->safeHtml($task->description),
                'stage'           => $task->stage,
                'stage_label'     => self::stageLabel($task->stage),
                'progress'        => isset($task->progress) ? (int) $task->progress : null,
                'due_date'        => $task->due_date,
                'estimated_hours' => $this->isCustomer() ? null : ($task->estimated_hours !== null ? (float) $task->estimated_hours : null),
                'work_type'       => $this->isCustomer() ? null : $task->work_type,
                'company_name'    => $task->company_name ?? null,
                'project_name'    => $task->project_name ?? null,
                'sprint_name'     => $task->sprint_name ?? null,
                'created_on'      => $task->created_on,
                'scope'           => [
                    'client_expectation' => $task->scope_client_expectation,
                    'not_included'       => $task->scope_not_included,
                    'when_done'          => $task->scope_when_done,
                ],
            ],
            'assignees' => $assignees,
            'notes'     => array_map(function ($n) {
                return [
                    'id'         => (int) $n->id,
                    'notes_html' => $this->safeHtml($n->notes),
                    'author'     => $this->noteAuthor($n),
                    'is_customer'=> empty($n->developer ?? null) && empty($n->staff_author ?? null) && !empty($n->customer),
                    'public'     => isset($n->display_type) ? $n->display_type === 'public' : null,
                    'created_on' => $n->created_on,
                ];
            }, $notes ?: []),
            'files' => array_map(function ($f) {
                return [
                    'id'         => (int) $f->id,
                    'url'        => base_url('uploads/tasks/' . rawurlencode($f->file_name)),
                    'thumb_url'  => !empty($f->thumb_name) ? base_url('uploads/tasks/' . rawurlencode($f->thumb_name)) : null,
                    'ext'        => $f->file_ext,
                    'created_on' => $f->created_on,
                ];
            }, $task->files ?: []),
            'stage_history' => array_map(function ($h) {
                return [
                    'old_stage'  => $h->old_stage,
                    'new_stage'  => $h->new_stage,
                    'by'         => $h->name,
                    'by_type'    => $h->user_type,
                    'created_on' => $h->created_on,
                ];
            }, $task->stage_history ?: []),
            'can' => [
                'move_stage' => $this->isDeveloper() || $this->staffCan('tasks', 'add'),
                'add_note'   => $this->isDeveloper() || $this->staffCan('tasks', 'view'),
                'assign'     => $this->staffCan('tasks', 'add'),
                'stages'     => $this->allowedStages(),
            ],
        ]);
    }

    public function moveStage($uuid = '')
    {
        $this->requireMethod('post');
        if ($this->isCustomer()) $this->forbidden('Tasks are read-only in the app.');
        $stage = (string) $this->param('stage');
        if (!in_array($stage, $this->allowedStages(), true)) {
            $this->fail('validation', $stage === 'validated' ? 'Only the customer can move a task to Validated.' : 'Invalid stage.', 422);
        }
        $task = $this->taskOr404($uuid);
        $note = trim((string) $this->param('note', ''));

        if ($this->isStaff()) {
            $this->requireStaffPermission('tasks', 'add');
            $result = $this->Tasks_model->move_stage([
                'task_uuid' => $task->uuid,
                'stage'     => $stage,
                'progress'  => self::STAGES[$stage]['progress'],
            ]);
            if (!$result['result']) $this->fail('validation', $result['reason'], 422);
            if ($note !== '') {
                $this->Tasks_model->saveNote(['task_id' => (int) $task->id, 'notes' => $note]);
            }
        } else {
            $this->load->model('Developersportal_model');
            $this->Developersportal_model->moveStage((int) $task->id, $stage);
            if ($note !== '') {
                $this->Developersportal_model->saveNotes((int) $task->id, $note, $this->param('note_public') ? 'public' : 'private');
            }
        }
        $this->ok(['stage' => $stage, 'stage_label' => self::stageLabel($stage)]);
    }

    public function addNote($uuid = '')
    {
        $this->requireMethod('post');
        if ($this->isCustomer()) $this->forbidden('Tasks are read-only in the app.');
        $notes = trim((string) $this->param('notes', ''));
        if ($notes === '') $this->fail('validation', 'Please write a note.', 422);
        $notes = nl2br(htmlspecialchars($notes, ENT_QUOTES, 'UTF-8'));
        $task = $this->taskOr404($uuid);

        if ($this->isStaff()) {
            $this->requireStaffPermission('tasks', 'view');
            $result = $this->Tasks_model->saveNote(['task_id' => (int) $task->id, 'notes' => $notes]);
            if (!$result['result']) $this->fail('error', $result['reason'], 500);
        } else {
            $this->load->model('Developersportal_model');
            $this->Developersportal_model->saveNotes((int) $task->id, $notes, $this->param('public') ? 'public' : 'private');
        }
        $this->ok(['saved' => true], 201);
    }

    public function assign($uuid = '')
    {
        $this->requireMethod('post');
        $this->requireStaffPermission('tasks', 'add');
        $task = $this->taskOr404($uuid);
        $userId = $this->assignableUserOr422($this->intParam('user_id'));
        if (!$this->Tasks_model->assignUser($userId, (int) $task->id)) {
            $this->fail('invalid_state', 'This user is already assigned to the task.', 409);
        }
        $this->ok(['assigned' => true]);
    }

    public function unassign($uuid = '')
    {
        $this->requireMethod('post');
        $this->requireStaffPermission('tasks', 'add');
        $task = $this->taskOr404($uuid);
        $userId = $this->intParam('user_id');
        $this->db->where(['task_id' => (int) $task->id, 'user_id' => $userId])->delete('task_user');
        if ($this->db->affected_rows() === 0) {
            $this->fail('invalid_state', 'This user is not assigned to the task.', 409);
        }

        // Same email as Tasks::removeUser.
        $this->load->model(['Email_model3', 'system_model']);
        $user = $this->db->select('email, name')->get_where('users', ['id' => $userId])->row();
        if ($user && !empty($user->email)) {
            $emailData = ['user' => $user, 'task' => $this->Tasks_model->getSingleById((int) $task->id),
                'logo' => $this->system_model->getParam('logo'), 'link' => '', 'link_label' => ''];
            $content = $this->load->view('_email/header', $emailData, true)
                . $this->load->view('_email/userHasBeenRemovedTask', $emailData, true)
                . $this->load->view('_email/footer', [], true);
            $this->Email_model3->save($user->email, 'You have been removed from a task', $content);
        }
        $this->ok(['unassigned' => true]);
    }

    public function meta()
    {
        $stages = [];
        foreach (self::STAGES as $k => $s) $stages[] = ['value' => $k, 'label' => $s['label']];
        $data = ['stages' => $stages, 'allowed_stages' => $this->allowedStages()];

        if ($this->isStaff()) {
            $data['users'] = array_map(function ($u) {
                return ['id' => (int) $u->id, 'name' => $u->display_name ?: $u->name, 'user_type' => $u->user_type];
            }, $this->db->select('id, name, display_name, user_type')->from('users')
                ->where('status', 1)->where_in('user_type', ['regular', 'developer'])
                ->order_by('user_type DESC, name')->get()->result());
            $data['customers'] = $this->rows($this->db->select('customer_id AS id, company_name AS name')->from('customers')
                ->where(['status' => 1, 'active' => 1])->order_by('company_name')->get()->result());
            $data['projects'] = $this->rows($this->db->select('id, name, customer_id')->from('projects')
                ->where(['status' => 1, 'active' => 1])->order_by('name')->get()->result());
        } elseif ($this->isDeveloper()) {
            $this->load->model('Developersportal_model');
            $data['customers'] = $this->rows($this->Developersportal_model->getMyCustomers((int) $this->principal['id']));
            $data['projects'] = $this->rows($this->Developersportal_model->getMyProjects((int) $this->principal['id']));
        } else {
            $this->load->model('Customersportal_model');
            $data['projects'] = array_map(function ($p) {
                return ['id' => (int) $p->id, 'name' => $p->name];
            }, $this->Customersportal_model->getProjects((int) $this->principal['id']));
            $data['sprints'] = array_map(function ($s) {
                return ['id' => (int) $s->id, 'name' => $s->name, 'project_name' => $s->project_name,
                    'tasks_count' => (int) $s->tasks_count, 'progress_pct' => (int) $s->progress_pct];
            }, $this->Customersportal_model->getSprints());
        }
        $this->ok($data);
    }

    /** Active sprints that accept tasks, for converting a ticket into a task. */
    public function sprints()
    {
        $this->requireStaffPermission('helpdesk', 'edit');
        $this->db->select('s.id, s.name, p.name AS project_name, c.company_name, p.customer_id')
            ->from('sprints s')
            ->join('projects p', 'p.id = s.project_id')
            ->join('customers c', 'c.customer_id = p.customer_id')
            ->where(['s.status' => 1, 's.active' => 1, 'p.active' => 1]);
        if ($cid = $this->intParam('customer_id')) {
            $this->db->where('p.customer_id', $cid);
        }
        $rows = $this->db->order_by('c.company_name, p.name, s.name')->get()->result();
        $this->ok(array_map(function ($s) {
            return ['id' => (int) $s->id, 'name' => $s->name, 'project_name' => $s->project_name,
                'company_name' => $s->company_name, 'customer_id' => (int) $s->customer_id];
        }, $rows));
    }

    // ------------------------------------------------------------------

    private function taskOr404($uuid)
    {
        if (!self::isUuid($uuid)) $this->notFound('Task');
        if ($this->isStaff()) {
            $this->requireStaffPermission('tasks', 'view');
            $task = $this->Tasks_model->fetchSingle($uuid);
        } elseif ($this->isDeveloper()) {
            $this->load->model('Developersportal_model');
            $task = $this->Developersportal_model->getSingleTask($uuid); // assigned tasks only
        } else {
            $this->load->model('Customersportal_model');
            $task = $this->Customersportal_model->getTask($uuid); // company's tasks only
        }
        if (empty($task)) $this->notFound('Task');
        return $task;
    }

    private function allowedStages()
    {
        if ($this->isCustomer()) return [];
        $stages = array_keys(self::STAGES);
        if ($this->isDeveloper()) {
            $stages = array_values(array_diff($stages, ['validated']));
        }
        return $stages;
    }

    private function stageFilter()
    {
        $raw = $this->param('stage', '');
        $list = is_array($raw) ? $raw : array_filter(explode(',', (string) $raw));
        return array_values(array_intersect($list, array_keys(self::STAGES)));
    }

    private function customerOwnsSprint($sprintId)
    {
        return (bool) $this->db->from('sprints s')->join('projects p', 'p.id = s.project_id')
            ->where(['s.id' => (int) $sprintId, 'p.customer_id' => (int) $this->principal['customer_id']])
            ->count_all_results();
    }

    private function assignableUserOr422($userId)
    {
        $ok = $userId > 0 && $this->db->where(['id' => $userId, 'status' => 1])
            ->where_in('user_type', ['regular', 'developer'])->count_all_results('users');
        if (!$ok) $this->fail('validation', 'Invalid user.', 422);
        return $userId;
    }

    private function searchFilter($rows, $search, $nameField)
    {
        if ($search === '') return $rows;
        return array_values(array_filter($rows, function ($t) use ($search, $nameField) {
            return stripos((string) $t->$nameField, $search) !== false
                || stripos((string) ($t->task_ref ?? ''), $search) !== false
                || stripos((string) ($t->section ?? ''), $search) !== false;
        }));
    }

    private function taskSummary($t, $name, $stage, $assignees)
    {
        $out = [
            'uuid'         => $t->uuid,
            'task_ref'     => $t->task_ref ?? '',
            'name'         => $name,
            'section'      => $t->section ?? null,
            'stage'        => $stage,
            'stage_label'  => self::stageLabel($stage),
            'due_date'     => $t->due_date ?? null,
            'overdue'      => !empty($t->due_date) && $t->due_date < date('Y-m-d') && !in_array($stage, ['completed', 'validated', 'stopped'], true),
            'company_name' => $t->company_name ?? null,
            'project_name' => $t->project_name ?? null,
            'sprint_name'  => $t->sprint_name ?? null,
            'notes_count'  => isset($t->notes_count) ? (int) $t->notes_count : (isset($t->notes) && is_numeric($t->notes) ? (int) $t->notes : null),
        ];
        if ($assignees !== null) $out['assignees'] = array_values($assignees);
        if (!$this->isCustomer()) {
            $out['estimated_hours'] = isset($t->estimated_hours) && $t->estimated_hours !== null ? (float) $t->estimated_hours : null;
            $out['timer_running'] = !empty($t->start_time) && empty($t->finish_time);
        }
        return $out;
    }

    private function noteAuthor($n)
    {
        foreach (['staff_author', 'developer', 'author', 'customer'] as $k) {
            if (!empty($n->$k)) return $n->$k;
        }
        return 'Unknown';
    }

    private function taskRef($task)
    {
        return function_exists('task_ref') && !empty($task->task_number)
            ? task_ref($task->project_code ?? null, $task->sprint_code ?? null, $task->task_number) : '';
    }

    /** Task descriptions/notes are stored as HTML written by staff and customers; sanitize for the app. */
    private function safeHtml($html)
    {
        $this->load->model('Tickets_model');
        return Tickets_model::sanitizeHtml((string) $html);
    }

    private static function stageLabel($stage)
    {
        return isset(self::STAGES[$stage]) ? self::STAGES[$stage]['label'] : ucfirst(str_replace('_', ' ', (string) $stage));
    }

    private function rows($result)
    {
        return array_map(function ($r) {
            $a = (array) $r;
            foreach (['id', 'customer_id'] as $k) if (isset($a[$k])) $a[$k] = (int) $a[$k];
            return $a;
        }, $result ?: []);
    }
}
