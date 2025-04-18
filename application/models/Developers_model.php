<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Developers_model extends CI_Model{

    public function get()
    {
        $this->db->select("u.*, d.name department");
        $this->db->join("departments d","d.id=u.department_id","left");
        $this->db->where(["u.status"=>"1","user_type"=>"developer"]);
        $this->db->order_by("u.name");
        return $this->db->get("users u")->result();
    }

    public function getByLevel($level="Normal")
    {
        $this->db->select("u.*, d.name department");
        $this->db->join("departments d","d.id=u.department_id","left");
        $this->db->where("u.status >", "0");
        $this->db->where("u.user_level",$level);
        $this->db->order_by("u.username");
        return $this->db->get("users u")->result();
    }

    public function getById($id)
    {
        $this->db->where("id","$id");
        return $this->db->get("users")->row();
    }

    public function getByUUID($uuid,$fieldname=false){
        if(!empty($fieldname)){
            $this->db->select($fieldname);
        }
        $this->db->where("uuid","$uuid");
        $obj = $this->db->get("users");
        if(!empty($fieldname)){
            return $obj->row($fieldname);
        }else{
            return $obj->row();
        }

    }


     public function getAllDepartments()
     {

        return $this->db->select('id,name')
            ->from('departments')
            ->where('status', '1')
            ->get()
            ->result();
     }


    public function insert($data)
    {
        if(!isset($data['name'])){
            return array('result'=>false,'reason'=>'No Data Received');
        }
        $data['created_by'] = $_SESSION['user_id'];
        $data['created'] = date('Y-m-d H:i:s');
        $data['uuid'] = gen_uuid();
        
        $this->db->set('uuid',$data['uuid']);
        $this->db->set('name',$data['name']);
        $this->db->set('job_title',$data['job_title']);
        $this->db->set('user_type','developer');
        $this->db->set('email',$data['email']);
        $this->db->set('username',$data['email']);
        $this->db->set('user_level','Normal');
        $this->db->set('department_id',1);
        $this->db->set('password',md5($data['password']), true);
        $this->db->set('created_by',$data['created_by']);
        $this->db->set('created',$data['created']);
        $this->db->insert("users");
        $data['id'] = $this->db->insert_id();
        // unset($data['password']);
        unset($data['pswd2']);

        return array('result'=>true,'data'=>$data);

    }

    public function publish($uuid)
    {
        $user = $this->db->select('id,uuid,email,username,name,user_level,job_title')->from("users")->where(array('uuid'=>$uuid,"status"=>"2"))->get()->row();

        if(empty($user)) return false;

        $this->db->set("status","1");
        $this->db->where("uuid",$uuid);
        $this->db->update("users");
        return $user;

    }

    public function unpublish($uuid)
    {
        $user = $this->db->select('id,uuid,email,username,name,user_level,job_title')->from("users")->where(array('uuid'=>$uuid,"status"=>"1"))->get()->row();

        if(empty($user)) return false;

        $this->db->set("status","2");
        $this->db->where("uuid",$uuid);
        $this->db->update("users");
        return $user;
    }

    public function delete($id)
    {
        $this->db->set("status","0");
        $this->db->where("id",$id);
        $this->db->update("users");
        
        return $this->db->select("uuid,name,email,username")->from('users')->where("id",$id)->get()->row();
    }

    public function authenticate($user_info) {
        $this->db->select("users.id, username, users.name, photo, user_level, user_type, users.email, created, last_login, ip, job_title, users.status,department_id,dp.name AS department");
        $this->db->join("departments as dp","dp.id=users.department_id","left");
        $this->db->where("password", md5($user_info['inputPassword']), true );
        $this->db->group_start();
        $this->db->where("username", $user_info['inputEmail']);
        $this->db->or_where("users.email", $user_info['inputEmail']);
        $this->db->group_end();
        // $this->db->where("status", '1');
        $result = $this->db->get("users")->row();
        // if(empty($result->email)){
        //     $result->status=99;
        // }
        if($result) {
            $this->recordSignIn($result->id);
            $this->recordLogin($result);
        }else{
            $user = new stdClass;
            $user->id = null;
            $user->username = $user_info['inputEmail'];
            $this->recordLogin($user);
        }
        return $result;
    }
    
    public function recordSignIn($id)
    {
        $ip = $this->input->ip_address();
        $this->db->set('last_login',date('Y-m-d H:i:s'));
        $this->db->set('ip',$ip);
        $this->db->where('id',$id);
        $this->db->update('users');
    }
    
    public function countActive()
    {
        $this->db->where('status','ACTIVE');
        return $this->db->count_all_results('users');
    }

    public function update($userDetails) //edit user
    {
        $pswd = trim($userDetails['pswd']);
        $pswd2 = trim($userDetails['pswd2']);
        

        if( (strlen($pswd)>0) && (strlen($pswd)<6) ) return array('result'=>false,'reason'=>'Password must be at least 6 characters.');
        if( (strlen($pswd)>0) && ($pswd != $pswd2) ) return array('result'=>false,'reason'=>'Password and Confirm Password does not match');
       
        $this->db->set("name",$userDetails['name']);
        $this->db->set("job_title",$userDetails['job_title']);
        $this->db->set("email",$userDetails['email']);
        if(isset($userDetails['image'])) $this->db->set('photo',$userDetails['image']);

        if(isset($userDetails['level']) && !empty($userDetails['level'])) $this->db->set("user_level",$userDetails['level']);
        if(isset($userDetails['pswd']) && !empty($userDetails['pswd'])) $this->db->set("password",md5($userDetails['pswd']));

        $this->db->where('id',$userDetails['id']);
        $this->db->update('users');

        if($userDetails['id'] == $_SESSION['user_id']){
            $_SESSION['department_id'] = $userDetails['department_id'];
        }

        return array('result'=>true,"user"=>$this->db->select()->from("users")->where("id",$userDetails["id"])->get()->row_array());

    }

    public function updateprofile($userDetails) //edit user
    {

        $this->db->set("name",$userDetails['name']);
        $this->db->set("job_title",$userDetails['job_title']);
        $this->db->set("email",$userDetails['email']);
        if(!empty($userDetails['image'])) $this->db->set("photo",$userDetails['image']);

        if(isset($userDetails['level']) && !empty($userDetails['level'])) $this->db->set("user_level",$userDetails['level']);

        $this->db->where('id',$userDetails['id']);
        $this->db->update('users');

        return array('result'=>true);

    }


    public function update_password($data)
    {
        $pswd = trim($data['pswd']);
        $pswd2 = trim($data['pswd2']);

        if( (strlen($pswd)>0) && (strlen($pswd)<6) ) return array('result'=>false,'reason'=>'Password must be at least 6 characters.');
        if( (strlen($pswd)>0) && ($pswd != $pswd2) ) return array('result'=>false,'reason'=>'Password and Confirm Password does not match');

        if(isset($data['pswd']) && !empty($data['pswd'])) {
            $this->db->set("password",md5($data['pswd']));

            $this->db->where('id',$data['id']);
            $this->db->update('users');

            return array('result'=>true);
        }

        return array('result'=>false,"reason"=>"Please enter same password twice");

    }

    public function get_by_id($id) {

        $user_level = $_SESSION['user_level'];
        $this->db->where("status",1);
        $this->db->where('users.id', $id);
        $obj = $this->db->get('users');
        if ($obj) {
            $result = $obj->row();
            if ($user_level == "Normal") {
                if ($result->user_level == "Normal") {
                    return $result;
                } else {
                    return false;
                }
            } elseif ($user_level == "Admin") {
                if (($result->user_level == "Admin") || ($result->user_level == "Normal")) {
                    return $result;
                } else {
                    return false;
                }
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }    

    public function check_username($username) {
        $this->db->select("count(id) as ct");
        $this->db->where("username",$username);
        $qry = $this->db->get("users");
        $result = $qry->row("ct");

        return ($result>0)?false:true;

    }

    public function check_email($email,$id) {
        $this->db->select("count(id) as ct");
        $this->db->where("email",$email);
        $this->db->where("id !=",$id);
        $qry = $this->db->get("users");
        $result = $qry->row("ct");

        return ($result>0)?false:true;

    }

    public function check_username_email($username,$email,$id="",$level) {

        if(!empty($username)){
            $this->db->select("count(id) as ct");
            $this->db->where(array("username"=>$username,"status"=>1));
            if(!empty($id)) $this->db->where("id !=",$id);
            $qry1 = $this->db->get("users");
            $result1 = $qry1->row("ct");
        }else{
            $result1=88;
        }
        // if($level !== 'normal'){
        if(!empty($email)){
            $this->db->select("count(id) as ct");
            $this->db->where(array("email"=>$email,"status"=>1));
            if(!empty($id)) $this->db->where("id !=",$id);
            $qry2 = $this->db->get("users");
            $result2 = $qry2->row("ct");        
        }else{
            $result2 = 99;
        }
        // }

        // if($level == 'normal'){
            // return array('username'=>($result1>0)?false:true);
        // }
        return array('username'=>($result1>0)?false:true,'email'=>($result2>0)?false:true);
    }

    public function getAdmins()
    {
        $this->db->select('id,uuid,username,name,email,job_title');
        $this->db->from('users');
        if($_SESSION['user_level']=='Root'){
            $this->db->where_in('user_level',array('Admin','Root'));
        }else{
            $this->db->where('user_level','Admin');
        }
        $this->db->where('status',1);
        $users = $this->db->get()->result();
        return $users;
    }

    public function getAll()
    {
        $this->db->select('id,uuid,username,name,email,job_title');
        $this->db->from('users');
        $this->db->where('status',1);
        $users = $this->db->get()->result();
        return $users;
    }

    public function countAll($user_level="")
    {
        $this->db->select('count(id) as ct');
        $this->db->from('users');
        $this->db->where('status',1);
        switch ($_SESSION['user_level']) {
            case 'Root':
                break;
            
            case 'Admin':
                if(!empty($user_level)) {
                    $this->db->where("user_level",$user_level);
                }else{
                    $this->db->where_in("user_level", array("Normal","Admin"));
                }
                break;
            
            case 'Normal':
                $this->db->where("user_level", "Normal");
                break;
        }
        $totalUsers = $this->db->get()->row('ct');
        return $totalUsers;
    }
    public function resetPassword($username,$user_level)
    {
        $user = $this->db->select('u.id,u.uuid,u.username,u.name,u.email,u.last_login,u.user_level,u.job_title, d.name departmentName')
                                    ->from('users u')
                                    ->join("departments d","d.id=u.department_id","left")
                                    ->where(array(
                                        'u.status'=>1,
                                        'u.username'=>$username
                                    ))->get()->row();
        if( !empty($user)) {
            if($user->user_level == "Normal"){
                $this->load->model("System_model");
                $this->load->model("email_model2");

                $members = $this->System_model->getParam("notification_reset_password",true);
                $content = "We have a request to reset password for user:";
                $content .= "<br><br><b>Name:</b> ".$user->name;
                $content .= "<br><b>Username:</b> ".$user->username;
                $content .= "<br><b>Last Login:</b> ".$user->last_login;
                $content .= "<br><b>Job Title:</b> ".$user->job_title;
                $content .= "<br><b>Department:</b> ".$user->departmentName;
                $content .= "<br><br>Please do needful";
                foreach($members as $m){
                    $email = $this->db->select("email")->from("users")->where("id",$m)->get()->row("email");
                    $this->email_model2->save($email,"Forgot Password Request",$content);
                }
                return true;
            }else{
                $user->newPassword = genPassword();
                $this->db->set('password',md5($user->newPassword),true);
                $this->db->where(array('username'=>$username,'status'=>1));
                $this->db->update('users');
                $this->load->model("email_model2");
                $content = "Dear " . $user->name . "<br>Your password has been reset to " . $user->newPassword;
                $this->email_model2->save($user->email,"Forgot Password Request",$content);
                return true;
            }
            
        }else{
            return false;
        }
    }

    private function recordLogin($user)
	{
        if(!$this->db->table_exists("login_history")) return;
		$this->load->library("user_agent");
		$this->db->set("username",$user->username);
		$this->db->set("datetime",date('Y-m-d H:i:s'));
		$this->db->set("result",($user->id==0)?"FAILED":"SUCCESS");
		$this->db->set("ip",$this->input->ip_address());
		$this->db->set("result_other");
		$this->db->set("os",$this->agent->platform());
		$this->db->set("browser",$this->agent->browser().' '.$this->agent->version());
		$this->db->set("result_other",$this->agent->agent_string());
		$this->db->set("user_id",$user->id);
        // $this->db->set("department_id",$this->input->post('inputDepartment'));
		$this->db->insert("login_history");
	}

    public function get_login_history($records=10)
	{
        if(!$this->db->table_exists("login_history")) return;
		$this->db->select("l.*,u.name,u.email,u.user_level,d.name store");
		$this->db->from("login_history l");
		$this->db->join("users u","u.id = l.user_id","left");
        $this->db->join("departments d","d.id=l.department_id","left");
		$this->db->limit($records);
		$this->db->order_by("l.datetime","desc");
		return $this->db->get()->result();
	}

    public function getUserDepartment()
    {
        $username = $this->input->post("username");
        if(empty($username)) {
            return array(
                "result" => false,
                "reason" => "No username entered"
            );
        }
        $department_id = $this->db->select("department_id")->from("users")->where("username",$username)->get()->row("department_id");
        return array(
            "result"        => true,
            "department_id" => $department_id
        );
    }

    public function isUserPermanent()
    {
        $username = $this->input->post("username");
        if(empty($username)) {
            return array(
                "result" => false,
                "reason" => "No username entered"
            );
        }
        $permanent = $this->db->select("permanent")->from("users")->where("username",$username)->get()->row("permanent");
        return array(
            "result"        => true,
            "permanent" => ($permanent==null)?0:$permanent
        );
    }

    public function lookup($user_type="")
    {
        return $this->db->select("id,uuid,name,email,user_type,photo")
                        ->from("users")
                        ->order_by('email')
                        ->where(array("status"=>"1", 'user_type'=>'developer'))
                        ->get()->result();
    }

}