<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transmissions extends MY_Controller {

    public $data;
    
    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("transmissions_model");
    }
    
    public function index()
    {
        $this->listing();
    }

    public function listing()
    {
        $this->data['transmissions'] = $this->transmissions_model->getAll(0,10);

        $this->load->view('common/headers',$this->data);
        $this->load->view('common/left-nav',$this->data);
        $this->load->view('transmissions/listing',$this->data);
        $this->load->view('common/footer',$this->data);
    }

}
