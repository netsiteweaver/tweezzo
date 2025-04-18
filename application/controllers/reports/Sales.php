<?php defined('BASEPATH') or exit('No direct script access allowed');

class Sales extends MY_Controller
{

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method']       = $this->uri->segment(2, "index");
        $this->mybreadcrumb->add('Reports', base_url('reports/sales/index'));
        $this->load->model("accesscontrol_model");
        $this->load->model("sales_model");
        $this->load->model("system_model");
        $this->load->model("channels_model");
        $this->load->model("regions_model");
        $this->load->model("routes_model");
    }

    public function index()
    {
        //Access Control
        if (!isAuthorised("reports","sales")) return false;

        //Breadcrumbs
        $this->mybreadcrumb->add('Sales', base_url('reports/sales/index'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();
        $this->data['page_title'] = "Reports :: Orders";

        $rows_per_page = $this->system_model->getParam("rows_per_page");
        $start_date = $this->input->get("start_date");
        $end_date = $this->input->get("end_date");
        $channel = $this->input->get("channel");
        $stage = $this->input->get("stage");
        $deleted = strtolower(trim($this->input->get("deleted")));
        $page = $this->uri->segment(4, '1');

        $this->data['channels'] = $this->channels_model->fetchAllChannels();
        $this->data['regions'] = $this->regions_model->fetchAllRegions();
        $this->data['routes'] = $this->routes_model->fetchAllRoutes();

        $this->data["content"] = $this->load->view("/reports/sales/index", $this->data, true);
        $this->load->view("/layouts/default",$this->data);
    }

    public function fetch()
    {
        $start_date = $this->input->post("start_date");
        $end_date   = $this->input->post("end_date");
        $stockref   = $this->input->post("stockref");
        $description = $this->input->post("description");
        $color      = $this->input->post("color");
        $category   = $this->input->post("category");
        $channel    = $this->input->post("channel_id");
        $route      = $this->input->post("route_id");
        $region     = $this->input->post("region_id");
        $stage      = $this->input->post("stage");
        $search_text = $this->input->post("search_text");

        $filters = [];
        if(!empty($stockref)) $filters['pr.stockref'] = $stockref;
        if(!empty($description)) $filters['sd.description'] = $description;
        if(!empty($color)) $filters['cl.color_name'] = $color;
        if(!empty($channel)) $filters['s.channel_id'] = $channel;
        if(!empty($stockref)) $filters['pr.stockref'] = $stockref;
        if(!empty($category)) $filters['pc.category_name'] = $category;
        if(!empty($route)) $filters['t.route_id'] = $route;
        if(!empty($region)) $filters['r.id'] = $region;
        if(!empty($stockref)) $filters['pr.stockref'] = $stockref;

        $rows = $this->sales_model->get($start_date, $end_date, $stage, $channel, 'no', 1, 0, false, 5000, $filters, $search_text);

        echo json_encode(array("result"=>true,"rows"=>$rows));
        exit;
    }
}
