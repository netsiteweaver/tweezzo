<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks_model extends CI_Model{

    public function fetchAll($customer_id="",$project_id="",$sprint_id="",$stage=[],$assigned_to="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10,$output="",$notes_only="",$search_text="",$totalRows=false,$work_type="",$billable="")
    {
        if(!$totalRows){
            if( (empty($page)) || ($page <= 0) ) $page =1;
            $offset = ( ($page-1)*$rows_per_page);  

            $this->db->select('t.*,count(tn.id) as notes, c.company_name,c.full_name, c.email, p.name project_name, p.code project_code, s.name sprint_name, s.code sprint_code, u.name created_by_name');
        }else{
            $this->db->select('count(1) as ct');
        }
        
        $this->db->from('tasks t');
        $this->db->join('sprints s','s.id=t.sprint_id','left');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->join('users u','u.id=t.created_by','left');
        if(!$totalRows) $this->db->join('task_notes tn','tn.task_id=t.id','left');
        if(!empty($assigned_to)) {
            $this->db->join('task_user tu','tu.task_id=t.id','left');
            $this->db->where("tu.user_id",$assigned_to);
        }
        if(!$totalRows){
            if($notes_only=="without") {
                $this->db->having("notes = 0");
            }elseif($notes_only=="with") {
                $this->db->having("notes > 0");
            }
        }
        
        $this->db->where(['t.status'=>'1','t.closed'=>'0','s.active'=>'1','p.active'=>'1','c.active'=>'1']);
        if(!empty($customer_id)) $this->db->where('c.customer_id',$customer_id);
        if(!empty($project_id)) $this->db->where('p.id',$project_id);
        if(!empty($sprint_id)) $this->db->where('s.id',$sprint_id);
        if(!empty($stage)) $this->db->where_in('t.stage',$stage);
        if(!empty($search_text)){
            $this->db->group_start();
            $this->db->like("t.name",$search_text);
            $this->db->or_like("t.description",$search_text);
            $this->db->or_like("t.stage",$search_text);
            $this->db->or_like("t.task_number",$search_text);
            $this->db->or_like("t.section",$search_text);
            $this->db->or_like("s.name",$search_text);
            $this->db->or_like("p.name",$search_text);
            $this->db->or_like("c.company_name",$search_text);
            $this->db->or_like("t.stage",$search_text);
            // Match combined task ref (e.g. WR-S3-001) for global search / sharing
            $like_val = '%' . $this->db->escape_like_str($search_text) . '%';
            $this->db->or_where("CONCAT(IFNULL(p.code,''), '-', IFNULL(s.code,''), '-', IFNULL(t.task_number,'')) LIKE " . $this->db->escape($like_val), null, false);
            $this->db->group_end();
        }
        if(!empty($work_type)) $this->db->where('t.work_type',$work_type);
        if($billable !== '' && $billable !== null) $this->db->where('t.billable',$billable);
        // echo $this->db->get_compiled_select();die;
        if(!$totalRows){
            if(!empty($order_by)) {
                $this->db->order_by($order_by,$order_dir);
            }else{
                $this->db->order_by('t.task_number');
            }
            if(empty($output)) {
                $this->db->order_by($order_by,$order_dir);
                $this->db->limit($rows_per_page,$offset);
            }
            $this->db->group_by('t.id');
        }
        if(!$totalRows){
            $tasks = $this->db->get()->result();
            if (!function_exists('task_ref')) {
                $CI =& get_instance();
                $CI->load->helper('general');
            }
            foreach($tasks as $i => $task){
                $tasks[$i]->users = $this->db->select("u.name,u.display_name,u.email,u.user_type,u.photo")
                                    ->from("task_user t")
                                    ->join("users u","u.id=t.user_id")
                                    ->where(["t.task_id"=>$task->id])
                                    ->get()->result();
                $pc = isset($task->project_code) ? $task->project_code : null;
                $sc = isset($task->sprint_code) ? $task->sprint_code : null;
                $tn = isset($task->task_number) ? $task->task_number : '';
                $tasks[$i]->task_ref = $tn !== '' ? task_ref($pc, $sc, $tn) : '';
            }
            return $tasks;
        }else{
            return $this->db->get()->row('ct');
        }
        
    }

    public function totalRows($customer_id="",$project_id="",$sprint_id="",$stage="",$assigned_to="",$order_by="",$order_dir="asc",$notes_only="",$search_text="",$work_type="",$billable="")
    {
        $rows = $this->fetchAll($customer_id, $project_id, $sprint_id, $stage, $assigned_to, $order_by, $order_dir, 1, 10, '', $notes_only, $search_text, true, $work_type, $billable);
        return $rows;

    }

    /**
     * Sum estimated_hours for all tasks matching the same filters as tasks/listing (full result set, not current page).
     * Mirrors fetchAll() listing logic including notes filter (with/without).
     */
    public function sumEstimatedHoursForListing($customer_id = "", $project_id = "", $sprint_id = "", $stage = [], $assigned_to = "", $notes_only = "", $search_text = "", $work_type = "", $billable = "")
    {
        $this->db->select('t.id, COALESCE(t.estimated_hours, 0) AS est_hours, COUNT(tn.id) AS notes', false);
        $this->db->from('tasks t');
        $this->db->join('sprints s', 's.id=t.sprint_id', 'left');
        $this->db->join('projects p', 'p.id=s.project_id', 'left');
        $this->db->join('customers c', 'c.customer_id=p.customer_id', 'left');
        $this->db->join('users u', 'u.id=t.created_by', 'left');
        $this->db->join('task_notes tn', 'tn.task_id=t.id', 'left');
        if (!empty($assigned_to)) {
            $this->db->join('task_user tu', 'tu.task_id=t.id', 'left');
            $this->db->where("tu.user_id", $assigned_to);
        }

        $this->db->where(['t.status' => '1', 't.closed' => '0', 's.active' => '1', 'p.active' => '1', 'c.active' => '1']);
        if (!empty($customer_id)) {
            $this->db->where('c.customer_id', $customer_id);
        }
        if (!empty($project_id)) {
            $this->db->where('p.id', $project_id);
        }
        if (!empty($sprint_id)) {
            $this->db->where('s.id', $sprint_id);
        }
        if (!empty($stage)) {
            $this->db->where_in('t.stage', $stage);
        }
        if (!empty($search_text)) {
            $this->db->group_start();
            $this->db->like("t.name", $search_text);
            $this->db->or_like("t.description", $search_text);
            $this->db->or_like("t.stage", $search_text);
            $this->db->or_like("t.task_number", $search_text);
            $this->db->or_like("t.section", $search_text);
            $this->db->or_like("s.name", $search_text);
            $this->db->or_like("p.name", $search_text);
            $this->db->or_like("c.company_name", $search_text);
            $this->db->or_like("t.stage", $search_text);
            $like_val = '%' . $this->db->escape_like_str($search_text) . '%';
            $this->db->or_where("CONCAT(IFNULL(p.code,''), '-', IFNULL(s.code,''), '-', IFNULL(t.task_number,'')) LIKE " . $this->db->escape($like_val), null, false);
            $this->db->group_end();
        }
        if (!empty($work_type)) {
            $this->db->where('t.work_type', $work_type);
        }
        if ($billable !== '' && $billable !== null) {
            $this->db->where('t.billable', $billable);
        }

        $this->db->group_by('t.id');
        if ($notes_only == "without") {
            $this->db->having("notes = 0");
        } elseif ($notes_only == "with") {
            $this->db->having("notes > 0");
        }

        $inner_sql = $this->db->get_compiled_select();
        $row = $this->db->query(
            "SELECT COALESCE(SUM(est_hours), 0) AS total_estimated_hours FROM (" . $inner_sql . ") AS task_sums"
        )->row();

        return $row ? (float) $row->total_estimated_hours : 0.0;
    }

    public function loadNotes($task_id)
    {
        $this->db->select('tn.*,u.name, c.company_name customer')
                ->from('task_notes tn')
                ->join('users u','u.id=tn.created_by','left')
                ->join('customers c','c.customer_id=tn.created_by_customer','left')
                ->where('tn.task_id',$task_id)
                ->order_by('tn.created_on','desc');
        return $this->db->get()->result();
    }

    public function fetchSingle($uuid){
        $this->db->select('t.*, c.customer_id, c.company_name, c.full_name, p.id project_id, p.name project_name, p.code project_code, s.name sprint_name, s.code sprint_code');
        $this->db->from('tasks t');
        $this->db->join('sprints s','s.id=t.sprint_id','left');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('t.uuid',$uuid);
        $this->db->where(['t.status'=>'1','t.closed'=>'0']);
        $task = $this->db->get()->row();
        if(empty($task)) return [];
        if (!function_exists('task_ref')) {
            get_instance()->load->helper('general');
        }
        $tn = isset($task->task_number) ? $task->task_number : '';
        $task->task_ref = $tn !== '' ? task_ref(isset($task->project_code) ? $task->project_code : null, isset($task->sprint_code) ? $task->sprint_code : null, $tn) : '';
        $task->notes = $this->db->select('tn.id, tn.notes,tn.created_by, tn.created_on,u.name, tn.out_of_scope, c.company_name customer')
                                ->from('task_notes tn')
                                ->join('users u','u.id=tn.created_by','left')
                                ->join('customers c','c.customer_id=tn.created_by_customer','left')
                                ->where('tn.task_id',$task->id)
                                ->order_by('tn.created_on','desc')
                                ->get()->result();
        $t = $this->db->select('GROUP_CONCAT(tu.user_id) as users')
                                        ->from('task_user tu')
                                        ->join('users u','u.id=tu.user_id','left')
                                        ->where('tu.task_id',$task->id)
                                        ->get()->row()->users;
        $task->assigned_users = explode(',',$t);
        //fetch stage history
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

    public function getSingleById($id)
    {
        $uuid = $this->db->select("uuid")->from("tasks")->where("id",$id)->get()->row()->uuid;
        return $this->fetchSingle($uuid);
    }

    public function getByIds($ids)
    {
        $this->db->select('t.*, c.customer_id, c.company_name, c.full_name, p.id project_id, p.name project_name, s.name sprint_name');
        $this->db->from('tasks t');
        $this->db->join('sprints s','s.id=t.sprint_id','left');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where_in('t.id',$ids);
        $this->db->where(['t.status'=>'1','t.closed'=>'0']);
        $this->db->order_by("task_number");
        $tasks = $this->db->get()->result();
        return $tasks;
    }

    public function move_stage($data)
    {
        $this->load->model("System_model");
        $this->load->model("email_model2");
        
        // Get current stage before updating
        $current_task = $this->db->select('stage')->from('tasks')->where('uuid', $data['task_uuid'])->get()->row();
        $old_stage = $current_task ? $current_task->stage : null;
        
        $this->db->query("SET @current_user_email = '{$_SESSION['authenticated_user']->email}'");
        $this->db->query("SET @current_user_ip = '{$_SERVER['REMOTE_ADDR']}'");
        $this->db->query("SET @current_user_agent = '{$_SERVER['HTTP_USER_AGENT']}'");
        $this->db->query("SET @current_user_id = " . (int) $_SESSION['user_id']);
        $this->db->query("SET @current_user_type = 'user'");
        $this->db->query("SET @@session.time_zone = '+04:00'");

        $this->db->set('stage',$data['stage']);
        $this->db->set('progress',$data['progress']);
        
        // Handle completed_date
        if($data['stage'] == 'completed') {
            // Set completed_date when stage changes to completed
            $this->db->set('completed_date', date('Y-m-d H:i:s'));
        } elseif($old_stage == 'completed' && $data['stage'] != 'completed') {
            // Clear completed_date when changing from completed to another stage
            $this->db->set('completed_date', null);
        }
        
        $this->db->where('uuid',$data['task_uuid']);
        $this->db->update('tasks');

        $rows = $this->db->affected_rows();

        if($this->db->affected_rows() == 0)
        {
            return ['result'=>false,'reason'=>'Stage submitted was same as previous'];
        }
        $author = $this->db->select()->from("users")->where("id",$_SESSION['user_id'])->get()->row();
        $task = $this->db->query("SELECT t.task_number, 
                                        t.name task_name, 
                                        t.description task_description, 
                                        t.section task_section, 
                                        t.stage task_stage, 
                                        p.name project_name, 
                                        s.name sprint_name, 
                                        c.company_name customer_name 
                                    FROM tasks t 
                                    JOIN sprints s ON s.id = t.sprint_id
                                    JOIN projects p ON p.id = s.project_id
                                    JOIN customers c ON c.customer_id = p.customer_id
                                    WHERE t.uuid = '{$data['task_uuid']}'")->row();
        $members = $this->System_model->getParam("notification_update_tasks",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            
            // $this->load->model("Sprints_model");
            // $projectInfo = $this->Sprints_model->getProjectInfo($data['sprint_id']);

            $this->load->model("Email_model3");
            $this->load->model("system_model");
            $emailData = [
                // 'title'         =>  'Task Updated',
                // 'projectInfo'   =>  $projectInfo,
                'task'          =>  $task,
                'logo'          =>  $this->system_model->getParam("logo"),
                // 'link'          =>  base_url('tasks/view?task_uuid='.$data['task_uuid']),
                // 'link_label'    =>  'View Task',
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/taskStageChange2",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);

            $subject = "{$author->name} Moved Task {$task->task_number}/{$task->project_name}/{$task->sprint_name} to ".strtoupper(str_replace("_"," ",$task->task_stage));

            $this->Email_model3->save($user->email,$subject,$content);

        }
        return ['result'=>true];

    }

    public function save($data,$uploadedFiles)
    {
        $this->load->model("System_model");
        $this->load->model("email_model2");
        
        $this->db->query("SET @current_user_email = '{$_SESSION['authenticated_user']->email}'");
        $this->db->query("SET @current_user_ip = '{$_SERVER['REMOTE_ADDR']}'");
        $this->db->query("SET @current_user_agent = '{$_SERVER['HTTP_USER_AGENT']}'");
        $this->db->query("SET @current_user_id = " . (int) $_SESSION['user_id']);
        $this->db->query("SET @current_user_type = 'user'");
        $this->db->query("SET @@session.time_zone = '+04:00'");

        $this->db->set('name',$data['name']);
        $this->db->set('description',$data['description']);
        $this->db->set('task_number',$data['task_number']);
        $this->db->set('sprint_id',$data['sprint_id']);
        $this->db->set('section',$data['section']);
        $this->db->set('due_date',!empty($data['due_date']) ? $data['due_date'] : null);
        $this->db->set('estimated_hours',!empty($data['estimated_hours']) ? $data['estimated_hours'] : null);
        $this->db->set('work_type',!empty($data['work_type']) ? $data['work_type'] : null);
        $this->db->set('billable',isset($data['billable']) && $data['billable'] ? 1 : (isset($data['billable']) ? 0 : null));
        $this->db->set('settled', isset($data['settled']) && $data['settled'] ? 1 : (isset($data['settled']) ? 0 : null));
        $this->db->set('settled_on', !empty($data['settled_on']) ? $data['settled_on'] : null);
        $this->db->set('ref', !empty($data['ref']) ? $data['ref'] : null);
        $this->db->set('scope_client_expectation',$data['scope_client_expectation']);
        $this->db->set('scope_not_included',$data['scope_not_included']);
        $this->db->set('scope_when_done',$data['scope_when_done']);

        if(!empty($data['scope_client_expectation'])) $this->db->set('scope_client_expectation',$data['scope_client_expectation']);
        if(!empty($data['scope_not_included'])) $this->db->set('scope_not_included',$data['scope_not_included']);
        if(!empty($data['scope_when_done'])) $this->db->set('scope_when_done',$data['scope_when_done']);

        if(empty($data['uuid'])){
            $uuid = gen_uuid();
            $this->db->set('uuid',$uuid);
            $this->db->set('created_by',$_SESSION['user_id']);
            $this->db->set('created_on',date('Y-m-d H:i:s'));
            $this->db->set('stage',$data['stage']);
            $this->db->set('progress',floatval($data['progress']));
            $this->db->set('status','1');
            // Set completed_date if stage is completed
            if($data['stage'] == 'completed') {
                $this->db->set('completed_date', date('Y-m-d H:i:s'));
            }
            $this->db->insert('tasks');

            $taskId = $this->db->insert_id();
            $this->saveFiles($uploadedFiles,$taskId);

            $members = $this->System_model->getParam("notification_create_tasks",true);
            foreach($members as $m){
                $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();

                $this->load->model("Sprints_model");
                $projectInfo = $this->Sprints_model->getProjectInfo($data['sprint_id']);

                $this->load->model("Email_model3");
                $this->load->model("system_model");
                $emailData = [
                    'title'         =>  'New Task Created',
                    'projectInfo'   =>  $projectInfo,
                    'data'          =>  $data,
                    'logo'          =>  $this->system_model->getParam("logo"),
                    'link'          =>  base_url('tasks/view?task_uuid='.$uuid),
                    'link_label'    =>  'View Task',
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/taskCreatedOrUpdated",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);
                // echo $content;die;
                $this->Email_model3->save($user->email,"New Task Created",$content);
                
            }
            //notify developers of newly created task which has been assigned to them
            $newTask = $this->db->query("SELECT p.id project_id, c.customer_id, s.id sprint_id
                                            FROM tasks t
                                            join sprints s on s.id = t.sprint_id
                                            join projects p on p.id = s.project_id
                                            join customers c on c.customer_id = p.customer_id
                                            where t.id = '$taskId'")->row();
            $this->assignUsers(json_decode($data['userIds']),[$taskId],$newTask->customer_id,$newTask->project_id,$newTask->sprint_id);

        }else{
            $this->db->where('uuid',$data['uuid']);
            $this->db->update('tasks');

            $taskId = $this->db->select("id")->from("tasks")->where("uuid",$data['uuid'])->get()->row()->id;
            $this->saveFiles($uploadedFiles,$taskId);

            $members = $this->System_model->getParam("notification_update_tasks",true);
            foreach($members as $m){
                $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
                
                $this->load->model("Sprints_model");
                $projectInfo = $this->Sprints_model->getProjectInfo($data['sprint_id']);

                $this->load->model("Email_model3");
                $this->load->model("system_model");
                $emailData = [
                    'title'         =>  'Task Updated',
                    'projectInfo'   =>  $projectInfo,
                    'data'          =>  $data,
                    'logo'          =>  $this->system_model->getParam("logo"),
                    'link'          =>  base_url('tasks/view?task_uuid='.$data['uuid']),
                    'link_label'    =>  'View Task',
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/taskCreatedOrUpdated",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);

                $this->Email_model3->save($user->email,"Task Updated",$content);

            }
        }
        return array('result'=>true,'data'=>$data);

    }

    private function saveFiles($uploadedFiles=[],$taskId)
    {
        foreach($uploadedFiles as $uploadedFile){
            $this->db->query("SET @@session.time_zone = '+04:00'");
            $this->db->set('uuid',gen_uuid());
            $this->db->set('task_id',$taskId);
            $this->db->set('created_on','NOW()',false);
            $this->db->set('created_by',$_SESSION['user_id']);
            $this->db->set('file_name',$uploadedFile['file_name']);
            $this->db->set('thumb_name',$uploadedFile['image_thumb']);
            $this->db->set('file_ext',$uploadedFile['file_ext']);
            $this->db->set('file_size',$uploadedFile['file_size']);
            $this->db->set('image_width',$uploadedFile['image_width']);
            $this->db->set('image_height',$uploadedFile['image_height']);
            $this->db->set('image_type',$uploadedFile['image_type']);
            $this->db->insert('task_images');
        }
        
        
        // return array('result'=>true,'data'=>$data);
    }

    public function saveNote($data)
    {
        $this->db->set('task_id',$data['task_id']);
        $this->db->set('notes',$data['notes']);
        $this->db->set('created_by',$_SESSION['user_id']);
        $this->db->set('created_on',date('Y-m-d H:i:s'));
        $this->db->insert('task_notes');

        //get task details by id
        $taskUuid = $this->db->select("uuid")->from("tasks")->where("id",$data['task_id'])->get()->row()->uuid;
        $taskDetails = $this->fetchSingle($taskUuid);

        // get user email
        $userEmail = $this->db->select("email,name")->from('users')->where('id',$_SESSION['user_id'])->get()->row();

        $check = $this->notifyUsers($taskDetails,$data, $userEmail);

        if(!$check['result']) {
            return array('result'=>false,'reason'=>$check['reason']);
        }
        return array('result'=>true,'data'=>$data);
    }

    public function notifyUsers($taskDetails, $data, $author, $public='public')
    {
        $query = "SELECT t.*, s.name sprint_name, p.name project_name, c.email customer_email, c.company_name customer, u.email developer_email, u.name developer_name, c.customer_id
                    FROM tasks t 
                    Left join sprints s on s.id = t.sprint_id 
                    left join projects p on p.id = s.project_id 
                    left join customers c on c.customer_id = p.customer_id 
                    left join task_user tu on tu.task_id = t.id
                    left join users u on u.id = tu.user_id 
                    where t.status = 1
                    and t.closed = 0
                    and t.uuid = '{$taskDetails->uuid}'";
        $result = $this->db->query($query)->result();

        if(!empty($result)){
            $result[0]->customer_access = $this->db->select("id,name,email,admin, country_code")->from("customer_access")->where(array("status"=>"1","customer_id"=>$result[0]->customer_id))->get()->result();
        }

        $this->load->model("Email_model3");
        $this->load->model("system_model");

        $emailData = [
            'addressee'         =>  '',
            'notes'             =>  $data['notes'],
            'logo'              =>  $this->system_model->getParam("logo"),
            'taskDetails'       =>  $taskDetails,
            'author'            =>  $author,
            'show_lifecycle'    =>  false
        ];
        
        $subject = "{$author->name} added a note for Task {$taskDetails->task_number}/{$taskDetails->sprint_name}/{$taskDetails->project_name}";

        //first send to client if notes is public
        if($public == "public"){
            $emailData['addressee'] = 'Customer';
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/noteHasBeenAdded",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            foreach($result[0]->customer_access as $user){
                $check = $this->Email_model3->save($user->email,$subject,$content);
                if($check == '401'){
                    return array('result'=>false,'reason'=>'Mail Server: Not Authorised');
                    exit;
                }
            }
        }

        //then send to developers
        $emailData['addressee'] = 'Developer';
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/noteHasBeenAdded",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        foreach($result as $developer){
            if(empty($developer->developer_email)) continue;
            $check = $this->Email_model3->save($developer->developer_email,$subject,$content);
            if($check == '401'){
                return array('result'=>false,'reason'=>'Mail Server: Not Authorised');
            }
        }

        //then send to admins, if defined
        $emailData['addressee'] = 'Admin';
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/noteHasBeenAdded",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $admins = $this->system_model->getParam("notification_create_notes",true);
        foreach($admins as $admin){
            $user = $this->db->select("*")->from("users")->where("id",$admin)->get()->row();
            $check = $this->Email_model3->save($user->email,$subject,$content);
            if($check == '401'){
                return array('result'=>false,'reason'=>'Mail Server: Not Authorised');
            }
        }

        return array('result'=>true);
    }

    public function delete($uuid)
    {
        $this->db->set("status","0");
        $this->db->where("uuid",$uuid);
        $this->db->update("tasks");
        return $this->db->affected_rows();
    }

    public function deleteMultiple($taskIds)
    {
        $this->db->set("status","0");
        $this->db->where_in("id",$taskIds);
        $this->db->update("tasks");
    }

    public function closeMultiple($taskIds)
    {
        $this->db->set("closed","1");
        $this->db->set("mark_closed_by",$_SESSION['user_id']);
        $this->db->set("mark_closed_on","NOW()",false);
        $this->db->where_in("id",$taskIds);
        $this->db->update("tasks");

        $this->db->select('t.id, t.uuid, t.name, t.section, t.task_number, s.name as sprint_name, p.name as project_name, c.company_name');
        $this->db->from('tasks t');
        $this->db->join('sprints s', 's.id = t.sprint_id');
        $this->db->join('projects p', 'p.id = s.project_id');
        $this->db->join('customers c', 'c.customer_id = p.customer_id');
        $this->db->where_in('t.id', $taskIds);
        $query = $this->db->get();
        $result = $query->result();

        // send email to users 
        $this->load->model("Email_model3");
        $this->load->model("system_model");
        $emailData = [
            'tasks'     =>  $result,
            'logo'      =>  $this->system_model->getParam("logo")
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/tasksClosed",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);

        $notification_delete_tasks = $this->system_model->getParam("notification_delete_tasks",true);
        foreach($notification_delete_tasks as $user_id){
            $user = $this->db->select("email")->from("users")->where(["status"=>"1","id"=>$user_id])->get()->row();
            if(!empty($user)){
                $this->Email_model3->save($user->email,"Tasks Closed",$content);
            }
        }
        
    }

    public function bulkChangeStage($taskIds, $stage)
    {
        $taskids = implode(',',$taskIds);
        
        // Handle completed_date
        if($stage == 'completed') {
            // Set completed_date when stage changes to completed
            $this->db->query("UPDATE tasks SET stage = '$stage', completed_date = NOW() WHERE id IN ($taskids)");
        } else {
            // Clear completed_date when changing from completed to another stage, or set stage normally
            $this->db->query("UPDATE tasks SET stage = '$stage', completed_date = CASE WHEN stage = 'completed' THEN NULL ELSE completed_date END WHERE id IN ($taskids)");
        }
    }
    
    public function bulkChangeSprint($taskIds, $sprintId)
    {
        $taskids = implode(',',$taskIds);
        $this->db->query("UPDATE tasks SET sprint_id = '$sprintId' WHERE id IN ($taskids)");
    }

    public function bulkSetDueDate($taskIds, $dueDate)
    {
        $taskids = implode(',',$taskIds);
        $this->db->query("UPDATE tasks SET due_date = '$dueDate' WHERE id IN ($taskids)");


        //get task details to email developers
        $tasks = $this->db->query("SELECT t.name, t.task_number, s.name sprint_name, p.name project_name, c.company_name FROM tasks t 
                                    JOIN sprints s ON s.id = t.sprint_id
                                    JOIN projects p ON p.id = s.project_id
                                    JOIN customers c ON c.customer_id = p.customer_id
                                    WHERE t.id IN ($taskids)")->result();


        //get developers assigned to tasks
        $developers = $this->db->query("SELECT DISTINCT tu.user_id, u.email, u.name from task_user tu 
                                        join users u on u.id = tu.user_id 
                                        join tasks t on t.id = tu.task_id 
                                        where t.id in ($taskids)")->result();

        $this->load->model("Email_model3");
        $this->load->model("Tasks_model");
        $this->load->model("system_model");

        $emailData = [
            'dueDate'   =>  $dueDate,
            'tasks'      =>  $tasks,
            'logo'      =>  $this->system_model->getParam("logo"),
            'link'      =>  "",
            'link_label'=>  ""
        ];

        foreach($developers as $developer){
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/dueDateSet",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($developer->email,"Tasks Due Date",$content);
        }
        
    }

    public function upload_file($sprint_id)
    {
        $this->load->model("files_model");
        $data = $this->files_model->uploadCSV("file");

        $handle = fopen($data['full_path'], "r");
        $header = fgetcsv($handle);
        $output = [];
        while (($row = fgetcsv($handle)) !== FALSE) {
            $output[] = $row;
        }
        return $output;
    }

    public function process_import($data)
    {
        $sprint = $data['sprintId'];
        
        //get task number
        $task_count = $this->db->select("MAX(task_number) as n")
                ->from("sprints s")
                ->join("tasks t","t.sprint_id = s.id","left")
                ->where("s.id",$data['sprintId'])
                ->where("s.status","1")
                ->get()
                ->row()
                ->n;

        
        if(!empty($task_count)) {
            $task_count = intval(str_replace(".","",$task_count));   
        }else{
            $task_count = 0; 
        }

        foreach($data['tasks'] as $task){
            $taskNumber = incrementTaskNumber($task_count);
            $data = array(
                'uuid'          =>  gen_uuid(), 
                'name'          =>  $task['task_name'],
                'description'   =>  $task['description'],
                'task_number'   =>  $taskNumber,
                'sprint_id'     =>  $sprint,
                'progress'      =>  0,
                'section'       =>  $task['section'],
                'scope_client_expectation'       =>  $task['expected'],
                'scope_not_included'       =>  $task['excluded'],
                'scope_when_done'       =>  $task['completed'],
                'stage'         =>  'new',
                'created_by'    =>  $_SESSION['user_id'],
                'created_on'    =>  date('Y-m-d H:i:s')
            );
            $this->db->insert('tasks',$data);
            $task_count++;
        }

    }

    public function getGeneralProgress()
    {
        $customers = $this->db->select("customer_id, company_name")->from("customers")->where("status","1")->order_by('company_name')->get()->result();

        $query = "SELECT 
                        c.company_name,
                        COUNT(1) AS total_tasks,
                        COUNT(CASE WHEN t.stage = 'completed' THEN 1 END) AS completed_tasks,
                        ROUND(
                            AVG(
                                CASE t.stage
                                    WHEN 'new' THEN 0
                                    WHEN 'in_progress' THEN 20
                                    WHEN 'testing' THEN 40
                                    WHEN 'staging' THEN 60
                                    WHEN 'validated' THEN 80
                                    WHEN 'completed' THEN 100
                                    WHEN 'on_hold' THEN 20
                                    WHEN 'stopped' THEN 0
                                    ELSE 0
                                END
                            ),
                            2
                        ) AS overall_progress_pct
                    FROM 
                        tasks t
                        JOIN sprints s ON s.id = t.sprint_id 
                        JOIN projects p ON p.id = s.project_id 
                        JOIN customers c ON c.customer_id = p.customer_id 
                    WHERE 
                        t.status = 1 AND t.closed = 0 
                    AND c.status = 1 AND c.active = 1 
                    AND p.status = 1 AND p.active = 1 
                    AND s.status = 1 AND s.active = 1 
                    AND s.name != 'Roadmap' 
                    GROUP BY 
                        c.company_name
                    ORDER BY 
                        c.company_name;";
        return $this->db->query($query)->result();

        // $result = [];
        // foreach($customers as $c){
        //     $query = "select 
        //                 c.company_name,
        //                 -- COUNT(CASE WHEN t.stage = 'new' THEN 1 END) AS tasksNew,
        //                 -- COUNT(CASE WHEN t.stage = 'in_progress' THEN 1 END) AS tasksInProgress,
        //                 -- COUNT(CASE WHEN t.stage = 'testing' THEN 1 END) AS tasksTesting,
        //                 -- COUNT(CASE WHEN t.stage = 'staging' THEN 1 END) AS tasksStaging,
        //                 -- COUNT(CASE WHEN t.stage = 'validated' THEN 1 END) AS tasksValidated,
        //                 -- COUNT(CASE WHEN t.stage = 'completed' THEN 1 END) AS tasksCompleted,
        //                 COUNT(1) AS tasksAll,
        //                 ROUND(COUNT(CASE WHEN t.stage = 'new' THEN 1 END) / COUNT(1) * 100) AS pctNew,
        //                 ROUND(COUNT(CASE WHEN t.stage = 'in_progress' THEN 1 END) / COUNT(1) * 100) AS pctInProgress,
        //                 ROUND(COUNT(CASE WHEN t.stage = 'testing' THEN 1 END) / COUNT(1) * 100) AS pctTesting,
        //                 ROUND(COUNT(CASE WHEN t.stage = 'staging' THEN 1 END) / COUNT(1) * 100) AS pctStaging,
        //                 ROUND(COUNT(CASE WHEN t.stage = 'validated' THEN 1 END) / COUNT(1) * 100) AS pctValidated,
        //                 ROUND(COUNT(CASE WHEN t.stage = 'completed' THEN 1 END) / COUNT(1) * 100) AS pctCompleted,
        //                 ROUND(COUNT(CASE WHEN t.stage = 'on_hold' THEN 1 END) / COUNT(1) * 100) AS pctOnHold
        //             from tasks t
        //             join sprints s on s.id = t.sprint_id 
        //             join projects p on p.id = s.project_id 
        //             join customers c on c.customer_id = p.customer_id 
        //             where t.status = 1 
        //             and c.customer_id = '{$c->customer_id}'";
        //     $data = $this->db->query($query)->row();
        //     $idx = $c->company_name;
        //     $result[$idx] = $data;
        // }
        // return $result;
    }

    public function assignUser($userId,$taskId)
    {
        $check = $this->db->select("count(1) as ct")->from("task_user")->where(array('task_id'=>$taskId,'user_id'=>$userId))->get()->row()->ct;

        if($check > 0){
            return false;

        }else{
            $this->db->set("task_id",$taskId);
            $this->db->set("user_id",$userId);
            $this->db->insert("task_user");

            // Verify the insert was successful
            if($this->db->affected_rows() == 0){
                return false;
            }

            // Try to send email, but don't let email failures prevent user assignment
            try {
                $this->load->model("Email_model3");
                $this->load->model("Tasks_model");
                $this->load->model("system_model");

                $user = $this->db->select("email, name")->from("users")->where("id",$userId)->get()->row();
                $task = $this->Tasks_model->getSingleById($taskId);

                if($user && $task) {
                    $emailData = [
                        'user'      =>  $user,
                        'task'      =>  $task,
                        'logo'      =>  $this->system_model->getParam("logo"),
                        'link'      =>  "",
                        'link_label'=>  ""
                    ];

                    $content = $this->load->view("_email/header",$emailData, true);
                    $content .= $this->load->view("_email/userHasBeenAssignedTask",$emailData, true);
                    $content .= $this->load->view("_email/footer",[], true);
                    $this->Email_model3->save($user->email,"You have been assigned a task",$content);
                }
            } catch (Exception $e) {
                // Log error but don't fail the assignment
                log_message('error', 'Failed to send assignment email: ' . $e->getMessage());
            }

            return true;
        }
    }

    public function assignUsers($userIds,$taskIds,$customerId,$projectId,$sprintId)
    {

        $this->load->model("Email_model3");
        $this->load->model("Tasks_model");
        $this->load->model("system_model");

        if(!empty($customerId)) $customer = $this->db->select('company_name')->from('customers')->where(['status'=>1,'customer_id'=>$customerId])->get()->row()->company_name;
        if(!empty($projectId)) $project = $this->db->select('name')->from('projects')->where(['status'=>1,'id'=>$projectId])->get()->row()->name;
        if(!empty($sprintId)) $sprint = $this->db->select('name')->from('sprints')->where(['status'=>1,'id'=>$sprintId])->get()->row()->name;

        //fetch all selected tasks
        $tasks = $this->Tasks_model->getByIds($taskIds);

        //first remove all users to task then assign the users
        foreach($taskIds as $taskId){
            $this->db->where("task_id",$taskId)->delete("task_user");
        }
        foreach($userIds as $userId){
            foreach($taskIds as $taskId){
                $this->db->set("task_id",$taskId);
                $this->db->set("user_id",$userId);
                $this->db->insert("task_user");
            }
            $user = $this->db->select("email, name")->from("users")->where("id",$userId)->get()->row();

                $emailData = [
                    'user'      =>  $user,
                    'customer'  =>  isset($customer) ? $customer : '',
                    'project'   =>  isset($project) ? $project : '',
                    'sprint'    =>  isset($sprint) ? $sprint : '',
                    'tasks'      =>  $tasks,
                    'logo'      =>  $this->system_model->getParam("logo"),
                    'link'      =>  "",
                    'link_label'=>  ""
                ];

                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/userHasBeenAssignedTasks",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);
                // echo $content;
                $this->Email_model3->save($user->email,"You have been assigned some tasks",$content);
        }
    }

}