<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tasks_model');
    }

    /**
     * Global search: tasks by task ref, name, etc. Returns JSON for topbar typeahead.
     */
    public function tasks()
    {
        if (!isAuthorised('Tasks', 'listing')) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => false, 'reason' => 'Permission denied']));
            return;
        }

        $q = $this->input->get('q');
        $q = is_string($q) ? trim($q) : '';

        if ($q === '') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'tasks' => []]));
            return;
        }

        $limit = 15;
        $rows = $this->Tasks_model->fetchAll('', '', '', [], '', '', 'asc', 1, $limit, '', '', $q, false, '', '', '');

        $tasks = [];
        foreach ($rows as $task) {
            $tasks[] = [
                'uuid'         => $task->uuid,
                'task_ref'     => isset($task->task_ref) ? $task->task_ref : '',
                'name'         => $task->name,
                'section'      => isset($task->section) ? $task->section : '',
                'project_name' => isset($task->project_name) ? $task->project_name : '',
                'sprint_name'  => isset($task->sprint_name) ? $task->sprint_name : '',
                'view_url'     => base_url('tasks/view?task_uuid=' . rawurlencode($task->uuid)),
            ];
        }

        $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'tasks' => $tasks]));
    }
}
