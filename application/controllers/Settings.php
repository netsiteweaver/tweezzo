<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller'] = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method'] = $this->uri->segment(2, "index");
        $this->load->model("system_model");

        $this->load->model("accesscontrol_model");
        $this->data['perms']['notifications'] = $this->accesscontrol_model->authorised("settings","notifications");
    }

    public function params()
    {
        //Access Control
        if(!isAuthorised(get_class(),"params")) return false;
        //Get all params. If you need to get only one specific parameter, use getParam() with the 
        //the name of the parameter as parameter, like getParam('rows_per_page')
        $params = $this->system_model->getAllParams();
        foreach ($params as $param) {
            $this->data[$param->title] = $param->value;
        }

        $this->data['send_enquiries_to'] = $this->system_model->getParam('send_enquiries_to',true);
        $this->data['smtp_settings'] = $this->system_model->getParam('smtp_settings',true);
        $this->load->model('users_model');
        $this->data['admins'] = $this->users_model->getAdmins();
        $this->data['users'] = $this->users_model->getAll();

        $this->mybreadcrumb->add('System Params', base_url('settings/params'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/settings/params2",$this->data,true);
        $this->load->view("/layouts/default",$this->data);        
    }

    public function test_email()
    {
        //fetch current user's email
        $user = $this->db->select("email")->from("users")->where("id",$_SESSION['user_id'])->get()->row();
        $this->load->model("email_model");
        $result = $this->email_model->send($user->email,'✅ Test Email','Hello<br>This is a test email.');
        if($result){
            echo json_encode(array(
                'result'=>true,
                'email'=>$user->email
            ));
        }else{
            echo json_encode(array("result"=>false));
        }
        exit;
    }

    public function updateparams()
    {
        //Access Control
        if(!isAuthorised("settings","params")) return false;

        $this->load->model("system_model");
        $this->system_model->updateParams();

        flashSuccess("Params have been updated");
        redirect(base_url('settings/params'));
    }

    public function company()
    {
        //Access Control
        if(!isAuthorised(get_class(),"company")) return false;

        $this->load->model("system_model");
        $this->data['company'] = $this->system_model->getCompanyInfo();

        $this->mybreadcrumb->add('Company Settings', base_url('settings/company'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/settings/company",$this->data,true);
        $this->load->view("/layouts/default",$this->data);        
    }

    public function updatecompany()
    {
        //Access Control
        if(!isAuthorised(get_class(),"company")) return false;

        $this->load->model("system_model");
        $this->system_model->updateCompany();

        flashSuccess("Company details have been updated");
        redirect(base_url('settings/company'));
    }

    public function menu_order()
    {
        //Access Control
        if(!isAuthorised(get_class(),"menu_order")) return false;

        $this->data['page_title'] = "Menu Order";

        $this->load->model("menu_model");
        $this->data['resources'] = $this->menu_model->getforlisting();

        $this->data["content"]=$this->load->view("/settings/menu",$this->data,true);
        $this->load->view("/layouts/default",$this->data);        

    }

    public function notifications()
    {
        //Access Control
        if(!isAuthorised(get_class(),"notifications")) return false;

        $this->data['page_title'] = "Notifications";
        
        $this->data['create_projects'] = $this->system_model->getParam("notification_create_projects",true);
        $this->data['update_projects'] = $this->system_model->getParam("notification_update_projects",true);
        $this->data['delete_projects'] = $this->system_model->getParam("notification_delete_projects",true);

        $this->data['create_sprints'] = $this->system_model->getParam("notification_create_sprints",true);
        $this->data['update_sprints'] = $this->system_model->getParam("notification_update_sprints",true);
        $this->data['delete_sprints'] = $this->system_model->getParam("notification_delete_sprints",true);

        $this->data['create_tasks'] = $this->system_model->getParam("notification_create_tasks",true);
        $this->data['update_tasks'] = $this->system_model->getParam("notification_update_tasks",true);
        $this->data['delete_tasks'] = $this->system_model->getParam("notification_delete_tasks",true);

        $this->data['create_notes'] = $this->system_model->getParam("notification_create_notes",true);
        $this->data['update_notes'] = $this->system_model->getParam("notification_update_notes",true);
        $this->data['delete_notes'] = $this->system_model->getParam("notification_delete_notes",true);

        $this->data['create_customers'] = $this->system_model->getParam("notification_create_customers",true);
        $this->data['update_customers'] = $this->system_model->getParam("notification_update_customers",true);
        $this->data['delete_customers'] = $this->system_model->getParam("notification_delete_customers",true);

        $this->data['create_developers'] = $this->system_model->getParam("notification_create_developers",true);
        $this->data['update_developers'] = $this->system_model->getParam("notification_update_developers",true);
        $this->data['delete_developers'] = $this->system_model->getParam("notification_delete_developers",true);

        $this->data['create_users'] = $this->system_model->getParam("notification_create_users",true);
        $this->data['update_users'] = $this->system_model->getParam("notification_update_users",true);
        $this->data['delete_users'] = $this->system_model->getParam("notification_delete_users",true);

        
        $this->mybreadcrumb->add('Notifications', base_url('settings/notifications'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('users_model');
        $this->data['admins'] = $this->users_model->getAdmins();

        $this->load->model("users_model");
        $this->data['users'] = $this->users_model->lookup("regular");

        $this->data["content"]=$this->load->view("/settings/notifications",$this->data,true);
        $this->load->view("/layouts/default",$this->data);        
    }

    public function updatenotifications()
    {
        //Access Control
        if(!isAuthorised("settings","params")) return false;

        $this->load->model("system_model");
        $this->system_model->updatenotifications();

        echo json_encode(array("result",true));
    }

    public function updateAllNotifications()
    {
        //Access Control
        if(!isAuthorised("settings","params")) return false;

        $this->load->model("system_model");
        $this->system_model->updateAllNotifications();

        echo json_encode(array("result",true));
    }

    public function updatenotifications2()
    {
        //Access Control
        if(!isAuthorised("settings","params")) return false;

        $this->load->model("system_model");
        $this->system_model->updatenotifications2();

        echo json_encode(array("result",true));
    }

    public function updatenotifications3()
    {
        //Access Control
        if(!isAuthorised("settings","params")) return false;

        $this->load->model("system_model");
        $this->system_model->updatenotifications3();

        echo json_encode(array("result",true));
    }
}
