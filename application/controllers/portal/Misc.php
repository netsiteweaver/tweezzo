<?php

class Misc extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsersByTaskUuid()
    {
        $uuid = $this->input->get("taskUuid");
        if(empty($uuid)) {
            echo json_encode(array(
                "result"        =>  false,
                "reason"        =>  'No UUID received'
            ));
            exit;
        }
        $this->load->model("Users_model");
        $result = $this->Users_model->getUsersByTaskUUID($uuid);

        echo json_encode(array(
            "result"        =>  true,
            "customer"      =>  $result['customer'],
            "developers"    =>  $result['developers'],
            "admins"        =>  $result['admins'],
        ));
        exit;
    }

}