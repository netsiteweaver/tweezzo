<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks_model extends CI_Model{

    public function fetchAll($customer_id="",$project_id="",$sprint_id="",$stage="",$assigned_to="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10,$output="",$notes_only="",$search_text="",$totalRows=false)
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
        if(!empty($stage)) $this->db->where('t.stage',$stage);
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
        $task->notes = $this->db->select('tn.id, tn.notes,tn.created_by, tn.created_on,u.name, c.company_name customer')
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
        $this->db->set('stage',$data['stage']);
        $this->db->set('section',$data['section']);
        $this->db->set('due_date',!empty($data['due_date']) ? $data['due_date'] : null);
        $this->db->set('estimated_hours',!empty($data['estimated_hours']) ? $data['estimated_hours'] : null);
        $this->db->set('progress',floatval($data['progress']));

        $stageColors = array(
            'new'		    =>	'#1c8be6',
            'in_progress'	=>	'#44ab8e',
            'testing'	    =>	'#98c363',
            'staging'	    =>	'#f36930',
            'validated'	    =>	'#c44866',
            'completed'	    =>	'#4e67c7',
            'on_hold'	    =>	'#ff0000'
        );

        if(empty($data['uuid'])){
            $uuid = gen_uuid();
            $this->db->set('uuid',$uuid);
            $this->db->set('created_by',$_SESSION['user_id']);
            $this->db->set('created_on',date('Y-m-d H:i:s'));
            $this->db->insert('tasks');

            $taskId = $this->db->insert_id();
            $this->saveFiles($uploadedFiles,$taskId);

            $members = $this->System_model->getParam("notification_create_task",true);
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
                    'link'          =>  base_url('tasks/view/'.$uuid),
                    'link_label'    =>  'View Task',
                    'stageColors'   =>  $stageColors
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/taskCreatedOrUpdated",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);

                $this->Email_model3->save($user->email,"New Task Created",$content);
                
            }
        }else{
            $this->db->where('uuid',$data['uuid']);
            $this->db->update('tasks');

            $taskId = $this->db->select("id")->from("tasks")->where("uuid",$data['uuid'])->get()->row()->id;
            $this->saveFiles($uploadedFiles,$taskId);

            $members = $this->System_model->getParam("notification_update_task",true);
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
                    'link'          =>  base_url('tasks/view/'.$data['uuid']),
                    'link_label'    =>  'View Task',
                    'stageColors'   =>  $stageColors
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/taskCreatedOrUpdated",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);

                $this->Email_model3->save($user->email,"Task Updated",$content);

                // $content = "Dear {$user->name}<br>";
                // $content .= "A task has been updated as follows:<br><br>";
                // $content .= "<b>Customer</b>: {$projectInfo->customerName}<br>";
                // $content .= "<b>Project</b>: {$projectInfo->projectName}<br>";
                // $content .= "<b>Sprint</b>: {$projectInfo->sprintName}<br>";
                // $content .= "<b>Task Name</b>: {$data['name']}<br>";
                // $content .= "<b>Task Description</b>: {$data['description']}<br>";
                // $content .= "<b>Task Number</b>: {$data['task_number']}<br>";
                // $content .= "<b>Section</b>: {$data['section']}<br><br>";

                // $subject = "Task has been updated";

                // $email = $this->load->view("email/tasks/test",[
                //     'subject'   =>  $subject,
                //     'blocks'   =>  [
                //         $content
                //     ]
                // ],true);
                // $this->email_model2->save($user->email,$subject,$email);
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
        
        //first send to client if notes is public
        if($public == "public"){
            $emailData['addressee'] = 'Customer';
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/noteHasBeenAdded",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $check = $this->Email_model3->save($result[0]->customer_email,"A note has been added",$content);

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
            $check = $this->Email_model3->save($developer->developer_email,"A note has been added",$content);
            if($check == '401'){
                return array('result'=>false,'reason'=>'Mail Server: Not Authorised');
            }
        }

        //then send to admins, if defined
        $emailData['addressee'] = 'Admin';
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/noteHasBeenAdded",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $admins = $this->system_model->getParam("notification_add_notes",true);
        foreach($admins as $admin){
            $user = $this->db->select("*")->from("users")->where("id",$admin)->get()->row();
            $check = $this->Email_model3->save($user->email,"A note has been added",$content);
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
            $data = array(
                'uuid'          =>  gen_uuid(), 
                'name'          =>  $row[0],
                'description'   =>  $row[1],
                'task_number'   =>  str_pad(++$task_count, 2, '0', STR_PAD_LEFT),
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

}