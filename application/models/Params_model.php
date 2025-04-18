<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Makes_model extends CI_Model{

    public function getAll($offset=0,$rows=25)
    {
        $this->db->order_by("make");
        $query =$this->db->get("tblvehiclemake");//,$rows,$offset);
        $result = $query->result();
        return $result;

    }


    public function getById($id)
    {
        $this->db->where("id","$id");
        return $this->db->get("tblvehiclemake")->row();
    }

}