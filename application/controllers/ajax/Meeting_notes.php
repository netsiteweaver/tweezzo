<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_notes extends CI_Controller
{
    public function getAttendeesByCustomerId()
    {
        $customer_id = $this->input->post("customer_id");
        
        if (empty($customer_id) || $customer_id == 'other') {
            echo json_encode(array("result" => false, "emails" => []));
            exit;
        }

        // Query customer_access table for active records with matching customer_id
        $this->db->select("email");
        $this->db->from("customer_access");
        $this->db->where(array("customer_id" => $customer_id, "status" => "1"));
        $this->db->order_by("email");
        $query = $this->db->get();
        
        $emails = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if (!empty($row->email)) {
                    $emails[] = $row->email;
                }
            }
        }

        echo json_encode(array("result" => true, "emails" => $emails));
        exit;
    }
}

