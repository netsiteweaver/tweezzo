<?php

class Customersportal_model extends CI_Model
{
    var $data;

    public function authenticate($user_info) {
        $this->db->select("c.customer_id, c.company_name, ca.name, ca.email, ca.id customer_access_id");
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

    public function getProjects($customer_access_id)
    {
        //get master customer id
        $customer_id = $this->db->select()->from("customer_access")->where("id",$customer_access_id)->get()->row()->customer_id;
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
        //get master customer id
        $customer_id = $this->db->select()->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->customer_id;
        $this->db->select("s.*,u.name createdBy, p.name project_name, count(t.id) tasks_count")
                        ->from("sprints s")
                        ->join("projects p","p.id=s.project_id")
                        ->join("customers c","c.customer_id=p.customer_id")
                        ->join("tasks t","t.sprint_id=s.id","left")
                        ->group_by("s.id")                        
                        ->join("users u","u.id=s.created_by","");

        $this->db->where(["s.status"=>'1']);
        if(empty($project_id)){
            $this->db->where(["c.customer_id"=>$customer_id]);
        }else{
            $this->db->where(["s.project_id"=>$project_id]);
        }

        $this->db->order_by("s.name");
        
        return $this->db->get()->result();
    }

    public function getTasks($sprint_id,$sort_by="task_number",$sort_dir="asc",$stages,$notes_only="")
    {
        //get master customer id
        $customer_id = $this->db->select()->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->customer_id;
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
            $this->db->where(["c.customer_id"=>$customer_id]);
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
        //get master customer id
        $customer_id = $this->db->select()->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->customer_id;
        $task = $this->db->select("t.*,u.name createdBy")
                            ->from("tasks t")
                            ->join("users u","u.id=t.created_by","")
                            ->where(["t.status"=>'1',"t.uuid"=>$uuid])
                            ->order_by("t.task_number")
                            ->get()
                            ->row();
        if(empty($task)) {
            return false;
        }
        $task->notes = $this->db->select("t.*, u.name developer, ca.name as customer, COALESCE(u.name, ca.name) as author")
                                ->from("task_notes t")
                                ->join("users u","u.id=t.created_by","left")
                                ->join("customer_access ca","ca.id=t.created_by_customer","left")
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
        //get master customer id
        // $customer_id = $this->db->select()->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->customer_id;
        $this->db->set("task_id",$task_id);
        $this->db->set("notes",$note);
        $this->db->set("created_by_customer",$_SESSION['customer_access_id']);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("display_type",'public');
        $this->db->set("status",'1');
        $this->db->insert("task_notes");

        $this->load->model("Tasks_model");

        //get task details by id
        $taskUuid = $this->db->select("uuid")->from("tasks")->where("id",$task_id)->get()->row()->uuid;
        $taskDetails = $this->Tasks_model->fetchSingle($taskUuid);

        // get customer email
        $author = $this->db->select("email, name")->from('customer_access')->where('id',$_SESSION['customer_access_id'])->get()->row()->email;

        $this->Tasks_model->notifyUsers($taskDetails, ['task_id'=>$task_id, 'notes'=>$note], $author);

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
            $this->db->query("SET @current_user_id = " . (int) $_SESSION['customer_access_id']);
            $this->db->query("SET @current_user_type = 'customer'");
            $this->db->query("SET @@session.time_zone = '+04:00'");

            $this->db->set("stage","validated");

            $this->db->set("validated_on","NOW()",false);
            $this->db->set("validated_by",$_SESSION['customer_access_id']);

            $this->db->where("id",$task_id);
            $this->db->update("tasks");
            return true;
        }
    }

