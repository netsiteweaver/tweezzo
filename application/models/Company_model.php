<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Company_model extends CI_Model{

    public function get($fields="")
    {
        if(!empty($fields)){
            if(is_array($fields)){
                $allfields = "";
                foreach($fields as $field){
                    $allfields .= $field . ',';
                }
                $allfields = substr($allfields, 0, strlen($allfields)-1);
                $this->db->select($allfields);
            }else{
                $this->db->select($fields);
            }
        }
        return $this->db->get("company")->row();
    }

    public function getById($id)
    {
        $this->db->where("id","$id");
        return $this->db->get("locations")->row();
    }

    public function saveCompany($data)
    {
        $this->db->update("company",$data);
    }

}