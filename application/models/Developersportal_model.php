<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Developersportal_model extends CI_Model{

    public function getMyTasks($developer_id, $customer_id="",$project_id="",$sprint_id="",$stage="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10,$notes_only="")
    {
        if( (empty($page)) || ($page <= 0) ) $page =1;
        $offset = ( ($page-1)*$rows_per_page);  

        $query = "SELECT t.id, t.uuid, t.name task_name, t.stage task_stage, t.task_number, t.section, t.description task_description, t.due_date, t.estimated_hours, s.name sprint_name, p.name project_name, c.company_name, count(tn.id) notes_count 
                    FROM task_user tu
                    LEFT JOIN tasks t ON t.id = tu.task_id
                    LEFT JOIN sprints s ON s.id = t.sprint_id
                    LEFT JOIN projects p ON p.id = s.project_id
                    LEFT JOIN customers c ON c.customer_id = p.customer_id
                    LEFT JOIN task_notes tn ON tn.task_id = t.id
                    WHERE tu.user_id = {$developer_id} 
                    AND t.status = '1'";
        if(!empty($customer_id)) $query .= " AND c.customer_id = '{$customer_id}'";
        if(!empty($project_id)) $query .= " AND p.id = '{$project_id}'";
        if(!empty($sprint_id)) $query .= " AND s.id = '{$sprint_id}'";
        if(!empty($stage)) $query .= " AND t.stage = '{$stage}'";
        $query .= " GROUP BY t.id";
        if($notes_only=="without") {
            $query .= " HAVING notes_count = 0";
            // $this->db->having("notes_count = 0");
        }elseif($notes_only=="with") {
            $query .= " HAVING notes_count > 0";
            // $this->db->having("notes_count > 0");
        }
        if(!empty($order_by)){
            $query .= " ORDER BY {$order_by} {$order_dir}";
        } else {
            $query .= " ORDER BY t.task_number";
        }
// echo $query;die;
        return $this->db->query($query)->result();

    }

    public function getSingleTask($uuid)
    {
        $query = "SELECT t.*, 
                    s.name sprint_name, 
                    p.name project_name, 
                    c.company_name
                    FROM tasks t
                    LEFT JOIN sprints s ON s.id = t.sprint_id
                    LEFT JOIN projects p ON p.id = s.project_id
                    LEFT JOIN customers c ON c.customer_id = p.customer_id
                    WHERE t.uuid = '$uuid'";
        $task = $this->db->query($query)->row();
        $task->assigned_to = $this->db->select("u.name,u.email,u.user_type, u.photo")
                                    ->from("task_user tu")
                                    ->join("users u","u.id=tu.user_id","left")
                                    ->where("tu.task_id",$task->id)
                                    ->get()->result();
        $task->notes = $this->db->select("n.id, n.notes,n.created_on,n.created_by,u.name developer, c.company_name customer")
                                ->from("task_notes n")
                                ->join("users u","u.id=n.created_by","left")
                                ->join("customers c","c.customer_id=n.created_by_customer","left")
                                ->where("n.task_id",$task->id)
                                ->where("n.status",'1')
                                ->order_by("created_on","desc")
                                ->get()->result();
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

    public function getMyProjects($developer_id)
    {
        $query = "SELECT 
                        DISTINCT (p.name), p.id, c.customer_id, c.company_name 
                    FROM
                        task_user tu
                    LEFT JOIN tasks t ON
                        t.id = tu.task_id
                    LEFT JOIN sprints s ON
                        s.id = t.sprint_id
                    LEFT JOIN projects p ON
                        p.id = s.project_id
                    LEFT JOIN customers c ON
                        c.customer_id = p.customer_id
                    WHERE
                        tu.user_id ={$developer_id}
                    ORDER BY p.name";
        return $this->db->query($query)->result();

    }

    public function getMyCustomers($developer_id)
    {
        $query = "SELECT 
                        DISTINCT (c.company_name), c.customer_id
                    FROM
                        task_user tu
                    LEFT JOIN tasks t ON
                        t.id = tu.task_id
                    LEFT JOIN sprints s ON
                        s.id = t.sprint_id
                    LEFT JOIN projects p ON
                        p.id = s.project_id
                    LEFT JOIN customers c ON
                        c.customer_id = p.customer_id
                    WHERE
                        tu.user_id ={$developer_id}
                    AND 
                        c.status = 1
                    ORDER BY c.company_name";
        return $this->db->query($query)->result();

    }

    public function getMySprints($developer_id)
    {
        $query = "SELECT 
                        DISTINCT (s.name), s.id, p.id project_id, p.name project_name, c.customer_id, c.company_name
                    FROM
                        task_user tu
                    LEFT JOIN tasks t ON
                        t.id = tu.task_id
                    LEFT JOIN sprints s ON
                        s.id = t.sprint_id
                    LEFT JOIN projects p ON
                        p.id = s.project_id
                    LEFT JOIN customers c ON
                        c.customer_id = p.customer_id
                    WHERE
                        tu.user_id ={$developer_id}
                    ORDER BY s.name";
        return $this->db->query($query)->result();

    }

    public function saveNotes($task_id,$notes, $public)
    {
        $this->db->set("task_id",$task_id);
        $this->db->set("status","1");
        $this->db->set("notes",$notes);
        $this->db->set("created_by",$_SESSION['developer_id']);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("display_type",$public);
        $this->db->insert("task_notes");

        $this->load->model("Tasks_model");

        //get task details by id
        $taskUuid = $this->db->select("uuid")->from("tasks")->where("id",$task_id)->get()->row()->uuid;
        $taskDetails = $this->Tasks_model->fetchSingle($taskUuid);

        // get developer email
        $userEmail = $this->db->select("email")->from('users')->where(array(
            'id'    => $_SESSION['developer_id'],
            'user_type'  => 'developer'
        ))->get()->row()->email;

        
        $this->Tasks_model->notifyUsers($taskDetails, ['task_id'=>$task_id, 'notes'=>$notes], $userEmail, $public);
    }

    public function authenticate($user_info) {
        $this->db->select("u.id, u.name, u.photo, u.user_level, u.user_type, u.email, u.job_title, u.status");
        $this->db->from("users u");
        $this->db->where("u.password", md5($user_info['password']), true );
        $this->db->where("u.email", trim($user_info['email']));
        $this->db->where("u.user_type", 'developer');
        $this->db->where("u.status", '1');
        $result = $this->db->get()->row();
        $this->recordSignIn($result, trim($user_info['email']));
        return $result;
    }

    private function recordSignIn($user,$email)
	{
		$this->load->library("user_agent");

        if(!empty($user)){
            $this->db->set('last_login',date('Y-m-d H:i:s'));
            $this->db->where('id',$user->id);
            $this->db->update('users');
        }

		$this->db->set("email",$email);
		$this->db->set("datetime",date('Y-m-d H:i:s'));
		$this->db->set("result",(empty($user))?"FAILED":"SUCCESS");
		$this->db->set("ip",$this->input->ip_address());
		$this->db->set("os",$this->agent->platform());
		$this->db->set("type",'developer');
		$this->db->set("browser",$this->agent->browser().' '.$this->agent->version());
		$this->db->set("result_other",$this->agent->agent_string());
		$this->db->insert("portal_login_history");
	}

    public function forgotPassword($email)
    {
        $check = $this->db->select("*")->from("users")->where(["email"=>$email,"status"=>1])->get()->row();
        if(empty($check)) {
            return false;
        }else{
            $token = randomName(32);
            $this->db->set("token",$token)->where("email",$email)->update("users");
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
            'resetLink' =>  'portal/developers/processForgotPassword/'
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
        $this->db->update("users");
        if ($this->db->affected_rows() == 1) {
            $this->db->set("token", '');
            $this->db->where(["email"=>urldecode($email),"token"=>$token]);
            $this->db->update("users");
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
            'signinUrl' =>  'portal/developers/signin/'
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/forgotPasswordConfirmation",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        // $content = $this->load->view("_email/forgotPasswordConfirmation",$emailData, true);
        $this->Email_model3->save($recipient,"Forgot Password Complete",$content);
    }

    public function moveStage($task_id,$stage)
    {
        $this->db->query("SET @current_user_email = '{$_SESSION['developer_email']}'");
        $this->db->query("SET @current_user_ip = '{$_SERVER['REMOTE_ADDR']}'");
        $this->db->query("SET @current_user_agent = '{$_SERVER['HTTP_USER_AGENT']}'");
        $this->db->query("SET @current_user_id = " . (int) $_SESSION['developer_id']);
        $this->db->query("SET @current_user_type = 'developer'");
        $this->db->query("SET @@session.time_zone = '+04:00'");

        $this->db->set("stage",$stage)->where("id",$task_id)->update("tasks");

        $result = $this->db->select("c.customer_id,c.company_name, c.email customer_email, 
                                    s.id sprint_id, s.name sprint_name, 
                                    p.id project_id, p.name project_name,
                                    t.uuid task_uuid, t.name task_name, t.description task_description, t.section task_section, t.task_number, t.stage task_stage")
                        ->from("tasks t")
                        ->join("sprints s","s.id = t.sprint_id","left")
                        ->join("projects p","p.id = s.project_id","left")
                        ->join("customers c","c.customer_id = p.customer_id")
                        ->where("t.id",$task_id)
                        ->get()
                        ->row();

        if(!empty($result->customer_email)) {
            $email = "reeaz@ramoly.info";//$result->customer_email;
            $this->load->model("Email_model3");
            $this->load->model("system_model");
            $emailData = [
                'task'      =>  $result,
                'logo'      =>  $this->system_model->getParam("logo"),
                'url'       =>  'portal/customers/view?uuid='.$result->task_uuid
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/taskStageChange",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($email,"A Task has changed stage",$content);
        }
    }

    public function get_login_history($records=10)
	{
        if(!$this->db->table_exists("portal_login_history")) return;
		$this->db->from("portal_login_history");
		$this->db->limit($records);
		$this->db->order_by("datetime","desc");
        $this->db->where("type","developer");
		return $this->db->get()->result();
	}

}