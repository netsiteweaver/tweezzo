<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller
{
    var $data = [];

    public function __construct()
    {
        parent::__construct();

        if( ( !in_array( $this->uri->segment(3) , ['signin', 'authenticate', 'forgotPassword','processForgotPassword']) ) && (!isset($_SESSION['customer_id'])) ){
            redirect('portal/customers/signin');
        }

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
        $this->load->model("Customersportal_model");
        $this->data['logo'] = $this->system_model->getParam("logo");
        $this->data['page_title'] = "";
        if(isset($_SESSION['customer_id'])){
            $this->data['projects'] = $this->Customersportal_model->getProjects($_SESSION['customer_id']);
            $this->data['sprints'] = $this->Customersportal_model->getSprints();
        }
    }

    public function index()
    {
        $this->signin();
    }

    public function signin()
    {
        if(isset($_SESSION['customer_id'])){
            redirect('portal/customers/projects');
        }
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Signin";
        $this->load->view("/portal/customers/signin_18",$this->data);
    }

    public function authenticate()
    {

        $email = $this->input->post("email");
        $pswd = $this->input->post("password");

        $this->load->model("Customersportal_model");
        $result = $this->Customersportal_model->authenticate($_POST);

        if($result) {
            $_SESSION['customer_id'] = $result->customer_id;
            $_SESSION['customer_email'] = $result->email;
            $_SESSION['customer_name'] = $result->company_name;
        }

        echo json_encode(array(
            "result"    =>  (!empty($result)) ? true : false,
            "data"      =>  $result
        ));
    }

    public function projects()
    {
        $this->data['page_title'] = "Projects";

        $this->data['projects'] = $this->Customersportal_model->getProjects($_SESSION['customer_id']);
        $this->data['content'][] = $this->load->view("/portal/customers/projects",$this->data,true);
        // debug($this->data['projects']);
        $this->load->view("/portal/customers/shared/layout",$this->data);
    }

    public function sprints()
    {
        $this->data['page_title'] = "Sprints";

        $project_id = $this->input->get("project_id");
        $this->data['sprints'] = $this->Customersportal_model->getSprints($project_id);
        $this->data['content'][] = $this->load->view("/portal/customers/sprints",$this->data,true);
        // debug($this->data['sprints']);
        $this->load->view("/portal/customers/shared/layout",$this->data);
    }

    public function tasks()
    {
        $this->data['page_title'] = "Tasks";

        $sprint_id = $this->input->get("sprint_id");
        $sort_by = $this->input->get("sort_by");
        $sort_dir = $this->input->get("sort_dir");
        $stages = $this->input->get("stages");
        $sprint_id = $this->input->get("sprint_id");
        $notes_only = $this->input->get("notes_only");
        $this->data['stages'] = (empty($this->input->get('stages'))) ? [] : explode(',',$this->input->get('stages'));

        $this->data['tasks'] = $this->Customersportal_model->getTasks($sprint_id,$sort_by,$sort_dir,$stages,$notes_only);
        $this->data['content'][] = $this->load->view("/portal/customers/tasks",$this->data,true);
        // debug($this->data['tasks']);
        $this->load->view("/portal/customers/shared/layout",$this->data);
    }

    public function view()
    {
        $this->data['page_title'] = "View";

        $uuid = $this->input->get("uuid");
        $this->data['task'] = $this->Customersportal_model->getTask($uuid);
        // debug($this->data['task']);
        $this->data['content'][] = $this->load->view("/portal/customers/view",$this->data,true);
        $this->load->view("/portal/customers/shared/layout",$this->data);
         
    }

    public function saveNote()
    {
        $task_id = $this->input->post("task_id");
        $note = $this->input->post("note");
        if(empty($note)){
            echo json_encode(['result'=>false,'reason'=>'Notes cannot be empty']);
            exit;
        }
        $result = $this->Customersportal_model->saveNote($task_id, $note);

        echo json_encode(['result'=>true,'affected_rows'=>$result]);
        exit;
    }

    public function deleteNote()
    {
        $this->load->model("Notes_model");
        $note_id = $this->input->post("note_id");
        $affected_rows = $this->Notes_model->deleteNote($note_id,'customer');
        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
        exit;
    }

    public function signout()
    {
        unset($_SESSION['customer_id']);
        redirect(base_url("portal/customers/signin"));
    }

    public function myaccount()
    {
        

        //Breadcrumbs
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Departments";

        $this->load->view("/portal/customers/myaccount",$this->data);
         
    }

    public function validateTask()
    {
        $task_id = $this->input->post("task_id");
        $result = $this->Customersportal_model->validateTask($task_id);
        if($result) {
            echo json_encode(['result'=>true]);
        }else{
            echo json_encode(['result'=>false,'reason'=>"Failed! Possible reason is that the task has already been validated"]);
        }
        exit;
    }

    public function forgotPassword()
    {
        $email = $this->input->post("email");
        $result = $this->Customersportal_model->forgotPassword($email);
        echo json_encode(['result'=>true]);
        exit;
    }

    public function processForgotPassword()
    {
        $token = $this->uri->segment(4);
        $email = $this->uri->segment(5);
        $this->Customersportal_model->processForgotPassword($token, $email);
        redirect(base_url("portal/customers/signin"));
    }

    public function loadNotes()
    {
        $this->load->model("Tasks_model");
        $task_id = $this->input->post('task_id');
        $this->data['notes'] = $this->Tasks_model->loadNotes($task_id);
        echo json_encode(array(
            "result"    =>  true,
            "user_id"   =>  $_SESSION['customer_id'],
            "notes"     =>  $this->data['notes']
        ));
        exit;
    }
    
}