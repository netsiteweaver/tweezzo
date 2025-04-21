<?php

class Customersportal_model extends CI_Model
{
    var $data;

    public function authenticate($user_info) {
        $this->db->select("c.customer_id, c.company_name, c.full_name, c.email");
        $this->db->from("customers c");
        $this->db->join("customer_access ca","ca.customer_id = c.customer_id");
        $this->db->where("ca.password", md5($user_info['password']), true );
        $this->db->where("ca.email", trim($user_info['email']));
        $this->db->where("c.status", '1');
        $result = $this->db->get()->row();
        $this->recordSignIn($result, trim($user_info['email']));
        return $result;
    }

    private function recordSignIn($customer,$email)
	{
		$this->load->library("user_agent");

        if(!empty($customer)){
            $this->db->set('last_login',date('Y-m-d H:i:s'));
            $this->db->where('customer_id',$customer->customer_id);
            $this->db->update('customers');
        }

		$this->db->set("email",$email);
		$this->db->set("datetime",date('Y-m-d H:i:s'));
		$this->db->set("result",(empty($customer))?"FAILED":"SUCCESS");
		$this->db->set("ip",$this->input->ip_address());
		$this->db->set("os",$this->agent->platform());
		$this->db->set("type",'customer');
		$this->db->set("browser",$this->agent->browser().' '.$this->agent->version());
		$this->db->set("result_other",$this->agent->agent_string());
		$this->db->insert("portal_login_history");
	}

    public function getProjects($customer_id)
    {
        return $this->db->select("p.*,u.name createdBy, count(s.id) sprints_count")
                        ->from("projects p")
                        ->join("users u","u.id=p.created_by","")
                        ->join("sprints s","s.project_id=p.id","left")
                        ->where(["p.status"=>'1',"p.customer_id"=>$customer_id])
                        ->group_by("p.id")
                        ->order_by("p.name")
                        ->get()
                        ->result();
    }

    public function getSprints($project_id="")
    {
        $this->db->select("s.*,u.name createdBy, p.name project_name, count(t.id) tasks_count")
                        ->from("sprints s")
                        ->join("projects p","p.id=s.project_id")
                        ->join("customers c","c.customer_id=p.customer_id")
                        ->join("tasks t","t.sprint_id=s.id","left")
                        ->group_by("s.id")                        
                        ->join("users u","u.id=s.created_by","");

        $this->db->where(["s.status"=>'1']);
        if(empty($project_id)){
            $this->db->where(["c.customer_id"=>$_SESSION['customer_id']]);
        }else{
            $this->db->where(["s.project_id"=>$project_id]);
        }

        $this->db->order_by("s.name");
        
        return $this->db->get()->result();
    }

    public function getTasks($sprint_id,$sort_by="task_number",$sort_dir="asc",$stages,$notes_only="")
    {
        $this->db->select("t.*,u.name createdBy, p.name project_name, s.name sprint_name, count(tn.id) notes_count")
                        ->from("tasks t")
                        ->join("sprints s","s.id=t.sprint_id")
                        ->join("projects p","p.id=s.project_id")
                        ->join("customers c","c.customer_id=p.customer_id")
                        ->join("task_notes tn","tn.task_id=t.id","left")
                        ->join("users u","u.id=t.created_by","")
                        ->group_by("t.id");
        if($notes_only=="without") {
            $this->db->having("notes_count = 0");
        }elseif($notes_only=="with") {
            $this->db->having("notes_count > 0");
        }
        $this->db->where(["t.status"=>'1']);

        $stagesArr = (empty($this->input->get('stages'))) ? [] : explode(',',$stages);

        if(!empty($stagesArr)){
            $this->db->where_in('t.stage',$stagesArr);
        }

        if(empty($sprint_id)){
            $this->db->where(["c.customer_id"=>$_SESSION['customer_id']]);
        }else{
            $this->db->where(["t.sprint_id"=>$sprint_id]);
        }
        if(empty($sort_by)) {
            $this->db->order_by("task_number");
        }else{
            $this->db->order_by($sort_by,$sort_dir);
        }
        
        
        return $this->db->get()->result();                        
    }

