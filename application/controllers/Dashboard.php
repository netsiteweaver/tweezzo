<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public $data;

    public function __construct()
    {
        parent::__construct();

        $this->load->model("accesscontrol_model");
        $this->data['perms']['dashboard'] = $this->accesscontrol_model->authorised("settings","dashboard");

        $this->data['stage_classes'] = ['bg-blue','bg-maroon','bg-purple','bg-lime','bg-red',"bg-orange","bg-yellow","bg-green","bg-teal","bg-olive","bg-navy",'bg-blue','bg-maroon','bg-purple','bg-lime','bg-red',"bg-orange","bg-yellow","bg-green","bg-teal","bg-olive","bg-navy"];
    }

    public function index()
    {
        //Access Control
        if(!isAuthorised(get_class(),"index")) return false;

        $this->load->model("general_model");

        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data['page_title'] = "Home";

        if($_SESSION['user_level'] == 'Normal'){
            $this->data['perms'] = array(
                "OrdersAdd"             =>  $this->accesscontrol_model->authorised("orders","add"),
                "OrdersListing"         =>  $this->accesscontrol_model->authorised("orders","listing"),
                "CustomersListing"      =>  $this->accesscontrol_model->authorised("customers","listing"),
                "CustomersAdd"          =>  $this->accesscontrol_model->authorised("customers","add"),
                "MessagesListing"       =>  $this->accesscontrol_model->authorised("messages","listing")
            );
            $this->addContent($this->load->view("/dashboard/normal_user",$this->data,true));
        }else{
            $this->data['dashboardItems'][] = array(

                array(
                    "label"     =>  "Customers",
                    "icon"      =>  "fa-users",
                    "class"     =>  "bg-yellow",
                    "count"     =>  $this->db->select("count(1) as ct")->from("customers")->where("status","1")->get()->row()->ct,
                    "link"      =>  "customers/listing"
                ),

                array(
                    "label"     =>  "Developers",
                    "icon"      =>  "fa-users",
                    "class"     =>  "bg-orange",
                    "count"     =>  $this->db->select("count(1) as ct")->from("users")->where(["status"=>"1",'user_type'=>'developer'])->get()->row()->ct,
                    "link"      =>  "developers/listing"
                ),
    
                array(
                    "label"     =>  "Users",
                    "icon"      =>  "fa-users",
                    "class"     =>  "bg-teal",
                    "count"     =>  $this->db->select("count(1) as ct")->from("users")->where(["status"=>"1",'user_type'=>'regular'])->get()->row()->ct,
                    "link"      =>  "users/listing"
                ),
    
            );

            $this->data['dashboardItems'][] = array(

                array(
                    "label"     =>  "Projects",
                    "icon"      =>  "fa-list",
                    "class"     =>  "bg-teal",
                    "count"     =>  $this->db->select("count(1) as ct")->from("projects")->where(["status"=>"1"])->get()->row()->ct,
                    "link"      =>  "projects/listing"
                ),
    
                array(
                    "label"     =>  "Sprints",
                    "icon"      =>  "fa-list",
                    "class"     =>  "bg-green",
                    "count"     =>  $this->db->select("count(1) as ct")->from("sprints")->where(["status"=>"1"])->get()->row()->ct,
                    "link"      =>  "sprints/listing"
                ),

                array(
                    "label"     =>  "Total Tasks",
                    "icon"      =>  "fa-list",
                    "class"     =>  "bg-purple",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1"])->get()->row()->ct,
                    "link"      =>  "tasks/listing"
                )
            );

            $this->data['dashboardItems'][] = array(

                array(
                    "label"     =>  "News Tasks",
                    "icon"      =>  "fa-list",
                    "class"     =>  "bg-blue",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1","stage"=>"new"])->get()->row()->ct,
                    "link"      =>  "tasks/listing?stage=new"
                ),

                array(
                    "label"     =>  "Tasks In Progress",
                    "icon"      =>  "fa-truck",
                    "class"     =>  "bg-purple",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1","stage"=>"in_progress"])->get()->row()->ct,
                    "link"      =>  "tasks/listing?stage=in_progress"
                ),

                array(
                    "label"     =>  "Tasks being Testing",
                    "icon"      =>  "fa-exclamation-triangle",
                    "class"     =>  "bg-yellow",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1","stage"=>"testing"])->get()->row()->ct,
                    "link"      =>  "tasks/listing?stage=testing"
                ),

                array(
                    "label"     =>  "Tasks on Staging",
                    "icon"      =>  "fa-question",
                    "class"     =>  "bg-orange",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1","stage"=>"staging"])->get()->row()->ct,
                    "link"      =>  "tasks/listing?stage=staging"
                ),

                array(
                    "label"     =>  "Validated Tasks",
                    "icon"      =>  "fa-check-square",
                    "class"     =>  "bg-teal",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1","stage"=>"validated"])->get()->row()->ct,
                    "link"      =>  "tasks/listing?stage=validated"
                ),

                array(
                    "label"     =>  "Tasks Completed",
                    "icon"      =>  "fa-check",
                    "class"     =>  "bg-green",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1","stage"=>"completed"])->get()->row()->ct,
                    "link"      =>  "tasks/listing?stage=completed"
                ),
    
                array(
                    "label"     =>  "Tasks on Hold",
                    "icon"      =>  "fa-exclamation",
                    "class"     =>  "bg-red",
                    "count"     =>  $this->db->select("count(1) as ct")->from("tasks")->where(["status"=>"1","stage"=>"on_hold"])->get()->row()->ct,
                    "link"      =>  "tasks/listing?stage=on_hold"
                )    
            );

            if( (isset($_SESSION['backoffice'])) && ($_SESSION['backoffice']) ){
                $this->addContent($this->load->view("/dashboard/backoffice",$this->data,true));
            }else{
                $this->load->model("users_model");
                $this->load->model("customersportal_model");
                $this->load->model("developersportal_model");
                $this->data['latest_logins'] = $this->users_model->get_login_history(10);
                $this->data['latest_customer_access'] = $this->customersportal_model->get_login_history(10);
                $this->data['latest_developer_access'] = $this->developersportal_model->get_login_history(10);
                $this->data['customers'] = $this->db->select("count(1) as ct")->from("customers")->where("status","1")->get()->row("ct");
                $this->addContent($this->load->view("/dashboard/summary",$this->data,true));
                $this->addContent($this->load->view("/dashboard/misc",$this->data,true));
            }
            
        }
        $this->load->view("/layouts/default",$this->data);

    }

}