<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Developersportal_model extends CI_Model{

    public function getMyTasks($developer_id, $customer_id="",$project_id="",$sprint_id="",$stageJson="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10,$notes_only="",$due_in_days="")
    {
        if( (empty($page)) || ($page <= 0) ) $page =1;
        $offset = ( ($page-1)*$rows_per_page);  
        $query = "WITH latest_timesheets AS (
                    SELECT
                        *,
                        ROW_NUMBER() OVER (PARTITION BY task_id ORDER BY start_time DESC) AS rn
                    FROM timesheet
                    WHERE status = 1
                ) ";
        $query .= "SELECT t.id
                    , t.uuid
                    , t.name task_name
                    , t.stage task_stage
                    , t.task_number
                    , t.section
                    , t.description task_description
                    , t.due_date, t.estimated_hours
                    , t.work_type, t.billable
                    , t.created_on
                    , u.name created_by_name
                    , s.name sprint_name
                    , s.code sprint_code
                    , p.name project_name
                    , p.code project_code
                    , c.company_name
                    , count(tn.id) notes_count 
                    , ts.id timesheet_id
                    , ts.start_time
                    , ts.finish_time
                    FROM task_user tu
                    LEFT JOIN tasks t ON t.id = tu.task_id
                    LEFT JOIN users u ON u.id = t.created_by
                    LEFT JOIN sprints s ON s.id = t.sprint_id
                    LEFT JOIN projects p ON p.id = s.project_id
                    LEFT JOIN customers c ON c.customer_id = p.customer_id
                    LEFT JOIN task_notes tn ON tn.task_id = t.id
                    LEFT JOIN latest_timesheets ts ON ts.task_id = t.id AND ts.rn = 1
                    WHERE tu.user_id = {$developer_id} 
                    AND t.status = '1' AND t.closed = '0'
                    AND s.status = 1 AND s.active = 1
                    AND p.active = 1
                    AND c.status = 1 AND c.active = 1";
        if(!empty($customer_id)) $query .= " AND c.customer_id = '{$customer_id}'";
        if(!empty($project_id)) $query .= " AND p.id = '{$project_id}'";
        if(!empty($sprint_id)) $query .= " AND s.id = '{$sprint_id}'";
        $stageArray = json_decode($stageJson);
        if(!empty($stageArray)) {
            $escaped = array_map(function($stage) {
                return "'" . addslashes($stage) . "'";
            }, $stageArray);
            $stages = implode(",",$escaped);
            $query .= " AND t.stage IN ({$stages})";
        }
        if(!empty($due_in_days) && is_numeric($due_in_days)) {
            $due_in_days = (int)$due_in_days;
            $query .= " AND t.due_date IS NOT NULL AND t.due_date >= CURDATE() AND t.due_date <= CURDATE() + INTERVAL {$due_in_days} DAY";
        }
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
        $results = $this->db->query($query)->result();
        if (!function_exists('task_ref')) {
            $CI =& get_instance();
            $CI->load->helper('general');
        }
        foreach ($results as $task) {
            $pc = isset($task->project_code) ? $task->project_code : null;
            $sc = isset($task->sprint_code) ? $task->sprint_code : null;
            $tn = isset($task->task_number) ? $task->task_number : '';
            $task->task_ref = $tn !== '' ? task_ref($pc, $sc, $tn) : '';
        }
        return $results;

    }

    /**
     * Search tasks assigned to developer (for portal global search).
     * @param int $developer_id
     * @param string $q search term (task ref, name, section, etc.)
     * @param int $limit
     * @return array
     */
    public function searchTasks($developer_id, $q, $limit = 15)
    {
        $q = trim((string) $q);
        if ($q === '') {
            return [];
        }
        $developer_id = (int) $developer_id;
        $like_val = '%' . $this->db->escape_like_str($q) . '%';
        $this->db->select('t.id, t.uuid, t.name, t.task_number, t.section, p.name project_name, p.code project_code, s.name sprint_name, s.code sprint_code');
        $this->db->from('task_user tu');
        $this->db->join('tasks t', 't.id = tu.task_id');
        $this->db->join('sprints s', 's.id = t.sprint_id');
        $this->db->join('projects p', 'p.id = s.project_id');
        $this->db->join('customers c', 'c.customer_id = p.customer_id');
        $this->db->where('tu.user_id', $developer_id);
        $this->db->where('t.status', '1');
        $this->db->where('t.closed', '0');
        $this->db->where('s.status', 1);
        $this->db->where('s.active', 1);
        $this->db->where('p.active', 1);
        $this->db->where('c.status', 1);
        $this->db->where('c.active', 1);
        $this->db->group_start();
        $this->db->like('t.name', $q);
        $this->db->or_like('t.task_number', $q);
        $this->db->or_like('t.section', $q);
        $this->db->or_where('CONCAT(IFNULL(p.code,\'\'), \'-\', IFNULL(s.code,\'\'), \'-\', IFNULL(t.task_number,\'\')) LIKE ' . $this->db->escape($like_val), null, false);
        $this->db->group_end();
        $this->db->limit($limit);
        $this->db->order_by('t.task_number');
        $results = $this->db->get()->result();
        if (!function_exists('task_ref')) {
            $CI =& get_instance();
            $CI->load->helper('general');
        }
        foreach ($results as $task) {
            $pc = isset($task->project_code) ? $task->project_code : null;
            $sc = isset($task->sprint_code) ? $task->sprint_code : null;
            $tn = isset($task->task_number) ? $task->task_number : '';
            $task->task_ref = $tn !== '' ? task_ref($pc, $sc, $tn) : '';
        }
        return $results;
    }

    public function getSingleTask($uuid)
    {
        $developer_id = isset($_SESSION['developer_id']) ? (int) $_SESSION['developer_id'] : 0;
        if ($developer_id < 1) {
            return false;
        }
        $uuid_esc = $this->db->escape((string) $uuid);
        $query = "SELECT t.*, 
                    s.name sprint_name, 
                    s.code sprint_code,
                    p.name project_name, 
                    p.code project_code,
                    c.company_name
                    FROM tasks t
                    LEFT JOIN sprints s ON s.id = t.sprint_id
                    LEFT JOIN projects p ON p.id = s.project_id
                    LEFT JOIN customers c ON c.customer_id = p.customer_id
                    WHERE t.uuid = $uuid_esc
                    AND t.status = '1'
                    AND t.closed = '0'
                    AND EXISTS (SELECT 1 FROM task_user tu WHERE tu.task_id = t.id AND tu.user_id = " . $developer_id . ")";
        $task = $this->db->query($query)->row();
        if(empty($task)) {
            return false;
        }
        if (!function_exists('task_ref')) {
            $CI =& get_instance();
            $CI->load->helper('general');
        }
        $task->task_ref = task_ref(
            isset($task->project_code) ? $task->project_code : null,
            isset($task->sprint_code) ? $task->sprint_code : null,
            isset($task->task_number) ? $task->task_number : ''
        );
        $task->assigned_to = $this->db->select("u.name,u.email,u.user_type, u.photo")
                                    ->from("task_user tu")
                                    ->join("users u","u.id=tu.user_id","left")
                                    ->where("tu.task_id",$task->id)
                                    ->get()->result();
        $task->notes = $this->db->select("n.id, n.notes,n.created_on,n.created_by,n.out_of_scope,u.name developer, c.company_name customer, u.country_code")
                                ->from("task_notes n")
                                ->join("users u","u.id=n.created_by","left")
                                ->join("customers c","c.customer_id=n.created_by_customer","left")
                                ->where("n.task_id",$task->id)
                                ->where("n.status",'1')
                                ->order_by("created_on","desc")
                                ->get()->result();
        $this->load->model('tasks_model');
        $task->stage_history = $this->tasks_model->get_stage_history_rows($task->id);
        $task->files = $this->db->select('ti.*, u.name AS uploader_user_name, uca.name AS uploader_customer_access_name, c.full_name AS uploader_customer_full, c.company_name AS uploader_customer_company', false)
                                ->from('task_images ti')
                                ->join('users u', 'u.id = ti.created_by', 'left')
                                ->join('customer_access uca', 'uca.id = ti.uploaded_by_customer_access_id', 'left')
                                ->join('customers c', 'c.customer_id = ti.created_by_customer', 'left')
                                ->where('ti.task_id',$task->id)
                                ->order_by('ti.created_on', 'desc')
                                ->get()->result();                                       

        return $task;

    }

    /**
     * Snapshot for developer portal task polling (assigned tasks only).
     *
     * @return array<string,mixed>|false
     */
    public function getDeveloperTaskPollSnapshot($uuid)
    {
        $developer_id = isset($_SESSION['developer_id']) ? (int) $_SESSION['developer_id'] : 0;
        if ($developer_id < 1) {
            return false;
        }
        $uuid_esc = $this->db->escape((string) $uuid);
        $task = $this->db->query(
            "SELECT t.id, t.stage FROM tasks t
            INNER JOIN task_user tu ON tu.task_id = t.id AND tu.user_id = " . $developer_id . "
            WHERE t.uuid = $uuid_esc AND t.status = '1' AND t.closed = '0'"
        )->row();
        if (empty($task)) {
            return false;
        }
        $tid = (int) $task->id;

        $notes_row = $this->db->query(
            "SELECT MD5(IFNULL((SELECT GROUP_CONCAT(id ORDER BY id) FROM task_notes WHERE task_id = ? AND status = 1), '')) AS fp",
            [$tid]
        )->row();
        $files_row = $this->db->query(
            "SELECT MD5(IFNULL((SELECT GROUP_CONCAT(id ORDER BY id) FROM task_images WHERE task_id = ?), '')) AS fp",
            [$tid]
        )->row();
        $hist_row = $this->db->query(
            "SELECT MD5(IFNULL((SELECT GROUP_CONCAT(id ORDER BY id) FROM stage_change_history WHERE task_id = ?), '')) AS fp",
            [$tid]
        )->row();

        return [
            'stage'   => $task->stage,
            'notes'   => $notes_row && isset($notes_row->fp) ? $notes_row->fp : md5(''),
            'files'   => $files_row && isset($files_row->fp) ? $files_row->fp : md5(''),
            'history' => $hist_row && isset($hist_row->fp) ? $hist_row->fp : md5(''),
        ];
    }

    /**
     * Delete a task_images row uploaded by this developer on the portal (assigned task only).
     *
     * @return array{result:bool, reason?:string}
     */
    public function deleteDeveloperTaskImage($task_image_id)
    {
        $task_image_id = (int) $task_image_id;
        $developer_id = isset($_SESSION['developer_id']) ? (int) $_SESSION['developer_id'] : 0;
        if ($task_image_id < 1 || $developer_id < 1) {
            return ['result' => false, 'reason' => 'Invalid request'];
        }

        $row = $this->db->select('ti.id, ti.uploaded_by_user_type, ti.created_by')
            ->from('task_images ti')
            ->join('tasks t', 't.id = ti.task_id')
            ->join('task_user tu', 'tu.task_id = t.id AND tu.user_id = ' . $developer_id, 'inner')
            ->where('ti.id', $task_image_id)
            ->get()->row();

        if (empty($row)) {
            return ['result' => false, 'reason' => 'Attachment not found'];
        }
        if ($row->uploaded_by_user_type !== 'developer' || (int) $row->created_by !== $developer_id) {
            return ['result' => false, 'reason' => 'You can only delete your own uploads'];
        }

        $this->load->model('Files_model');
        $this->Files_model->deleteFile($task_image_id);
        $this->db->where('id', $task_image_id)->delete('task_images');

        return ['result' => true];
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
                    AND
                        p.active = 1
                    AND 
                        s.status = 1 AND s.active = 1
                    AND 
                        t.status = 1 AND t.closed = 0
                    AND 
                        c.status = 1 AND c.active = 1
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
                    AND 
                        c.active = 1
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
                    AND 
                        s.active = 1 AND s.status = 1
                    AND 
                        p.active = 1
                    AND 
                        t.status = 1 AND t.closed = 0
                    AND 
                        c.status = 1 AND c.active = 1
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
        $author = $this->db->select("email, name")->from('users')->where(array(
            'id'    => $_SESSION['developer_id'],
            'user_type'  => 'developer'
        ))->get()->row();

        
        $this->Tasks_model->notifyUsers($taskDetails, ['task_id'=>$task_id, 'notes'=>$notes], $author, $public);
    }

    public function authenticate($user_info) {
        $this->db->select("u.id, u.name, u.photo, u.user_level, u.user_type, u.email, u.job_title, u.status");
        $this->db->from("users u");
        $this->db->where("u.password", md5($user_info['password']), true );
        $this->db->where("u.email", trim($user_info['email']));
        $this->db->where("u.user_type", 'developer');
        $result = $this->db->get()->row();
        
        // Check status and return appropriate response
        if($result) {
            if($result->status == '2') {
                // Account is suspended
                $this->recordSignIn(null, trim($user_info['email']));
                return array(
                    'result' => false,
                    'status' => 'suspended',
                    'message' => 'Your account has been suspended due to inactivity. Please contact the administrator to reactivate your account.'
                );
            } elseif($result->status != '1') {
                // Account is inactive or deleted
                $this->recordSignIn(null, trim($user_info['email']));
                return array(
                    'result' => false,
                    'status' => 'inactive',
                    'message' => 'Your account is currently inactive. Please contact the administrator.'
                );
            }
            // Account is active
            $this->recordSignIn($result, trim($user_info['email']));
            return $result;
        }
        
        // Invalid credentials
        $this->recordSignIn(null, trim($user_info['email']));
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
        // Get current stage before updating
        $current_task = $this->db->select('stage')->from('tasks')->where('id', $task_id)->get()->row();
        $old_stage = $current_task ? $current_task->stage : null;

        $this->load->model('tasks_model');
        $this->tasks_model->set_stage_change_trigger_session_vars(
            'developer',
            (int) $_SESSION['developer_id'],
            isset($_SESSION['developer_email']) ? $_SESSION['developer_email'] : ''
        );

        $this->db->set("stage",$stage);
        
        // Handle completed_date
        if($stage == 'completed') {
            // Set completed_date when stage changes to completed
            $this->db->set('completed_date', date('Y-m-d H:i:s'));
        } elseif($old_stage == 'completed' && $stage != 'completed') {
            // Clear completed_date when changing from completed to another stage
            $this->db->set('completed_date', null);
        }
        
        $this->db->where("id",$task_id)->update("tasks");

        $stageUpdateRows = (int) $this->db->affected_rows();
        if ($current_task && (string) $current_task->stage !== (string) $stage && $stageUpdateRows > 0) {
            $this->tasks_model->record_stage_change_history(
                (int) $task_id,
                $current_task->stage,
                $stage,
                'developer',
                (int) $_SESSION['developer_id'],
                isset($_SESSION['developer_email']) ? $_SESSION['developer_email'] : null
            );
        }

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

        $this->load->model('system_model');

        if(!empty($result->customer_email)) {
            $email = $result->customer_email;
            $this->load->model("Email_model3");
            $emailData = [
                'task'      =>  $result,
                'logo'      =>  $this->system_model->getParam("logo"),
                'url'       =>  'portal/customers/view?task_uuid='.$result->task_uuid,
                'label'     =>  'Open Customer Portal'
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/taskStageChange",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $subject = "* Task/{$result->task_number}/{$result->sprint_name}/{$result->project_name} moved to " . strtoupper(str_replace("_"," ",$result->task_stage));
            $this->Email_model3->save($email,$subject,$content);
        }

        //notification_update_tasks
        $notification_update_tasks = $this->system_model->getParam("notification_update_tasks",true);
        foreach($notification_update_tasks as $id){
            $email = $this->db->select("email")->from("users")->where(array("status"=>"1","id"=>$id))->get()->row()->email;
            if(!empty($email)){
                $this->load->model("Email_model3");
                $this->load->model("system_model");
                $emailData = [
                    'task'      =>  $result,
                    'logo'      =>  $this->system_model->getParam("logo"),
                    'url'       =>  'tasks/view?task_uuid='.$result->task_uuid,
                    'label'     =>  'View Task'
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/taskStageChange",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);
                $subject = "Task/{$result->task_number}/{$result->sprint_name}/{$result->project_name} moved to " . strtoupper(str_replace("_"," ",$result->task_stage));
                $this->Email_model3->save($email,$subject,$content);
            }
            
        }

        if ($stageUpdateRows > 0 && !empty($result) && !empty($result->sprint_id)) {
            $this->load->model("Sprints_model");
            $this->Sprints_model->clearValidationReadinessIfNoStagingTasks((int) $result->sprint_id);
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

    public function submitTask()
    {
        $valid = true;
        $errorMessage = "";
        // $customer_id = $this->input->post("customer_id");
        // $project_id = $this->input->post("project_id");
        // Mandatory fields
        $sprint_id = $this->input->post("sprint_id");
        $section = $this->input->post("section");
        $name = $this->input->post("name");
        $description = $this->input->post("description");
        // Non Mandatory fields
        $task_number = $this->input->post("task_number");
        $due_date = $this->input->post("due_date");
        $scope_when_done = $this->input->post("scope_when_done");
        $scope_not_included = $this->input->post("scope_not_included"); 
        $scope_client_expectation = $this->input->post("scope_client_expectation");

        if(empty($sprint_id) || empty($name) || empty($section) || empty($description) ) {
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
        $this->db->set("created_by",$_SESSION['developer_id']);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("sprint_id",$sprint_id);
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
        $submitted_task = $this->db->query("SELECT st.*, s.name sprintName, p.name projectName, c.company_name customerName, u.name developerName, u.email developerEmail
                            FROM submitted_tasks st
                            JOIN sprints s ON s.id = st.sprint_id
                            JOIN projects p ON p.id = s.project_id
                            JOIN customers c ON c.customer_id = p.customer_id
                            JOIN users u on u.id = st.created_by
                            WHERE st.id = $task_id")->row();
        $emailData = [
            'title'     =>  'Task Submitted',
            'task'      =>  $submitted_task,
            'logo'      =>  $this->System_model->getParam("logo"),
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/taskSubmittedDeveloper",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $subject = "{$_SESSION['developer_name']} Submitted a Task";
        $this->Email_model3->save($_SESSION['developer_email'],$subject,$content);

        // notify admins for task created
        $members = $this->System_model->getParam("notification_create_tasks",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            $this->Email_model3->save($user->email,$subject,$content);
        }
    }

}