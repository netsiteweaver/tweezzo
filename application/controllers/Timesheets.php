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
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("timesheets", "edit");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("timesheets", "delete");
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
        $this->data['listing_qs'] = http_build_query(array_filter($filters, function ($v) {
            return $v !== null && $v !== '';
        }));

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

    public function edit()
    {
        if (!isAuthorised(get_class(), "edit")) {
            return false;
        }

        $id = (int) $this->uri->segment(3);
        $timesheet = $this->Timesheets_model->adminGetById($id);
        if (empty($timesheet)) {
            flashDanger("Timesheet entry not found");
            redirect(base_url('timesheets/listing'));
            return;
        }

        $returnQs = $this->input->get('return');
        if ($returnQs === null) {
            $returnQs = '';
        }

        if ($this->input->post()) {
            $result = $this->Timesheets_model->adminUpdate($id, [
                'start_time'  => $this->input->post('start_time'),
                'finish_time' => $this->input->post('finish_time'),
                'notes'       => $this->input->post('notes'),
            ]);
            if (!$result['result']) {
                flashDanger(!empty($result['reason']) ? $result['reason'] : 'Could not update timesheet');
                redirect(base_url('timesheets/edit/' . $id . ($returnQs !== '' ? '?return=' . rawurlencode($returnQs) : '')));
                return;
            }
            flashSuccess("Timesheet entry updated");
            $redirect = base_url('timesheets/listing');
            if ($returnQs !== '') {
                $redirect .= '?' . $returnQs;
            }
            redirect($redirect);
            return;
        }

        $this->mybreadcrumb->add('Timesheets', base_url('timesheets/listing'));
        $this->mybreadcrumb->add('Edit', base_url('timesheets/edit/' . $id));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Edit timesheet";
        $this->data['timesheet'] = $timesheet;
        $this->data['return_qs'] = $returnQs;

        $this->loadScript('assets/js/pages/timesheets_edit.js');
        $this->data["content"] = $this->load->view("/timesheets/edit", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }

    public function delete()
    {
        if (!isAuthorised(get_class(), "delete")) {
            return false;
        }

        $id = (int) $this->uri->segment(3);
        $confirm = $this->uri->segment(4);
        $returnQs = $this->input->get('return');
        if ($returnQs === null) {
            $returnQs = '';
        }

        $listingUrl = base_url('timesheets/listing');
        if ($returnQs !== '') {
            $listingUrl .= '?' . $returnQs;
        }

        if ($confirm === 'confirm') {
            if ($this->Timesheets_model->adminDelete($id)) {
                flashSuccess("Timesheet entry deleted");
            } else {
                flashDanger("Timesheet entry not found");
            }
            redirect($listingUrl);
            return;
        }

        $timesheet = $this->Timesheets_model->adminGetById($id);
        if (empty($timesheet)) {
            flashDanger("Timesheet entry not found");
            redirect($listingUrl);
            return;
        }

        $this->mybreadcrumb->add('Timesheets', base_url('timesheets/listing'));
        $this->mybreadcrumb->add('Delete', base_url('timesheets/delete/' . $id));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Delete timesheet";
        $this->data['timesheet'] = $timesheet;
        $this->data['return_qs'] = $returnQs;

        $this->data["content"] = $this->load->view("/timesheets/delete", $this->data, true);
        $this->load->view("/layouts/default", $this->data);
    }
}
