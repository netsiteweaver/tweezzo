<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Developers extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->data['stageColors'] = array(
            'new'		    =>	'#1c8be6',
            'in_progress'	=>	'#44ab8e',
            'testing'	    =>	'#98c363',
            'staging'	    =>	'#f36930',
            'validated'	    =>	'#c44866',
            'completed'	    =>	'#4e67c7',
            'on_hold'	    =>	'#ff0000'
        );
        
        $this->load->library("migration");
        $this->load->model("system_model");
        $this->load->model("developersportal_model");
        $this->data['logo'] = $this->system_model->getParam("logo");
        $this->data['page_title'] = "";

        if(isset($_SESSION['developer_id'])){
            $this->data['customers'] = $this->developersportal_model->getMyCustomers($_SESSION['developer_id']);
            $this->data['projects'] = $this->developersportal_model->getMyProjects($_SESSION['developer_id']);
            $this->data['sprints'] = $this->developersportal_model->getMySprints($_SESSION['developer_id']);
        }

    }

    public function index()
    {
        $this->signin();
    }

    public function signin()
    {
        //Breadcrumbs
        // $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Signin";

        $this->load->view("/portal/developers/signin_18",$this->data);
        
    }

    public function authenticate()
    {

        $email = $this->input->post("email");
        $pswd = $this->input->post("password");
        
        $result = $this->developersportal_model->authenticate($_POST);

        if($result) {
            $_SESSION['developer_id'] = $result->id;
            $_SESSION['developer_email'] = $result->email;
            $_SESSION['developer_name'] = $result->name;
        }

        echo json_encode(array(
            "result"    =>  (!empty($result)) ? true : false,
            "data"      =>  $result
        ));
    }

    public function signout()
    {
        unset($_SESSION['developer_id']);
        redirect(base_url("portal/developers/signin"));
    }

    public function myaccount()
    {
        //Breadcrumbs
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Departments";

        $this->load->view("/portal/developers/myaccount",$this->data);
         
    }

    public function tasks()
    {
        $this->data['page_title'] = "Tasks";

        $this->load->model("developersportal_model");
        $this->data['tasks'] = $this->developersportal_model->getMyTasks($_SESSION['developer_id'],$this->input->get("customer_id"),$this->input->get("project_id"),$this->input->get("sprint_id"),$this->input->get("stage"),$this->input->get("order_by"),$this->input->get("order_dir"),1,999,$this->input->get('notes_only'));
        $this->data['myProjects'] = $this->developersportal_model->getMyProjects($_SESSION['developer_id']);
        $this->data['myCustomers'] = $this->developersportal_model->getMyCustomers($_SESSION['developer_id']);
        $this->data['mySprints'] = $this->developersportal_model->getMySprints($_SESSION['developer_id']);

        $this->data['content'][] = $this->load->view("/portal/developers/tasks",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);

    }

    public function view()
    {
        $this->data['page_title'] = "View";

        $uuid = $this->uri->segment(4);
        $this->load->model("developersportal_model");
        $this->data['task'] = $this->developersportal_model->getSingleTask($uuid);

        $this->data['content'][] = $this->load->view("/portal/developers/view",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
        
    }

    public function myCustomers()
    {
        $this->data['page_title'] = "Customers";

        $this->load->model("developersportal_model");
        $this->data['myCustomers'] = $this->developersportal_model->getMyCustomers($_SESSION['developer_id']);
        // $this->load->view("/portal/developers/myCustomers",$this->data);
        $this->data['content'][] = $this->load->view("/portal/developers/myCustomers",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
        
    }

    public function myProjects()
    {
        $this->data['page_title'] = "Projects";

        $this->load->model("developersportal_model");
        $this->data['myProjects'] = $this->developersportal_model->getMyProjects($_SESSION['developer_id']);
        // $this->load->view("/portal/developers/myProjects",$this->data);
        $this->data['content'][] = $this->load->view("/portal/developers/myProjects",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
    }

    public function mySprints()
    {
        $this->data['page_title'] = "Sprints";
        
        $this->load->model("developersportal_model");
        $this->data['mySprints'] = $this->developersportal_model->getMySprints($_SESSION['developer_id']);
        // $this->load->view("/portal/developers/mySprints",$this->data);
        $this->data['content'][] = $this->load->view("/portal/developers/mySprints",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
    }

    public function notes()
    {
        $this->data['page_title'] = "Sprints";
        
        // $past_days = $this->input->get("past_days");
        $start_date = (!empty($this->input->get("start_date"))) ? $this->input->get("start_date") : date("Y-m-01");
        $end_date = (!empty($this->input->get("end_date"))) ? $this->input->get("end_date") : date("Y-m-t");
        $customer_id = $this->input->get("customer_id");
        $project_id = $this->input->get("project_id");
        $sprint_id = $this->input->get("sprint_id");
        $this->load->model("Notes_model");
        $this->data['notes'] = $this->Notes_model->getNotesByUserId($_SESSION['developer_id'], $start_date,$end_date,$project_id,$sprint_id,$customer_id);
        // $this->load->view("/portal/developers/mySprints",$this->data);
        $this->data['content'][] = $this->load->view("/portal/developers/notes",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
    }

    public function saveNotes()
    {
        // debug($_POST);
        $task_uuid = $this->input->post("task_uuid");
        $task_id = $this->input->post("task_id");
        $notes = $this->input->post("notes");
        $public = ($this->input->post("display_type") == null ) ? 'private' : 'public';

        if(empty($notes)) {
            redirect(base_url("portal/developers/view/".$task_uuid));
        }
        
        $this->load->model("developersportal_model");
        $this->developersportal_model->saveNotes($task_id, $notes, $public);
        redirect(base_url("portal/developers/view/".$task_uuid));
    }

    public function deleteNote()
    {
        $this->load->model("Notes_model");
        $note_id = $this->input->post("note_id");
        $affected_rows = $this->Notes_model->deleteNote($note_id, 'developer');
        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
        exit;
    }

    public function forgotPassword()
    {
        $email = $this->input->post("email");
        $result = $this->developersportal_model->forgotPassword($email);
        echo json_encode(['result'=>true]);
        exit;
    }

    public function processForgotPassword()
    {
        $token = $this->uri->segment(4);
        $email = $this->uri->segment(5);
        $this->developersportal_model->processForgotPassword($token, $email);
        redirect(base_url("portal/developers/signin"));
    }

    public function moveStage()
    {
        $task_id = $this->input->post("task_id");
        $stage = $this->input->post("stage");
        $this->developersportal_model->moveStage($task_id,$stage);
        echo json_encode(['result'=>true]);
        exit;
    }

    public function loadNotes()
    {
        $this->load->model("Tasks_model");
        $task_id = $this->input->post('task_id');
        $this->data['notes'] = $this->Tasks_model->loadNotes($task_id);
        echo json_encode(array(
            "result"    =>  true,
            "user_id"   =>  $_SESSION['developer_id'],
            "notes"     =>  $this->data['notes']
        ));
        exit;
    }
}