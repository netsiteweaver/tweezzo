<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notes_model extends CI_Model{

    public function getById($note_id, $type="")
    {
        $this->db->select('tn.notes, t.name task_name, t.description task_description, t.task_number, c.company_name, p.id project_id, p.name project_name, s.name sprint_name, COALESCE(c2.company_name, u.name) as author, COALESCE(c2.email, u.email) as author_email');
        $this->db->from('tasks t');
        $this->db->join('sprints s','s.id=t.sprint_id','left');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->join("task_notes tn","tn.task_id = t.id");
        $this->db->join('customers c2','c.customer_id=tn.created_by_customer','left');
        $this->db->join('users u','u.id=tn.created_by','left');
        $this->db->where('tn.id',$note_id);
        $note = $this->db->get()->row();
        if(empty($note)){
            return false;
        }

        if(!empty($type)){
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
                    'customer_id'        =>  $_SESSION['customer_access_id']
                ))->get()->row();
            }
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
            $this->db->where("created_by_customer",$_SESSION['customer_access_id']);
        }
        $this->db->delete("task_notes");

        return $this->db->affected_rows();
    }

    public function outOfScope($note_id)
    {
        $this->load->model("Notes_model");
        $this->load->model("Email_model3");
        $this->load->model("system_model");

        $note = $this->getById($note_id);
        $emailData = [
            'note'          =>  $note,
            'logo'          =>  $this->system_model->getParam("logo"),
            // 'link'          =>  base_url('tasks/view/'.$data['uuid']),
            // 'link_label'    =>  'View Task',
            // 'stageColors'   =>  $stageColors
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/noteOutOfScope",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);

        $this->Email_model3->save($note->author_email,"Note Out Of Scope",$content);

        $notification_delete_notes = $this->system_model->getParam("notification_delete_notes",true);
        foreach($notification_delete_notes as $admin){
            $email = $this->db->select("email")->from("users")->where("id",$admin)->get()->row()->email;
            $this->Email_model3->save($email,"Note Out Of Scope",$content);
        }
        
        $this->db->set('out_of_scope','1');
        $this->db->set('out_of_scope_by',$_SESSION['user_id']);
        $this->db->set('out_of_scope_on','NOW()',false);
        
        $this->db->where("id",$note_id);
        $this->db->update("task_notes");

        return $this->db->affected_rows();
    }

    public function getNotesByUserId($id,$start_date="",$end_date="",$project_id="",$sprint_id="",$customer_id="")
    {
        $query = "select t.uuid task_uuid, COALESCE(u1.name, c.company_name) author, tn.notes, tn.created_on, t.name taskName, t.task_number taskNumber , t.`section` taskSection , s.id sprintId, s.name sprintName, p.id projectId, p.name projectName, c2.customer_id customerId, c2.company_name 
                from task_notes tn 
                left join users u1 ON u1.id = tn.created_by 
                left join customers c on c.customer_id = tn.created_by_customer 
                join tasks t on t.id = tn.task_id 
                join task_user tu on tu.task_id = t.id 
                JOIN sprints s on s.id = t.sprint_id 
                JOIN projects p on p.id = s.project_id 
                JOIN customers c2 ON c2.customer_id = p.customer_id 
                where tu.user_id = '$id'";
        if(!empty($start_date)) $query .= "and date(tn.created_on) >= '$start_date' ";
        if(!empty($end_date)) $query .= "and date(tn.created_on) <= '$end_date' ";
        if(!empty($project_id)) $query .= "and s.project_id = '$project_id' ";
        if(!empty($sprint_id)) $query .= "and t.sprint_id = '$sprint_id' ";
        if(!empty($customer_id)) $query .= "and c2.customer_id = '$customer_id' ";
                // -- AND tn.created_on >= CURDATE() - INTERVAL $interval DAY
        $query .= "ORDER BY tn.created_on DESC";
        // echo $query;die;
        $notes = $this->db->query($query)->result();
        return $notes;
    }

    public function getNotesByCustomerId($customer_id,$start_date="",$end_date="",$project_id="",$sprint_id="")
    {
        $query = "select DISTINCT tn.id, t.uuid task_uuid, COALESCE(u1.name, c.company_name) author, tn.notes, tn.created_on, t.name taskName, t.task_number taskNumber , t.`section` taskSection , s.id sprintId, s.name sprintName, p.id projectId, p.name projectName, c2.customer_id customerId, c2.company_name 
                from task_notes tn 
                left join users u1 ON u1.id = tn.created_by 
                left join customers c on c.customer_id = tn.created_by_customer 
                join tasks t on t.id = tn.task_id 
                join task_user tu on tu.task_id = t.id 
                JOIN sprints s on s.id = t.sprint_id 
                JOIN projects p on p.id = s.project_id 
                JOIN customers c2 ON c2.customer_id = p.customer_id 
                where c2.customer_id = '$customer_id'";
        if(!empty($start_date)) $query .= "and date(tn.created_on) >= '$start_date' ";
        if(!empty($end_date)) $query .= "and date(tn.created_on) <= '$end_date' ";
        if(!empty($project_id)) $query .= "and s.project_id = '$project_id' ";
        if(!empty($sprint_id)) $query .= "and t.sprint_id = '$sprint_id' ";
        // if(!empty($customer_id)) $query .= "and c2.customer_id = '$customer_id' ";
                // -- AND tn.created_on >= CURDATE() - INTERVAL $interval DAY
        $query .= "ORDER BY tn.created_on DESC";
        // echo $query;die;
        $notes = $this->db->query($query)->result();
        return $notes;
    }

}