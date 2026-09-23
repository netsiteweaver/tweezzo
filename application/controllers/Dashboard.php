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

            if( (isset($_SESSION['backoffice'])) && ($_SESSION['backoffice']) ){
                $this->addContent($this->load->view("/dashboard/backoffice",$this->data,true));
            }else{
                $this->load->model("dashboard_preferences_model");

                $blocks = $this->buildBlocks();
                $this->data['blocks'] = $this->dashboard_preferences_model->applyTo(
                    $blocks,
                    isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null
                );

                $this->addContent($this->load->view("/dashboard/customisable",$this->data,true));
            }

        }
        $this->load->view("/layouts/default",$this->data);

    }

    /**
     * The dashboard in its default order. Each block carries a stable key so a
     * user's saved layout survives blocks being added or reordered here later.
     *
     * @return array list of ['key','width','html']
     */
    private function buildBlocks()
    {
        $this->load->model("users_model");
        $this->load->model("customersportal_model");
        $this->load->model("developersportal_model");
        $this->load->model("Tasks_model");

        $blocks = array();

        $blocks[] = array(
            "key"   =>  "online_users",
            "width" =>  "col-md-12",
            "html"  =>  $this->load->view("/dashboard/blocks/online_users", array(), true)
        );

        foreach($this->counterItems() as $key => $item){
            $blocks[] = array(
                "key"   =>  $key,
                "width" =>  "col-lg-4 col-md-4 col-sm-6 col-xs-12",
                "html"  =>  $this->load->view("/dashboard/blocks/counter", array("item"=>$item), true)
            );
        }

        $blocks[] = array(
            "key"   =>  "latest_backoffice_access",
            "width" =>  "col-md-6",
            "html"  =>  $this->load->view("/dashboard/blocks/login_table", array(
                            "title"         =>  "Latest Back Office Access",
                            "header_class"  =>  "bg-yellow",
                            "rows"          =>  $this->users_model->get_login_history(20),
                            "show_username" =>  true
                        ), true)
        );

        $blocks[] = array(
            "key"   =>  "progress_per_client",
            "width" =>  "col-md-6",
            "html"  =>  $this->load->view("/dashboard/blocks/progress_per_client", array(
                            "task_progress" =>  $this->Tasks_model->getGeneralProgress()
                        ), true)
        );

        $blocks[] = array(
            "key"   =>  "latest_customer_access",
            "width" =>  "col-md-6",
            "html"  =>  $this->load->view("/dashboard/blocks/login_table", array(
                            "title"         =>  "Latest Customer Portal Access",
                            "header_class"  =>  "bg-orange",
                            "rows"          =>  $this->customersportal_model->get_login_history(20),
                            "show_username" =>  false
                        ), true)
        );

        $blocks[] = array(
            "key"   =>  "latest_developer_access",
            "width" =>  "col-md-6",
            "html"  =>  $this->load->view("/dashboard/blocks/login_table", array(
                            "title"         =>  "Latest Developer Portal Access",
                            "header_class"  =>  "bg-teal",
                            "rows"          =>  $this->developersportal_model->get_login_history(20),
                            "show_username" =>  false
                        ), true)
        );

        return $blocks;
    }

    /** @return array block_key => info-box definition */
    private function counterItems()
    {
        // Counts go through the listing models so each box matches the page it
        // links to: a project/sprint/task hanging off a deactivated customer or
        // project is hidden from the listing and must not be counted here either.
        $this->load->model("projects_model");
        $this->load->model("sprints_model");
        $this->load->model("Tasks_model");

        $items = array(
            "customers" => array(
                "label" => "Customers", "icon" => "fa-users", "class" => "bg-yellow",
                "count" => $this->db->select("count(1) as ct")->from("customers")->where(["status"=>"1","active"=>"1"])->get()->row()->ct,
                "link"  => "customers/listing"
            ),
            "developers" => array(
                "label" => "Developers", "icon" => "fa-users", "class" => "bg-orange",
                "count" => $this->db->select("count(1) as ct")->from("users")->where(["status"=>"1",'user_type'=>'developer'])->get()->row()->ct,
                "link"  => "developers/listing"
            ),
            "users" => array(
                "label" => "Users", "icon" => "fa-users", "class" => "bg-teal",
                "count" => $this->db->select("count(1) as ct")->from("users")->where(["status"=>"1",'user_type'=>'regular'])->get()->row()->ct,
                "link"  => "users/listing"
            ),
            "projects" => array(
                "label" => "Projects", "icon" => "fa-list", "class" => "bg-teal",
                "count" => $this->projects_model->totalRows("","","active"),
                "link"  => "projects/listing"
            ),
            "sprints" => array(
                "label" => "Sprints", "icon" => "fa-list", "class" => "bg-green",
                "count" => $this->sprints_model->totalRows("","active"),
                "link"  => "sprints/listing"
            ),
            "tasks_total" => array(
                "label" => "Total Tasks", "icon" => "fa-list", "class" => "bg-purple",
                "count" => $this->Tasks_model->totalRows(),
                "link"  => "tasks/listing"
            ),
        );

        $stages = array(
            "tasks_new"         => array("News Tasks",          "fa-list",                  "bg-blue",   "new"),
            "tasks_in_progress" => array("Tasks In Progress",   "fa-truck",                 "bg-purple", "in_progress"),
            "tasks_testing"     => array("Tasks being Testing", "fa-exclamation-triangle",  "bg-yellow", "testing"),
            "tasks_staging"     => array("Tasks on Staging",    "fa-question",              "bg-orange", "staging"),
            "tasks_validated"   => array("Validated Tasks",     "fa-check-square",          "bg-teal",   "validated"),
            "tasks_completed"   => array("Tasks Completed",     "fa-check",                 "bg-green",  "completed"),
            "tasks_on_hold"     => array("Tasks on Hold",       "fa-exclamation",           "bg-red",    "on_hold"),
        );

        foreach($stages as $key => $stage){
            list($label, $icon, $class, $stage_value) = $stage;
            $items[$key] = array(
                "label" => $label, "icon" => $icon, "class" => $class,
                "count" => $this->Tasks_model->totalRows("","","",array($stage_value)),
                "link"  => "tasks/listing?stage=" . $stage_value
            );
        }

        return $items;
    }

}
