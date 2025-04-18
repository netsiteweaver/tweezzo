<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Migrate extends CI_Controller {

    public function index() {
        $this->load->library('migration');

        if (!$this->migration->current()) {
            $err = $this->migration->error_string();
            echo json_encode(array("result" => false, "reason" => $err));
        } else {
            echo json_encode(array("result" => true));
        }
    }

    public function version() {
        $this->load->library('migration');

        $version = $this->uri->segment(3);

        if (!$this->migration->version($version)) {
            $err = $this->migration->error_string();
            echo json_encode(array("result" => false, "reason" => $err));
        } else {
            echo json_encode(array("result" => true));
        }
    }
}
