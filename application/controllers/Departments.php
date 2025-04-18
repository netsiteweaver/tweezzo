<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Departments extends MY_Controller {

    public $data;
    
    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("Departments_model");
        $this->mybreadcrumb->add('Departments', base_url('departments/listing'));
        $this->load->model("accesscontrol_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("Departments","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("Departments","edit");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("Departments","delete");

        
    }

    public function index()
    {
        redirect(base_url('departments/listing'));
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        $this->data['departments'] = $this->Departments_model->getAllDepartments();

        //Breadcrumbs
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Departments";

        $this->data["content"]=$this->load->view("/departments/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function add()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Add', base_url('Departments/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();


        $this->data["content"]=$this->load->view("/departments/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['department'] = $this->Departments_model->getDepartmentByUuid($uuid);
        
        //Breadcrumbs
        $this->mybreadcrumb->add('Edit', base_url('Departments/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/departments/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function save()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;
        $result = $this->Departments_model->save();
        if($result['result']){
            flashSuccess("Department created successfully..");
            redirect(base_url("Departments/listing"));
            return;
        } else {
            flashDanger("Error in creating Department");
            redirect(base_url("Departments/add"));
            return;
        }

    }

    public function update()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"edit")) return false;
        $this->load->model('Departments_model');
        $result = $this->Departments_model->save();
        if($result['result'])
        {
            flashSuccess("Department has been successfully updated");
            redirect(base_url('Departments/listing'));
        }else{
            
            flashSuccess("Error in updating Department");
            redirect(base_url('Departments/edit'));
        }
    }

}