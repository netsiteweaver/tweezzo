<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Customers extends MY_Controller
{

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method']       = $this->uri->segment(2, "index");
        $this->mybreadcrumb->add('Customers', base_url('customers/listing'));
        
        $this->load->model("accesscontrol_model");
        $this->data['perms']['add'] = $this->accesscontrol_model->authorised("customers", "add");
        $this->data['perms']['edit'] = $this->accesscontrol_model->authorised("customers", "edit");
        $this->data['perms']['view'] = $this->accesscontrol_model->authorised("customers", "view");
        $this->data['perms']['delete'] = $this->accesscontrol_model->authorised("customers", "delete");

        $this->load->model("customers_model");
    }

    public function index()
    {
        redirect(base_url('customers/listing'));
    }

    private function setFields($values=[])
    {
        $pswd = randomName(12);
        $this->data['fields'] = array(
            ['field_name'=>'uuid','type'=>'hidden','label'=>'','value'=>isset($values['uuid'])?$values['uuid']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
            ['field_name'=>'company_name','type'=>'text','label'=>'Company Name','value'=>isset($values['company_name'])?$values['company_name']:'','autofocus'=>true,'required'=>true,'placeholder'=>'Enter Company Name'],
            ['field_name'=>'address','type'=>'text','label'=>'Address','value'=>isset($values['address'])?$values['address']:'','autofocus'=>false,'required'=>false,'placeholder'=>'Enter Address'],
            ['field_name'=>'phone_number1','type'=>'text','label'=>'Phone 1','value'=>isset($values['phone_number1'])?$values['phone_number1']:'','autofocus'=>false,'required'=>false,'placeholder'=>'Enter Phone NUmber'],
            ['field_name'=>'phone_number2','type'=>'text','label'=>'Phone 2','value'=>isset($values['phone_number2'])?$values['phone_number2']:'','autofocus'=>false,'required'=>false,'placeholder'=>'Enter Phone NUmber'],
            ['field_name'=>'remarks','type'=>'textarea','label'=>'Remarks','value'=>isset($values['remarks'])?$values['remarks']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
            ['field_name'=>'full_name','type'=>'text','label'=>'Name','value'=>isset($values['full_name'])?$values['full_name']:'','autofocus'=>true,'required'=>false,'placeholder'=>'Enter Name of Customer'],
            ['field_name'=>'job_description','type'=>'text','label'=>'Job Description','value'=>isset($values['job_description'])?$values['job_description']:'','autofocus'=>true,'required'=>false,'placeholder'=>'Enter Job Description'],
            ['field_name'=>'email','type'=>'email','label'=>'Email','value'=>isset($values['email'])?$values['email']:'','autofocus'=>false,'required'=>true,'placeholder'=>'Enter Email'],
            // ['field_name'=>'brn','type'=>'text','label'=>'BRN','value'=>isset($values['brn'])?$values['brn']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
            // ['field_name'=>'vat','type'=>'text','label'=>'VAT','value'=>isset($values['vat'])?$values['vat']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
            // ['field_name'=>'password','type'=>'text','label'=>'Password','value'=>isset($values['password'])?$values['password']:$pswd,'autofocus'=>false,'required'=>true,'placeholder'=>'Enter Password For Portal Access']
        );
    }

    private function setFieldsEdit($values=[])
    {
        $pswd = randomName(12);
        $this->data['fields'] = array(
            ['field_name'=>'uuid','type'=>'hidden','label'=>'','value'=>isset($values['uuid'])?$values['uuid']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
            ['field_name'=>'company_name','type'=>'text','label'=>'Company Name','value'=>isset($values['company_name'])?$values['company_name']:'','autofocus'=>true,'required'=>true,'placeholder'=>'Enter Company Name'],
            ['field_name'=>'address','type'=>'text','label'=>'Address','value'=>isset($values['address'])?$values['address']:'','autofocus'=>false,'required'=>false,'placeholder'=>'Enter Address'],
            ['field_name'=>'phone_number1','type'=>'text','label'=>'Phone 1','value'=>isset($values['phone_number1'])?$values['phone_number1']:'','autofocus'=>false,'required'=>false,'placeholder'=>'Enter Phone NUmber'],
            ['field_name'=>'phone_number2','type'=>'text','label'=>'Phone 2','value'=>isset($values['phone_number2'])?$values['phone_number2']:'','autofocus'=>false,'required'=>false,'placeholder'=>'Enter Phone NUmber'],
            ['field_name'=>'full_name','type'=>'text','label'=>'Name','value'=>isset($values['full_name'])?$values['full_name']:'','autofocus'=>true,'required'=>false,'placeholder'=>'Enter Name of Customer'],
            // ['field_name'=>'job_description','type'=>'text','label'=>'Job Description','value'=>isset($values['job_description'])?$values['job_description']:'','autofocus'=>true,'required'=>false,'placeholder'=>'Enter Job Description'],
            ['field_name'=>'email','type'=>'email','label'=>'Email','value'=>isset($values['email'])?$values['email']:'','autofocus'=>false,'required'=>true,'placeholder'=>'Enter Email'],
            // ['field_name'=>'password','type'=>'text','label'=>'Password','value'=>isset($values['password'])?$values['password']:$pswd,'autofocus'=>false,'required'=>true,'placeholder'=>'Enter Password For Portal Access'],
            ['field_name'=>'remarks','type'=>'textarea','label'=>'Remarks','value'=>isset($values['remarks'])?$values['remarks']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
            // ['field_name'=>'brn','type'=>'text','label'=>'BRN','value'=>isset($values['brn'])?$values['brn']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
            // ['field_name'=>'vat','type'=>'text','label'=>'VAT','value'=>isset($values['vat'])?$values['vat']:'','autofocus'=>false,'required'=>false,'placeholder'=>''],
        );
    }

    public function add()
    {
        //Access Control
        if (!isAuthorised(get_class(), "add")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Add', base_url('customers/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Add Customer";

        $this->setFieldsEdit();

        $this->data["content"] = $this->load->view("/customers/add", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }

    public function save()
    {
        //Access Control
        if (!isAuthorised(get_class(), "add")) return false;

        $referer = $this->input->post("referer");
        $result = $this->customers_model->save();

        if(empty($result['reason'])) {
            flashSuccess("Customers saved successfully..");
            redirect(base_url(empty($referer)?"customers/listing":$referer));
        }else{
            flashDanger("Error saving record. Reason:" . $result['reason']);
            redirect(base_url(empty($referer)?"customers/listing":$referer));
        }


    }

    public function edit()
    {
        //Access Control 
        if (!isAuthorised(get_class(), "edit")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['customer'] = $this->customers_model->get($uuid);

        if(empty($this->data['customer'])){
            flashDanger("Customer not found");
            redirect(base_url("customers/listing"));
        }

        // lockRecord("customers",$uuid,"customers/listing");

        // $editing = $this->general_model->lockTable("customers",$uuid);

        // if($editing['result'] == false) {
        //     flashDanger($editing['reason']);
        //     redirect(base_url("customers/listing"));
        // }

        $this->setFieldsEdit($values=array(
            "uuid"                  =>  $this->data['customer']->uuid,
            "company_name"          =>  $this->data['customer']->company_name,
            "address"               =>  $this->data['customer']->address,
            "phone_number1"         =>  $this->data['customer']->phone_number1,
            "phone_number2"         =>  $this->data['customer']->phone_number2,
            "remarks"               =>  $this->data['customer']->remarks,
            "full_name"             =>  $this->data['customer']->full_name,
            "email"                 =>  $this->data['customer']->email,
            // "brn"                   =>  $this->data['customer']->brn,
            // "vat"                   =>  $this->data['customer']->vat,
            // "password"               =>  $this->data['customer']->password
        ));

        $this->data['uuid'] = $this->data['customer']->uuid;

        $this->data["page_title"] = "Edit Customer";

        //Breadcrumbs
        $this->mybreadcrumb->add('Edit', base_url('customers/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"] = $this->load->view("/customers/edit", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }

    public function portal_access()
    {
        //Access Control 
        if (!isAuthorised(get_class(), "edit")) return false;

        $uuid = $this->uri->segment(3);
        // if(empty($uuid)){
        //     flashDanger("No parameters received (uuid)");
        //     redirect(base_url("customers/listing"));
        // }
        $this->data['customer'] = $this->customers_model->get($uuid);

        // if(empty($this->data['customer'])){
        //     flashDanger("Customer not found");
        //     redirect(base_url("customers/listing"));
        // }

        //Breadcrumbs
        $this->mybreadcrumb->add('Edit', base_url('customers/edit/'.$uuid));
        $this->mybreadcrumb->add('Portal Access', base_url('customers/portal_access'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"] = $this->load->view("/customers/portal_access", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }

    public function update_portal_access()
    {
        //Access Control 
        if (!isAuthorised(get_class(), "edit")) return false;

        $uuid = $this->input->post("uuid");
        $password = $this->input->post('password');

        if(strlen($password) < 4){
            redirect(base_url("customers/portal_access/$uuid?reason=Password must be at least 4 characters"));
        }

        $this->customers_model->update_portal_access($uuid, $password);
        redirect(base_url("customers/edit/$uuid"));

    }

    public function view()
    {
        //Access Control 
        if (!isAuthorised(get_class(), "view")) return false;

        $uuid = $this->uri->segment(3);
        $this->data['customer'] = $this->customers_model->getCustomerByUuid($uuid);
        $this->data['history'] = $this->customers_model->salesHistory($uuid);
        //Breadcrumbs
        $this->mybreadcrumb->add('View', base_url('customers/view'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->load->model("regions_model");
        $this->data['regions'] = $this->regions_model->getAll();

        $this->data["content"] = $this->load->view("/customers/view", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }

    public function update()
    {
        //Access Control        
        if (!isAuthorised(get_class(), "edit")) return false;

        $postedData = $this->input->post();


        $this->load->model('customers_model');
        $result = $this->customers_model->save($postedData);

        if ($result['result']) {
                flashSuccess("Customers has been successfully updated");
                redirect(base_url('customers/listing'));
        } else {

            flashSuccess("Error updating Customer");
            redirect(base_url('customers/edit'));
        }
    }

    public function listing()
    {
        //Access Control        
        if (!isAuthorised(get_class(), "listing")) return false;

        $this->load->model("system_model");
        $this->data['rows_per_page'] = empty($this->input->get("display"))?$this->system_model->getParam("rows_per_page"):$this->input->get("display");
        $page = $this->uri->segment(3, '1');

        $this->data['customers'] = $this->customers_model->get(null,$page,$this->data['rows_per_page'],$this->input->get('search_text'));
        $total_records = $this->customers_model->total_records($this->input->get('search_text'));

        //Breadcrumbs
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Customers";

        $this->data['pagination'] = getPagination("customers/listing",$total_records,$this->data['rows_per_page']);

        $this->data["content"] = $this->load->view("/customers/listing", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }

    public function getCustomerDetailsForSalesById()
    {
        $id = $this->input->post("id");
        $customer = $this->db->select('*')->from('customers')->where('customer_id',$id)->get()->row();
        $routes = $this->db->select("rr.route_id,rt.description")
                        ->from("routes_regions as rr")
                        ->join("routes as rt","rt.route_id=rr.route_id")
                        ->where("rr.region_id",$customer->region)
                        ->get()->result();

        $sql = $this->SqlQry_model->getSalesPerCustomer($id);
        $sales = $this->db->query($sql)->result();

        echo json_encode(array("result" => true, "customer"=>$customer,"routes"=>$routes,"sales"=>$sales));
    }

    public function quick_save()
    {
        $result = $this->customers_model->quick_save();
        echo json_encode($result);
        exit;
    }

    public function getById()
    {
        $id = $this->input->post("id");
        $customer = $this->db->select("*")
                            ->from("customers c")
                            ->where(array("status"=>"1","customer_id"=>$id))
                            ->get()
                            ->row();
        $this->output
                ->set_content_type("application/json")
                ->set_output(json_encode(array("result"=>true,"customer"=>$customer)));
    }

    public function getHistoryById()
    {
        $id = $this->input->post("id");
        $history = $this->db->select("o.created_date,o.uuid,o.order_date,o.amount,o.discount_pct,o.discount,o.amount,o.deposit,o.delivery_datetime,o.document_number,o.trial_date,
                            st.name stage,
                            dp1.name delivery_store,
                            dp2.name store,
                            u.name agent
                            ")
                            ->from("orders o")
                            ->join("stages st","st.id=o.stage_id","left")
                            ->join("departments dp1","dp1.id=o.delivery_store_id","left")
                            ->join("departments dp2","dp2.id=o.department_id","left")
                            ->join("users u","u.id=o.created_by","left")
                            ->where(array("o.status"=>"1","o.customer_id"=>$id))
                            ->order_by("order_date","desc")
                            ->get()
                            ->result();
        $this->output
                ->set_content_type("application/json")
                ->set_output(json_encode(array("result"=>true,"history"=>$history,"rows"=>count($history))));
    }

    public function fetch()
    {
        $this->load->model("customers_model");
        $customers = $this->customers_model->fetch($this->input->post("uuid"));
        echo json_encode(array("result"=>true,"customers"=>$customers));
        exit;
    }

    public function info()
    {
        //Access Control        
        if (!isAuthorised(get_class(), "delete")) return false;

        $uuid = $this->input->post("uuid");
        $info = $this->customers_model->info($uuid);

        echo json_encode(array(
            "result"=>true,
            "info"=>$info
        ));
        exit;
    }

    public function delete()
    {
        //Access Control        
        if (!isAuthorised(get_class(), "delete")) return false;

        $uuid = $this->input->post("uuid");
        $result = $this->customers_model->delete($uuid);

        echo json_encode($result);
        exit;
    }
}
