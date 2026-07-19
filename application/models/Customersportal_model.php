<?php

class Customersportal_model extends CI_Model
{
    var $data;

    public function authenticate($user_info) {
        $this->db->select("c.customer_id, c.company_name, ca.name, ca.email, ca.id customer_access_id");
        $this->db->from("customers c");
        $this->db->join("customer_access ca","ca.customer_id = c.customer_id");
        $this->db->where("ca.password", md5($user_info['password']), true );
        $this->db->where("ca.email", trim($user_info['email']));
        $this->db->where("c.status", '1');
        $this->db->where("c.active", '1');
        $this->db->where("ca.status", '1');
        if(!empty($user_info['customer_id'])) {
            $this->db->where("c.customer_id",$user_info['customer_id']);
        }
        $result = $this->db->get()->result();
        if(count($result) > 1){
            //authentication successful but email is associated with multiple customers
            return array(
                "result"    =>  false,
                "user"      =>  '',
                "customers" =>  $result
            );
        }elseif(count($result)==1){
            //authentication successful
            return array(
                "result"    =>  true,
                "user"      =>  $result,
                "customers" =>  ''
            );
            // $this->recordSignIn($result, trim($user_info['email']));
        }else{
            //authentication failed
            return array(
                "result"    =>  false,
                "user"      =>  '',
                "customers" =>  ''
            );
        }
        
        
    }

    private function recordSignIn($customer,$email)
	{
		$this->load->library("user_agent");

        if(!empty($customer)){
            $this->db->set('last_login',date('Y-m-d H:i:s'));
            $this->db->where('customer_id',$customer->customer_id);
            $this->db->update('customers');
        }

		$this->db->set("email",$email);
		$this->db->set("datetime",date('Y-m-d H:i:s'));
		$this->db->set("result",(empty($customer))?"FAILED":"SUCCESS");
		$this->db->set("ip",$this->input->ip_address());
		$this->db->set("os",$this->agent->platform());
		$this->db->set("type",'customer');
		$this->db->set("browser",$this->agent->browser().' '.$this->agent->version());
		$this->db->set("result_other",$this->agent->agent_string());
		$this->db->insert("portal_login_history");
	}

    public function getProjects($customer_access_id)
    {
        //get master customer id
        $customer_id = $this->db->select()->from("customer_access")->where("id",$customer_access_id)->get()->row()->customer_id;
        return $this->db->select("p.*,u.name createdBy, count(s.id) sprints_count")
                        ->from("projects p")
                        ->join("users u","u.id=p.created_by","")
                        ->join("sprints s","s.project_id=p.id","left")
                        ->join("customers c","c.customer_id = p.customer_id","left")
                        ->where(["p.status"=>'1',"p.active"=>1,"p.customer_id"=>$customer_id])
                        ->where(["c.status"=>1,"c.active"=>1])
                        ->group_by("p.id")
                        ->order_by("p.name")
                        ->get()
                        ->result();
    }

    /**
     * Projects for this login that have at least one environment URL recorded, for the
     * "Environments" dropdown in the portal top bar. Same visibility rules as getProjects().
     */
    public function getProjectEnvironments($customer_access_id)
    {
        $ca = $this->db->select('customer_id')
            ->from('customer_access')
            ->where('id', (int) $customer_access_id)
            ->get()->row();
        if (empty($ca)) {
            return [];
        }

        return $this->db->select("p.id, p.name, p.staging_url, p.production_url")
                        ->from("projects p")
                        ->join("customers c","c.customer_id = p.customer_id","left")
                        ->where(["p.status"=>'1',"p.active"=>1,"p.customer_id"=>(int)$ca->customer_id])
                        ->where(["c.status"=>1,"c.active"=>1])
                        ->group_start()
                            ->where("COALESCE(p.staging_url,'') <> ''", null, false)
                            ->or_where("COALESCE(p.production_url,'') <> ''", null, false)
                        ->group_end()
                        ->order_by("p.name")
                        ->get()
                        ->result();
    }

    public function getSprints($project_id = '')
    {
        $ca = $this->db->select('customer_id')
            ->from('customer_access')
            ->where('id', (int) $_SESSION['customer_access_id'])
            ->get()->row();
        if (empty($ca)) {
            return [];
        }
        $customer_id = (int) $ca->customer_id;
        $project_id = (int) $project_id;

        $projectSql = $project_id > 0 ? ' AND s.project_id = ' . $project_id : '';

        // Match customer dashboard: weighted stage progress (not only stage = completed).
        // LEFT JOIN so sprints with zero tasks still appear at 0%.
        $sql = "
            SELECT
                s.id,
                s.name,
                u.name AS createdBy,
                p.name AS project_name,
                COUNT(t.id) AS tasks_count,
                SUM(CASE WHEN t.stage = 'completed' THEN 1 ELSE 0 END) AS completed_tasks,
                ROUND(COALESCE(AVG(
                    CASE t.stage
                        WHEN 'new' THEN 0
                        WHEN 'in_progress' THEN 20
                        WHEN 'testing' THEN 40
                        WHEN 'staging' THEN 60
                        WHEN 'validated' THEN 80
                        WHEN 'completed' THEN 100
                        WHEN 'on_hold' THEN 20
                        WHEN 'stopped' THEN 0
                        ELSE 0
                    END
                ), 0), 0) AS progress_pct
            FROM sprints s
            JOIN projects p ON p.id = s.project_id
            JOIN customers c ON c.customer_id = p.customer_id
            JOIN users u ON u.id = s.created_by
            LEFT JOIN tasks t ON t.sprint_id = s.id AND t.status = 1 AND t.closed = 0
            WHERE s.status = 1
              AND s.active = 1
              AND p.active = 1
              AND c.status = 1
              AND c.active = 1
              AND c.customer_id = {$customer_id}
              AND s.name <> 'Roadmap'
              {$projectSql}
            GROUP BY s.id, s.name, u.name, p.name
            ORDER BY s.name ASC
        ";

        return $this->db->query($sql)->result();
    }

    public function getTasks($sprint_id,$sort_by="task_number",$sort_dir="asc",$stages,$notes_only="")
    {
        //get master customer id
        $customer_id = $this->db->select()->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->customer_id;
        $this->db->select("t.*,u.name createdBy, p.name project_name, p.code project_code, s.name sprint_name, s.code sprint_code, count(tn.id) notes_count")
                        ->from("tasks t")
                        ->join("sprints s","s.id=t.sprint_id")
                        ->join("projects p","p.id=s.project_id")
                        ->join("customers c","c.customer_id=p.customer_id")
                        ->join("task_notes tn","tn.task_id=t.id","left")
                        ->join("users u","u.id=t.created_by","")
                        ->group_by("t.id");
        if($notes_only=="without") {
            $this->db->having("notes_count = 0");
        }elseif($notes_only=="with") {
            $this->db->having("notes_count > 0");
        }
        $this->db->where(["t.status"=>'1', "t.closed" => "0"]);
        $this->db->where(["s.status"=>1, "s.active"=>1]);
        $this->db->where(["p.active"=>1]);
        $this->db->where(["c.status"=>1, "c.active"=>1]);

        $stagesRaw = $this->input->get('stages');
        if ($stagesRaw === null || $stagesRaw === false || $stagesRaw === '') {
            $stagesRaw = is_string($stages) ? $stages : '';
        }
        $stagesArr = array_filter(explode(',', (string) $stagesRaw));

        if(!empty($stagesArr)){
            $this->db->where_in('t.stage',$stagesArr);
        }

        if(empty($sprint_id)){
            $this->db->where(["c.customer_id"=>$customer_id]);
        }else{
            $this->db->where(["t.sprint_id"=>$sprint_id]);
        }
        if(empty($sort_by)) {
            $this->db->order_by("task_number");
        }else{
            $this->db->order_by($sort_by,$sort_dir);
        }

        $tasks = $this->db->get()->result();
        if (!function_exists('task_ref')) {
            $CI =& get_instance();
            $CI->load->helper('general');
        }
        foreach ($tasks as $task) {
            $pc = isset($task->project_code) ? $task->project_code : null;
            $sc = isset($task->sprint_code) ? $task->sprint_code : null;
            $tn = isset($task->task_number) ? $task->task_number : '';
            $task->task_ref = $tn !== '' ? task_ref($pc, $sc, $tn) : '';
        }
        return $tasks;
    }

