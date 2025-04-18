<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Projects_model extends CI_Model{

    public function fetchAll($customer_id="",$stage="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10)
    {
        if( (empty($page)) || ($page <= 0) ) $page =1;
        $offset = ( ($page-1)*$rows_per_page);  

        $this->db->select('p.*,c.company_name,c.full_name');
        $this->db->from('projects p');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('p.status',1);
        if(!empty($customer_id)) $this->db->where('p.customer_id',$customer_id);
        if(!empty($stage)) $this->db->where('p.stage',$stage);
        if(!empty($order_by)) {
            $this->db->order_by($order_by,$order_dir);
        }else{
            $this->db->order_by('p.name');
        }
        $this->db->order_by('p.created_on','asc');
        $this->db->limit($rows_per_page,$offset);
        $users = $this->db->get()->result();
        return $users;
    }

    public function totalRows($customer_id="",$stage="")
    {
        $this->db->select('count(1) as ct');
        $this->db->from('projects p');
        $this->db->where('p.status',1);
        if(!empty($customer_id)) $this->db->where('p.customer_id',$customer_id);
        if(!empty($stage)) $this->db->where('p.stage',$stage);
        return $this->db->get()->row('ct');
    }

    public function fetchSingle($uuid){
        $this->db->select('p.*');
        $this->db->from('projects p');
        $this->db->where('p.uuid',$uuid);
        $this->db->where('p.status',1);
        $task = $this->db->get()->row();
        return $task;
    }

    public function fetchSingleById($id){
        $this->db->select('p.*');
        $this->db->from('projects p');
        $this->db->where('p.id',$id);
        $this->db->where('p.status',1);
        $task = $this->db->get()->result();
        return $task;
    }

    public function save($data)
    {
        $this->load->model("System_model");

        $this->db->set('name',$data['name']);
        $this->db->set('description',$data['description']);
        $this->db->set('start_date',(!empty($data['start_date']))?$data['start_date']:null);
        $this->db->set('end_date',(!empty($data['end_date']))?$data['end_date']:null);
        $this->db->set('customer_id',$data['customer_id']);

        if(empty($data['uuid'])){
            $uuid = gen_uuid();
            $this->db->set('uuid',$uuid);
            $this->db->set('created_by',$_SESSION['user_id']);
            $this->db->set('created_on',date('Y-m-d H:i:s'));
            $this->db->insert('projects');

            $members = $this->System_model->getParam("notification_create_tasks",true);
            $author = $this->db->select("*")->from("users")->where("id",$_SESSION['user_id'])->get()->row();
            $project = $this->db->select("p.*,c.company_name, u.name author_name, u.email author_email")
                                ->from("projects p")
                                ->join("customers c","c.customer_id=p.customer_id","left")
                                ->join("users u","u.id=p.created_by","left")
                                ->where("p.uuid",$uuid)->get()->row();

            foreach($members as $m){
                $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();

                $this->load->model("Email_model3");
                
                $emailData = [
                    'title'         =>  'New Project Created',
                    'project'       =>  $project,
                    // 'data'          =>  $data,
                    // 'author'        =>  $author,
                    'logo'          =>  $this->system_model->getParam("logo"),
                    'link'          =>  base_url('projects/view/'.$uuid),
                    'link_label'    =>  'View Project'
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/projectCreatedOrUpdated",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);

                $this->Email_model3->save($user->email,"New Project Created",$content);
                
            }
        }else{
            $this->db->where('uuid',$data['uuid']);
            $this->db->update('projects');
        }
        return array('result'=>true,'data'=>$data);

    }

    public function notes($data)
    {
        $this->db->set('task_id',$data['task_id']);
        $this->db->set('notes',$data['notes']);
        $this->db->set('created_by',$_SESSION['user_id']);
        $this->db->set('created_on',date('Y-m-d H:i:s'));
        $this->db->insert('task_notes');
        return array('result'=>true,'data'=>$data);
    }

    public function delete($uuid)
    {
        $this->db->set("status","0");
        $this->db->where("uuid",$uuid);
        $this->db->update("projects");
        return $this->db->affected_rows();
    }

    public function process_import($customer_id)
    {
        $this->load->model("files_model");
        $data = $this->files_model->uploadCSV("file");
        if(!is_array($data)){
            return $data;
        }
        $handle = fopen($data['full_path'], "r");
        //get header
        $header = fgetcsv($handle);
        $task_prefix = '02';
        $task_count = 1;
        while (($row = fgetcsv($handle)) !== FALSE) {
            if($row[15]=='DONE'){
                $stage = 'completed';
            }elseif($row[15]=='IN PROGRESS'){
                $stage = 'in_progress';
            }elseif($row[15]=='TO DO'){
                $stage = 'new';
            }else{
                $stage = 'new';
            }
            $data = array(
                'uuid'          =>  gen_uuid(), 
                'name'          =>  $row[1],
                'description'   =>  $row[1],
                'task_number'   =>  $task_prefix.'.'.str_pad($task_count++, 2, '0', STR_PAD_LEFT),
                'sprint'        =>  '2502',
                'progress'      =>  ($stage=='completed') ? 100 : 0,
                'stage'         =>  $stage,
                'customer_id'     =>  $customer_id,
                'created_by'    =>  $_SESSION['user_id'],
                'created_on'    =>  date('Y-m-d H:i:s')
            );
            $this->db->insert('projects',$data);
        }
        fclose($handle);
    }

    public function lookup($customer_id="")
    {
        $this->db->select('p.*,c.company_name,c.full_name, c.customer_id');
        $this->db->from('projects p');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('p.status',1);
        $this->db->order_by('p.name','asc');
        if(!empty($customer_id)) $this->db->where(array("p.customer_id"=>$customer_id));
        return $this->db->get()->result();
    }

    public function getByCustomerId($customer_id)
    {
        $this->db->select("*")->from("projects")->where("customer_id",$customer_id)->where("status",1);
        return $this->db->get()->result();
    }

}