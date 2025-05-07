<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks_model extends CI_Model{

    public function fetchAll($customer_id="",$project_id="",$sprint_id="",$stage=[],$assigned_to="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10,$output="",$notes_only="",$search_text="",$totalRows=false)
    {
        if(!$totalRows){
            if( (empty($page)) || ($page <= 0) ) $page =1;
            $offset = ( ($page-1)*$rows_per_page);  

            $this->db->select('t.*,count(tn.id) as notes, c.company_name,c.full_name, c.email, p.name project_name, s.name sprint_name');
        }else{
            $this->db->select('count(1) as ct');
        }
        
        $this->db->from('tasks t');
        $this->db->join('sprints s','s.id=t.sprint_id','left');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
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
        
        $this->db->where('t.status',1);
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
            $this->db->group_end();
        }
        // echo $this->db->get_compiled_select();die;
        if(!empty($order_by)) {
            $this->db->order_by($order_by,$order_dir);
        }else{
            $this->db->order_by('t.task_number');
        }
        if(!$totalRows){
            if(empty($output)) {
                $this->db->order_by($order_by,$order_dir);
                $this->db->limit($rows_per_page,$offset);
            }
            $this->db->group_by('t.id');
        }
        if(!$totalRows){
            $tasks = $this->db->get()->result();

            foreach($tasks as $i => $task){
                $tasks[$i]->users = $this->db->select("u.name,u.display_name,u.email,u.user_type,u.photo")
                                    ->from("task_user t")
                                    ->join("users u","u.id=t.user_id")
                                    ->where(["t.task_id"=>$task->id])
                                    ->get()->result();
            }
            return $tasks;
        }else{
            return $this->db->get()->row('ct');
        }
        
    }

    public function totalRows($customer_id="",$project_id="",$sprint_id="",$stage="",$assigned_to="",$order_by="",$order_dir="asc",$notes_only="",$search_text="")
    {
        $rows = $this->fetchAll($customer_id, $project_id, $sprint_id, $stage, $assigned_to, $order_by, $order_dir, 1, 10, '', $notes_only, $search_text, true);
        return $rows;

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
        $this->db->select('t.*, c.customer_id, c.company_name, c.full_name, p.id project_id, p.name project_name, s.name sprint_name');
        $this->db->from('tasks t');
        $this->db->join('sprints s','s.id=t.sprint_id','left');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('t.uuid',$uuid);
        $this->db->where('t.status',1);
        $task = $this->db->get()->row();
        if(empty($task)) return [];
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
        $this->db->where('t.status',1);
        $this->db->order_by("task_number");
        $tasks = $this->db->get()->result();
        return $tasks;
    }

    public function move_stage($data)
    {
        $this->load->model("System_model");
        $this->load->model("email_model2");
        
        $this->db->query("SET @current_user_email = '{$_SESSION['authenticated_user']->email}'");
        $this->db->query("SET @current_user_ip = '{$_SERVER['REMOTE_ADDR']}'");
        $this->db->query("SET @current_user_agent = '{$_SERVER['HTTP_USER_AGENT']}'");
        $this->db->query("SET @current_user_id = " . (int) $_SESSION['user_id']);
        $this->db->query("SET @current_user_type = 'user'");
        $this->db->query("SET @@session.time_zone = '+04:00'");

        $this->db->set('stage',$data['stage']);
        $this->db->set('progress',$data['progress']);
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
        $userEmail = $this->db->select("email")->from('users')->where('id',$_SESSION['user_id'])->get()->row()->email;

        $check = $this->notifyUsers($taskDetails,$data, $userEmail);

        if(!$check['result']) {
            return array('result'=>false,'reason'=>$check['reason']);
        }
        return array('result'=>true,'data'=>$data);
    }

    public function notifyUsers($taskDetails, $data, $userEmail, $public='public')
    {
        $query = "SELECT t.*, s.name sprint_name, p.name project_name, c.email customer_email, c.company_name customer, u.email developer_email, u.name developer_name
                    FROM tasks t 
                    Left join sprints s on s.id = t.sprint_id 
                    left join projects p on p.id = s.project_id 
                    left join customers c on c.customer_id = p.customer_id 
                    left join task_user tu on tu.task_id = t.id
                    left join users u on u.id = tu.user_id 
                    where t.status = 1
                    and t.uuid = '{$taskDetails->uuid}'";
        $result = $this->db->query($query)->result();

        $this->load->model("Email_model3");
        $this->load->model("system_model");

        $emailData = [
            'addressee'         =>  '',
            'notes'             =>  $data['notes'],
            'logo'              =>  $this->system_model->getParam("logo"),
            'taskDetails'       =>  $taskDetails,
            'userEmail'         =>  $userEmail,
            'show_lifecycle'    =>  false
        ];
        
        $subject = "{$taskDetails->full_name} added a note on {$taskDetails->task_number}/{$taskDetails->sprint_name}/{$taskDetails->project_name}";

        //first send to client if notes is public
        if($public == "public"){
            $emailData['addressee'] = 'Customer';
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/noteHasBeenAdded",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $check = $this->Email_model3->save($result[0]->customer_email,$subject,$content);
            if($check == '401'){
                return array('result'=>false,'reason'=>'Mail Server: Not Authorised');
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

    public function bulkChangeStage($taskIds, $stage)
    {
        $taskids = implode(',',$taskIds);
        $this->db->query("UPDATE tasks SET stage = '$stage' WHERE id IN ($taskids)");
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
        // return $data;
    //     $this->parseUploadedFile($data);
    // }

    // public function parseUploadedFile()
    // {
        $handle = fopen($data['full_path'], "r");
        $header = fgetcsv($handle);
        $output = [];
        while (($row = fgetcsv($handle)) !== FALSE) {
            $output[] = $row;
        }
        return $output;
    }

    public function process_import($sprint_id)
    {
        $this->load->model("files_model");
        $data = $this->files_model->uploadCSV("file");
        if(!is_array($data)){
            return $data;
        }
        $handle = fopen($data['full_path'], "r");
        //get header
        $header = fgetcsv($handle);

        //get task number
        $task_count = $this->db->select("MAX(task_number) as n")
                ->from("sprints s")
                ->join("tasks t","t.sprint_id = s.id","left")
                ->where("s.id",$sprint_id)
                ->get()
                ->row()
                ->n;

        
        if(!empty($task_count)) {
            $task_count = intval(str_replace(".","",$task_count));   
        }else{
            $task_count = 0; 
        }

        /**
         * column 1 : task name
         * column 2 : task description
         * column 3 : stage
         * column 4 : section
         */
        while (($row = fgetcsv($handle)) !== FALSE) {
            if( ($row[2]=='DONE') || ($row[2]=='completed') ){
                $stage = 'completed';
            }elseif( ($row[2]=='IN PROGRESS') || ($row[2]=='in_progress') ){
                $stage = 'in_progress';
            }elseif($row[2]=='STAGING'){
                $stage = 'staging';
            }elseif($row[2]=='VALIDATED'){
                $stage = 'validated';
            }elseif( ($row[2]=='TO DO') || ($row[2]=='new') ){
                $stage = 'new';
            }else{
                $stage = 'new';
            }
            $maxTN = $this->db->query("SELECT MAX(task_number) as tn FROM tasks WHERE sprint_id = '$sprint_id' AND status = 1")->row()->tn;
            $taskNumber = incrementTaskNumber($maxTN);
            $data = array(
                'uuid'          =>  gen_uuid(), 
                'name'          =>  $row[0],
                'description'   =>  $row[1],
                // 'task_number'   =>  str_pad(++$task_count, 3, '0', STR_PAD_LEFT),
                'task_number'   =>  $taskNumber,
                'sprint_id'     =>  $sprint_id,
                'progress'      =>  0,
                'section'       =>  $row[3],
                'stage'         =>  $stage,
                'created_by'    =>  $_SESSION['user_id'],
                'created_on'    =>  date('Y-m-d H:i:s')
            );
            $this->db->insert('tasks',$data);
        }
        fclose($handle);
    }

    public function getGeneralProgress()
    {
        $customers = $this->db->select("customer_id, company_name")->from("customers")->where("status","1")->order_by('company_name')->get()->result();

        $result = [];
        foreach($customers as $c){
            $query = "select 
                        c.company_name,
                        -- COUNT(CASE WHEN t.stage = 'new' THEN 1 END) AS tasksNew,
                        -- COUNT(CASE WHEN t.stage = 'in_progress' THEN 1 END) AS tasksInProgress,
                        -- COUNT(CASE WHEN t.stage = 'testing' THEN 1 END) AS tasksTesting,
                        -- COUNT(CASE WHEN t.stage = 'staging' THEN 1 END) AS tasksStaging,
                        -- COUNT(CASE WHEN t.stage = 'validated' THEN 1 END) AS tasksValidated,
                        -- COUNT(CASE WHEN t.stage = 'completed' THEN 1 END) AS tasksCompleted,
                        COUNT(1) AS tasksAll,
                        ROUND(COUNT(CASE WHEN t.stage = 'new' THEN 1 END) / COUNT(1) * 100) AS pctNew,
                        ROUND(COUNT(CASE WHEN t.stage = 'in_progress' THEN 1 END) / COUNT(1) * 100) AS pctInProgress,
                        ROUND(COUNT(CASE WHEN t.stage = 'testing' THEN 1 END) / COUNT(1) * 100) AS pctTesting,
                        ROUND(COUNT(CASE WHEN t.stage = 'staging' THEN 1 END) / COUNT(1) * 100) AS pctStaging,
                        ROUND(COUNT(CASE WHEN t.stage = 'validated' THEN 1 END) / COUNT(1) * 100) AS pctValidated,
                        ROUND(COUNT(CASE WHEN t.stage = 'completed' THEN 1 END) / COUNT(1) * 100) AS pctCompleted,
                        ROUND(COUNT(CASE WHEN t.stage = 'on_hold' THEN 1 END) / COUNT(1) * 100) AS pctOnHold
                    from tasks t
                    join sprints s on s.id = t.sprint_id 
                    join projects p on p.id = s.project_id 
                    join customers c on c.customer_id = p.customer_id 
                    where t.status = 1 
                    and c.customer_id = '{$c->customer_id}'";
            $data = $this->db->query($query)->row();
            $idx = $c->company_name;
            $result[$idx] = $data;
        }
        return $result;
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

            $this->load->model("Email_model3");
            $this->load->model("Tasks_model");
            $this->load->model("system_model");

            $user = $this->db->select("email, name")->from("users")->where("id",$userId)->get()->row();
            $task = $this->Tasks_model->getSingleById($taskId);

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