    public function getTask($uuid)
    {
        $task = $this->db->select("t.*,u.name createdBy")
                            ->from("tasks t")
                            ->join("users u","u.id=t.created_by","")
                            ->where(["t.status"=>'1',"t.uuid"=>$uuid])
                            ->order_by("t.task_number")
                            ->get()
                            ->row();
        $task->notes = $this->db->select("t.*, u.name developer, c.company_name customer")
                                ->from("task_notes t")
                                ->join("users u","u.id=t.created_by","left")
                                ->join("customers c","c.customer_id=t.created_by_customer","left")
                                ->where("t.task_id",$task->id)
                                ->where("t.display_type","public")
                                ->order_by("created_on","desc")
                                ->get()
                                ->result();
        $task->stage_history = $this->db->select('sh.*,u.name')
                                ->from('stage_change_history sh')
                                ->join('users u','u.id=sh.created_by','left')
                                ->where('sh.task_id',$task->id)
                                ->order_by('sh.created_on','desc')
                                ->get()->result();                                
        $task->files = $this->db->select('ti.*')
                                ->from('task_images ti')
                                ->where('ti.task_id',$task->id)
                                ->get()->result();                                     
        return $task;
    }

    public function saveNote($task_id, $note)
    {
        $this->db->set("task_id",$task_id);
        $this->db->set("notes",$note);
        $this->db->set("created_by_customer",$_SESSION['customer_id']);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("display_type",'public');
        $this->db->set("status",'1');
        $this->db->insert("task_notes");

        $this->load->model("Tasks_model");

        //get task details by id
        $taskUuid = $this->db->select("uuid")->from("tasks")->where("id",$task_id)->get()->row()->uuid;
        $taskDetails = $this->Tasks_model->fetchSingle($taskUuid);

        // get customer email
        $userEmail = $this->db->select("email")->from('customers')->where('customer_id',$_SESSION['customer_id'])->get()->row()->email;

        $this->Tasks_model->notifyUsers($taskDetails, ['task_id'=>$task_id, 'notes'=>$note], $userEmail);

        return $this->db->affected_rows();
    }

    public function validateTask($task_id)
    {
        $stage = $this->db->select("stage")->from("tasks")->where("id",$task_id)->get()->row()->stage;
        if( in_array($stage, ['completed','validated']) ){
            return false;
        }elseif($stage == 'staging'){
            $this->db->query("SET @current_user_email = '{$_SESSION['customer_email']}'");
            $this->db->query("SET @current_user_ip = '{$_SERVER['REMOTE_ADDR']}'");
            $this->db->query("SET @current_user_agent = '{$_SERVER['HTTP_USER_AGENT']}'");
            $this->db->query("SET @current_user_id = " . (int) $_SESSION['customer_id']);
            $this->db->query("SET @current_user_type = 'customer'");
            $this->db->query("SET @@session.time_zone = '+04:00'");

            $this->db->set("stage","validated");
            $this->db->where("id",$task_id);
            $this->db->update("tasks");
            return true;
        }
    }

    public function forgotPassword($email)
    {
        $check = $this->db->select("*")->from("customers")->where(["email"=>$email,"status"=>1])->get()->row();
        if(empty($check)) {
            return false;
        }else{
            $token = randomName(32);
            $this->db->set("token",$token)->where("email",$email)->update("customers");
            $output = new stdClass;
            $output->result = true;
            $output->token = $token;

            $this->sendEmail($check->email,$token);
            return $output;
        }
    }

    private function sendEmail($recipient, $token)
    {
        $this->load->model("Email_model3");
        $this->load->model("system_model");
        $emailData = [
            'email'     =>  $recipient,
            'token'     =>  $token,
            'logo'      =>  $this->system_model->getParam("logo"),
            'resetLink' =>  'portal/customers/processForgotPassword/'
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/forgotPassword",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        // $content = $this->load->view("_email/forgotPassword",$emailData, true);
        $this->Email_model3->save($recipient,"Forgot Password Request",$content);
    }

    public function processForgotPassword($token, $email)
    {
        $password = genPassword(12);
        $this->db->set("password", "md5('$password')", false);
        $this->db->where(["email"=>urldecode($email),"token"=>$token]);
        $this->db->update("customers");
        if ($this->db->affected_rows() == 1) {
            $this->db->set("token", '');
            $this->db->where(["email"=>urldecode($email),"token"=>$token]);
            $this->db->update("customers");
            $this->sendConfirmationEmail(urldecode($email), $password);
        }
    }

    private function sendConfirmationEmail($recipient, $password)
    {
        $this->load->model("Email_model3");
        $this->load->model("system_model");
        $emailData = [
            'email'     =>  $recipient,
            'password'  =>  $password,
            'logo'      =>  $this->system_model->getParam("logo"),
            'signinUrl' =>  'portal/customers/signin/'
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/forgotPasswordConfirmation",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        // $content = $this->load->view("_email/",$emailData, true);
        $this->Email_model3->save($recipient,"Forgot Password Complete",$content);
    }

    public function get_login_history($records=10)
	{
        if(!$this->db->table_exists("portal_login_history")) return;
		$this->db->from("portal_login_history");
		$this->db->limit($records);
		$this->db->order_by("datetime","desc");
        $this->db->where("type","customer");
        return $this->db->get()->result();
	}

}