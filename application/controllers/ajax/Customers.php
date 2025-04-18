<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends CI_Controller
{
    public function get()
    {
        $id = $this->input->post("customer_id");
        $searchTerm = $this->input->post("searchTerm");
        $random = $this->input->post("random");
        $this->load->model("customers_model");
        echo json_encode(array("result"=>true,"customers"=>$this->customers_model->fetch($id,$searchTerm,$random)));
        exit;
    }

    public function getHistory()
    {
        $customer_id = $this->input->post("customer_id");
        $this->load->model("customers_model");
        $orders = $this->customers_model->getHistory($customer_id);
        echo json_encode(array("result"=>true,"orders"=>$orders));
        exit;

    }

    public function save()
    {
        $this->load->model("customers_model");
        $result = $this->customers_model->save();

        echo json_encode($result);
        exit;
    }
}