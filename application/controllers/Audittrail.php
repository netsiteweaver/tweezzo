<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Audittrail extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("logger_model");
        $this->mybreadcrumb->add('Audit Trail', base_url('audittrail/listing'));

        $this->load->model("accesscontrol_model");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("audittrail","view");

        $this->load->model("audittrail_model");
    }

    public function listing()
    {
        //Access Control
        if(!isAuthorised(get_class(),"listing")) return false;

        $page = $this->uri->segment(3,1);
        $this->data['rpp'] = isset($_SESSION['rpp'])?$_SESSION['rpp']:'50';
        $this->data['rows'] = $this->audittrail_model->getPaged($page,$this->data['rpp']);
        $this->data['users'] = $this->audittrail_model->getUsers();
        $this->data['ips'] = $this->audittrail_model->getIPs();

        $this->data['filter_start_date'] = isset($_SESSION['filter_start_date'])?$_SESSION['filter_start_date']:"";
        $this->data['filter_end_date'] = isset($_SESSION['filter_end_date'])?$_SESSION['filter_end_date']:"";
        $this->data['filter_user'] = isset($_SESSION['filter_user'])?$_SESSION['filter_user']:"";
        $this->data['filter_ip'] = isset($_SESSION['filter_ip'])?$_SESSION['filter_ip']:"";
        $this->data['filter_controller'] = isset($_SESSION['filter_controller'])?$_SESSION['filter_controller']:"";
        $this->data['filter_method'] = isset($_SESSION['filter_method'])?$_SESSION['filter_method']:"";

        $this->data['pagination'] = getPagination("audittrail/listing/",$this->audittrail_model->totalRows(),$this->data['rpp']);

        $this->mybreadcrumb->add('List', base_url('audittrail/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/audittrail/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);      
    }

    public function view()
    {
        $this->mybreadcrumb->add('List', base_url('audittrail/listing'));
        $this->mybreadcrumb->add('View', base_url('audittrail/view'),"fa-eye");
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $id = $this->uri->segment(3);
        $this->data['record'] = $this->audittrail_model->get($id);

        $this->data["content"]=$this->load->view("/audittrail/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);      
    }

    public function set_rpp()
    {
        $rpp = $this->uri->segment(3);
        $_SESSION['rpp'] = $rpp;
    }

    public function filters()
    {
        $_SESSION['filter_start_date'] = $this->input->post('start_date');
        $_SESSION['filter_end_date'] = $this->input->post('end_date');
        $_SESSION['filter_user'] = $this->input->post('user');
        $_SESSION['filter_ip'] = $this->input->post('ip');
        $_SESSION['filter_controller'] = $this->input->post('controller');
        $_SESSION['filter_method'] = $this->input->post('method');
        
    }

    public function clearFilters()
    {
        unset($_SESSION['filter_start_date']);
        unset($_SESSION['filter_end_date']);
        unset($_SESSION['filter_user']);
        unset($_SESSION['filter_ip']);
        unset($_SESSION['filter_controller']);
        unset($_SESSION['filter_method']);

    }

}