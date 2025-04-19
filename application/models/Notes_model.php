<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notes_model extends CI_Model{

    public function getById($note_id, $type)
    {
        $this->db->select('tn.notes, t.name task_name, t.task_number, c.company_name, p.id project_id, p.name project_name, s.name sprint_name');
        $this->db->from('tasks t');
        $this->db->join('sprints s','s.id=t.sprint_id','left');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->join("task_notes tn","tn.task_id = t.id");
        $this->db->where('tn.id',$note_id);
        $note = $this->db->get()->row();
        if(empty($note)){
            return false;
        }

        if($type == 'user'){
            $note->deleted_by = $this->db->select("id, name, email")->from("users")->where(array(
                'id'        =>  $_SESSION['user_id']
            ))->get()->row();
        }elseif($type == 'developer'){
            $note->deleted_by = $this->db->select("id, name, email")->from("users")->where(array(
                'id'        =>  $_SESSION['developer_id']
            ))->get()->row();
        }elseif($type == 'customer'){
            $note->deleted_by = $this->db->select("customer_id as id, company_name as name, email")->from("customers")->where(array(
                'customer_id'        =>  $_SESSION['customer_id']
            ))->get()->row();
        }

        return $note;
    }
}