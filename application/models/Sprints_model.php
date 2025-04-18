<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sprints_model extends CI_Model{

    public function fetchAll($customer_id="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10)
    {
        if( (empty($page)) || ($page <= 0) ) $page =1;
        $offset = ( ($page-1)*$rows_per_page);  

        $this->db->select('s.*,p.name project_name, c.company_name,c.full_name');
        $this->db->from('sprints s');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('s.status',1);
        if(!empty($customer_id)) $this->db->where('c.customer_id',$customer_id);
        if(!empty($order_by)) {
            $this->db->order_by($order_by,$order_dir);
        }else{
            $this->db->order_by('s.name');
        }
        $this->db->limit($rows_per_page,$offset);
        $users = $this->db->get()->result();
        return $users;
    }

    public function totalRows($customer_id="")
    {
        $this->db->select('count(1) as ct');
        $this->db->from('sprints s');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('s.status',1);
        if(!empty($customer_id)) $this->db->where('c.customer_id',$customer_id);
        return $this->db->get()->row('ct');
    }

    public function fetchSingle($uuid){
        $this->db->select('s.*');
        $this->db->from('sprints s');
        $this->db->where('s.uuid',$uuid);
        $this->db->where('s.status',1);
        $sprints = $this->db->get()->row();
        return $sprints;
    }

    public function fetchSingleById($id){
        $this->db->select('s.*');
        $this->db->from('sprints s');
        $this->db->where('s.id',$id);
        $this->db->where('s.status',1);
        $sprint = $this->db->get()->result();
        return $sprint;
    }

    public function getAttachedTasks($uuid)
    {
        return $this->db->query("select count(1) as ct from sprints s join tasks t on	 t.sprint_id = s.id where s.uuid = '$uuid'")->row()->ct;
    }

    public function save($data)
    {
        $this->load->model("System_model");

        $this->db->set('name',$data['name']);
        $this->db->set('project_id',$data['project_id']);

        if(empty($data['uuid'])){
            $uuid = gen_uuid();
            $this->db->set('uuid',$uuid);
            $this->db->set('created_by',$_SESSION['user_id']);
            $this->db->set('created_on',date('Y-m-d H:i:s'));
            $this->db->insert('sprints');

            $members = $this->System_model->getParam("notification_create_task",true);
            $author = $this->db->select("*")->from("users")->where("id",$_SESSION['user_id'])->get()->row();
            $sprint = $this->db->select("s.*,p.name project_name, c.company_name, u.name author_name, u.email author_email")
                                ->from("sprints s")
                                ->join("projects p","p.id=s.project_id","left")
                                ->join("customers c","c.customer_id=p.customer_id","left")
                                ->join("users u","u.id=p.created_by","left")
                                ->where("s.uuid",$uuid)->get()->row();

            foreach($members as $m){
                $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();

                $this->load->model("Email_model3");
                
                $emailData = [
                    'title'         =>  'New Sprint Created',
                    'sprint'        =>  $sprint,
                    // 'data'          =>  $data,
                    // 'author'        =>  $author,
                    'logo'          =>  $this->system_model->getParam("logo"),
                    'link'          =>  base_url('sprints/view/'.$uuid),
                    'link_label'    =>  'View Project'
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/sprintCreatedOrUpdated",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);

                $this->Email_model3->save($user->email,"New Sprint Created",$content);
                
            }
        }else{
            $this->db->where('uuid',$data['uuid']);
            $this->db->update('sprints');
        }
        return array('result'=>true,'data'=>$data);

    }

    public function delete($uuid)
    {
        $this->db->set("status","0");
        $this->db->where("uuid",$uuid);
        $this->db->update("sprints");
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
            $this->db->insert('sprints',$data);
        }
        fclose($handle);
    }

    public function getByProjectId($project_id)
    {
        $this->db->select("*")->from("sprints")->where("project_id",$project_id)->where("status",1);
        return $this->db->get()->result();
    }

    public function lookup()
    {
        return $this->db->select("id,name,project_id")->from("sprints")->order_by('name')->where("status","1")->get()->result();
    }

    public function lookup2($project_id)
    {
        $this->db->select("s.*,p.name project_name, c.company_name customer_name")
                ->from("sprints s")
                ->join("projects p","p.id=s.project_id")
                ->join("customers c","c.customer_id=p.customer_id")
                ->where("s.status",1)
                ->order_by("c.company_name,p.name,s.name");
        if(!empty($project_id)) $this->db->where("s.project_id",$project_id);
        return $this->db->get()->result();
    }

    public function getProjectInfo($sprint_id)
    {
        return $this->db->query("select s.name sprintName, p.name projectName, c.company_name customerName from sprints s 
                                    left join projects p on p.id = s.project_id 
                                    left join customers c on c.customer_id = p.customer_id 
                                    where s.id = $sprint_id")->row();
    }

}