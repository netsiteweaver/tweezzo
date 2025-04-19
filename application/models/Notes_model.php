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

    public function deleteNote($note_id,$user_type)
    {
        $this->load->model("Notes_model");
        $this->load->model("Email_model3");
        $this->load->model("system_model");

        $note = $this->getById($note_id,$user_type);
        $emailData = [
            'type'          =>  $user_type,
            'note'          =>  $note,
            'logo'          =>  $this->system_model->getParam("logo"),
            // 'link'          =>  base_url('tasks/view/'.$data['uuid']),
            // 'link_label'    =>  'View Task',
            // 'stageColors'   =>  $stageColors
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/noteDeleted",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);

        $this->Email_model3->save($note->deleted_by->email,"Note Deleted",$content);

        $notification_delete_notes = $this->system_model->getParam("notification_delete_notes",true);
        foreach($notification_delete_notes as $admin){
            $email = $this->db->select("email")->from("users")->where("id",$admin)->get()->row()->email;
            $this->Email_model3->save($email,"Note Deleted",$content);
        }
        
        $this->db->where("id",$note_id);
        if($user_type == 'user'){
            $this->db->where("created_by",$_SESSION['user_id']);
        }elseif($user_type == 'developer'){
            $this->db->where("created_by",$_SESSION['developer_id']);
        }elseif($user_type == 'customer'){
            $this->db->where("created_by_customer",$_SESSION['customer_id']);
        }
        $this->db->delete("task_notes");

        return $this->db->affected_rows();
    }
}