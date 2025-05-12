<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("users_model");
        $this->data['userLevels'] = array('','Normal','Administrator','Root');
        $this->load->model("accesscontrol_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("users","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("users","edit");
        $this->data['perms']['permission'] = $this->accesscontrol_model->authorised("users","permission");
        $this->data['perms']['activate'] = $this->accesscontrol_model->authorised("users","activate");
        $this->data['perms']['deactivate'] = $this->accesscontrol_model->authorised("users","deactivate");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("users","delete");
        $this->data['perms']['reset_password'] = $this->accesscontrol_model->authorised("users","reset_password");
        $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
   
        
        $this->data['user_status']=array(1=>"Active",2=>"Inactive",3=>"Deleted");
    }

    public function index()
    {
        redirect(base_url('users/listing'));
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        $user_type = 'regular';//$this->input->get("user_type");
        $this->data['users'] = $this->users_model->get([],$user_type);
        // debug($this->data['users']);
        // $this->data['users_admin'] = $this->users_model->getByLevel("Admin");
        if($_SESSION['user_level'] == 'Root') $this->data['users_root'] = $this->users_model->getByLevel("Root");

        //Breadcrumbs
        $this->mybreadcrumb->add('Users', base_url('users/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Users";

        $this->data["content"]=$this->load->view("/users/listing2",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $id = $this->uri->segment(3);
        $this->data['user'] = $this->users_model->getById($id);
        $this->data['dpt'] = $this->users_model->getAllDepartments();
        $this->data['levels'] = array('1'=>'Normal','2'=>'Administrator','3'=>'Root');
        
        //Breadcrumbs
        $this->mybreadcrumb->add('Users', base_url('users/listing'));
        $this->mybreadcrumb->add('Edit', base_url('users/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data['landing_pages'] = array(
            array("url"=>"dashboard/index","label"=>"Dashboard"),
            array("url"=>"messages/listing","label"=>"Messages"),
            array("url"=>"orders/add","label"=>"New Order"),
            array("url"=>"orders/listing","label"=>"Orders")
        );

        $this->data["content"]=$this->load->view("/users/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function myprofile()
    {
        //Access Control        
        //   if(!isAuthorised(get_class(),"myprofile")) return false;

        $id = $_SESSION['user_id'];
        $this->data['user'] = $this->users_model->getById($id);
        $this->data['levels'] = array('1'=>'Normal','2'=>'Administrator','3'=>'Root');
        
        //Breadcrumbs
        $this->mybreadcrumb->add('My Profile', base_url('users/myprofile'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data['page_title'] = "My Profile";

        $this->data["content"]=$this->load->view("/users/profile",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function update()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"edit")) return false;

        $postedData = $this->input->post();
        if(empty($postedData)){
            flashDanger("Missing parameters. Redirecting to users list");
            redirect(base_url('users/listing'));
        }

        if(!empty($_FILES['image']['name'])){
            $this->load->model("files_model");
            $images = $this->files_model->uploadImage('image','uploads/users');
            $postedData['image'] = $images['file_name'];
        }

        $this->load->model('users_model');
        
        $result = $this->users_model->update($postedData);

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

    public function updateprofile()
    {
        //Access Control        
        // if(!isAuthorised(get_class(),"myprofile")) return false;

        $postedData = $this->input->post();
        if(empty($postedData)){
            flashDanger("Missing parameters. Redirecting to users list");
            redirect(base_url('users/listing'));
        }

        $s3=$this->uri->segment(3);

        if($s3=='password'){
            $result = $this->users_model->update_password($postedData);
        }else{

            if(!empty($_FILES['image']['name'])){
                $this->load->model("files_model");
                $images = $this->files_model->uploadImage('image','uploads/users');
                $postedData['image'] = $images['file_name'];
            }

            $result = $this->users_model->updateprofile($postedData);
        }
        

        if($result['result']==true){
            
            if($s3!='password'){
                $_SESSION['authenticated_user']->email = $postedData['email'];
                $_SESSION['authenticated_user']->name = $postedData['name'];
                if(!empty($images['file_name'])) $_SESSION['authenticated_user']->photo = $images['file_name'];
                flashSuccess("Your profile has been successfully updated");
            }else{
                flashSuccess("Your password has been successfully updated");
            }
            redirect(base_url('users/myprofile'));
        }else{
            flashDanger($result['reason']);
            redirect(base_url('users/myprofile'));
        }
    }

    public function add()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Users', base_url('users/listing'));
        $this->mybreadcrumb->add('Add', base_url('users/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['dpt'] = $this->users_model->getAllDepartments();

        $this->data["content"]=$this->load->view("/users/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function insert()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $data = $this->input->post();
        $checkPassword = validatePassword($data['password']);
        if(!empty($checkPassword)){
            echo json_encode(array(
                'result'=>false,
                'reason'=>"Password must be <b>at least 8 characters</b> long and <b>must</b> contain <b>at least one uppercase</b> letter, <b>one lowercase</b> letter, <b>one number</b>, and <b>one special character</b>."
            ));
            exit;
        }

        if($data['password'] != $data['pswd2']){
            echo json_encode(array('result'=>false,'reason'=>"Password and Confirm Password do not match"));
            exit;
        }

        if($this->input->post("generate_password")=="yes"){
            $data['password'] = randomName(12);
        }
        $result = $this->data['new_user'] = $this->users_model->insert($data);
        if($result['result']== false){
            flashDanger($result['reason']);
            redirect(base_url("users"));
            return;
        }

        $this->load->model('system_model');
        $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
        $message = $this->load->view("email/new_account",$this->data, true);
        $this->load->model("email_model");
        $this->email_model->send($result['data']['email'],'Account has been created',$message);

        echo json_encode(array("result"=>true,"permissions_url"=>base_url("users/permission/".$result['data']['id'])));
    }

    public function activate()
    {
        //Access Control
        if(!isAuthorised(get_class(),"activate")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['user'] = $this->users_model->publish($uuid);

        $message = $this->load->view("email/users/account_activated",$this->data, true);

        $this->load->model("email_model");
        $this->email_model->send($this->data['user']->email,'Your account has been unsuspended',$message);

        flashInfo($this->data['user']->name . "'s account has been activated and an email has been sent");
        redirect(base_url("users/listing"));
    }

    public function deactivate()
    {
        //Access Control
        if(!isAuthorised(get_class(),"deactivate")) return false;

        $uuid = $this->uri->segment(3);
        if($uuid == $_SESSION['user_id']){
            flashWarning("Sorry! You cannot de-activate your own account");
            redirect(base_url("users/listing"));
        }
        $this->data['user'] = $this->users_model->unpublish($uuid);

        $message = $this->load->view("email/users/account_deactivated",$this->data, true);

        $this->load->model("email_model");
        $this->email_model->send($this->data['user']->email,'Your account has been suspended',$message);

        flashInfo($this->data['user']->name . "'s account has been de-activated and an email has been sent");
        redirect(base_url("users/listing"));
    }

    public function delete()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $id = $this->uri->segment(3);
        $this->users_model->delete($id);
        if($_SESSION['user_id'] == $id){
            redirect(base_url("users/signout"));
        }
        flashDanger("User has been deleted");
        redirect(base_url("users/listing"));
    }

    public function deleteAjax()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $id = $this->input->post("id");
        $this->data['user'] = $this->users_model->delete($id);

        $message = $this->load->view("email/users/account_deleted",$this->data, true);

        $this->load->model("email_model");
        $this->email_model->send($this->data['user']->email,'Account has been deleted',$message);

        echo json_encode(array("result"=>true));
    }

    public function signin()
    {
        if( (isset($_SESSION['user_id'])) && (!empty($_SESSION['user_id'])) ){
            redirect(base_url('dashboard'));
        }
        $uuid = $this->uri->segment(3);
        if(!empty($uuid)){
            $this->load->model('users_model');
            $this->data['username'] = $this->users_model->getByUUID($uuid,'username');
        }
        $this->data['page_title'] = "Signin";
        $this->data['session_timeout']=$param = $this->uri->segment(3);

        $this->data['video'] = ['filename'=>'99656-653113881_tiny.mp4','poster'=>'99656-653113881_tiny.jpg'];

        $this->load->view("/layouts/login",$this->data);
    }

    public function authenticate()
    {
        $data = array(
            'inputEmail'	=> 	$this->input->post('inputEmail'),
            'inputPassword'	=>	$this->input->post('inputPassword')
        );
        $user = $this->users_model->authenticate($data);

        if($user){

            if($user->user_type == 'developer'){
                echo json_encode(array("result"=>false,"reason"=>"Please use <a href='".base_url("portal/developers/signin?email=".$this->input->post('inputEmail'))."'>Developers Portal</a>"));
                exit;
            }

            if($user->status==1){
                $hour = intval(date('H'));
                $greeting = "Good " . (($hour > 12) ? 'Evening' : 'Morning') . ' <b>' . $user->name . "</b> !";
                $_SESSION['authenticated_user']=$user;
                $_SESSION['user_id'] 	= $user->id;
                $_SESSION['user_name']  = $user->username;
                $_SESSION['user_level'] 	= $user->user_level;
                $_SESSION['department_name'] 	= $user->department;
                $_SESSION['photo'] 	= $user->photo;

                echo json_encode(array("result"=>true,"greeting"=>$greeting,"expired_url"=>(isset($_SESSION['expired_url'])?$_SESSION['expired_url']:false),"landing_page"=>$user->landing_page));
            }elseif($user->status==2){
                echo json_encode(array("result"=>false,"reason"=>"We are sorry, but your account is currently inactive."));
            }elseif($user->status==99){
                echo json_encode(array("result"=>false,"reason"=>"We are sorry, missing or invalid email in records."));
            }else{
                echo json_encode(array("result"=>false,"reason"=>"Sorry, this account has been deleted."));
            }
        }else{
            echo json_encode(array("result"=>false,"reason"=>"Invalid username or password."));
        }
    }

    public function signout()
    {
        session_destroy();
        $param = $this->uri->segment(3);
        redirect(base_url("users/signin/".$param));
    }

    public function forget_password()
    {
        $this->data['pageTitle'] = "FORGET PASSWORD";

        $status = $this->uri->segment(3);
        $this->data['status'] = $status;
        $this->load->view('common/headers',$this->data);
        // $this->load->view('common/nav',$this->data);
        $this->load->view('users/forget_password',$this->data);
        $this->load->view('common/footer',$this->data);
    }

    public function changePassword()
    {
        // $this->load->model("user_model");
        
        $reset_token = $this->uri->segment(3);
        $email = urldecode($this->uri->segment(4));
        $user = $this->user_model->checkToken($reset_token, $email);

        if(!$user){
            redirect(base_url('user/forget-password/error101'));
        }else{
            $this->data['pageTitle'] = "CHANGE PASSWORD";

            $this->data['reset_token']  = $reset_token;
            $status = $this->uri->segment(3);
            $this->data['status'] = $status;
            $this->load->view('common/header',$this->data);
            $this->load->view('common/nav',$this->data);
            $this->load->view('user/change_password',$this->data);
            $this->load->view('common/closure',$this->data);
        }
    }

    public function saveNewPassword()
    {
        $password = $this->input->post("newPassword");
        $reset_token = $this->input->post("reset_token");
        $this->load->model("user_model");
        $this->user_model->changePassword($reset_token,$password);
       
        redirect(base_url("user"));
    }

    public function permission()
    {
        //Access Control
        if(!isAuthorised(get_class(),"permission")) return false;
        
        $params = $this->uri->segment(3);
        $this->load->model('users_model');
        if(!empty($params)){
            $this->data['user_info'] = $this->users_model->get_by_id($params);
        }
        $this->mybreadcrumb->add('Users', base_url('users/listing'));
        $this->mybreadcrumb->add('Permission', base_url('users/permission'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('menu_model');
        $this->data['resources'] = (!empty($this->data['user_info']))?$this->menu_model->listAll($params,$this->data['user_info']->user_level):null;
        
        $this->data["content"]=$this->load->view("/users/permissions",$this->data,true);
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

    public function check_username()
    {
        $username = $this->input->post("username");
        if(empty($username)) echo json_encode(array("result"=>false));
        $result = $this->users_model->check_username($username);
        echo json_encode(array("result"=>$result));
    }
    public function check_email()
    {
        $email = $this->input->post("email");
        $id = $this->input->post("id");
        if(empty($email)) echo json_encode(array("result"=>false));
        $result = $this->users_model->check_email($email,$id);
        echo json_encode(array("result"=>$result));
    }
    public function check_username_email()
    {
        $username = $this->input->post("username");
        $email = $this->input->post("email");
        $id = $this->input->post("id");
        $level = $this->input->post("level");
        $user_type = $this->input->post("user_type");
        // if( (empty($username)) || (empty($email)) ) echo json_encode(array("result"=>false));
        
        $result = $this->users_model->check_username_email($username,$email,$id,$level,$user_type);
        echo json_encode($result);
    }

    public function check_user_level()
    {
        $username = $this->input->post("username");
        $user_level = $this->db->select("user_level")->from("users")->where(array("username"=>$username,"status"=>"1"))->get()->row("user_level");
        if(!empty($user_level)){
            $this->forget_password_process($username,$user_level);
            echo json_encode(array("result"=>true,"user_level"=>$user_level));
        }else{
            echo json_encode(array("result"=>false,"reason"=>"Username not found"));
        }
        exit;   
    }

    public function forget_password_process($username,$user_level)
    {
        $this->load->model("users_model");
        $result = $this->users_model->resetPassword($username,$user_level);
        echo json_encode(array("result"=>$result));
        exit;
    }

    public function init()
    {
        $users = $this->db->select("*")->from("users")->get()->result();
        $menu_items = $this->db->select("*")->from("menu")->get()->result();

        foreach($users as $user){
            foreach($menu_items as $menu_item){
                $this->db->query("INSERT INTO permissions (`user_id`,`menu_id`,`create`,`read`,`update`,`delete`) VALUES ($user->id,$menu_item->id,0,1,0,0)");
            }
        }
    }
}
