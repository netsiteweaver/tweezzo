<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Developers extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("developers_model");
        $this->load->model("accesscontrol_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("developers","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("developers","edit");
        $this->data['perms']['permission'] = $this->accesscontrol_model->authorised("developers","permission");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("developers","delete");
        $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
    }

    public function index()
    {
        redirect(base_url('developers/listing'));
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        $this->data['developers'] = $this->developers_model->get();

        //Breadcrumbs
        $this->mybreadcrumb->add('Developers', base_url('developers/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Developers";

        $this->data["content"]=$this->load->view("/developers/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $id = $this->uri->segment(3);
        $this->data['user'] = $this->developers_model->getById($id);
        $this->data['dpt'] = $this->developers_model->getAllDepartments();
        $this->data['levels'] = array('1'=>'Normal','2'=>'Administrator','3'=>'Root');
        
        //Breadcrumbs
        $this->mybreadcrumb->add('Developers', base_url('developers/listing'));
        $this->mybreadcrumb->add('Edit', base_url('developers/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data['landing_pages'] = array(
            array("url"=>"dashboard/index","label"=>"Dashboard"),
            array("url"=>"messages/listing","label"=>"Messages"),
            array("url"=>"orders/add","label"=>"New Order"),
            array("url"=>"orders/listing","label"=>"Orders")
        );

        $this->data["content"]=$this->load->view("/developers/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function update()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"edit")) return false;

        $postedData = $this->input->post();
        if(empty($postedData)){
            flashDanger("Missing parameters. Redirecting to developers list");
            redirect(base_url('developers/listing'));
        }

        if(!empty($_FILES['image']['name'])){
            $this->load->model("files_model");
            $images = $this->files_model->uploadImage('image','uploads/users');
            $postedData['image'] = $images['file_name'];
        }

        $this->load->model('developers_model');
        
        $result = $this->developers_model->update($postedData);

        if($result['result']=='1'){

            if($postedData['id'] == $_SESSION['user_id']){
                $_SESSION['authenticated_user']->email = $postedData['email'];
                $_SESSION['authenticated_user']->name = $postedData['name'];
                if(!empty($images['file_name'])) $_SESSION['authenticated_user']->photo = $images['file_name'];
            }

            $this->data['user'] = $result['user'];
            $message = $this->load->view("email/account_updated",$this->data, true);
            $this->load->model("email_model");
            $this->email_model->send($result['user']['email'],'Your account has been updated',$message);

            echo json_encode(array("result"=>true));
        }else{
            echo json_encode(array("result"=>false,"reason"=>$result['reason']));
        }
    }

    public function add()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Developers', base_url('developers/listing'));
        $this->mybreadcrumb->add('Add', base_url('developers/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['dpt'] = $this->developers_model->getAllDepartments();

        $this->data["content"]=$this->load->view("/developers/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function insert()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $data = $this->input->post();
        if($this->input->post("generate_password")=="yes"){
            $data['password'] = randomName(12);
        }
        $result = $this->data['new_user'] = $this->developers_model->insert($data);
        if($result['result']== false){
            flashDanger($result['reason']);
            redirect(base_url("developers"));
            return;
        }

        $this->load->model('system_model');
        $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
        $message = $this->load->view("email/new_account",$this->data, true);
        $this->load->model("email_model");
        $this->email_model->send($result['data']['email'],'Account has been created',$message);

        echo json_encode(array("result"=>true,"permissions_url"=>base_url("developers/permission/".$result['data']['id'])));
    }

    public function delete()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $id = $this->uri->segment(3);
        $this->developers_model->delete($id);
        if($_SESSION['user_id'] == $id){
            redirect(base_url("developers/signout"));
        }
        flashDanger("User has been deleted");
        redirect(base_url("developers/listing"));
    }

    public function deleteAjax()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $id = $this->input->post("id");
        $this->data['user'] = $this->developers_model->delete($id);

        // $message = $this->load->view("email/developers/account_deleted",$this->data, true);

        // $this->load->model("email_model");
        // $this->email_model->send($this->data['user']->email,'Account has been deleted',$message);

        echo json_encode(array("result"=>true));
    }

    public function permission()
    {
        //Access Control
        if(!isAuthorised(get_class(),"permission")) return false;
        
        $params = $this->uri->segment(3);
        $this->load->model('developers_model');
        if(!empty($params)){
            $this->data['user_info'] = $this->developers_model->get_by_id($params);
        }
        $this->mybreadcrumb->add('Developers', base_url('developers/listing'));
        $this->mybreadcrumb->add('Permission', base_url('developers/permission'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('menu_model');
        $this->data['resources'] = (!empty($this->data['user_info']))?$this->menu_model->listAll($params,$this->data['user_info']->user_level):null;
        
        $this->data["content"]=$this->load->view("/developers/permissions",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    } 

    public function save_permission()
    {
        //Access Control
        if(!isAuthorised(get_class(),"permission")) return false;
        
        $updateJson = $this->input->post('updateJson');
        $to_update = json_decode($updateJson);
        foreach($to_update as $key => $data1){

            $var=array(
                'menu_id'   =>  $data1->menu_id,
                'create'    =>  $data1->cr,
                'read'      =>  $data1->rd,
                'update'    =>  $data1->up,
                'delete'    =>  $data1->de
                );
            $this->db->where('id',$data1->permission_id);
            $id = $this->db->update('permissions',$var);
        }

        $addJson = $this->input->post('addJson');
        $to_add = json_decode($addJson);
        foreach($to_add as $key => $data2){
            $var=array(
                'user_id'   =>  $data2->user_id,
                'menu_id'   =>  $data2->menu_id,
                'create'    =>  $data2->cr,
                'read'      =>  $data2->rd,
                'update'    =>  $data2->up,
                'delete'    =>  $data2->de
                );
            $id = $this->db->insert('permissions',$var);
        }

        echo 1;
    }

    public function check_username_email()
    {
        $email = $username = $this->input->post("email");
        $id = $this->input->post("id");
        $level = $this->input->post("level");
        // if( (empty($username)) || (empty($email)) ) echo json_encode(array("result"=>false));
        
        $result = $this->developers_model->check_username_email($username,$email,$id,$level);
        echo json_encode($result);
    }

}
