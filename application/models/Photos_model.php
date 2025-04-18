<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Photos_model extends CI_Model{

    public function getByVehicleId($id)
    {
        $this->db->where("vehicle_id","$id");
        $this->db->order_by("display_order",'asc');
        return $this->db->get("tblvehiclephoto")->result();
    }


}