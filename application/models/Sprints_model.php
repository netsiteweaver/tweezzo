<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sprints_model extends CI_Model{

    public function fetchAll($customer_id="",$order_by="",$order_dir="asc",$page=1,$rows_per_page=10,$active_filter="active")
    {
        if( (empty($page)) || ($page <= 0) ) $page =1;
        $offset = ( ($page-1)*$rows_per_page);  

        $task_count_sql = '(SELECT COUNT(t.id) FROM tasks t '
            . 'INNER JOIN sprints ss ON ss.id = t.sprint_id AND ss.active = \'1\' '
            . 'INNER JOIN projects pp ON pp.id = ss.project_id AND pp.active = \'1\' '
            . 'INNER JOIN customers cc ON cc.customer_id = pp.customer_id AND cc.active = \'1\' '
            . 'WHERE t.sprint_id = s.id AND t.status = \'1\' AND t.closed = \'0\') AS task_count';
        $this->db->select('s.*, p.name AS project_name, c.company_name, c.full_name, c.customer_id AS customer_id, ' . $task_count_sql, false);
        $this->db->from('sprints s');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('s.status',1);
        $this->db->where('p.active',1);
        $this->db->where('c.active',1);
        if ($active_filter === 'active') {
            $this->db->where('s.active', 1);
        } elseif ($active_filter === 'inactive') {
            $this->db->where('s.active', 0);
        }
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

    public function totalRows($customer_id="",$active_filter="active")
    {
        $this->db->select('count(1) as ct');
        $this->db->from('sprints s');
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where('s.status',1);
        $this->db->where('p.active',1);
        $this->db->where('c.active',1);
        if ($active_filter === 'active') {
            $this->db->where('s.active', 1);
        } elseif ($active_filter === 'inactive') {
            $this->db->where('s.active', 0);
        }
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
        // Match tasks/listing: only active, non-closed tasks (exclude deleted/closed rows).
        $this->db->select('COUNT(t.id) AS ct', false);
        $this->db->from('sprints s');
        $this->db->join('tasks t', 't.sprint_id = s.id', 'inner');
        $this->db->where('s.uuid', $uuid);
        $this->db->where('t.status', '1');
        $this->db->where('t.closed', '0');
        $row = $this->db->get()->row();

        return $row ? (int) $row->ct : 0;
    }

    public function save($data)
    {
        $this->load->model("System_model");

        $this->db->set('name',$data['name']);
        $this->db->set('code',!empty($data['code']) ? trim($data['code']) : null);
        $this->db->set('project_id',$data['project_id']);

        if(empty($data['uuid'])){
            $uuid = gen_uuid();
            $this->db->set('uuid',$uuid);
            $this->db->set('created_by',$_SESSION['user_id']);
            $this->db->set('created_on',date('Y-m-d H:i:s'));
            $this->db->set("status",'1');
            $this->db->set("active",'1');
            $this->db->insert('sprints');

            $members = $this->System_model->getParam("notification_create_sprints",true);
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
            $this->db->set("active",isset($_POST['active'])?'1':'0');
            $this->db->set('code',!empty($data['code']) ? trim($data['code']) : null);
            $this->db->where('uuid',$data['uuid']);
            $this->db->update('sprints');

            $sprint = $this->db->select("id")->from("sprints")->where("uuid",$data['uuid'])->get()->row();
            if(!empty($sprint)){
                $isReady = isset($data['ready_for_validation']) ? 1 : 0;
                $this->setValidationReadiness((int)$sprint->id, $isReady, (int)$_SESSION['user_id']);
            }
        }
        return array('result'=>true,'data'=>$data);

    }

    public function getValidationReminderState($sprint_id)
    {
        $row = $this->db->select("*")
                        ->from("sprint_validation_reminders")
                        ->where("sprint_id", (int)$sprint_id)
                        ->get()->row();

        if(empty($row)){
            return (object)[
                "sprint_id" => (int)$sprint_id,
                "ready_for_validation" => 0,
                "ready_set_on" => null,
                "ready_set_by" => null,
                "last_sent_on" => null
            ];
        }

        return $row;
    }

    public function setValidationReadiness($sprint_id, $is_ready, $user_id = null)
    {
        $sprint_id = (int)$sprint_id;
        $is_ready = (int)$is_ready;
        $user_id = empty($user_id) ? null : (int)$user_id;
        $now = date("Y-m-d H:i:s");

        $existing = $this->db->select("id")->from("sprint_validation_reminders")->where("sprint_id", $sprint_id)->get()->row();
        if(empty($existing)){
            $this->db->set("sprint_id", $sprint_id);
            $this->db->set("ready_for_validation", $is_ready);
            $this->db->set("ready_set_on", $is_ready ? $now : null);
            $this->db->set("ready_set_by", $is_ready ? $user_id : null);
            $this->db->set("last_sent_on", null);
            $this->db->insert("sprint_validation_reminders");
            return $this->db->affected_rows() > 0;
        }

        $this->db->set("ready_for_validation", $is_ready);
        $this->db->set("ready_set_on", $is_ready ? $now : null);
        $this->db->set("ready_set_by", $is_ready ? $user_id : null);
        if($is_ready === 0){
            $this->db->set("last_sent_on", null);
        }
        $this->db->where("sprint_id", $sprint_id);
        $this->db->update("sprint_validation_reminders");
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Disable "ready for validation" when nothing is left in staging for this sprint,
     * so automated reminders cannot target an empty queue (same filters as the cron).
     */
    public function clearValidationReadinessIfNoStagingTasks($sprint_id)
    {
        $sprint_id = (int) $sprint_id;
        if ($sprint_id <= 0) {
            return;
        }

        $reminder = $this->db->select("svr.id")
            ->from("sprint_validation_reminders svr")
            ->join("sprints s", "s.id = svr.sprint_id")
            ->join("projects p", "p.id = s.project_id")
            ->join("customers c", "c.customer_id = p.customer_id")
            ->where("svr.sprint_id", $sprint_id)
            ->where("svr.ready_for_validation", 1)
            ->where("s.status", "1")
            ->where("s.active", "1")
            ->where("p.active", "1")
            ->where("c.active", "1")
            ->get()
            ->row();

        if (empty($reminder)) {
            return;
        }

        $stagingCount = (int) $this->db->select("COUNT(t.id) AS c", false)
            ->from("tasks t")
            ->where("t.sprint_id", $sprint_id)
            ->where("t.status", "1")
            ->where("t.closed", "0")
            ->where("t.stage", "staging")
            ->get()
            ->row()->c;

        if ($stagingCount === 0) {
            $this->setValidationReadiness($sprint_id, 0, null);
        }
    }

    public function toggleActive($uuid)
    {
        $row = $this->db->select('id, active')
            ->from('sprints')
            ->where('uuid', $uuid)
            ->where('status', 1)
            ->get()
            ->row();
        if (empty($row)) {
            return ['result' => false, 'reason' => 'Sprint not found'];
        }
        $new = ((string)$row->active === '1') ? '0' : '1';
        $this->db->set('active', $new)->where('uuid', $uuid)->update('sprints');
        return ['result' => true, 'active' => $new];
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
        $this->db->select("*")->from("sprints")->where("project_id",$project_id)->where(["status"=>1,"active"=>1]);
        return $this->db->get()->result();
    }

    public function lookup()
    {
        $this->db->select("s.id,s.name,s.project_id");
        $this->db->from("sprints s");
        $this->db->join('projects p','p.id=s.project_id','left');
        $this->db->join('customers c','c.customer_id=p.customer_id','left');
        $this->db->where(["s.status"=>"1","s.active"=>"1","p.active"=>"1","c.active"=>"1"]);
        $this->db->order_by('s.name');
        return $this->db->get()->result();
    }

    public function lookup2($project_id)
    {
        $this->db->select("s.*,p.name project_name, c.company_name customer_name")
                ->from("sprints s")
                ->join("projects p","p.id=s.project_id")
                ->join("customers c","c.customer_id=p.customer_id")
                ->where("s.status",1)
                ->where("s.active",1)
                ->where("p.active",1)
                ->where("c.active",1)
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

    public function codeExists($project_id, $code, $exclude_uuid = "")
    {
        $code = trim((string)$code);
        if (empty($project_id) || $code === "") return false;

        $this->db->select("id");
        $this->db->from("sprints");
        $this->db->where("project_id", $project_id);
        $this->db->where("code", $code);

        if (!empty($exclude_uuid)) {
            $this->db->where("uuid !=", $exclude_uuid);
        }

        return (bool)$this->db->get()->row();
    }

}