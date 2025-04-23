<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("Tasks_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("tasks","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("tasks","edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("tasks","view");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("tasks","delete");
        $this->data['perms']['import'] = $this->accesscontrol_model->authorised("tasks","import");

        $this->data['stageColors'] = array(
            'new'		    =>	'#1c8be6',
            'in_progress'	=>	'#44ab8e',
            'testing'	    =>	'#98c363',
            'staging'	    =>	'#f36930',
            'validated'	    =>	'#c44866',
            'completed'	    =>	'#4e67c7',
            'on_hold'	    =>	'#ff0000'
        );
        
        // $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
    }

    public function add()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('tasks/listing'));
        $this->mybreadcrumb->add('Add', base_url('tasks/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        if(!empty($this->input->get('project_id')))
        {
            $this->load->model('Projects_model');
            $this->data['projects'] = $this->Projects_model->fetchSingleById($this->input->get('project_id'));
        }

        if(!empty($this->input->get('sprint_id')))
        {
            $this->load->model('Sprints_model');
            $this->data['sprints'] = $this->Sprints_model->fetchSingleById($this->input->get('sprint_id'));
        }

        $this->data["content"]=$this->load->view("/tasks/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function import()
    {
        //Access Control
        if(!isAuthorised(get_class(),"import")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('tasks/listing'));
        $this->mybreadcrumb->add('Add', base_url('tasks/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup();

        $this->load->model('Sprints_model');
        $this->data['sprints'] = $this->Sprints_model->lookup();

        $this->data["content"]=$this->load->view("/tasks/import",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function process_import()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $sprint_id = $this->input->post('sprint_id');
        $this->data['headers'] = $this->Tasks_model->process_import($sprint_id);

        redirect(base_url("tasks/listing?customer_id=".$this->input->post("customer_id")."&project_id=".$this->input->post("project_id")."&sprint_id=".$this->input->post("sprint_id")));
    }

    public function edit()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"edit")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['task'] = $this->Tasks_model->fetchSIngle($uuid);

        $this->data['progress'] = ($this->data['task']->progress == 0) ? "bg-danger" : (($this->data['task']->progress == 100) ? "bg-success" : "bg-warning");


        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('tasks/listing'));
        $this->mybreadcrumb->add('Edit', base_url('tasks/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup();

        $this->load->model('Sprints_model');
        $this->data['sprints'] = $this->Sprints_model->lookup();

        // $this->load->model('Users_model');
        // $this->data['users'] = $this->Users_model->lookup();

        $this->load->model("Developers_model");
        $this->data['developers'] = $this->Developers_model->lookup();

        // $this->loadStyleSheet("node_modules/lightbox2/dist/css/lightbox.min.css");
        // $this->loadScript("node_modules/lightbox2/dist/js/lightbox.min.js");

        $this->data["content"]=$this->load->view("/tasks/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function view()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"view")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['task'] = $this->Tasks_model->fetchSIngle($uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('tasks/listing'));
        $this->mybreadcrumb->add('Edit', base_url('tasks/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup();

        $this->load->model('Users_model');
        $this->data['users'] = $this->Users_model->lookup();

        // $this->loadStyleSheet("node_modules/lightbox2/dist/css/lightbox.min.css");
        // $this->loadScript("node_modules/lightbox2/dist/js/lightbox.min.js");

        $this->data["content"]=$this->load->view("/tasks/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('tasks/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Tasks";

        $customer_id = $this->input->get('customer_id');
        $project_id = $this->input->get('project_id');
        $sprint_id = $this->input->get('sprint_id');
        $stage = $this->input->get('stage');
        $assigned_to = $this->input->get('assigned_to');
        $order_by = $this->input->get('order_by');
        $order_dir = $this->input->get('order_dir');
        $notes_only = $this->input->get('notes_only');
        $search_text = $this->input->get('search_text');

        $page = $this->uri->segment(3);
        $per_page = (!empty($this->input->get("display"))) ? $this->input->get("display") : $this->system_model->getParam("rows_per_page");
        $this->data['tasks'] = $this->Tasks_model->fetchAll($customer_id,$project_id,$sprint_id,$stage,$assigned_to,$order_by,$order_dir,$page,$per_page,"",$notes_only,$search_text);
        $total_rows = $this->Tasks_model->totalRows($customer_id,$project_id,$sprint_id,$stage,$assigned_to,$order_by,$order_dir,$notes_only,$search_text);
        $this->data['total_rows'] = $total_rows;
        $this->data['pagination'] = getPagination("tasks/listing",$total_rows,$per_page);

        $this->load->model('Users_model');
        $this->data['users'] = $this->Users_model->lookup();

        $this->load->model('Developers_model');
        $this->data['developers'] = $this->Developers_model->lookup();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup($customer_id);

        $this->load->model('Sprints_model');
        $this->data['sprints'] = $this->Sprints_model->lookup2($project_id);

        $this->data["content"]=$this->load->view("/tasks/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function email()
    {
        $customer_id = $this->input->get('customer_id');
        $project_id = $this->input->get('project_id');
        $sprint_id = $this->input->get('sprint_id');
        $customer_email = $this->input->get('customer_email');
        $stage = $this->input->get('stage');
        $assigned_to = $this->input->get('assigned_to');
        $order_by = $this->input->get('order_by');
        $order_dir = $this->input->get('order_dir');
        $output = $this->input->get('output');
        $type = $this->input->get('type');

        //since we allow to override email when submitting, let us check if email is for customer, developer or other
        $isCustomer = $this->db->select("count(1) as ct")->from("customers")->where(["email"=>$customer_email,"status"=>"1"])->get()->row("ct");
        $isDeveloper = $this->db->select("count(1) as ct")->from("users")->where(["email"=>$customer_email,"user_type"=>"developer","status"=>"1"])->get()->row("ct");
        $isUser = $this->db->select("count(1) as ct")->from("users")->where(["email"=>$customer_email,"user_type !="=>"developer","status"=>"1"])->get()->row("ct");
        $link = "";
        $linkLabel = "";
        // if( ($isCustomer == 0) && ($isDeveloper == 0) && ($isUser == 0) ){
        //     $link = "";
        //     $linkLabel = "";
        // }else
        if($type == 'developer'){
            $link = "portal/developers/signin";
            $linkLabel = "Developer's Portal";
        }else{
            if($isCustomer > 0){
                $link = "portal/customers/signin";
                $linkLabel = "Customer's Portal";
            }elseif($isDeveloper > 0){
                $link = "portal/developers/signin";
                $linkLabel = "Developer's Portal";
            }elseif($isUser > 0){
                $link = "users/signin";
                $linkLabel = "Task Manager";
            }
        }
        
        // else{
        //     $link = "";
        //     $linkLabel = "";
        // }
        // $email = $this->db->query("select t.name task_name, t.task_number, s.name sprintName, p.name projectName, c.company_name, c.email from tasks t 
        //                     join sprints s on s.id = t.sprint_id 
        //                     join projects p on p.id = s.project_id 
        //                     join customers c on c.customer_id = p.customer_id 
        //                     where c.customer_id = $customer_id")->result();

        $tasks = $this->Tasks_model->fetchAll($customer_id,$project_id,$sprint_id,$stage,$assigned_to,$order_by,$order_dir,1,1,$output);
        // debug($tasks);
        // debug($this->data['tasks']);

        if(!empty($output)){
            $email = $customer_email;
            $this->load->model("Email_model3");
            $this->load->model("system_model");
            $emailData = [
                'stageColors'   =>  $this->data['stageColors'],
                'tasks'     =>  $tasks,
                'logo'      =>  $this->system_model->getParam("logo"),
                'link'      =>  base_url($link)."?email=".$email,
                'link_label'=>  $linkLabel
            ];
            $content = $this->load->view("_email/header",$emailData, true);
            $content .= $this->load->view("_email/taskListToClient",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            // $path = realpath(".");
            // $h = fopen($path . "/data/taskListSent_".date('YmdHis').".html",'w');
            // if($h){
            //     fwrite($h,$content);
            //     fclose($h);    
            // }else{
            //     die('cannot create file');
            // }
            $this->Email_model3->save($email,"Task List Progress",$content);
        }
    }

    public function save()
    {
        //Access Control
        if(!isAuthorised(get_class(),"add")) return false;

        $this->load->model("files_model");

        //check for any file to delete
        $deleted_images_json = (empty($this->input->post('deleted_images'))) ? [] : $this->input->post('deleted_images');
        $deleted_images = json_decode($deleted_images_json);
        if(!empty($deleted_images)){
            foreach($deleted_images as $image){
                $this->files_model->deleteFile($image);
                $this->db->where("id",$image)->delete("task_images");
            }
        }

        $uploadedFiles = [];
        if($_FILES['file1']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file1","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file2']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file2","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file3']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file3","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file4']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file4","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file5']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file5","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);

        $data = $this->input->post();
        $response = $this->Tasks_model->save($data,$uploadedFiles);
        if($response['result']== false){
            flashDanger($response['reason']);
            redirect(base_url("tasks"));
            return;
        }
        if(!empty($this->input->post('add_more'))){
            flashSuccess("Task has been added successfully");
            redirect(base_url("tasks/add?customer_id=".$data['customer_id']."&project_id=".$data['project_id']."&sprint_id=".$data['sprint_id'].'&add_more=1'));
        }else{
            redirect(base_url("tasks/listing?customer_id=".$data['customer_id']."&stage=".$data['stage']));
        }
        
    }

    public function saveNote()
    {
        //Access Control
        if(!isAuthorised(get_class(),"view")) return false;

        $data = $this->input->post();

        $response = $this->Tasks_model->saveNote($data);
        if($response['result']== false){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  $response['reason']
            ));
            exit;
        }
        echo json_encode(array(
            "result"    =>  true
        ));
        exit;
    }

    public function deleteNote()
    {
        $this->load->model("Notes_model");
        $note_id = $this->input->post('note_id');
        $affected_rows = $this->Notes_model->deleteNote($note_id,'user');
        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
        exit;
    }

    public function loadNotes()
    {
        $task_id = $this->input->post('task_id');
        $this->data['notes'] = $this->Tasks_model->loadNotes($task_id);
        echo json_encode(array(
            "result"    =>  true,
            "user_id"   =>  $_SESSION['user_id'],
            "notes"     =>  $this->data['notes']
        ));
        exit;
    }

    public function delete()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $uuid = $this->input->post('uuid');
        $affected_rows = $this->Tasks_model->delete($uuid);

        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
    }

    public function deleteMultiple()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $taskIds = $this->input->post('taskIds');
        $affected_rows = $this->Tasks_model->deleteMultiple($taskIds);

        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
    }

    public function assignUser()
    {
        $userId = $this->input->post("userId");
        $taskId = $this->input->post("taskId");
        $check = $this->db->select("count(1) as ct")->from("task_user")->where(array('task_id'=>$taskId,'user_id'=>$userId))->get()->row()->ct;

        if($check > 0){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'User is already assigned to this task'
            ));

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

            echo json_encode(array(
                "result"    =>  true
            ));
        }
    }

    public function assignUsers()
    {
        $userIds = $this->input->post("userIds");
        $taskIds = $this->input->post("taskIds");

        $this->load->model("Email_model3");
        $this->load->model("Tasks_model");
        $this->load->model("system_model");

        //first remove all users to task then assign the users
        foreach($taskIds as $taskId){
            $this->db->where("task_id",$taskId)->delete("task_user");
        }
        foreach($userIds as $userId){
            foreach($taskIds as $taskId){
                $this->db->set("task_id",$taskId);
                $this->db->set("user_id",$userId);
                $this->db->insert("task_user");

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
                $this->Email_model3->save($user->email,"You have been assigned a task [BULK]",$content);

            }
        }

        echo json_encode(array(
            "result"    =>  true
        ));

    }

    public function removeUser()
    {
        $userId = $this->input->post("userId");
        $taskId = $this->input->post("taskId");
        $check = $this->db->select("count(1) as ct")->from("task_user")->where(array('task_id'=>$taskId,'user_id'=>$userId))->get()->row()->ct;

        if($check == 0){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'Cannot remove user since user is not assigned to this task'
            ));

        }else{
            $this->db->where(array(
                "task_id"   =>  $taskId,
                "user_id"   =>  $userId
            ));
            $this->db->delete("task_user");
            echo json_encode(array(
                "result"    =>  true
            ));

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
            $content .= $this->load->view("_email/userHasBeenRemovedTask",$emailData, true);
            $content .= $this->load->view("_email/footer",[], true);
            $this->Email_model3->save($user->email,"You have been removed from a task",$content);
        }
    }

    public function bulkChangeStage()
    {
        $stage = $this->input->post("stage");
        $taskIds = $this->input->post("taskIds");

        if(empty($stage)){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'No Stage Selected'
            ));
            exit;
        }

        if(empty($taskIds)){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'No Task(s) Selected'
            ));
            exit;
        }

        $this->Tasks_model->bulkChangeStage($taskIds,$stage);

        echo json_encode(array(
            "result"    =>  true
        ));
        exit;


    }

    public function bulkChangeSprint()
    {
        $sprintId = $this->input->post("sprintId");
        $taskIds = $this->input->post("taskIds");

        if(empty($sprintId)){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'No Sprint Selected'
            ));
            exit;
        }

        if(empty($taskIds)){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'No Task(s) Selected'
            ));
            exit;
        }

        $this->Tasks_model->bulkChangeSprint($taskIds,$sprintId);

        echo json_encode(array(
            "result"    =>  true
        ));
        exit;


    }

    public function bulkSetDueDate()
    {
        $dueDate = $this->input->post("dueDate");
        $taskIds = $this->input->post("taskIds");

        if(empty($dueDate)){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'No Due Date Selected'
            ));
            exit;
        }

        if(empty($taskIds)){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  'No Task(s) Selected'
            ));
            exit;
        }

        $this->Tasks_model->bulkSetDueDate($taskIds,$dueDate);

        echo json_encode(array(
            "result"    =>  true
        ));
        exit;


    }

    public function index()
    {
        $this->listing();
    }

    public function getMaxTaskNumberBySprintId()
    {
        $sprint_id = $this->input->post("sprint_id");
        $tn = $this->db->query("SELECT MAX(task_number) as tn FROM tasks WHERE sprint_id = '$sprint_id' AND status = 1")->row()->tn;
        echo json_encode(array(
            'result'    =>  true,
            'maxTaskNumber' =>  incrementTaskNumber($tn)
        ));
        exit;
    }

}
