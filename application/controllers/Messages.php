<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Messages extends MY_Controller
{

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method']       = $this->uri->segment(2, "index");
        $this->mybreadcrumb->add('Messaging', base_url('messages/listing'));
        
        $this->load->model("accesscontrol_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("messages", "add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("messages", "edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("messages", "view");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("messages", "delete");
        $this->data['perms']['pdf'] = $this->accesscontrol_model->authorised("orders", "pdf");

        $this->load->model("messages_model");
    }

    // public function add()
    // {
    //     //Access Control
    //     if (!isAuthorised(get_class(), "add")) return false;

    //     //Breadcrumbs
    //     $this->mybreadcrumb->add('Add', base_url('messages/add'));
    //     $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
    //     $this->data['page_title'] = "Add Customer";

    //     $this->load->model("nationalities_model");
    //     $this->data['nationalities'] = $this->nationalities_model->get();

    //     $this->data["content"] = $this->load->view("/messages/add", $this->data, true);
    //     $this->load->view("/layouts/default",$this->data);
    // }

    // public function save()
    // {
    //     //Access Control
    //     if (!isAuthorised(get_class(), "add")) return false;

    //     $referer = $this->input->post("referer");
    //     $result = $this->messages_model->save();

    //     flashSuccess("Message saved successfully..");
    //     redirect(base_url(empty($referer)?"messages/listing":$referer));
    // }

    // public function edit()
    // {
    //     //Access Control 
    //     if (!isAuthorised(get_class(), "edit")) return false;

    //     $uuid = $this->uri->segment(3);
    //     $this->data['customer'] = $this->messages_model->get($uuid);

    //     if(empty($this->data['customer'])){
    //         flashDanger("Customer not found");
    //         redirect(base_url("messages/listing"));
    //     }

    //     $this->load->model("nationalities_model");
    //     $this->data['nationalities'] = $this->nationalities_model->get();

    //     //Breadcrumbs
    //     $this->mybreadcrumb->add('Edit', base_url('messages/edit'));
    //     $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

    //     $this->data["content"] = $this->load->view("/messages/edit", $this->data, true);
    //     $this->load->view("/layouts/default",$this->data);
    // }

    public function view()
    {
        //Access Control 
        if (!isAuthorised(get_class(), "view")) return false;

        $this->data['page_title'] = "View Message & Order";

        $uuid = $this->uri->segment(3);
        $this->data['order'] = $this->messages_model->getByUuid($uuid);
        if(empty($this->data['order'])){
            $this->errorPage("Order Not Found",base_url("messages/listing/".$this->input->get("page")));
        }

        //Breadcrumbs
        $this->mybreadcrumb->add('View', base_url('messages/view'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->loadScript("assets/js/pages/orders_view.js");

        // $this->load->model("system_model");
        // $this->data["barcode"] = $this->system_model->getParam("barcode");

        // $this->loadJs(base_url("assets/js/JsBarcode.code128.min.js"));
        // $this->loadJs(base_url("assets/AdminLTE-3.2.0/plugins/ekko-lightbox/ekko-lightbox.min.js"));
        // $this->loadStyleSheet(base_url("assets/AdminLTE-3.2.0/plugins/ekko-lightbox/ekko-lightbox.css"));

        // if($this->data['result']->message->order_id==NULL){
        //     $this->data["content"] = $this->load->view("/messages/message", $this->data, true);
        // }else{
            $this->data["content"] = $this->load->view("/orders/view1", $this->data, true);
        // }
        // debug($this->data["content"]);
        $this->load->view("/layouts/default",$this->data);
    }

    // public function update()
    // {
    //     //Access Control        
    //     if (!isAuthorised(get_class(), "edit")) return false;

    //     $postedData = $this->input->post();


    //     $this->load->model('messages_model');
    //     $result = $this->messages_model->update($postedData);

    //     if ($result['result']) {
    //         flashSuccess("Message has been successfully updated");
    //         redirect(base_url('messages/listing'));
    //     } else {

    //         flashSuccess("Error in updating Customer");
    //         redirect(base_url('messages/edit'));
    //     }
    // }

    public function listing()
    {
        //Access Control        
        if (!isAuthorised(get_class(), "listing")) return false;

        $this->load->model("system_model");
        $rows_per_page = $this->system_model->getParam("rows_per_page");
        $page = $this->uri->segment(3, '1');

        $this->data['messages'] = $this->messages_model->get(null,$page,$rows_per_page);
        $total_records = $this->messages_model->total_records();
        //Breadcrumbs
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Messaging";

        $this->data['pagination'] = getPagination("messages/listing",$total_records,$rows_per_page);

        $this->data["content"] = $this->load->view("/messages/listing", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }

    public function get()
    {
        $this->load->model("messages_model");
        $id = $this->input->get("id");
        $unreadMessages = $this->messages_model->getByRecipientId($id);

        echo json_encode(array("result"=>true,"rows"=>count($unreadMessages),"messages"=>$unreadMessages));
        exit;
    }

    public function deleteReadMessages()
    {
        $readMessages = $this->db->select("uuid")->from("messages")->where(array("recipient_id"=>$_SESSION['user_id'],"read_on !="=>null,"status"=>"1"))->get()->result();
        $deletedRecords = array();
        foreach($readMessages as $m){
            $this->db->set("status","0")->where("uuid",$m->uuid)->update("messages");
            $deletedRecords[] =$m->uuid;
        }
        echo json_encode(array("result"=>true,"deletedRecords"=>$deletedRecords ));
    }
}
