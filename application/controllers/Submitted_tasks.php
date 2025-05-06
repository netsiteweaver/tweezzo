<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Submitted_tasks extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("Submitted_tasks_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("submitted_tasks","add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("submitted_tasks","edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("submitted_tasks","view");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("submitted_tasks","delete");
        $this->data['perms']['import'] = $this->accesscontrol_model->authorised("submitted_tasks","import");

        $this->data['qs'] = $_SERVER["QUERY_STRING"];

        // $this->data['companyInfo'] = $this->system_model->getCompanyInfo();
    }

    // public function add()
    // {
    //     //Access Control
    //     if(!isAuthorised(get_class(),"add")) return false;

    //     //Breadcrumbs
    //     $this->mybreadcrumb->add('Tasks', base_url('submitted_tasks/listing'));
    //     $this->mybreadcrumb->add('Add', base_url('submitted_tasks/add'));
    //     $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

    //     $this->load->model('Customers_model');
    //     $this->data['customers'] = $this->Customers_model->lookup();

    //     if(!empty($this->input->get('project_id')))
    //     {
    //         $this->load->model('Projects_model');
    //         $this->data['projects'] = $this->Projects_model->fetchSingleById($this->input->get('project_id'));
    //     }

    //     if(!empty($this->input->get('sprint_id')))
    //     {
    //         $this->load->model('Sprints_model');
    //         $this->data['sprints'] = $this->Sprints_model->fetchSingleById($this->input->get('sprint_id'));
    //     }

    //     $this->load->model("Developers_model");
    //     $this->data['developers'] = $this->Developers_model->lookup();

    //     $this->data["content"]=$this->load->view("/submitted_tasks/add",$this->data,true);
    //     $this->load->view("/layouts/default",$this->data);   
    // }

    // public function edit()
    // {
    //     //Access Control 
    //     if(!isAuthorised(get_class(),"edit")) return false;

    //     $task_uuid = $this->input->get("task_uuid");
    //     $this->data['task'] = $this->Submitted_tasks_model->fetchSIngle($task_uuid);

    //     $this->data['progress'] = ($this->data['task']->progress == 0) ? "bg-danger" : (($this->data['task']->progress == 100) ? "bg-success" : "bg-warning");

    //     //Breadcrumbs
    //     $this->mybreadcrumb->add('Tasks', base_url('submitted_tasks/listing'));
    //     $this->mybreadcrumb->add('Edit', base_url('submitted_tasks/edit'));
    //     $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

    //     $this->load->model('Customers_model');
    //     $this->data['customers'] = $this->Customers_model->lookup();

    //     $this->load->model('Projects_model');
    //     $this->data['projects'] = $this->Projects_model->lookup();

    //     $this->load->model('Sprints_model');
    //     $this->data['sprints'] = $this->Sprints_model->lookup();

    //     // $this->load->model('Users_model');
    //     // $this->data['users'] = $this->Users_model->lookup();

    //     $this->load->model("Developers_model");
    //     $this->data['developers'] = $this->Developers_model->lookup();

    //     // $this->loadStyleSheet("node_modules/lightbox2/dist/css/lightbox.min.css");
    //     // $this->loadScript("node_modules/lightbox2/dist/js/lightbox.min.js");

    //     $this->data["content"]=$this->load->view("/submitted_tasks/edit",$this->data,true);
    //     $this->load->view("/layouts/default",$this->data);   
    // }

    public function view()
    {
        //Access Control 
        if(!isAuthorised(get_class(),"view")) return false;

        $task_uuid = $this->input->get("task_uuid");
        $this->data['task'] = $this->Submitted_tasks_model->fetchSIngle($task_uuid);

        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('submitted_tasks/listing'));
        $this->mybreadcrumb->add('Edit', base_url('submitted_tasks/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model('Customers_model');
        $this->data['customers'] = $this->Customers_model->lookup();

        $this->load->model('Projects_model');
        $this->data['projects'] = $this->Projects_model->lookup();

        $this->load->model('Users_model');
        $this->data['users'] = $this->Users_model->lookup();

        // $this->loadStyleSheet("node_modules/lightbox2/dist/css/lightbox.min.css");
        // $this->loadScript("node_modules/lightbox2/dist/js/lightbox.min.js");
        $this->data['page_title'] = "View Task";

        $this->data["content"]=$this->load->view("/submitted_tasks/view",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    public function listing()
    {
        //Access Control        
        if(!isAuthorised(get_class(),"listing")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Tasks', base_url('submitted_tasks/listing'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Submitted Tasks List";

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
        $this->data['submitted_tasks'] = $this->Submitted_tasks_model->fetchAll($customer_id,$project_id,$sprint_id,$stage,$assigned_to,$order_by,$order_dir,$page,$per_page,"",$notes_only,$search_text);
        $total_rows = $this->Submitted_tasks_model->totalRows($customer_id,$project_id,$sprint_id,$stage,$assigned_to,$order_by,$order_dir,$notes_only,$search_text);
        $this->data['total_rows'] = $total_rows;
        $this->data['pagination'] = getPagination("submitted_tasks/listing",$total_rows,$per_page);

        if(!empty($customer_id)){
            if(isset($this->data['submitted_tasks'][0]->company_name)) $this->data['page_title'] .= " - " . $this->data['submitted_tasks'][0]->company_name;
        }
        if(!empty($project_id)){
            if(isset($this->data['submitted_tasks'][0]->project_name)) $this->data['page_title'] .= " - " . $this->data['submitted_tasks'][0]->project_name;
        }
        if(!empty($sprint_id)){
            if(isset($this->data['submitted_tasks'][0]->sprint_name)) $this->data['page_title'] .= " - " . $this->data['submitted_tasks'][0]->sprint_name;
        }

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

        $this->data["content"]=$this->load->view("/submitted_tasks/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);   
    }

    // public function email()
    // {
    //     $customer_id = $this->input->get('customer_id');
    //     $project_id = $this->input->get('project_id');
    //     $sprint_id = $this->input->get('sprint_id');
    //     $customer_email = $this->input->get('customer_email');
    //     $stage = $this->input->get('stage');
    //     $assigned_to = $this->input->get('assigned_to');
    //     $order_by = $this->input->get('order_by');
    //     $order_dir = $this->input->get('order_dir');
    //     $output = $this->input->get('output');
    //     $type = $this->input->get('type');

    //     //since we allow to override email when submitting, let us check if email is for customer, developer or other
    //     $isCustomer = $this->db->select("count(1) as ct")->from("customers")->where(["email"=>$customer_email,"status"=>"1"])->get()->row("ct");
    //     $isDeveloper = $this->db->select("count(1) as ct")->from("users")->where(["email"=>$customer_email,"user_type"=>"developer","status"=>"1"])->get()->row("ct");
    //     $isUser = $this->db->select("count(1) as ct")->from("users")->where(["email"=>$customer_email,"user_type !="=>"developer","status"=>"1"])->get()->row("ct");
    //     $link = "";
    //     $linkLabel = "";
    //     // if( ($isCustomer == 0) && ($isDeveloper == 0) && ($isUser == 0) ){
    //     //     $link = "";
    //     //     $linkLabel = "";
    //     // }else
    //     if($type == 'developer'){
    //         $link = "portal/developers/signin";
    //         $linkLabel = "Developer's Portal";
    //     }else{
    //         if($isCustomer > 0){
    //             $link = "portal/customers/signin";
    //             $linkLabel = "Customer's Portal";
    //         }elseif($isDeveloper > 0){
    //             $link = "portal/developers/signin";
    //             $linkLabel = "Developer's Portal";
    //         }elseif($isUser > 0){
    //             $link = "users/signin";
    //             $linkLabel = "Task Manager";
    //         }
    //     }
        
    //     // else{
    //     //     $link = "";
    //     //     $linkLabel = "";
    //     // }
    //     // $email = $this->db->query("select t.name task_name, t.task_number, s.name sprintName, p.name projectName, c.company_name, c.email from submitted_tasks t 
    //     //                     join sprints s on s.id = t.sprint_id 
    //     //                     join projects p on p.id = s.project_id 
    //     //                     join customers c on c.customer_id = p.customer_id 
    //     //                     where c.customer_id = $customer_id")->result();

    //     $submitted_tasks = $this->Submitted_tasks_model->fetchAll($customer_id,$project_id,$sprint_id,$stage,$assigned_to,$order_by,$order_dir,1,1,$output);
    //     // debug($submitted_tasks);
    //     // debug($this->data['submitted_tasks']);

    //     if(!empty($output)){
    //         $email = $customer_email;
    //         $this->load->model("Email_model3");
    //         $this->load->model("system_model");
    //         $emailData = [
    //             'submitted_tasks'     =>  $submitted_tasks,
    //             'logo'      =>  $this->system_model->getParam("logo"),
    //             'link'      =>  base_url($link)."?email=".$email,
    //             'link_label'=>  $linkLabel
    //         ];
    //         $content = $this->load->view("_email/header",$emailData, true);
    //         $content .= $this->load->view("_email/taskListToClient",$emailData, true);
    //         $content .= $this->load->view("_email/footer",[], true);
    //         // $path = realpath(".");
    //         // $h = fopen($path . "/data/taskListSent_".date('YmdHis').".html",'w');
    //         // if($h){
    //         //     fwrite($h,$content);
    //         //     fclose($h);    
    //         // }else{
    //         //     die('cannot create file');
    //         // }
    //         $this->Email_model3->save($email,"Task List Progress",$content);
    //     }
    // }

    public function convertToTask()
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
        if($_FILES['file1']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file1","uploads/submitted_tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file2']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file2","uploads/submitted_tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file3']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file3","uploads/submitted_tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file4']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file4","uploads/submitted_tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);
        if($_FILES['file5']['error'] == 0) $uploadedFiles[] = $this->files_model->uploadImage("file5","uploads/submitted_tasks/",['width'=>200,'height'=>200,'thumb_name'=>'thumb']);

        $data = $this->input->post();
        $response = $this->Submitted_tasks_model->save($data,$uploadedFiles);
        if($response['result']== false){
            flashDanger($response['reason']);
            redirect(base_url("submitted_tasks/listing?".$this->input->post('qs')));
            return;
        }
        if(!empty($this->input->post('add_more'))){
            flashSuccess("Task ".$data['task_number']." has been added successfully");
            redirect(base_url("submitted_tasks/add?".$this->input->post("qs").'&add_more=1'));
        }else{
            flashSuccess("Task ".$data['task_number']." has been added successfully");
            redirect(base_url("submitted_tasks/listing?".$this->input->post('qs')));
        }
        
    }

    public function delete()
    {
        //Access Control
        if(!isAuthorised(get_class(),"delete")) return false;

        $uuid = $this->input->post('uuid');
        $affected_rows = $this->Submitted_tasks_model->delete($uuid);

        echo json_encode(array(
            "result"    =>  true,
            "affected_rows" =>  $affected_rows
        ));
    }

    public function index()
    {
        $this->listing();
    }

}
