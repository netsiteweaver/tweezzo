<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller
{
    var $data = [];

    public function __construct()
    {
        parent::__construct();

        if( ( !in_array( $this->uri->segment(3) , ['signin', 'authenticate', 'forgotPassword','processForgotPassword']) ) && (!isset($_SESSION['customer_access_id'])) ){
            redirect('portal/customers/signin');
        }

        $this->load->library("migration");
        $this->load->model("system_model");
        $this->load->model("Customersportal_model");
        $this->data['logo'] = $this->system_model->getParam("logo");
        $this->data['page_title'] = "";

        $this->load->model("Quotes_model");
        $this->data['random_quote'] = $this->Quotes_model->getRandomQuote();

        if(isset($_SESSION['customer_access_id'])){
            $this->data['user_access'] = $this->db->query("
                SELECT c.company_name, ca.name userName, ca.email userEmail, COALESCE(ca.admin, null) isAdmin
                FROM customers c
                LEFT JOIN customer_access ca ON ca.customer_id = c.customer_id
                WHERE c.status = 1 AND c.customer_id = (SELECT customer_id FROM customer_access WHERE id = {$_SESSION['customer_access_id']})
                ORDER BY ca.name
            ")->result();
        }


        if(isset($_SESSION['customer_access_id'])){
            $this->data['projects'] = $this->Customersportal_model->getProjects($_SESSION['customer_access_id']);
            $this->data['sprints'] = $this->Customersportal_model->getSprints();
        }
    }

    public function index()
    {
        $this->signin();
    }

    public function signin()
    {
        if(isset($_SESSION['customer_access_id'])){
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
            $_SESSION['customer_access_id'] = $result->customer_access_id;
            $_SESSION['customer_email'] = $result->email;
            $_SESSION['customer_company_name'] = $result->company_name;
            $_SESSION['customer_name'] = $result->name;
        }

        echo json_encode(array(
            "result"    =>  (!empty($result)) ? true : false,
            "data"      =>  $result
        ));
    }

    public function projects()
    {
        $this->data['page_title'] = "Projects";

        $this->data['projects'] = $this->Customersportal_model->getProjects($_SESSION['customer_access_id']);
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

    public function notes()
    {
        $this->data['page_title'] = "Notes";
        
        // $past_days = $this->input->get("past_days");
        $start_date = (!empty($this->input->get("start_date"))) ? $this->input->get("start_date") : date("Y-m-01");
        $end_date = (!empty($this->input->get("end_date"))) ? $this->input->get("end_date") : date("Y-m-t");
        $project_id = $this->input->get("project_id");
        $sprint_id = $this->input->get("sprint_id");
        $this->load->model("Notes_model");
        $this->data['notes'] = $this->Notes_model->getNotesByCustomerId($_SESSION['customer_access_id'], $start_date,$end_date,$project_id,$sprint_id);
        // $this->load->view("/portal/developers/mySprints",$this->data);
        $this->data['content'][] = $this->load->view("/portal/customers/notes",$this->data,true);
        $this->load->view("/portal/customers/shared/layout",$this->data);
    }

    public function view()
    {
        $this->data['page_title'] = "View";

        $task_uuid = $this->input->get("task_uuid");
        $this->data['task'] = $this->Customersportal_model->getTask($task_uuid);
        if(empty($this->data['task'])){
            redirect(base_url("portal/customers/tasks?error=Task not found"));
        }
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
        unset($_SESSION['customer_access_id']);
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
            "user_id"   =>  $_SESSION['customer_access_id'],
            "notes"     =>  $this->data['notes']
        ));
        exit;
    }

    public function submitTask()
    {
        $this->load->model("Customersportal_model");
        $status = $this->Customersportal_model->submitTask($_POST);
        if($status['result'] == false){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  $status['reason']
            ));
        }else{
            echo json_encode(array(
                "result"    =>  true
            ));
        }
        exit;
    }

    public function createUserAccess()
    {
        $name = trim($this->input->post("name"));
        $email = trim($this->input->post("email"));
        $password = trim($this->input->post("password"));
        // $confirm_password = trim($this->input->post("confirm_password"));
        $valid = true;
        $php_errormsg = "";

        if(strlen($name)<4){;
            $php_errormsg .= "Please enter a name (4 chars min)<br>";
            $valid = false;
        }
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $php_errormsg .= "Please enter a valid email<br>";
            $valid = false;
        }
        if(strlen($password)<4){
            $php_errormsg .= "Please enter a password (4 chars min)<br>";
            $valid = false;
        }

        if(!$valid){
            echo json_encode(['result'=>false,'reason'=>$php_errormsg]);
            exit;
        }

        $ct = $this->db->select("count(id) as ct")->from("customer_access")->where("email",$email)->get()->row()->ct;
        
        if($ct>0){
            echo json_encode(['result'=>false,'reason'=>"Email already used"]);
            exit;
        }

        $result = $this->Customersportal_model->createUserAccess($name, $email, $password);

        echo json_encode($result);

        exit;
    }
    
}