<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Developers extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->data['flash_danger'] = getFlashMessage("danger");
        $this->data['flash_success'] = getFlashMessage("success");
        $this->data['flash_warning'] = getFlashMessage("warning");
        $this->data['flash_info'] = getFlashMessage("info");

        $this->load->library("migration");
        $this->load->model("system_model");
        $this->load->model("Developersportal_model");
        $this->data['logo'] = $this->system_model->getParam("logo");
        $this->data['logoDark'] = $this->system_model->getParam("logo-dark");
        $this->data['page_title'] = "";

        $this->load->model("Quotes_model");
        $this->data['random_quote'] = $this->Quotes_model->getRandomQuote();

        if(isset($_SESSION['developer_id'])){
            $this->data['customers'] = $this->Developersportal_model->getMyCustomers($_SESSION['developer_id']);
            $this->data['projects'] = $this->Developersportal_model->getMyProjects($_SESSION['developer_id']);
            $this->data['sprints'] = $this->Developersportal_model->getMySprints($_SESSION['developer_id']);
        }
        if(isset($_SESSION['developer_id'])){
            $this->load->model("timesheets_model");
            $this->data['running_task'] = $this->timesheets_model->getRunningTasks();
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
        
        $result = $this->Developersportal_model->authenticate($_POST);

        // Check if result is an array (error response for suspended/inactive accounts)
        if(is_array($result)) {
            echo json_encode($result);
            return;
        }

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

    /**
     * Global search: tasks by task ref, name, etc. Returns JSON for navbar typeahead.
     */
    public function searchTasks()
    {
        if (empty($_SESSION['developer_id'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => false, 'reason' => 'Not signed in']));
            return;
        }
        $q = $this->input->get('q');
        $q = is_string($q) ? trim($q) : '';
        if ($q === '') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'tasks' => []]));
            return;
        }
        $rows = $this->Developersportal_model->searchTasks($_SESSION['developer_id'], $q, 15);
        $tasks = [];
        foreach ($rows as $task) {
            $tasks[] = [
                'uuid'         => $task->uuid,
                'task_ref'     => isset($task->task_ref) ? $task->task_ref : '',
                'name'         => $task->name,
                'section'      => isset($task->section) ? $task->section : '',
                'project_name' => isset($task->project_name) ? $task->project_name : '',
                'sprint_name'  => isset($task->sprint_name) ? $task->sprint_name : '',
                'view_url'     => base_url('portal/developers/view?task_uuid=' . rawurlencode($task->uuid)),
            ];
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(['result' => true, 'tasks' => $tasks]));
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

        $this->load->model("Developersportal_model");
        $this->data['tasks'] = $this->Developersportal_model->getMyTasks($_SESSION['developer_id'],$this->input->get("customer_id"),$this->input->get("project_id"),$this->input->get("sprint_id"),$this->input->get("stage"),$this->input->get("order_by"),$this->input->get("order_dir"),1,999,$this->input->get('notes_only'),$this->input->get('due_in_days'));
        // debug($this->data['tasks']);
        $this->data['myProjects'] = $this->Developersportal_model->getMyProjects($_SESSION['developer_id']);
        $this->data['myCustomers'] = $this->Developersportal_model->getMyCustomers($_SESSION['developer_id']);
        $this->data['mySprints'] = $this->Developersportal_model->getMySprints($_SESSION['developer_id']);

        $this->data['stages'] = ['new','in_progress','testing','staging','validated','completed','on_hold','stopped'];

        $this->data['content'][] = $this->load->view("/portal/developers/tasks",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);

    }

    public function timer_start()
    {
        $this->load->model("timesheets_model");
        $check = $this->timesheets_model->checkRunningTask();

        if(!empty($check)) {
            echo json_encode(array(
                "result"    =>  false,
                "reason"    =>  "Another task's timer is running for you: <br><strong>{$check[0]->task_name}</strong><br>Please stop the timer and retry."
            ));
            exit;
        }else{
            echo json_encode(array(
                "result"    =>  true
            ));
        }
    }

    public function timer_insert()
    {
        $this->load->model("timesheets_model");
        $result = $this->timesheets_model->startTimer();

        echo json_encode(array(
            "result"    =>  true,
            "task_uuid" =>  $result['task_uuid']
        ));
        exit;
    }

    public function timer_stop()
    {
        $this->load->model("timesheets_model");
        $result = $this->timesheets_model->stopTimer();
        
        if($result['result']){
            echo json_encode(array(
                "result"    =>  true,
                "task_uuid" =>  $result['task_uuid']
            ));
        }else{
            echo json_encode(array(
                "result"    =>  false,
                "reason"     =>  $result['reason']
            ));
        }

        exit;
    }

    public function view()
    {
        $this->data['page_title'] = "View";

        $task_uuid = $this->input->get("task_uuid");
        $this->load->model("Developersportal_model");
        $this->data['task'] = $this->Developersportal_model->getSingleTask($task_uuid);

        if(empty($this->data['task'])) {
            flashSuccess("assas");
            redirect(base_url("portal/developers/tasks?error=Task not found"));
        }

        $this->data['portal_developer_id'] = (int) $_SESSION['developer_id'];
        $this->data['task_poll_snapshot'] = $this->Developersportal_model->getDeveloperTaskPollSnapshot($task_uuid);
        $this->load->model('Customersportal_model');
        if (!empty($this->data['task']->files)) {
            foreach ($this->data['task']->files as $f) {
                $f->lightbox_caption = $this->Customersportal_model->task_image_lightbox_caption($f);
            }
        }

        $this->data['content'][] = $this->load->view("/portal/developers/view_partials/timer",$this->data,true);
        $this->data['content'][] = $this->load->view("/portal/developers/view1",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
        
    }

    public function myCustomers()
    {
        $this->data['page_title'] = "Customers";

        $this->load->model("Developersportal_model");
        $this->data['myCustomers'] = $this->Developersportal_model->getMyCustomers($_SESSION['developer_id']);
        // $this->load->view("/portal/developers/myCustomers",$this->data);
        $this->data['content'][] = $this->load->view("/portal/developers/myCustomers",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
        
    }

    public function myProjects()
    {
        $this->data['page_title'] = "Projects";

        $this->load->model("Developersportal_model");
        $this->data['myProjects'] = $this->Developersportal_model->getMyProjects($_SESSION['developer_id']);
        // $this->load->view("/portal/developers/myProjects",$this->data);
        $this->data['content'][] = $this->load->view("/portal/developers/myProjects",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
    }

    public function mySprints()
    {
        $this->data['page_title'] = "Sprints";
        
        $this->load->model("Developersportal_model");
        $this->data['mySprints'] = $this->Developersportal_model->getMySprints($_SESSION['developer_id']);
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
            redirect(base_url("portal/developers/view?task_uuid=".$task_uuid));
        }
        
        $this->load->model("Developersportal_model");
        $this->Developersportal_model->saveNotes($task_id, $notes, $public);
        redirect(base_url("portal/developers/view?task_uuid=".$task_uuid));
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
        $result = $this->Developersportal_model->forgotPassword($email);
        echo json_encode(['result'=>true]);
        exit;
    }

    public function processForgotPassword()
    {
        $token = $this->uri->segment(4);
        $email = $this->uri->segment(5);
        $this->Developersportal_model->processForgotPassword($token, $email);
        redirect(base_url("portal/developers/signin"));
    }

    public function moveStage()
    {
        $task_id = $this->input->post("task_id");
        $stage = $this->input->post("stage");

        // "validated" is the customer's call only - developers may never set it.
        if ($stage === 'validated') {
            echo json_encode([
                'result' => false,
                'reason' => 'Only the customer can move a task to Validated.'
            ]);
            exit;
        }

        $this->Developersportal_model->moveStage($task_id,$stage);

        // An optional note may be attached to the move; it is saved as a normal task note.
        $note = trim((string) $this->input->post("note"));
        $note_saved = false;
        if ($note !== '') {
            $public = ($this->input->post("display_type") == null) ? 'private' : 'public';
            $this->Developersportal_model->saveNotes($task_id, $note, $public);
            $note_saved = true;
        }

        echo json_encode(['result'=>true, 'note_saved'=>$note_saved]);
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

    public function submitTask()
    {
        $this->load->model("Developersportal_model");
        $status = $this->Developersportal_model->submitTask($_POST);
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

    /**
     * JSON snapshot for live task updates on developer task view.
     */
    public function taskPoll()
    {
        if (empty($_SESSION['developer_id'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => false, 'reason' => 'Not signed in']));
            return;
        }
        $uuid = $this->input->get('task_uuid');
        if ($uuid === null || $uuid === '') {
            echo json_encode(['result' => false, 'reason' => 'Missing task_uuid']);
            exit;
        }
        $snap = $this->Developersportal_model->getDeveloperTaskPollSnapshot($uuid);
        if ($snap === false) {
            echo json_encode(['result' => false, 'reason' => 'Not found']);
            exit;
        }
        echo json_encode(['result' => true, 'snapshot' => $snap]);
        exit;
    }

    public function deleteTaskImage()
    {
        if (empty($_SESSION['developer_id'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['result' => false, 'reason' => 'Not signed in']));
            return;
        }
        $id = (int) $this->input->post('task_image_id');
        $out = $this->Developersportal_model->deleteDeveloperTaskImage($id);
        echo json_encode($out);
        exit;
    }

    public function uploadFiles()
    {
        $uuid = $this->input->post("uuid");
        $dev_id = (int) $_SESSION['developer_id'];
        $task_row = $this->db->select('t.id')
            ->from('tasks t')
            ->join('task_user tu', 'tu.task_id = t.id AND tu.user_id = ' . $dev_id, 'inner')
            ->where('t.uuid', $uuid)
            ->where('t.status', '1')
            ->where('t.closed', '0')
            ->get()->row();
        if (empty($task_row)) {
            flashDanger('Task not found or you are not assigned to it.');
            redirect(base_url('portal/developers/tasks'));
            return;
        }
        $task_id = (int) $task_row->id;
        $this->load->model("files_model");
        $uploadedFiles = [];
        if($_FILES['file1']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file1","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file2']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file2","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file3']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file3","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file4']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file4","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file5']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file5","uploads/tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);

        foreach($uploadedFiles as $file){
            $this->db->insert("task_images",array(
                'uuid'          =>  gen_uuid(),
                'task_id'       =>  $task_id,
                'created_by'    =>  $_SESSION['developer_id'],
                'created_on'    =>  date('Y-m-d H:i:s'),
                'file_name'     =>  $file['file_name'],
                'thumb_name'    =>  $file['image_thumb'],
                'file_size'     =>  $file['file_size'],
                'image_width'   =>  $file['image_width'],
                'image_height'  =>  $file['image_height'],
                'image_type'    =>  $file['image_type'],
                'status'        =>  '1',
                'created_by_customer'   => null,
                'uploaded_by_user_type' =>  'developer'
            ));
        }

        if (!empty($uploadedFiles)) {
            $this->load->model('Tasks_model');
            $author = $this->db->select('email, name')->from('users')->where([
                'id'        => (int) $_SESSION['developer_id'],
                'user_type' => 'developer',
            ])->get()->row();
            $this->Tasks_model->notifyTaskFilesUploaded($task_id, $uploadedFiles, $author, 'developer');
        }

        redirect(base_url("portal/developers/view?task_uuid=".$uuid));

    }

    public function timesheets()
    {
        $sessionKey = 'portal_developer_timesheets_params';
        $get = $this->input->get();

        if (!empty($get)) {
            $saved = [
                'from'        => !empty($get['from']) ? $get['from'] : date('Y-m-01'),
                'to'          => !empty($get['to']) ? $get['to'] : date('Y-m-t'),
                'method'      => !empty($get['method']) ? $get['method'] : 'start_time',
                'customer_id' => isset($get['customer_id']) ? (string) $get['customer_id'] : '',
                'sprint_id'   => isset($get['sprint_id']) ? (string) $get['sprint_id'] : '',
                'project_id'  => isset($get['project_id']) ? (string) $get['project_id'] : '',
            ];
            $_SESSION[$sessionKey] = $saved;
        } else {
            $saved = isset($_SESSION[$sessionKey]) ? $_SESSION[$sessionKey] : null;
            if (!empty($saved) && is_array($saved)) {
                $qs = http_build_query($saved);
                redirect(base_url('portal/developers/timesheets?' . $qs));
                return;
            }
        }

        $this->data['page_title'] = "Timesheets";

        $this->load->model("Timesheets_model");

        $this->data['myCustomers'] = $this->Developersportal_model->getMyCustomers($_SESSION['developer_id'],);
        $this->data['myProjects'] = $this->Developersportal_model->getMyProjects($_SESSION['developer_id']);
        $this->data['mySprints'] = $this->Developersportal_model->getMySprints($_SESSION['developer_id']);

        $this->data['rows'] = $this->Timesheets_model->getTaskByDeveloperId($_SESSION['developer_id'], $this->input->get("customer_id"), $this->input->get("project_id"), $this->input->get("sprint_id"));

        $this->data['content'][] = $this->load->view("/portal/developers/timesheets",$this->data,true);
        $this->load->view("/portal/developers/shared/layout",$this->data);
    }

    public function delete_timesheet()
    {
        if(($this->uri->segment(5) == 'confirm')){
            $this->load->model("Timesheets_model");
            $check = $this->Timesheets_model->deleteTimesheet($this->uri->segment(4));
            redirect("portal/developers/timesheets");
        }else{
            $this->data['page_title'] = "Delete Timesheet";

            $this->load->model("Timesheets_model");

            $this->data['timesheet'] = $this->Timesheets_model->getSingle($this->uri->segment(4));
            if(empty($this->data['timesheet'])){
                flashDanger("Timesheet not found");
                // flashSuccess("Timesheet not found");
                // flashWarning("Timesheet not found");
                // flashInfo("Timesheet not found");
                redirect("portal/developers/timesheets");
            }
            $this->data['content'][] = $this->load->view("/portal/developers/delete_timesheet",$this->data,true);
            $this->load->view("/portal/developers/shared/layout",$this->data);
        }
        
    }
}