<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sprints extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("Sprints_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("sprints","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("sprints","edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("sprints","view");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("sprints","delete");
        // $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
    }

    public function add()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Sprints', base_url('sprints/listing'));
        $this->mybreadcrumb->add('Add', base_url('sprints/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup();

        $this->data["content"]=$this->load->view("/sprints/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['sprint'] = $this->Sprints_model->fetchSIngle($uuid);
        $this->data['validationReminder'] = $this->Sprints_model->getValidationReminderState((int)$this->data['sprint']->id);

        //Breadcrumbs
        $this->mybreadcrumb->add('Sprints', base_url('sprints/listing'));
        $this->mybreadcrumb->add('Edit', base_url('sprints/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup();

        $this->data["content"]=$this->load->view("/sprints/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function view()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"view")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['sprint'] = $this->Sprints_model->fetchSIngle($uuid);
        $this->data['tasks'] = $this->Sprints_model->getAttachedTasks($uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Sprints', base_url('sprints/listing'));
        $this->mybreadcrumb->add('Edit', base_url('sprints/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup();

        $this->data["content"]=$this->load->view("/sprints/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Sprints', base_url('sprints/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Sprints";

        $customer_id = $this->input->get('customer_id');
        $order_by = $this->input->get('order_by');
        $order_dir = $this->input->get('order_dir');
        $active_filter = $this->input->get('active_filter');
        if (!in_array($active_filter, ['active', 'inactive', 'all'], true)) {
            $active_filter = 'active';
        }
        $this->data['active_filter'] = $active_filter;

        $page = $this->uri->segment(3);
        $per_page = (!empty($this->input->get("display"))) ? $this->input->get("display") : $this->system_model->getParam("rows_per_page");
        $this->data['sprints'] = $this->Sprints_model->fetchAll($customer_id,$order_by,$order_dir,$page,$per_page,$active_filter);
        $total_rows = $this->Sprints_model->totalRows($customer_id,$active_filter);
        $this->data['pagination'] = getPagination("sprints/listing",$total_rows,$per_page);

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data['task_perms']['add'] = $this->accesscontrol_model->authorised('tasks', 'add');

        $this->data["content"]=$this->load->view("/sprints/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function save()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $data = $this->input->post();
        if (empty($data['uuid']) && !empty($data['project_id'])) {
            $empty_sprints = $this->Sprints_model->getEmptySprints($data['project_id']);
            if (!empty($empty_sprints)) {
                flashDanger($this->Sprints_model->emptySprintsBlockReason($empty_sprints));
                redirect(base_url('sprints/add'));
                return;
            }
        }

        $response = $this->Sprints_model->save($data);
        if($response['result']== false){
            flashDanger($response['reason']);
            if (!empty($data['uuid'])) {
                redirect(base_url('sprints/edit/' . $data['uuid']));
            } else {
                redirect(base_url('sprints/add'));
            }
            return;
        }
        redirect(base_url("sprints/listing"));
    }

    public function delete()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $uuid = $this->input->post('uuid');
        $affected_rows = $this->Sprints_model->delete($uuid);

        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
    }

    /** POST uuid: HMS emails the client's billing addresses what must be paid before going live. */
    public function send_payment_request()
    {
        //Access Control
        if(!isAuthorised(get_class(),"edit")) return false;

        $uuid = (string) $this->input->post('uuid');
        $back = base_url("sprints/edit/" . rawurlencode($uuid));
        $result = $this->Sprints_model->sendPaymentRequestManually($uuid);
        if ($result === null) {
            flashDanger("Sprint not found, or its project or customer is inactive.");
        } elseif ($result['error'] !== '') {
            flashDanger("Payment request NOT sent: " . $result['error']);
        } elseif (empty($result['refs'])) {
            flashWarning("No task in this sprint references a quotation or invoice (QUO/EST/INV number in Ref), so there is nothing to request.");
        } elseif (empty($result['invoices']) && empty($result['quotations'])) {
            flashSuccess("Nothing outstanding: " . implode(', ', $result['refs']) . " are fully paid. No email was sent.");
        } else {
            $sent = [];
            $problems = [];
            foreach ($result['emails'] as $mail) {
                $sent = array_merge($sent, $mail['sent']);
                if (empty($mail['recipients'])) {
                    $problems[] = "HMS client {$mail['client']} has no email address";
                }
                if (!empty($mail['failed'])) {
                    $problems[] = "email FAILED for " . implode(', ', $mail['failed']);
                }
            }
            if (empty($problems)) {
                flashSuccess("Payment request emailed to " . implode(', ', $sent) . ".");
            } else {
                flashDanger("Payment request: " . implode('; ', $problems) . "." . (!empty($sent) ? " It was sent to " . implode(', ', $sent) . "." : " Nobody was emailed."));
            }
        }
        redirect($back);
    }

    public function toggle_active()
    {
        if ($this->accesscontrol_model->authorised("sprints", "edit") == 0) {
            echo json_encode(['result' => false, 'reason' => 'Permission denied']);
            return;
        }

        $uuid = $this->input->post('uuid');
        if (empty($uuid)) {
            echo json_encode(['result' => false, 'reason' => 'Missing sprint']);
            return;
        }

        $out = $this->Sprints_model->toggleActive($uuid);
        echo json_encode($out);
    }

    public function index()
    {
        $this->listing();
    }

    public function getByProjectId()
    {
        $project_id = $this->input->post("project_id");
        $sprints = $this->Sprints_model->getByProjectId($project_id);

        foreach ($sprints as $sprint) {
            $sprint->past_end = $this->Sprints_model->isPastEndDate($sprint->end_date ?? '');
        }

        echo json_encode(array(
            "result"    =>  (count($sprints)==0)?false:true,
            "data"      =>  $sprints,
            "rows"      =>  count($sprints)
        ));

        exit;
    }

    public function checkSprintExists()
    {
        $project_id = $this->input->post("project_id");
        $name = $this->input->post("name");

        if (empty($project_id) || empty($name)) {
            echo json_encode(['result' => false, 'exists' => false]);
            exit;
        }

        $sprint = $this->db->select('s.*')
            ->from('sprints s')
            ->where('s.project_id', $project_id)
            ->where('s.name', $name)
            ->where('s.status', 1)
            ->get()
            ->row();

        if ($sprint) {
            $payload = [
                'result' => true,
                'exists' => true,
                'sprint' => [
                    'id' => $sprint->id,
                    'name' => $sprint->name
                ]
            ];
            if (preg_match('/^Sprint\s+\d+$/i', trim((string) $name))) {
                $payload['suggested_name'] = $this->Sprints_model->suggestSprintName($project_id);
            }
            echo json_encode($payload);
        } else {
            echo json_encode([
                'result' => true,
                'exists' => false
            ]);
        }
        exit;
    }

    public function suggestName()
    {
        if (!isAuthorised(get_class(), 'add')) {
            echo json_encode(['result' => false, 'reason' => 'Permission denied']);
            exit;
        }

        $project_id = $this->input->post('project_id');
        if (empty($project_id)) {
            echo json_encode(['result' => false, 'reason' => 'Project is required']);
            exit;
        }

        echo json_encode([
            'result' => true,
            'name' => $this->Sprints_model->suggestSprintName($project_id)
        ]);
        exit;
    }

    public function checkEmptySprints()
    {
        if (!isAuthorised(get_class(), 'add')) {
            echo json_encode(['result' => false, 'reason' => 'Permission denied']);
            exit;
        }

        $project_id = $this->input->post('project_id');
        if (empty($project_id)) {
            echo json_encode(['result' => false, 'has_empty' => false, 'empty_sprints' => []]);
            exit;
        }

        $empty_sprints = $this->Sprints_model->getEmptySprints($project_id);
        echo json_encode([
            'result' => true,
            'has_empty' => !empty($empty_sprints),
            'reason' => !empty($empty_sprints)
                ? $this->Sprints_model->emptySprintsBlockReason($empty_sprints)
                : '',
            'listing_url' => $this->Sprints_model->getListingUrlForProject($project_id),
            'empty_sprints' => array_map(function ($s) {
                return [
                    'id' => (int) $s->id,
                    'uuid' => $s->uuid,
                    'name' => $s->name,
                    'code' => $s->code,
                    'view_url' => base_url('sprints/view/' . $s->uuid),
                ];
            }, $empty_sprints),
        ]);
        exit;
    }

    public function createAjax()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) {
            echo json_encode(['result' => false, 'reason' => 'Permission denied']);
            exit;
        }

        $project_id = $this->input->post("project_id");
        $name = $this->input->post("name");

        if (empty($project_id) || empty($name)) {
            echo json_encode(['result' => false, 'reason' => 'Project ID and name are required']);
            exit;
        }

        // Check if sprint already exists
        $existing = $this->db->select('s.*')
            ->from('sprints s')
            ->where('s.project_id', $project_id)
            ->where('s.name', $name)
            ->where('s.status', 1)
            ->get()
            ->row();

        if ($existing) {
            $payload = [
                'result' => true,
                'sprint' => [
                    'id' => $existing->id,
                    'name' => $existing->name
                ],
                'existing' => true
            ];
            if (preg_match('/^Sprint\s+\d+$/i', trim((string) $name))) {
                $payload['suggested_name'] = $this->Sprints_model->suggestSprintName($project_id);
            }
            echo json_encode($payload);
            exit;
        }

        $empty_sprints = $this->Sprints_model->getEmptySprints($project_id);
        if (!empty($empty_sprints)) {
            echo json_encode([
                'result' => false,
                'reason' => $this->Sprints_model->emptySprintsBlockReason($empty_sprints),
                'listing_url' => $this->Sprints_model->getListingUrlForProject($project_id),
                'empty_sprints' => array_map(function ($s) {
                    return [
                        'id' => (int) $s->id,
                        'uuid' => $s->uuid,
                        'name' => $s->name,
                        'code' => $s->code,
                    ];
                }, $empty_sprints),
            ]);
            exit;
        }

        $data = [
            'project_id' => $project_id,
            'name' => $name,
            'start_date' => $this->input->post('start_date'),
            'end_date' => $this->input->post('end_date'),
        ];

        $response = $this->Sprints_model->save($data);
        
        if ($response['result']) {
            // Get the newly created sprint
            $sprint = $this->db->select('s.*')
                ->from('sprints s')
                ->where('s.project_id', $project_id)
                ->where('s.name', $name)
                ->where('s.status', 1)
                ->order_by('s.id', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            echo json_encode([
                'result' => true,
                'sprint' => [
                    'id' => $sprint->id,
                    'name' => $sprint->name
                ],
                'existing' => false
            ]);
        } else {
            echo json_encode(['result' => false, 'reason' => 'Failed to create sprint']);
        }
        exit;
    }

    public function checkCodeAvailable()
    {
        if(!isAuthorised(get_class(),"add")) return false;

        $project_id = $this->input->post("project_id");
        $code = trim((string)$this->input->post("code"));
        $uuid = trim((string)$this->input->post("uuid"));

        if (empty($project_id) || $code === "") {
            echo json_encode([
                "result" => false,
                "exists" => false,
                "reason" => "Project and code are required"
            ]);
            exit;
        }

        $exists = $this->Sprints_model->codeExists($project_id, $code, $uuid);
        echo json_encode([
            "result" => true,
            "exists" => $exists
        ]);
        exit;
    }

}
