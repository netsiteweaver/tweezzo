<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Documents_model extends CI_Model{

    public function getByVehicleId($id)
    {
    	// $this->db->select("*,tblvehicledocument.id as doc_id");
        $this->db->where("vehicle_id","$id");
        // $this->db->join("tbldocumentcategory","tbldocumentcategory.id = tblvehicledocument.document_category_id","left");

        return $this->db->get("tblvehicledocument")->result();
    }


}