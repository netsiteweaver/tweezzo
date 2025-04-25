<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("Notes_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("notes","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("notes","edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("notes","view");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("notes","delete");
        // $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Notes', base_url('notes/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Notes";

        $customer_id = $this->input->get('customer_id');
        $stage = $this->input->get('stage');
        $order_by = $this->input->get('order_by');
        $order_dir = $this->input->get('order_dir');

        $page = $this->uri->segment(3);
        $per_page = (!empty($this->input->get("display"))) ? $this->input->get("display") : $this->system_model->getParam("rows_per_page");
        $this->data['notes'] = $this->Notes_model->fetchAll($customer_id,$stage,$order_by,$order_dir,$page,$per_page);
        $total_rows = $this->Notes_model->totalRows($customer_id,$stage);
        $this->data['pagination'] = getPagination("notes/listing",$total_rows,$per_page);
        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data["content"]=$this->load->view("/notes/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function add()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Notes', base_url('notes/listing'));
        $this->mybreadcrumb->add('Add', base_url('notes/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data["content"]=$this->load->view("/notes/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['project'] = $this->Notes_model->fetchSIngle($uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Notes', base_url('notes/listing'));
        $this->mybreadcrumb->add('Edit', base_url('notes/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Users_model');
        $this->data['users'] = $this->Users_model->lookup();

        $this->data["content"]=$this->load->view("/notes/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function view()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"view")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['project'] = $this->Notes_model->fetchSIngle($uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Notes', base_url('notes/listing'));
        $this->mybreadcrumb->add('View', base_url('notes/view'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->data["content"]=$this->load->view("/notes/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function save()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $data = $this->input->post();
        $response = $this->Notes_model->save($data);
        if($response['result']== false){
            flashDanger($response['reason']);
            redirect(base_url("notes"));
            return;
        }
        redirect(base_url("notes/listing"));
    }

    public function notes()
    {
        //Access Control
        if(!isAuthorised(get_class(),"view")) return false;

        $data = $this->input->post();
        $response = $this->Notes_model->notes($data);
        if($response['result']== false){
            flashDanger($response['reason']);
            redirect(base_url("notes"));
            return;
        }
        redirect(base_url("notes/listing?customer_id=".$data['customer_id']."&stage=".$data['stage']));
    }

    public function delete()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $uuid = $this->input->post('uuid');
        $affected_rows = $this->Notes_model->delete($uuid);

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
        $notes = $this->Notes_model->getByCustomerId($customer_id);

        echo json_encode(array(
            "result"    =>  (count($notes)==0)?false:true,
            "data"      =>  $notes,
            "rows"      =>  count($notes)
        ));

        exit;
    }


    public function team()
    {
        //Access Control        
        if (!isAuthorised(get_class(), "edit")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Add', base_url('notes/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Developers";

        $uuid = $this->uri->segment(3);
        $this->data['project'] = $this->Notes_model->fetchSIngle($uuid);

        $this->load->model("Developers_model");
        $this->data['developers'] = $this->Developers_model->lookup();

        $this->data["content"] = $this->load->view("/notes/team", $this->data, true);
        $this->load->view("/layouts/default",$this->data);

    }

}
