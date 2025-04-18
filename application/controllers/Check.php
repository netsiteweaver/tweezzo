<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Check extends MY_Controller {

	public $data;

	public function index() {
		$this->data['page_title'] = "Permission Alert";
        $this->data["content"]=$this->load->view("/security/access_prohibited",$this->data,true);
        $this->load->view("/layouts/default",$this->data);            
	}
	
}