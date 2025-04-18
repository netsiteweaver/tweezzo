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
        $this->data['projects'] = $this->Projects_model->lookup("projects");

        $this->data["content"]=$this->load->view("/sprints/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['sprint'] = $this->Sprints_model->fetchSIngle($uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Sprints', base_url('sprints/listing'));
        $this->mybreadcrumb->add('Edit', base_url('sprints/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup("projects");

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
        $this->data['projects'] = $this->Projects_model->lookup("projects");

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

        $page = $this->uri->segment(3);
        $per_page = (!empty($this->input->get("display"))) ? $this->input->get("display") : $this->system_model->getParam("rows_per_page");
        $this->data['sprints'] = $this->Sprints_model->fetchAll($customer_id,$order_by,$order_dir,$page,$per_page);
        $total_rows = $this->Sprints_model->totalRows($customer_id);
        $this->data['pagination'] = getPagination("sprints/listing",$total_rows,$per_page);

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data["content"]=$this->load->view("/sprints/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function save()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $data = $this->input->post();
        $response = $this->Sprints_model->save($data);
        if($response['result']== false){
            flashDanger($response['reason']);
            redirect(base_url("sprints"));
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

    public function index()
    {
        $this->listing();
    }

    public function getByProjectId()
    {
        $project_id = $this->input->post("project_id");
        $sprints = $this->Sprints_model->getByProjectId($project_id);

        echo json_encode(array(
            "result"    =>  (count($sprints)==0)?false:true,
            "data"      =>  $sprints,
            "rows"      =>  count($sprints)
        ));

        exit;
    }

}
