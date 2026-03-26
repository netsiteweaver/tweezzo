<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller
{
    var $data = [];

    public function __construct()
    {
        parent::__construct();

        if( ( !in_array( $this->uri->segment(3) , ['signin', 'authenticate', 'forgotPassword','processForgotPassword','addUserAccess','updateUserAccess','removeAccess']) ) && (!isset($_SESSION['customer_access_id'])) ){
            $this->_remember_customer_portal_intended_url();
            redirect('portal/customers/signin');
        }

        $this->data['flash_danger'] = getFlashMessage("danger");
        $this->data['flash_success'] = getFlashMessage("success");
        $this->data['flash_warning'] = getFlashMessage("warning");
        $this->data['flash_info'] = getFlashMessage("info");

        $this->load->library("migration");
        $this->load->model("system_model");
        $this->load->model("Customersportal_model");
        $this->data['logo'] = $this->system_model->getParam("logo");
        $this->data['logoDark'] = $this->system_model->getParam("logo-dark");
        $this->data['page_title'] = "";

        $this->load->model("Quotes_model");
        $this->data['random_quote'] = $this->Quotes_model->getRandomQuote();

        if(isset($_SESSION['customer_access_id'])){
            $adminRow = $this->db->select("admin")->from("customer_access")->where(array(
                "status"        =>  "1",
                "id"            =>  $_SESSION['customer_access_id']
            ))->get()->row();
            $this->data['isAdmin'] = $adminRow ? $adminRow->admin : 0;
            $this->data['user_access'] = $this->db->query("
                SELECT ca.id, c.company_name, ca.name userName, ca.email userEmail, COALESCE(ca.admin, null) isAdmin
                FROM customers c
                LEFT JOIN customer_access ca ON ca.customer_id = c.customer_id
                WHERE c.status = 1 AND ca.status = 1 AND c.customer_id = (SELECT customer_id FROM customer_access WHERE id = {$_SESSION['customer_access_id']})
                ORDER BY ca.name
            ")->result();
        }


        if(isset($_SESSION['customer_access_id'])){
            $this->data['projects'] = $this->Customersportal_model->getProjects($_SESSION['customer_access_id']);
            $this->data['sprints'] = $this->Customersportal_model->getSprints();
        }
    }

    /**
     * Remember full requested path + query so post-login redirect can return to e.g. staging-filtered tasks.
     */
    private function _remember_customer_portal_intended_url()
    {
        $uri = $this->uri->uri_string();
        if ($uri === '') {
            return;
        }
        if (strpos($uri, 'portal/customers/') !== 0) {
            return;
        }
        $qs = (string) $this->input->server('QUERY_STRING');
        $full = $uri . ($qs !== '' ? '?' . $qs : '');
        $_SESSION['customer_portal_intended_url'] = $full;
    }

    /**
     * Prevent open redirects: only allow relative portal/customers destinations (no scheme, no odd chars).
     */
    private function _safe_customer_portal_redirect($path_and_query)
    {
        if (!is_string($path_and_query) || $path_and_query === '') {
            return false;
        }
        if (strpos($path_and_query, "\n") !== false || strpos($path_and_query, "\r") !== false) {
            return false;
        }
        if (strpos($path_and_query, '://') !== false || strpos($path_and_query, '//') === 0) {
            return false;
        }
        $path = $path_and_query;
        if (($pos = strpos($path_and_query, '?')) !== false) {
            $path = substr($path_and_query, 0, $pos);
        }
        if (strpos($path, 'portal/customers/') !== 0) {
            return false;
        }
        $seg = explode('/', $path);
        $method = isset($seg[2]) ? $seg[2] : '';
        if (in_array($method, ['signin', 'authenticate', 'forgotPassword', 'processForgotPassword', 'addUserAccess', 'updateUserAccess', 'removeAccess'], true)) {
            return false;
        }
        return true;
    }

    public function removeUser()
    {
        $userId = $this->input->post("userId");
        $isAdmin = $this->db->select('admin')->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->admin;
        if($isAdmin == 0){
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  "You do not have permission to remove a user"
            ));
        }else{
            $this->db->set("status","0")->where("id",$userId)->update("customer_access");
            echo json_encode(array(
                "result"    =>  true
            ));
        }
    }

    public function index()
    {
        $this->signin();
    }

    public function signin()
    {
        if(isset($_SESSION['customer_access_id'])){
            redirect('portal/customers/tasks');
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

        if($result['result'] === true) {
            $_SESSION['customer_access_id'] = $result['user'][0]->customer_access_id;
            $_SESSION['customer_email'] = $result['user'][0]->email;
            $_SESSION['customer_company_name'] = $result['user'][0]->company_name;
            $_SESSION['customer_name'] = $result['user'][0]->name;
            if (!empty($_SESSION['customer_portal_intended_url'])) {
                $cand = $_SESSION['customer_portal_intended_url'];
                unset($_SESSION['customer_portal_intended_url']);
                if ($this->_safe_customer_portal_redirect($cand)) {
                    $result['redirect'] = $cand;
                }
            }
            echo json_encode($result);
        }else{
            echo json_encode($result);
        }

    }

    /**
     * Global search: tasks by task ref, name, etc. Returns JSON for navbar typeahead.
     */
    public function searchTasks()
    {
        if (empty($_SESSION['customer_access_id'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => false, 'reason' => 'Not signed in']));
            return;
        }
        $customer_id = $this->db->select('customer_id')->from('customer_access')->where('id', (int) $_SESSION['customer_access_id'])->get()->row();
        if (empty($customer_id)) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => false, 'reason' => 'Invalid session']));
            return;
        }
        $customer_id = $customer_id->customer_id;
        $q = $this->input->get('q');
        $q = is_string($q) ? trim($q) : '';
        if ($q === '') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'tasks' => []]));
            return;
        }
        $rows = $this->Customersportal_model->searchTasks($customer_id, $q, 15);
        $tasks = [];
        foreach ($rows as $task) {
            $tasks[] = [
                'uuid'         => $task->uuid,
                'task_ref'     => isset($task->task_ref) ? $task->task_ref : '',
                'name'         => $task->name,
                'section'      => isset($task->section) ? $task->section : '',
                'project_name' => isset($task->project_name) ? $task->project_name : '',
                'sprint_name'  => isset($task->sprint_name) ? $task->sprint_name : '',
                'view_url'     => base_url('portal/customers/view?task_uuid=' . rawurlencode($task->uuid)),
            ];
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'tasks' => $tasks]));
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
        if(empty($this->data['sprints'])){
            setFlashMessage("danger","Selected project has no sprint yet");
            redirect(base_url("portal/customers/projects"));
        }
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

    public function submittedTasks()
    {
        $this->data['page_title'] = "Submitted Tasks";
        $this->data['submitted_tasks'] = $this->Customersportal_model->getSubmittedTasks($_SESSION['customer_access_id']);
        $this->data['content'][] = $this->load->view("/portal/customers/submitted_tasks", $this->data, true);
        $this->load->view("/portal/customers/shared/layout", $this->data);
    }

    public function deleteSubmittedTask()
    {
        $task_uuid = $this->input->post('task_uuid');
        $result = $this->Customersportal_model->deleteSubmittedTask($task_uuid, (int) $_SESSION['customer_access_id']);
        $this->output->set_content_type('application/json')->set_output(json_encode($result));
    }

    public function validationGuide()
    {
        $this->data['page_title'] = "Task Validation Guide";
        $this->data['content'][] = $this->load->view("/portal/customers/validation_guide", $this->data, true);
        $this->load->view("/portal/customers/shared/layout", $this->data);
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
        $note = $this->input->post("notes");
        // if(empty(trim($note))){
        //     echo json_encode(['result'=>false,'reason'=>'1 Notes cannot be empty']);
        //     exit;
        // }
        $note_row = $this->Customersportal_model->saveNote($task_id, $note);
        if(!$note_row){
            echo json_encode(['result'=>false,'reason'=>'2 Notes cannot be empty']);
            exit;
        }

        echo json_encode(['result'=>true,'note'=>$note_row]);
        exit;
    }

    public function deleteNote()
    {
        $this->load->model("Notes_model");
        $note_id = $this->input->post("note_id");
        $affected_rows = $this->Notes_model->deleteNote($note_id,( ($this->uri->segment(1) == 'portal') ? 'customer' : 'user') );
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

    public function rejectTask()
    {
        $task_id = $this->input->post("task_id");
        $reject_reason = $this->input->post("reject_reason");
        $result = $this->Customersportal_model->rejectTask($task_id,$reject_reason);
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
        echo json_encode(['result'=>($result) ? true : false]);
        exit;
    }

    public function processForgotPassword()
    {
        $token = $this->uri->segment(4);
        $email = $this->uri->segment(5);
        $this->Customersportal_model->processForgotPassword($token, $email);
        echo "<html><div style='font-family:Arial; font-size:18px;margin:10vh auto;width:400px;border:1px solid #ccc;padding:25px 50px;'>You will soon receive an email with your newly generated password. <br><button style='padding:5px 10px; text-align:center; text-transform:uppercase;background-color:#4c4c4c;color:#fff;margin-top:20px;' onclick=\"window.close()\">Close</button></div><script>
        function closeTab() {
            window.close();
            // fallback if close() is blocked
            setTimeout(() => {
                alert(\"If this tab did not close automatically, please close it manually.\");
            }, 500);
        }
        </script></html>";
        // redirect(base_url("portal/customers/signin?email={$email}"));
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
        $status = $this->Customersportal_model->submitTask();
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

        $ct = $this->db->select("count(id) as ct")->from("customer_access")->where(array("email"=>$email,"status"=>"1"))->get()->row()->ct;
        
        if($ct>0){
            echo json_encode(['result'=>false,'reason'=>"Email already used"]);
            exit;
        }

        $result = $this->Customersportal_model->createUserAccess($name, $email, $password);

        echo json_encode($result);

        exit;
    }

    public function addUserAccess()
    {
        $uuid = trim($this->input->post("uuid"));
        $name = trim($this->input->post("name"));
        $email = trim($this->input->post("email"));
        $phone = trim($this->input->post("phone"));
        $password = trim($this->input->post("password"));
        $country_code = trim($this->input->post("country_code"));
        $admin = $this->input->post("admin") ? 1 : 0; // from back-office Add User; portal users get 0
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
        if(strlen($password)<2){
            $php_errormsg .= "Please enter a valid country code<br>";
            $valid = false;
        }

        if(!$valid){
            echo json_encode(['result'=>false,'reason'=>$php_errormsg]);
            exit;
        }

        $ct = $this->db->select("count(id) as ct")
                    ->from("customer_access ca")
                    ->join("customers c","c.customer_id = ca.customer_id","left")
                    ->where(array(
                                "ca.email"      =>  $email,
                                "c.uuid"        =>  $uuid,
                                "ca.status"     =>  1
                            ))
                    ->get()->row()->ct;
        
        if($ct>0){
            echo json_encode(['result'=>false,'reason'=>"Email already used"]);
            exit;
        }

        $result = $this->Customersportal_model->addUserAccess($uuid, $name, $email, $phone, $password, $country_code, $admin);

        echo json_encode($result);

        exit;
    }

    public function updateUserAccess()
    {
        $access_id = (int) $this->input->post("access_id");
        $name = trim($this->input->post("name"));
        $email = trim($this->input->post("email"));
        $phone = trim($this->input->post("phone"));
        $country_code = trim($this->input->post("country_code"));
        $admin = $this->input->post("admin") ? 1 : 0;
        $password = trim($this->input->post("password")); // optional; if empty, keep current

        if ($access_id <= 0 || strlen($name) < 4 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['result' => false, 'reason' => 'Invalid access id, name (4 chars min), or email.']);
            exit;
        }

        $result = $this->Customersportal_model->updateUserAccess($access_id, $name, $email, $phone, $country_code, $admin, $password);
        echo json_encode($result);
        exit;
    }

    public function removeAccess()
    {
        $userId = $this->input->post("user_id");
        $name = $this->input->post("name");
        $result = $this->Customersportal_model->removeAccess($userId);
        echo json_encode(array(
            "result"    =>  $result
        ));
        exit;
    }

    public function getNotesForTask()
    {

    }
    
}