    public function rejectTask($task_id,$reject_reason)
    {
        $stage = $this->db->select("stage")->from("tasks")->where("id",$task_id)->get()->row()->stage;
        if( in_array($stage, ['completed','validated']) ){
            return false;
        }elseif($stage == 'staging'){
            $this->db->query("SET @current_user_email = '{$_SESSION['customer_email']}'");
            $this->db->query("SET @current_user_ip = '{$_SERVER['REMOTE_ADDR']}'");
            $this->db->query("SET @current_user_agent = '{$_SERVER['HTTP_USER_AGENT']}'");
            $this->db->query("SET @current_user_id = " . (int) $_SESSION['customer_access_id']);
            $this->db->query("SET @current_user_type = 'customer'");
            $this->db->query("SET @@session.time_zone = '+04:00'");

            $this->db->set("stage","on_hold");

            $this->db->set("rejected_on","NOW()",false);
            $this->db->set("rejected_by",$_SESSION['customer_access_id']);
            $this->db->set("rejected_reason",$reject_reason);

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

    public function submitTask()
    {
        $valid = true;
        $errorMessage = "";
        $name = $this->input->post("name");
        $section = $this->input->post("section");
        $description = $this->input->post("description");
        $scope_when_done = $this->input->post("scope_when_done");
        $scope_not_included = $this->input->post("scope_not_included"); 
        $scope_client_expectation = $this->input->post("scope_client_expectation");

        if(empty($name) || empty($section) || empty($description) ) {
            $errorMessage .= "Please fill all the required fields (name, section, description).<br>";
            $valid = false;
        }

        if(!$valid) {
            return [
                "result"    =>  false,
                "reason"   =>  $errorMessage
            ];
        }

        $this->db->set("uuid",gen_uuid());
        $this->db->set("created_by_customer",$_SESSION['customer_access_id']);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("name",$name);
        $this->db->set("section",$section);
        $this->db->set("description",$description);
        $this->db->set("scope_client_expectation",$scope_client_expectation);
        $this->db->set("scope_not_included",$scope_not_included);
        $this->db->set("scope_when_done",$scope_when_done);
        $this->db->set("stage","new");
        $this->db->set("status","1");
        $this->db->insert("submitted_tasks");
        $insert_id = $this->db->insert_id();
        if($insert_id) {
            $this->emailForTaskCreated($insert_id);
            return [
                "result"    =>  true
            ];
        }else{
            return [
                "result"    =>  false,
                "reason"   =>  "Unable to create task. Please try again."
            ];
        }
    }

    private function emailForTaskCreated($task_id)
    {
        $this->load->model("Email_model3");
        $this->load->model("System_model");
        $submitted_task = $this->db->query("SELECT st.*, ca.name customerName, ca.email customerEmail
                            FROM submitted_tasks st
                            JOIN customer_access ca on ca.customer_id = st.created_by_customer
                            WHERE st.id = $task_id")->row();
        $emailData = [
            'title'     =>  'Task Submitted',
            'task'      =>  $submitted_task,
            'logo'      =>  $this->System_model->getParam("logo"),
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/taskSubmittedCustomer",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $subject = "{$_SESSION['customer_name']} Submitted a Task";
        $this->Email_model3->save($_SESSION['customer_email'],$subject,$content);

        // notify admins for task created
        $members = $this->System_model->getParam("notification_create_tasks",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            $this->Email_model3->save($user->email,$subject,$content);
        }
    }

    public function createUserAccess($name,$email,$password)
    {
        $customer = $this->db->select("customer_id, email, name")->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row();

        $existing_users = $this->db->select("count(id) as ct")->from("customer_access")->where("customer_id",$customer->customer_id)->get()->row()->ct;

        if($existing_users >= 5){
            return [
                "result"    =>  false,
                "reason"    =>  'Exceeded quota. Maximum 5 users allowed'
            ];
        }
        $this->db->set("name",$name);
        $this->db->set("email",$email);
        $this->db->set("password",md5($password),true);
        $this->db->set("created_by_customer",$_SESSION['customer_access_id']);
        $this->db->set("customer_id",$customer->customer_id);
        $this->db->set("created_on",'NOW()',true);
        $this->db->set("created_by_type","customer");
        $this->db->set("admin","0");
        $this->db->insert("customer_access");

        $this->emailForUserCreated($name,$email,$customer);

        //return existing customer access for customer
        $users = $this->db->query("SELECT *
                        FROM customer_access
                        WHERE customer_id = $customer->customer_id")->result();
        return [
            'result'    =>  true,
            'users'     =>  $users
        ];
    }

    private function emailForUserCreated($name,$email,$customer)
    {
        $this->load->model("Email_model3");
        $this->load->model("System_model");
        // $submitted_task = $this->db->query("SELECT st.*, ca.name customerName, ca.email customerEmail
        //                     FROM submitted_tasks st
        //                     JOIN customer_access ca on ca.customer_id = st.created_by_customer
        //                     WHERE st.id = $task_id")->row();
        $emailData = [
            'user_created'  =>  ["name"=>$name,"email"=>$email],
            'customer'      =>  $customer,
            'logo'          =>  $this->System_model->getParam("logo"),
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/userAdded",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $subject = "{$customer->name} Added a User";
        $this->Email_model3->save($customer->email,$subject,$content);

        // notify admins for task created
        $members = $this->System_model->getParam("notification_create_users",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            $this->Email_model3->save($user->email,$subject,$content);
        }
    }
}