<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("Projects_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("projects","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("projects","edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("projects","view");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("projects","delete");
        // $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
    }

    public function add()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Projects', base_url('projects/listing'));
        $this->mybreadcrumb->add('Add', base_url('projects/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data["content"]=$this->load->view("/projects/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['project'] = $this->Projects_model->fetchSIngle($uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Projects', base_url('projects/listing'));
        $this->mybreadcrumb->add('Edit', base_url('projects/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Users_model');
        $this->data['users'] = $this->Users_model->lookup();

        $this->data["content"]=$this->load->view("/projects/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function view()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"view")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['project'] = $this->Projects_model->fetchSIngle($uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Projects', base_url('projects/listing'));
        $this->mybreadcrumb->add('View', base_url('projects/view'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data["content"]=$this->load->view("/projects/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Projects', base_url('projects/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Projects";

        $customer_id = $this->input->get('customer_id');
        $stage = $this->input->get('stage');
        $order_by = $this->input->get('order_by');
        $order_dir = $this->input->get('order_dir');

        $page = $this->uri->segment(3);
        $per_page = (!empty($this->input->get("display"))) ? $this->input->get("display") : $this->system_model->getParam("rows_per_page");
        $this->data['projects'] = $this->Projects_model->fetchAll($customer_id,$stage,$order_by,$order_dir,$page,$per_page);
        $total_rows = $this->Projects_model->totalRows($customer_id,$stage);
        $this->data['pagination'] = getPagination("projects/listing",$total_rows,$per_page);
// debug($this->data['pagination']);
        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data["content"]=$this->load->view("/projects/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function save()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $data = $this->input->post();
        $response = $this->Projects_model->save($data);
        if($response['result']== false){
            flashDanger($response['reason']);
            redirect(base_url("projects"));
            return;
        }
        redirect(base_url("projects/listing"));
    }

    public function notes()
    {
        //Access Control
        if(!isAuthorised(get_class(),"view")) return false;

        $data = $this->input->post();
        $response = $this->Projects_model->notes($data);
        if($response['result']== false){
            flashDanger($response['reason']);
            redirect(base_url("projects"));
            return;
        }
        redirect(base_url("projects/listing?customer_id=".$data['customer_id']."&stage=".$data['stage']));
    }

    public function delete()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $uuid = $this->input->post('uuid');
        $affected_rows = $this->Projects_model->delete($uuid);

        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
    }

    public function index()
    {
        $this->listing();
    }

    public function getByCustomerId()
    {
        $customer_id = $this->input->post("customer_id");
        $projects = $this->Projects_model->getByCustomerId($customer_id);

        echo json_encode(array(
            "result"    =>  (count($projects)==0)?false:true,
            "data"      =>  $projects,
            "rows"      =>  count($projects)
        ));

        exit;
    }


    public function team()
    {
        //Access Control        
        if (!isAuthorised(get_class(), "edit")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Add', base_url('projects/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Developers";

        $uuid = $this->uri->segment(3);
        $this->data['project'] = $this->Projects_model->fetchSIngle($uuid);

        $this->load->model("Developers_model");
        $this->data['developers'] = $this->Developers_model->lookup();

        $this->data["content"] = $this->load->view("/projects/team", $this->data, true);
        $this->load->view("/layouts/default",$this->data);

    }

}