    /**
     * Aggregate stats for the customer portal landing dashboard.
     */
    public function getDashboardStats($customer_access_id)
    {
        $customer_access_id = (int) $customer_access_id;
        if ($customer_access_id <= 0) {
            return (object) [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'staging_tasks' => 0,
                'new_tasks' => 0,
                'in_progress_tasks' => 0,
                'testing_tasks' => 0,
                'validated_tasks' => 0,
                'on_hold_tasks' => 0,
                'stopped_tasks' => 0,
                'open_tasks' => 0,
                'overall_progress_pct' => 0,
                'total_sprints' => 0,
                'completed_sprints' => 0,
                'sprint_progress_pct' => 0
            ];
        }

        $customer_id = $this->db->select('customer_id')
            ->from('customer_access')
            ->where('id', $customer_access_id)
            ->get()
            ->row();
        if (empty($customer_id)) {
            return (object) [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'staging_tasks' => 0,
                'new_tasks' => 0,
                'in_progress_tasks' => 0,
                'testing_tasks' => 0,
                'validated_tasks' => 0,
                'on_hold_tasks' => 0,
                'stopped_tasks' => 0,
                'open_tasks' => 0,
                'overall_progress_pct' => 0,
                'total_sprints' => 0,
                'completed_sprints' => 0,
                'sprint_progress_pct' => 0
            ];
        }
        $customer_id = (int) $customer_id->customer_id;

        $stats = $this->db->query("
            SELECT
                COUNT(DISTINCT t.id) AS total_tasks,
                SUM(CASE WHEN t.stage = 'new' THEN 1 ELSE 0 END) AS new_tasks,
                SUM(CASE WHEN t.stage = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_tasks,
                SUM(CASE WHEN t.stage = 'testing' THEN 1 ELSE 0 END) AS testing_tasks,
                SUM(CASE WHEN t.stage = 'completed' THEN 1 ELSE 0 END) AS completed_tasks,
                SUM(CASE WHEN t.stage = 'staging' THEN 1 ELSE 0 END) AS staging_tasks,
                SUM(CASE WHEN t.stage = 'validated' THEN 1 ELSE 0 END) AS validated_tasks,
                SUM(CASE WHEN t.stage = 'on_hold' THEN 1 ELSE 0 END) AS on_hold_tasks,
                SUM(CASE WHEN t.stage = 'stopped' THEN 1 ELSE 0 END) AS stopped_tasks,
                (COUNT(DISTINCT t.id) - SUM(CASE WHEN t.stage = 'completed' THEN 1 ELSE 0 END)) AS open_tasks,
                ROUND(
                    AVG(
                        CASE t.stage
                            WHEN 'new' THEN 0
                            WHEN 'in_progress' THEN 20
                            WHEN 'testing' THEN 40
                            WHEN 'staging' THEN 60
                            WHEN 'validated' THEN 80
                            WHEN 'completed' THEN 100
                            WHEN 'on_hold' THEN 20
                            WHEN 'stopped' THEN 0
                            ELSE 0
                        END
                    )
                , 0) AS overall_progress_pct,
                COUNT(DISTINCT s.id) AS total_sprints,
                COUNT(DISTINCT CASE WHEN sprint_stats.progress_pct = 100 THEN s.id END) AS completed_sprints,
                ROUND(
                    CASE WHEN COUNT(DISTINCT s.id) > 0
                    THEN AVG(COALESCE(sprint_stats.progress_pct, 0))
                    ELSE 0 END
                , 0) AS sprint_progress_pct
            FROM sprints s
            JOIN projects p ON p.id = s.project_id
            JOIN customers c ON c.customer_id = p.customer_id
            LEFT JOIN tasks t ON t.sprint_id = s.id AND t.status = 1 AND t.closed = 0
            LEFT JOIN (
                SELECT
                    t2.sprint_id,
                    ROUND(
                        AVG(
                            CASE t2.stage
                                WHEN 'new' THEN 0
                                WHEN 'in_progress' THEN 20
                                WHEN 'testing' THEN 40
                                WHEN 'staging' THEN 60
                                WHEN 'validated' THEN 80
                                WHEN 'completed' THEN 100
                                WHEN 'on_hold' THEN 20
                                WHEN 'stopped' THEN 0
                                ELSE 0
                            END
                        )
                    , 0) AS progress_pct
                FROM tasks t2
                WHERE t2.status = 1 AND t2.closed = 0
                GROUP BY t2.sprint_id
            ) sprint_stats ON sprint_stats.sprint_id = s.id
            WHERE s.status = 1
              AND s.active = 1
              AND p.active = 1
              AND c.status = 1
              AND c.active = 1
              AND c.customer_id = {$customer_id}
              AND s.name <> 'Roadmap'
        ")->row();

        return $stats ?: (object) [
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'staging_tasks' => 0,
            'new_tasks' => 0,
            'in_progress_tasks' => 0,
            'testing_tasks' => 0,
            'validated_tasks' => 0,
            'on_hold_tasks' => 0,
            'stopped_tasks' => 0,
            'open_tasks' => 0,
            'overall_progress_pct' => 0,
            'total_sprints' => 0,
            'completed_sprints' => 0,
            'sprint_progress_pct' => 0
        ];
    }

    /**
     * Per-sprint progress list for customer dashboard.
     */
    public function getDashboardSprintProgress($customer_access_id)
    {
        $customer_access_id = (int) $customer_access_id;
        if ($customer_access_id <= 0) {
            return [];
        }
        $customer_id = $this->db->select('customer_id')
            ->from('customer_access')
            ->where('id', $customer_access_id)
            ->get()
            ->row();
        if (empty($customer_id)) {
            return [];
        }
        $customer_id = (int) $customer_id->customer_id;

        return $this->db->query("
            SELECT
                s.id,
                s.name,
                p.name AS project_name,
                COUNT(t.id) AS tasks_count,
                SUM(CASE WHEN t.stage = 'completed' THEN 1 ELSE 0 END) AS completed_tasks,
                ROUND(
                    AVG(
                        CASE t.stage
                            WHEN 'new' THEN 0
                            WHEN 'in_progress' THEN 20
                            WHEN 'testing' THEN 40
                            WHEN 'staging' THEN 60
                            WHEN 'validated' THEN 80
                            WHEN 'completed' THEN 100
                            WHEN 'on_hold' THEN 20
                            WHEN 'stopped' THEN 0
                            ELSE 0
                        END
                    )
                , 0) AS progress_pct
            FROM sprints s
            JOIN projects p ON p.id = s.project_id
            JOIN customers c ON c.customer_id = p.customer_id
            LEFT JOIN tasks t ON t.sprint_id = s.id AND t.status = 1 AND t.closed = 0
            WHERE s.status = 1
              AND s.active = 1
              AND p.active = 1
              AND c.status = 1
              AND c.active = 1
              AND c.customer_id = {$customer_id}
              AND s.name <> 'Roadmap'
            GROUP BY s.id, s.name, p.name
            ORDER BY progress_pct ASC, s.name ASC
        ")->result();
    }

    /**
     * Search tasks for customer (for portal global search).
     * @param int $customer_id
     * @param string $q search term (task ref, name, section, etc.)
     * @param int $limit
     * @return array
     */
    public function searchTasks($customer_id, $q, $limit = 15)
    {
        $q = trim((string) $q);
        if ($q === '') {
            return [];
        }
        $customer_id = (int) $customer_id;
        $like_val = '%' . $this->db->escape_like_str($q) . '%';
        $this->db->select('t.id, t.uuid, t.name, t.task_number, t.section, p.name project_name, p.code project_code, s.name sprint_name, s.code sprint_code');
        $this->db->from('tasks t');
        $this->db->join('sprints s', 's.id = t.sprint_id');
        $this->db->join('projects p', 'p.id = s.project_id');
        $this->db->join('customers c', 'c.customer_id = p.customer_id');
        $this->db->where('c.customer_id', $customer_id);
        $this->db->where('t.status', '1');
        $this->db->where('t.closed', '0');
        $this->db->where('s.status', 1);
        $this->db->where('s.active', 1);
        $this->db->where('p.active', 1);
        $this->db->where('c.status', 1);
        $this->db->where('c.active', 1);
        $this->db->group_start();
        $this->db->like('t.name', $q);
        $this->db->or_like('t.task_number', $q);
        $this->db->or_like('t.section', $q);
        $this->db->or_where('CONCAT(IFNULL(p.code,\'\'), \'-\', IFNULL(s.code,\'\'), \'-\', IFNULL(t.task_number,\'\')) LIKE ' . $this->db->escape($like_val), null, false);
        $this->db->group_end();
        $this->db->limit($limit);
        $this->db->order_by('t.task_number');
        $results = $this->db->get()->result();
        if (!function_exists('task_ref')) {
            $CI =& get_instance();
            $CI->load->helper('general');
        }
        foreach ($results as $task) {
            $pc = isset($task->project_code) ? $task->project_code : null;
            $sc = isset($task->sprint_code) ? $task->sprint_code : null;
            $tn = isset($task->task_number) ? $task->task_number : '';
            $task->task_ref = $tn !== '' ? task_ref($pc, $sc, $tn) : '';
        }
        return $results;
    }

    public function getTask($uuid)
    {
        //get master customer id
        $customer_id = $this->db->select()->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->customer_id;
        $task = $this->db->select("t.*,u.name createdBy, t.scope_client_expectation as scope, t.scope_not_included as not_included, t.scope_when_done as done_when")
                            ->from("tasks t")
                            ->join("sprints s","s.id = t.sprint_id","left")
                            ->join("projects p","p.id = s.project_id","left")
                            ->join("customers c","c.customer_id = p.customer_id","left")
                            ->join("users u","u.id=t.created_by","")
                            ->where(["t.status"=>'1',"t.closed"=>"0", "t.uuid"=>$uuid])
                            ->where(["s.status"=>1, "s.active"=>1])
                            ->where(["p.active"=>1])
                            ->where(["c.status"=>1, "c.active"=>1])
                            ->where("c.customer_id = (SELECT customer_id FROM customer_access WHERE id = ".(int)$_SESSION['customer_access_id'].")", null, false)
                            ->order_by("t.task_number")
                            ->get()
                            ->row();
        if(empty($task)) {
            return false;
        }
        // For backward compatibility in views
        $task->scope = $task->scope_client_expectation;
        $task->not_included = $task->scope_not_included;
        $task->done_when = $task->scope_when_done;
        $task->notes = $this->db->select("t.*, u.name developer, ca.name as customer, COALESCE(u.name, ca.name) as author, u.country_code developer_country_code, 'mu' as customer_country_code, COALESCE(u.country_code, ca.country_code) as country_code")
                                ->from("task_notes t")
                                ->join("users u","u.id=t.created_by","left")
                                ->join("customer_access ca","ca.id=t.created_by_customer","left")
                                ->where("t.task_id",$task->id)
                                ->where("t.display_type","public")
                                ->order_by("created_on","desc")
                                ->get()
                                ->result();
        $this->load->model('tasks_model');
        $task->stage_history = $this->tasks_model->get_stage_history_rows($task->id);
        $task->files = $this->db->select('ti.*, u.name AS uploader_user_name, uca.name AS uploader_customer_access_name, c.full_name AS uploader_customer_full, c.company_name AS uploader_customer_company', false)
                                ->from('task_images ti')
                                ->join('users u', 'u.id = ti.created_by', 'left')
                                ->join('customer_access uca', 'uca.id = ti.uploaded_by_customer_access_id', 'left')
                                ->join('customers c', 'c.customer_id = ti.created_by_customer', 'left')
                                ->where('ti.task_id', $task->id)
                                ->order_by('ti.created_on', 'desc')
                                ->get()->result();
        return $task;
    }

    /**
     * Lightweight snapshot for live polling on customer task view (stage + counts).
     *
     * @return array<string,mixed>|false
     */
    public function getTaskPollSnapshot($uuid)
    {
        $access_id = (int) $_SESSION['customer_access_id'];
        $task = $this->db->select('t.id, t.stage')
            ->from('tasks t')
            ->join('sprints s', 's.id = t.sprint_id', 'left')
            ->join('projects p', 'p.id = s.project_id', 'left')
            ->join('customers c', 'c.customer_id = p.customer_id', 'left')
            ->where('t.status', '1')
            ->where('t.closed', '0')
            ->where('t.uuid', $uuid)
            ->where('s.status', 1)
            ->where('s.active', 1)
            ->where('p.active', 1)
            ->where('c.status', 1)
            ->where('c.active', 1)
            ->where('c.customer_id = (SELECT customer_id FROM customer_access WHERE id = ' . $access_id . ')', null, false)
            ->get()
            ->row();
        if (empty($task)) {
            return false;
        }
        $tid = (int) $task->id;

        // Fingerprints of which rows exist (MD5 of sorted ids): add/delete always changes the hash.
        // Notes = public only, same as customer getTask().
        $notes_row = $this->db->query(
            "SELECT MD5(IFNULL((SELECT GROUP_CONCAT(id ORDER BY id) FROM task_notes WHERE task_id = ? AND display_type = 'public'), '')) AS fp",
            [$tid]
        )->row();
        $files_row = $this->db->query(
            "SELECT MD5(IFNULL((SELECT GROUP_CONCAT(id ORDER BY id) FROM task_images WHERE task_id = ?), '')) AS fp",
            [$tid]
        )->row();
        $hist_row = $this->db->query(
            "SELECT MD5(IFNULL((SELECT GROUP_CONCAT(id ORDER BY id) FROM stage_change_history WHERE task_id = ?), '')) AS fp",
            [$tid]
        )->row();

        return [
            'stage'   => $task->stage,
            'notes'   => $notes_row && isset($notes_row->fp) ? $notes_row->fp : md5(''),
            'files'   => $files_row && isset($files_row->fp) ? $files_row->fp : md5(''),
            'history' => $hist_row && isset($hist_row->fp) ? $hist_row->fp : md5(''),
        ];
    }

    /**
     * Lightbox2 caption: upload time, uploader name (or company), and role (customer portal).
     *
     * @param object|array $file Row from task_images (+ uploader_user_name, uploader_customer_access_name, uploader_customer_* from joins)
     */
    public function task_image_lightbox_caption($file)
    {
        $labels = ['customer' => 'Customer', 'developer' => 'Developer', 'admin' => 'Team'];
        $created = is_object($file) ? (isset($file->created_on) ? $file->created_on : '') : (isset($file['created_on']) ? $file['created_on'] : '');
        $when = $created !== '' ? date('d M Y \a\t h:i A', strtotime($created)) : '';
        $t = is_object($file) ? (isset($file->uploaded_by_user_type) ? $file->uploaded_by_user_type : '') : (isset($file['uploaded_by_user_type']) ? $file['uploaded_by_user_type'] : '');
        $role = isset($labels[$t]) ? $labels[$t] : ($t !== '' ? ucfirst($t) : '');

        $displayName = '';
        if ($t === 'customer') {
            $caName = is_object($file)
                ? (isset($file->uploader_customer_access_name) ? trim((string) $file->uploader_customer_access_name) : '')
                : (isset($file['uploader_customer_access_name']) ? trim((string) $file['uploader_customer_access_name']) : '');
            if ($caName !== '') {
                $displayName = $caName;
            } else {
                $full = is_object($file) ? (isset($file->uploader_customer_full) ? trim((string) $file->uploader_customer_full) : '') : (isset($file['uploader_customer_full']) ? trim((string) $file['uploader_customer_full']) : '');
                $comp = is_object($file) ? (isset($file->uploader_customer_company) ? trim((string) $file->uploader_customer_company) : '') : (isset($file['uploader_customer_company']) ? trim((string) $file['uploader_customer_company']) : '');
                $displayName = $full !== '' ? $full : ($comp !== '' ? $comp : '');
            }
        } else {
            $uname = is_object($file) ? (isset($file->uploader_user_name) ? trim((string) $file->uploader_user_name) : '') : (isset($file['uploader_user_name']) ? trim((string) $file['uploader_user_name']) : '');
            $displayName = $uname;
        }
        if ($displayName === '' && $role !== '') {
            $displayName = $role;
        }

        $parts = [];
        if ($when !== '') {
            $parts[] = 'Uploaded ' . $when;
        }
        if ($displayName !== '') {
            $parts[] = $displayName;
        }
        if ($role !== '' && strcasecmp($displayName, $role) !== 0) {
            $parts[] = $role;
        }

        return !empty($parts) ? implode(' · ', $parts) : 'Attachment';
    }

    /**
     * task_images row with uploader joins for captions (after insert).
     */
    private function task_image_row_for_caption($task_image_id)
    {
        return $this->db->select('ti.created_on, ti.uploaded_by_user_type, u.name AS uploader_user_name, uca.name AS uploader_customer_access_name, c.full_name AS uploader_customer_full, c.company_name AS uploader_customer_company', false)
            ->from('task_images ti')
            ->join('users u', 'u.id = ti.created_by', 'left')
            ->join('customer_access uca', 'uca.id = ti.uploaded_by_customer_access_id', 'left')
            ->join('customers c', 'c.customer_id = ti.created_by_customer', 'left')
            ->where('ti.id', (int) $task_image_id)
            ->get()
            ->row();
    }

    public function saveNote($task_id, $note)
    {
        //get master customer id
        // $customer_id = $this->db->select()->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row()->customer_id;

        if ( (empty($task_id)) || (empty(trim($note))) ) {
            return false;
        }

        $attachment_file = null;
        $attachment_thumb = null;
        $attachment_task_image_id = null;

        if (isset($_FILES['note_file']) && $_FILES['note_file']['error'] === UPLOAD_ERR_OK) {
            $this->load->model('Files_model');
            $image = $this->Files_model->uploadImage('note_file', 'uploads/tasks/', ['width' => 200, 'height' => 200]);
            if (is_array($image) && !empty($image['file_name'])) {
                $cid_row = $this->db->select('customer_id')
                    ->from('customer_access')
                    ->where('id', (int) $_SESSION['customer_access_id'])
                    ->get()->row();
                $customer_id_fk = $cid_row ? (int) $cid_row->customer_id : null;
                $thumb = !empty($image['image_resized']) ? $image['image_resized'] : $image['file_name'];
                $attachment_file = $image['file_name'];
                $attachment_thumb = $thumb;
                $insertImage = [
                    'uuid'                  => gen_uuid(),
                    'task_id'               => $task_id,
                    'created_on'            => date('Y-m-d H:i:s'),
                    'created_by'            => null,
                    'file_name'             => $image['file_name'],
                    'thumb_name'            => $thumb,
                    'file_ext'              => isset($image['file_ext']) ? $image['file_ext'] : '',
                    'file_size'             => isset($image['file_size']) ? $image['file_size'] : 0,
                    'image_height'          => isset($image['image_height']) ? $image['image_height'] : 0,
                    'image_width'           => isset($image['image_width']) ? $image['image_width'] : 0,
                    'image_type'            => isset($image['image_type']) ? $image['image_type'] : '',
                    'status'                => 1,
                    'created_by_customer'   => $customer_id_fk,
                    'uploaded_by_user_type' => 'customer',
                ];
                if ($this->db->field_exists('uploaded_by_customer_access_id', 'task_images')) {
                    $insertImage['uploaded_by_customer_access_id'] = (int) $_SESSION['customer_access_id'];
                }
                $this->db->insert('task_images', $insertImage);
                $attachment_task_image_id = (int) $this->db->insert_id();
            }
        }
        $this->db->set("task_id",$task_id);
        $this->db->set("notes",$note);
        $this->db->set("created_by_customer",$_SESSION['customer_access_id']);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("display_type",'public');
        $this->db->set("status",'1');
        $this->db->insert("task_notes");
        
        // if ($uploaded_file) {
        //     $this->db->set('file', $uploaded_file);
        // }


        // Return the inserted note row for AJAX rendering
        $note_id = $this->db->insert_id();
        $note_row = $this->db->select('tn.*, ca.name customer, ca.country_code')->from('task_notes tn')
        ->join("customer_access ca","ca.id=tn.created_by_customer","left")
        ->where('tn.id', $note_id)->get()->row_array();
        if ($attachment_file) {
            $note_row['attachment_file'] = $attachment_file;
            $note_row['attachment_thumb'] = $attachment_thumb;
        }
        if ($attachment_task_image_id) {
            $note_row['attachment_task_image_id'] = $attachment_task_image_id;
            $ti = $this->task_image_row_for_caption($attachment_task_image_id);
            if ($ti) {
                $note_row['attachment_lightbox_caption'] = $this->task_image_lightbox_caption($ti);
            }
        }
        if (!empty($note_row['created_on'])) {
            $note_row['created_on_fmt'] = date('Y m d @ H:i', strtotime($note_row['created_on']));
        }

        $this->load->model('Tasks_model');
        $taskUuid = $this->db->select('uuid')->from('tasks')->where('id', (int) $task_id)->get()->row();
        if ($taskUuid) {
            $taskDetails = $this->Tasks_model->fetchSingle($taskUuid->uuid);
            $author = $this->db->select('email, name')->from('customer_access')->where('id', (int) $_SESSION['customer_access_id'])->get()->row();
            $filesForNotify = [];
            if ($attachment_file) {
                $filesForNotify[] = [
                    'file_name'   => $attachment_file,
                    'image_thumb' => $attachment_thumb,
                ];
            }
            $this->Tasks_model->notifyUsers(
                $taskDetails,
                ['task_id' => $task_id, 'notes' => $note],
                $author,
                'public',
                [
                    'files_added'                => $filesForNotify,
                    'exclude_customer_access_id' => (int) $_SESSION['customer_access_id'],
                ]
            );
        }

        return $note_row;
    }

    /**
     * Delete a task_images row uploaded by this portal customer (same company + customer uploader only).
     *
     * @return array{result:bool, reason?:string}
     */
    public function deleteCustomerTaskImage($task_image_id)
    {
        $task_image_id = (int) $task_image_id;
        if ($task_image_id < 1) {
            return ['result' => false, 'reason' => 'Invalid attachment'];
        }
        $ca = $this->db->select('customer_id')
            ->from('customer_access')
            ->where('id', (int) $_SESSION['customer_access_id'])
            ->get()->row();
        if (empty($ca)) {
            return ['result' => false, 'reason' => 'Unauthorized'];
        }
        $my_customer_id = (int) $ca->customer_id;

        $row = $this->db->select('ti.id, ti.uploaded_by_user_type, ti.created_by_customer, ti.uploaded_by_customer_access_id')
            ->from('task_images ti')
            ->join('tasks t', 't.id = ti.task_id')
            ->join('sprints s', 's.id = t.sprint_id')
            ->join('projects p', 'p.id = s.project_id')
            ->where('ti.id', $task_image_id)
            ->where('p.customer_id', $my_customer_id)
            ->get()->row();

        if (empty($row)) {
            return ['result' => false, 'reason' => 'Attachment not found'];
        }
        if ($row->uploaded_by_user_type !== 'customer' || (int) $row->created_by_customer !== $my_customer_id) {
            return ['result' => false, 'reason' => 'You can only delete your own uploads'];
        }
        $myAccessId = (int) $_SESSION['customer_access_id'];
        if ($this->db->field_exists('uploaded_by_customer_access_id', 'task_images')) {
            $uploaderAccessId = isset($row->uploaded_by_customer_access_id) ? (int) $row->uploaded_by_customer_access_id : 0;
            if ($uploaderAccessId < 1 || $uploaderAccessId !== $myAccessId) {
                return ['result' => false, 'reason' => 'You can only delete your own uploads'];
            }
        }

        $this->load->model('Files_model');
        $this->Files_model->deleteFile($task_image_id);
        $this->db->where('id', $task_image_id)->delete('task_images');

        return ['result' => true];
    }

    public function validateTask($task_id)
    {
        $taskRow = $this->db->select("stage, sprint_id")->from("tasks")->where("id", $task_id)->get()->row();
        if (empty($taskRow)) {
            return false;
        }
        $stage = $taskRow->stage;
        if( in_array($stage, ['completed','validated']) ){
            return false;
        }elseif($stage == 'staging'){
            $this->load->model('tasks_model');
            $this->tasks_model->set_stage_change_trigger_session_vars(
                'customer',
                (int) $_SESSION['customer_access_id'],
                isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : ''
            );

            $this->db->set("stage","validated");

            $this->db->set("validated_on","NOW()",false);
            $this->db->set("validated_by",$_SESSION['customer_access_id']);

            $this->db->where("id",$task_id);
            $this->db->update("tasks");

            if ((int) $this->db->affected_rows() > 0) {
                $this->tasks_model->record_stage_change_history(
                    (int) $task_id,
                    $stage,
                    'validated',
                    'customer',
                    (int) $_SESSION['customer_access_id'],
                    isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : null
                );
            }

            $this->emailForTaskValidationOrRejection($task_id,"validated");

            $this->load->model("Sprints_model");
            $this->Sprints_model->clearValidationReadinessIfNoStagingTasks((int) $taskRow->sprint_id);

            return true;
        }
    }

    public function rejectTask($task_id,$reject_reason)
    {
        $taskRow = $this->db->select("stage, sprint_id")->from("tasks")->where("id", $task_id)->get()->row();
        if (empty($taskRow)) {
            return false;
        }
        $stage = $taskRow->stage;
        if( in_array($stage, ['completed','validated']) ){
            return false;
        }elseif($stage == 'staging'){
            $this->load->model('tasks_model');
            $this->tasks_model->set_stage_change_trigger_session_vars(
                'customer',
                (int) $_SESSION['customer_access_id'],
                isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : ''
            );

            $this->db->set("stage","on_hold");

            $this->db->set("rejected_on","NOW()",false);
            $this->db->set("rejected_by",$_SESSION['customer_access_id']);
            $this->db->set("rejected_reason",$reject_reason);

            $this->db->where("id",$task_id);
            $this->db->update("tasks");

            if ((int) $this->db->affected_rows() > 0) {
                $this->tasks_model->record_stage_change_history(
                    (int) $task_id,
                    $stage,
                    'on_hold',
                    'customer',
                    (int) $_SESSION['customer_access_id'],
                    isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : null
                );
            }

            $this->emailForTaskValidationOrRejection($task_id,"rejected");

            $this->load->model("Sprints_model");
            $this->Sprints_model->clearValidationReadinessIfNoStagingTasks((int) $taskRow->sprint_id);

            return true;
        }
    }

    public function forgotPassword($email)
    {
        $check = $this->db->select("*")->from("customer_access")->where(["email"=>$email,"status"=>1])->get()->row();
        if(empty($check)) {
            return false;
        }else{
            $token = randomName(32);
            $this->db->set("token",$token)->where("email",$email)->update("customer_access");
            $output = new stdClass;
            $output->result = true;
            $output->token = $token;

            $this->sendEmail($check->email,$token);
            return $output;
        }
    }

    private function sendEmail($recipient, $token)
    {
        $this->load->model("Email_model3");
        $this->load->model("system_model");
        $emailData = [
            'email'     =>  $recipient,
            'token'     =>  $token,
            'logo'      =>  $this->system_model->getParam("logo"),
            'resetLink' =>  'portal/customers/processForgotPassword/'
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/forgotPassword",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        // $content = $this->load->view("_email/forgotPassword",$emailData, true);
        $this->Email_model3->save($recipient,"Forgot Password Request",$content);
    }

    public function processForgotPassword($token, $email)
    {
        $password = genPassword(12);
        $this->db->set("password", "md5('$password')", false);
        $this->db->where(["email"=>urldecode($email),"token"=>$token,"status"=>"1"]);
        $this->db->update("customer_access");
        if ($this->db->affected_rows() > 0) {
            $this->db->set("token", '');
            $this->db->where(["email"=>urldecode($email),"token"=>$token]);
            $this->db->update("customer_access");
            $this->sendConfirmationEmail(urldecode($email), $password);
        }
    }

    private function sendConfirmationEmail($recipient, $password)
    {
        $this->load->model("Email_model3");
        $this->load->model("system_model");
        $emailData = [
            'email'     =>  $recipient,
            'password'  =>  $password,
            'logo'      =>  $this->system_model->getParam("logo"),
            'signinUrl' =>  'portal/customers/signin?email='.$recipient
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/forgotPasswordConfirmation",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        // $content = $this->load->view("_email/",$emailData, true);
        $this->Email_model3->save($recipient,"Forgot Password Complete",$content);
    }

    public function get_login_history($records=10)
	{
        if(!$this->db->table_exists("portal_login_history")) return;
		$this->db->from("portal_login_history");
		$this->db->limit($records);
		$this->db->order_by("datetime","desc");
        $this->db->where("type","customer");
        return $this->db->get()->result();
	}

    public function submitTask()
    {
        $valid = true;
        $errorMessage = "";
        $name = $this->input->post("name");
        $section = $this->input->post("section");
        $description = $this->input->post("description");
        $scope_when_done = $this->input->post("scope_when_done");
        $scope_not_included = $this->input->post("scope_not_included"); 
        $scope_client_expectation = $this->input->post("scope_client_expectation");

        if(empty($name) || empty($section) || empty($description) ) {
            $errorMessage .= "Please fill all the required fields (name, section, description).<br>";
            $valid = false;
        }

        if(!$valid) {
            return [
                "result"    =>  false,
                "reason"   =>  $errorMessage
            ];
        }

        // get master customer id linked to this portal user
        $customer_id = $this->db->select("customer_id")
                                ->from("customer_access")
                                ->where("id", $_SESSION['customer_access_id'])
                                ->get()
                                ->row()
                                ->customer_id;

        $this->db->set("uuid",gen_uuid());
        $this->db->set("created_by_customer",$customer_id);
        $this->db->set("created_by_customer_access", (int) $_SESSION['customer_access_id']);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("name",$name);
        $this->db->set("section",$section);
        $this->db->set("description",$description);
        $this->db->set("scope_client_expectation",$scope_client_expectation);
        $this->db->set("scope_not_included",$scope_not_included);
        $this->db->set("scope_when_done",$scope_when_done);
        $this->db->set("stage","new");
        $this->db->set("status","1");
        $this->db->insert("submitted_tasks");
        $insert_id = $this->db->insert_id();
        if($insert_id) {
            $this->saveSubmittedTaskImages($insert_id, $customer_id);
            $this->emailForTaskCreated($insert_id);
            return [
                "result"    =>  true
            ];
        }else{
            return [
                "result"    =>  false,
                "reason"   =>  "Unable to create task. Please try again."
            ];
        }
    }

    /**
     * Upload images for a submitted task.
     * Saves originals and resized thumbs into `uploads/tasks/` and records them in `submitted_tasks_images`.
     */
    private function saveSubmittedTaskImages($submitted_task_id, $customer_id)
    {
        if (empty($submitted_task_id) || empty($_FILES['task_images'])) {
            return;
        }

        // If no files were selected
        if (empty($_FILES['task_images']['name']) || !is_array($_FILES['task_images']['name'])) {
            return;
        }

        $hasFiles = false;
        foreach ($_FILES['task_images']['name'] as $name) {
            if (!empty($name)) {
                $hasFiles = true;
                break;
            }
        }
        if (!$hasFiles) {
            return;
        }

        $this->load->model("Files_model");
        $uploadResult = $this->Files_model->uploadImages2('task_images', 'uploads/tasks/', false);
        $filesUploaded = isset($uploadResult['filesUploaded']) ? $uploadResult['filesUploaded'] : [];

        if (empty($filesUploaded)) {
            return;
        }

        $uploadBaseFolder = realpath('.') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'tasks';

        foreach ($filesUploaded as $uploaded) {
            if (empty($uploaded['file_name'])) {
                continue;
            }

            $fileName = (string) $uploaded['file_name'];
            $rawName = isset($uploaded['raw_name']) ? (string) $uploaded['raw_name'] : pathinfo($fileName, PATHINFO_FILENAME);
            $fileExt = isset($uploaded['file_ext']) ? (string) $uploaded['file_ext'] : (string) pathinfo($fileName, PATHINFO_EXTENSION);
            if ($fileExt !== '' && $fileExt[0] !== '.') {
                $fileExt = '.' . $fileExt;
            }

            $sourcePath = !empty($uploaded['full_path']) ? $uploaded['full_path'] : ($uploadBaseFolder . DIRECTORY_SEPARATOR . $fileName);
            if (!file_exists($sourcePath)) {
                continue;
            }

            // Create a thumb (resized marker) for the future gallery / previews.
            $this->Files_model->resizeImage($sourcePath, 200, 200, 'resized');

            $thumbName = $rawName . '_resized' . $fileExt;
            $thumbPath = $uploadBaseFolder . DIRECTORY_SEPARATOR . $thumbName;

            $width = 0;
            $height = 0;
            $mimeType = '';
            $imgInfo = @getimagesize($sourcePath);
            if (is_array($imgInfo) && count($imgInfo) >= 2) {
                $width = (int) $imgInfo[0];
                $height = (int) $imgInfo[1];
            }
            $mimeType = function_exists('mime_content_type') ? @mime_content_type($sourcePath) : '';
            if ($mimeType === false || empty($mimeType)) {
                $mimeType = $uploaded['file_type'] ?? '';
            }

            $fileSize = isset($uploaded['file_size']) ? (float) $uploaded['file_size'] : 0.0;

            $this->db->insert("submitted_tasks_images", [
                'uuid'                   => gen_uuid(),
                'submitted_task_id'     => (int) $submitted_task_id,
                'created_on'            => date("Y-m-d H:i:s"),
                'created_by_customer'  => !empty($customer_id) ? (int) $customer_id : null,
                'uploaded_by_user_type' => 'customer',
                'file_name'             => $fileName,
                'thumb_name'            => file_exists($thumbPath) ? $thumbName : $fileName,
                'file_ext'              => $fileExt,
                'file_size'             => $fileSize,
                'image_width'           => $width,
                'image_height'          => $height,
                'image_type'            => (string) $mimeType,
                'status'                => 1
            ]);
        }
    }

    /**
     * Return submitted task requests for the logged-in customer company.
     * Includes conversion link info when a request has been approved.
     */
    public function getSubmittedTasks($customer_access_id)
    {
        $customer_access_id = (int) $customer_access_id;
        if ($customer_access_id <= 0) {
            return [];
        }

        $customer_row = $this->db->select('customer_id, admin')
            ->from('customer_access')
            ->where('id', $customer_access_id)
            ->where('status', 1)
            ->get()
            ->row();
        if (empty($customer_row)) {
            return [];
        }

        $customer_id = (int) $customer_row->customer_id;
        $isAdmin = !empty($customer_row->admin) ? (int) $customer_row->admin : 0;
        $this->db->select('st.id, st.uuid, st.name, st.section, st.description, st.scope_client_expectation, st.scope_not_included, st.scope_when_done, st.rejection_reason, st.stage, st.created_on, st.converted_task_id, ca.name as submitted_by, t.uuid as converted_task_uuid, t.task_number as converted_task_number, p.code as project_code, s.code as sprint_code');
        $this->db->from('submitted_tasks st');
        $this->db->join('customer_access ca', 'ca.id = st.created_by_customer_access', 'left');
        $this->db->join('tasks t', 't.id = st.converted_task_id', 'left');
        $this->db->join('sprints s', 's.id = t.sprint_id', 'left');
        $this->db->join('projects p', 'p.id = s.project_id', 'left');
        $this->db->where('st.status', 1);
        $this->db->where('st.created_by_customer', $customer_id);
        $this->db->order_by('st.created_on', 'desc');
        $rows = $this->db->get()->result();

        if (!function_exists('task_ref')) {
            $CI =& get_instance();
            $CI->load->helper('general');
        }

        // Attach uploaded images to each submitted task.
        $imageMap = [];
        $taskIds = array_values(array_filter(array_map(function ($r) { return isset($r->id) ? (int) $r->id : 0; }, $rows)));
        if (!empty($taskIds)) {
            $imageRows = $this->db->select('submitted_task_id, file_name, thumb_name')
                ->from('submitted_tasks_images')
                ->where('status', 1)
                ->where_in('submitted_task_id', $taskIds)
                ->order_by('id', 'asc')
                ->get()
                ->result();

            foreach ($imageRows as $img) {
                $sid = (int) $img->submitted_task_id;
                if (!isset($imageMap[$sid])) {
                    $imageMap[$sid] = [];
                }
                $imageMap[$sid][] = $img;
            }
        }

        foreach ($rows as $row) {
            $row->converted_task_ref = '';
            if (!empty($row->converted_task_number)) {
                $row->converted_task_ref = task_ref($row->project_code, $row->sprint_code, $row->converted_task_number);
            }
            $row->images = isset($imageMap[(int) $row->id]) ? $imageMap[(int) $row->id] : [];
            $row->can_delete = ($isAdmin === 1) && (($row->stage !== 'validated') && empty($row->converted_task_id));
        }

        return $rows;
    }

    /**
     * Soft-delete a submitted task created by this customer, only if not approved yet.
     */
    public function deleteSubmittedTask($task_uuid, $customer_access_id)
    {
        $task_uuid = trim((string) $task_uuid);
        $customer_access_id = (int) $customer_access_id;
        if ($task_uuid === '' || $customer_access_id <= 0) {
            return ['result' => false, 'reason' => 'Invalid request.'];
        }

        $customer_row = $this->db->select('customer_id, admin')
            ->from('customer_access')
            ->where('id', $customer_access_id)
            ->where('status', 1)
            ->get()
            ->row();
        if (empty($customer_row)) {
            return ['result' => false, 'reason' => 'Invalid session.'];
        }
        $isAdmin = !empty($customer_row->admin) ? (int) $customer_row->admin : 0;
        if ($isAdmin !== 1) {
            return ['result' => false, 'reason' => 'Only admins can delete submitted tasks.'];
        }

        $row = $this->db->select('id, stage, converted_task_id')
            ->from('submitted_tasks')
            ->where('uuid', $task_uuid)
            ->where('status', 1)
            ->where('created_by_customer', (int) $customer_row->customer_id)
            ->get()
            ->row();
        if (empty($row)) {
            return ['result' => false, 'reason' => 'Task not found.'];
        }
        if ($row->stage === 'validated' || !empty($row->converted_task_id)) {
            return ['result' => false, 'reason' => 'Approved task requests cannot be deleted.'];
        }

        $this->db->set('status', 0)->where('id', (int) $row->id)->update('submitted_tasks');
        return ['result' => true];
    }

    private function emailForTaskCreated($task_id)
    {
        $this->load->model("Email_model3");
        $this->load->model("System_model");
        $submitted_task = $this->db->query("SELECT st.*,
                            COALESCE(ca_sub.name, ca_fallback.name) AS customerName,
                            COALESCE(ca_sub.email, ca_fallback.email) AS customerEmail
                            FROM submitted_tasks st
                            LEFT JOIN customer_access ca_sub ON ca_sub.id = st.created_by_customer_access
                            LEFT JOIN (SELECT customer_id, MIN(id) AS id FROM customer_access GROUP BY customer_id) ca_fb ON ca_fb.customer_id = st.created_by_customer
                            LEFT JOIN customer_access ca_fallback ON ca_fallback.id = ca_fb.id
                            WHERE st.id = " . (int) $task_id)->row();
        $emailData = [
            'title'     =>  'Task Submitted',
            'task'      =>  $submitted_task,
            'logo'      =>  $this->System_model->getParam("logo"),
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/taskSubmittedCustomer",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $subject = "{$_SESSION['customer_name']} Submitted a Task";
        $this->Email_model3->save($_SESSION['customer_email'],$subject,$content);

        // notify admins for task created
        $members = $this->System_model->getParam("notification_create_tasks",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            $this->Email_model3->save($user->email,$subject,$content);
        }
    }

    /**
     * createUserAccess is called from customer portal
     */
    public function createUserAccess($name, $email, $password, $job_description = '')
    {
        $customer = $this->db->select("customer_id, email, name")->from("customer_access")->where("id",$_SESSION['customer_access_id'])->get()->row();

        $existing_users = $this->db->select("count(id) as ct")->from("customer_access")->where(array("customer_id"=>$customer->customer_id,"status"=>"1"))->get()->row()->ct;

        if($existing_users >= 5){
            return [
                "result"    =>  false,
                "reason"    =>  'Exceeded quota. Maximum 5 users allowed'
            ];
        }
        $this->db->set("name",$name);
        $this->db->set("email",$email);
        if (!empty($job_description)) {
            $this->db->set("job_description", $job_description);
        }
        $this->db->set("password",md5($password),true);
        $this->db->set("created_by_customer",$_SESSION['customer_access_id']);
        $this->db->set("customer_id",$customer->customer_id);
        $this->db->set("created_on",date("Y-m-d H:i:s"));
        $this->db->set("created_by_type","customer");
        $this->db->set("admin","0");
        $this->db->insert("customer_access");

        // $this->emailForUserCreated($name,$email,$customer);

        //return existing customer access for customer
        $users = $this->db->query("SELECT *
                        FROM customer_access
                        WHERE customer_id = $customer->customer_id
                        AND status = '1'")->result();
        return [
            'result'    =>  true,
            'users'     =>  $users
        ];
    }

    /**
     * addUserAccess is called from the back office or from the portal (portal always passes admin=0).
     * @param int $admin 1 = portal admin (can add/remove other users), 0 = normal user
     */
    public function addUserAccess($uuid, $name,$email,$phone,$password, $country_code, $admin = 0, $generate_password = 0)
    {
        $author = $this->db->select("name,email")->from("users")->where("id",$_SESSION['user_id'])->get()->row();
        $customer = $this->db->select("customer_id, email, full_name, company_name")->from("customers")->where("uuid",$uuid)->get()->row();
        $final_password = ($generate_password == 1 || strlen(trim((string) $password)) < 4) ? genPassword(12) : $password;

        $existing_users = $this->db->select("count(id) as ct")->from("customer_access")->where(array("customer_id"=>$customer->customer_id,"status"=>"1"))->get()->row()->ct;

        if($existing_users >= 5){
            return [
                "result"    =>  false,
                "reason"    =>  'Exceeded quota. Maximum 5 users allowed'
            ];
        }
        $this->db->set("name",$name);
        $this->db->set("email",$email);
        $this->db->set("phone_number1",$phone);
        $this->db->set("country_code",$country_code);
        $this->db->set("password",md5($final_password),true);
        $this->db->set("created_by",$_SESSION['user_id']);
        $this->db->set("customer_id",$customer->customer_id);
        $this->db->set("created_on",'NOW()',true);
        $this->db->set("created_by_type","customer");
        $this->db->set("admin", (int) $admin);
        $this->db->insert("customer_access");
        $newUserId = $this->db->insert_id();

        $this->emailForUserCreated($author,$name,$email,$final_password,$customer, (int) $generate_password === 1);

        return [
            'result'    =>  true,
            "user_id"   =>  $newUserId
        ];
    }

    /**
     * Update an existing portal user (customer_access). Called from back office or portal.
     * @param int $access_id customer_access.id
     * @param string $password optional; if empty, password is not changed
     * @param string|null $job_description optional; if null, leave unchanged
     */
    public function updateUserAccess($access_id, $name, $email, $phone = null, $country_code = null, $admin = null, $password = '', $job_description = null)
    {
        $row = $this->db->select("id, customer_id")->from("customer_access")->where(array("id" => $access_id, "status" => "1"))->get()->row();
        if (empty($row)) {
            return ['result' => false, 'reason' => 'User not found.'];
        }

        // If email is being changed, ensure it's not already used by another user for this customer
        $existing = $this->db->select("id")->from("customer_access")
            ->where(array("customer_id" => $row->customer_id, "status" => "1", "email" => $email))
            ->get()->row();
        if ($existing && (int) $existing->id !== (int) $access_id) {
            return ['result' => false, 'reason' => 'Email already used by another user for this company.'];
        }

        $this->db->set("name", $name);
        $this->db->set("email", $email);
        // Only update extra fields when explicitly provided.
        if ($phone !== null) {
            $this->db->set("phone_number1", $phone);
        }
        if ($country_code !== null) {
            $this->db->set("country_code", $country_code);
        }
        if ($admin !== null) {
            $this->db->set("admin", (int) $admin);
        }
        if ($password !== '') {
            $this->db->set("password", md5($password), true);
        }
        if ($job_description !== null) {
            $this->db->set("job_description", $job_description);
        }
        $this->db->where("id", $access_id);
        $this->db->update("customer_access");

        // If password was changed, email the new password to the user
        if ($password !== '') {
            // Reuse the same confirmation email as the forgot password flow
            $this->sendConfirmationEmail($email, $password);
        }

        return ['result' => true];
    }

    public function removeAccess($userId)
    {
        $userToDelete = $this->db->select("name,email")->from("customer_access")->where("id",$userId)->get()->row();
        if(empty($userToDelete)){
            return false;
        }
        // $this->db->where("id",$userId)->delete("customer_access");
        $this->db->set("status","0")->where("id",$userId)->update("customer_access");

        $this->load->model("Email_model3");
        $this->load->model("System_model");

        $emailData = [
            'author'        =>  $this->db->select("name,email")->from("users")->where("id",$_SESSION['user_id'])->get()->row(),
            'user_created'  =>  ["name"=>$userToDelete->name,"email"=>$userToDelete->email],
            'user'          =>  $userToDelete,
            'logo'          =>  $this->System_model->getParam("logo")
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/userDeleted",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $subject = "User Has Been Removed Access";
        $this->Email_model3->save($userToDelete->email,$subject,$content);

        // notify admins for task created
        $members = $this->System_model->getParam("notification_create_users",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            $this->Email_model3->save($user->email,$subject,$content);
        }

        return true;
    }

    private function emailForUserCreated($author,$name,$email,$password,$customer, $is_generated_password = false)
    {
        $this->load->model("Email_model3");
        $this->load->model("System_model");
        $emailData = [
            'author'        =>  $author,
            'user_created'  =>  ["name"=>$name,"email"=>$email,"password"=>$password],
            'customer'      =>  $customer,
            'logo'          =>  $this->System_model->getParam("logo"),
            'link'          =>  base_url('portal/customers/'),
            'link_label'    =>  'Sign In',
            'is_generated_password' => (bool) $is_generated_password
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/userAdded",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $subject = "User Has Been Granted Access";
        $this->Email_model3->save($email,$subject,$content);

        //send customer introduction email
        $emailData = [
            'email'        =>  $email,
            // 'password'     =>  $data['password'],
            'title'         =>  'New Customer Created',
            'logo'          =>  $this->system_model->getParam("logo"),
            'link'          =>  '',
            'link_label'    =>  ''
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/welcomeTaskManager",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $this->Email_model3->save($email,"Experience our newly developed Task Manager",$content);

        // notify admins for task created
        $members = $this->System_model->getParam("notification_create_users",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            $this->Email_model3->save($user->email,$subject,$content);
        }
        
    }

    private function emailForTaskValidationOrRejection($task_id,$validationOrRejection="validated")
    {
        $this->load->model("Email_model3");
        $this->load->model("System_model");
        $task = $this->db->query("select 
                                    t.id , t.name taskName , t.task_number taskNumber
                                    , s.name sprintName
                                    , p.name projectName
                                    , c.company_name customerName
                                    , t.validated_on validatedOn
                                    , ca1.name as validatedBy
                                    , t.rejected_on rejectedOn, t.rejected_reason rejectedReason
                                    , ca2.name as rejectedBy
                                from tasks t 
                                join sprints s on s.id = t.sprint_id 
                                join projects p on p.id = s.project_id 
                                join customers c on c.customer_id = p.customer_id 
                                left join customer_access ca1 on ca1.customer_id = t.validated_by 
                                left join customer_access ca2  on ca2.customer_id  = t.rejected_by 
                                where t.id = $task_id")->row();
        $emailData = [
            'title'     =>  'Task ' . (($validationOrRejection == 'validated') ? ' Validated ' : ' Rejected '),
            'task'      =>  $task,
            'type'      =>  $validationOrRejection,
            'logo'      =>  $this->System_model->getParam("logo"),
        ];
        $content = $this->load->view("_email/header",$emailData, true);
        $content .= $this->load->view("_email/taskRejectedOrValidated",$emailData, true);
        $content .= $this->load->view("_email/footer",[], true);
        $subject = "{$_SESSION['customer_name']} " . (($validationOrRejection == 'validated') ? ' Validated ' : ' Rejected ') . ' a Task ';
        $this->Email_model3->save($_SESSION['customer_email'],$subject,$content);

        // notify admins for task created
        $members = $this->System_model->getParam("notification_create_tasks",true);
        foreach($members as $m){
            $user = $this->db->select("*")->from("users")->where("id",$m)->get()->row();
            $this->Email_model3->save($user->email,$subject,$content);
        }
    }
}