<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Timesheets extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller'] = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method'] = $this->uri->segment(2, "index");
        $this->load->model("Timesheets_model");
        $this->load->model("accesscontrol_model");
        $this->data['perms']['listing'] = $this->accesscontrol_model->authorised("timesheets", "listing");
    }

    public function index()
    {
        redirect(base_url('timesheets/listing'));
    }

    public function listing()
    {
        if (!isAuthorised(get_class(), "listing")) {
            return false;
        }

        $from = $this->input->get('from');
        $to = $this->input->get('to');
        if (empty($from)) {
            $from = date('Y-m-01');
        }
        if (empty($to)) {
            $to = date('Y-m-t');
        }

        $filters = [
            'from'         => $from,
            'to'           => $to,
            'developer_id' => $this->input->get('developer_id'),
            'customer_id'  => $this->input->get('customer_id'),
            'project_id'   => $this->input->get('project_id'),
            'sprint_id'    => $this->input->get('sprint_id'),
            'task_id'      => $this->input->get('task_id'),
            'task_uuid'    => $this->input->get('task_uuid'),
        ];

        $this->mybreadcrumb->add('Timesheets', base_url('timesheets/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Timesheets";

        $this->data['filters'] = $filters;
        $this->data['rows'] = $this->Timesheets_model->adminFetch($filters);

        $this->load->model('Developers_model');
        $this->data['developers'] = $this->Developers_model->lookup();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup($filters['customer_id']);

        $this->load->model('Sprints_model');
        $this->data['sprints'] = $this->Sprints_model->lookup2($filters['project_id']);

        if (!empty($filters['task_uuid']) || !empty($filters['task_id'])) {
            $this->load->model('Tasks_model');
            if (!empty($filters['task_uuid'])) {
                $task = $this->Tasks_model->fetchSingle($filters['task_uuid']);
            } else {
                $taskRow = $this->db->select('uuid')->from('tasks')->where('id', (int) $filters['task_id'])->get()->row();
                $task = $taskRow ? $this->Tasks_model->fetchSingle($taskRow->uuid) : null;
            }
            if (!empty($task)) {
                $this->data['filtered_task'] = $task;
                $ref = !empty($task->task_ref) ? $task->task_ref : $task->task_number;
                $this->data['page_title'] .= " — " . $ref . " / " . $task->name;
            }
        }

        $this->data["content"] = $this->load->view("/timesheets/listing", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }
}
