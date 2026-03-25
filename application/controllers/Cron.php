<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public $data;
    
    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-","",$this->uri->segment(1,"dashboard"));
        $this->data['method']       = $this->uri->segment(2,"index");
        $this->load->model("accesscontrol_model");
    }

    public function index()
    {
        $fh = fopen("cron.txt",'a+');
        if(!$fh){
            echo "Unable to create file";
        }else{
            fwrite($fh,date("YmdHis").": Hello World!");
            fwrite($fh,"\r\n");
            fclose($fh);
        }
    }

    public function sendEmails()
    {
        $emails = $this->db->select("*")->from("email_queue")->where("stage",NULL)->limit(10)->get()->result();
        if(!empty($emails)){
            $this->load->model("email_model2");
            foreach($emails as $email){
                $this->stage($email->id,"sending");
                $to = $email->recipients;
                $subject = $email->subject;
                $message = $email->content;
                $result = $this->email_model2->sendFromQueue($to,$subject,$message);
                if($result){
                    $this->stage($email->id,"sent");
                }else{
                    $this->stage($email->id,"failed");
                }
            }
        }
    }

    private function stage($id,$stage)
    {
        $this->db->query("SET @@session.time_zone = '+04:00'");
        $this->db->set("stage",$stage);
        $this->db->set("date_sent","NOW()",false);
        $this->db->where("id",$id)->update("email_queue");
    }

    public function getDueTasks($days = null, $subject = null)
    {
        // Allow calling via URI or internally with a parameter
        if($days === null){
            $days = $this->uri->segment(3);
        }
        // Accept 0 (today); only default when truly absent
        if($days === null || $days === ''){
            $days = 7;
        }
        $days = (int)$days;
        
        // Set default subject if not provided
        if($subject === null){
            $subject = "Tasks Due Reminder";
        }
        
        $this->db->query("SET @@session.time_zone = '+04:00'");
        $query = "select t.uuid, t.id, t.task_number, t.name, t.stage, t.description, t.section, t.due_date, t.estimated_hours, s.name as sprint_name, p.name as project_name, c.company_name, u.name developer_name, u.email as developer_email
                from tasks t 
                left join sprints s on s.id = t.sprint_id
                left join projects p on p.id = s.project_id
                left join task_user tu on tu.task_id = t.id
                left join customers c on c.customer_id = p.customer_id
                left join users u on u.id = tu.user_id
                where due_date = CURDATE() + INTERVAL $days DAY
                and t.stage not in('completed','on_hold')
                and u.email IS NOT NULL
                and t.status = '1'
                and t.closed = '0'
                and s.active = '1'
                and p.active = '1'
                and c.active = '1'
                order by u.email";
        $result = $this->db->query($query)->result();
        $grouped = array();
        if(!empty($result)){
            $this->load->model("Email_model3");
            $this->load->model("system_model");

            foreach($result as $row){
                if(empty($row->developer_email)) continue;
                if(!isset($grouped[$row->developer_email])){
                    $grouped[$row->developer_email] = array();
                }
                $grouped[$row->developer_email][] = array(
                    "email" => $row->developer_email,
                    "tasks" => $row
                );
            }

            foreach($grouped as $tasks){
                $emailData = [
                    'days'              =>  $days,    
                    'logo'              =>  $this->system_model->getParam("logo"),
                    'tasks'             =>  $tasks,
                    'show_lifecycle'    =>  false
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/dueTasks",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);
                // echo $content;
                $this->Email_model3->save($tasks[0]['email'], $subject, $content);
            }
        }

    }

    public function sendDueTaskReminders()
    {
        // Send reminders for tasks due in the next 3 days (3, 2, and 1 days from now)
        foreach([3, 2, 1] as $d){
            $this->getDueTasks($d, "Tasks Due Reminder");
        }
    }

    public function sendDueTodayReminders()
    {
        // Send reminders for tasks due today
        $this->getDueTasks(0, "Tasks Due Today - Urgent");
    }

    public function sendOverdueTaskReminders()
    {
        // Send reminders for tasks that are past due
        $this->db->query("SET @@session.time_zone = '+04:00'");
        $query = "select t.uuid, t.id, t.task_number, t.name, t.stage, t.description, t.section, t.due_date, t.estimated_hours, 
                DATEDIFF(CURDATE(), t.due_date) as days_overdue,
                s.name as sprint_name, p.name as project_name, c.company_name, c.customer_id, p.id as project_id,
                u.name developer_name, u.email as developer_email
                from tasks t 
                left join sprints s on s.id = t.sprint_id
                left join projects p on p.id = s.project_id
                left join task_user tu on tu.task_id = t.id
                left join customers c on c.customer_id = p.customer_id
                left join users u on u.id = tu.user_id
                where t.due_date < CURDATE()
                and t.stage not in('completed','on_hold')
                and u.email IS NOT NULL
                and t.status = '1'
                and t.closed = '0'
                and s.active = '1'
                and p.active = '1'
                and c.active = '1'
                order by u.email, c.company_name, p.name, t.due_date ASC";
        $result = $this->db->query($query)->result();
        $grouped = array();
        if(!empty($result)){
            $this->load->model("Email_model3");
            $this->load->model("system_model");

            foreach($result as $row){
                if(empty($row->developer_email)) continue;
                
                // Group by developer email
                if(!isset($grouped[$row->developer_email])){
                    $grouped[$row->developer_email] = array();
                }
                
                // Create a key for customer-project grouping
                $customerProjectKey = $row->company_name . '|' . $row->project_name . '|' . $row->customer_id . '|' . $row->project_id;
                
                // Group by customer-project within each developer
                if(!isset($grouped[$row->developer_email][$customerProjectKey])){
                    $grouped[$row->developer_email][$customerProjectKey] = array(
                        'customer_name' => $row->company_name,
                        'project_name' => $row->project_name,
                        'customer_id' => $row->customer_id,
                        'project_id' => $row->project_id,
                        'tasks' => array()
                    );
                }
                
                $grouped[$row->developer_email][$customerProjectKey]['tasks'][] = $row;
            }

            foreach($grouped as $developerEmail => $customerProjects){
                $emailData = [
                    'logo'              =>  $this->system_model->getParam("logo"),
                    'customerProjects'  =>  $customerProjects,
                    'show_lifecycle'    =>  false
                ];
                $content = $this->load->view("_email/header",$emailData, true);
                $content .= $this->load->view("_email/overdueTasks",$emailData, true);
                $content .= $this->load->view("_email/footer",[], true);
                $this->Email_model3->save($developerEmail, "URGENT: Overdue Tasks - Action Required", $content);
            }
        }
    }

	public function suspendInactiveDevelopers()
	{
		$this->db->query("SET @@session.time_zone = '+04:00'");
		$thresholdDays = 30;
		$query = "SELECT u.id, u.email, u.name
				FROM users u
				WHERE u.user_type = 'developer'
				AND u.status = '1'
				AND NOT EXISTS (
					SELECT 1 FROM portal_login_history pl
					WHERE pl.email COLLATE utf8mb4_unicode_ci = u.email COLLATE utf8mb4_unicode_ci
					AND pl.type = 'developer'
					AND pl.result = 'SUCCESS'
					AND pl.datetime >= (NOW() - INTERVAL {$thresholdDays} DAY)
				)";

		$developers = $this->db->query($query)->result();
		if(empty($developers)) return;

		$this->load->model("Email_model3");
		$this->load->model("system_model");

		foreach($developers as $dev){
			// Suspend developer
			$this->db->set('status','2');
			$this->db->where('id',$dev->id);
			$this->db->update('users');

			// Notify developer
			$emailData = [
				'user'          => $dev,
				'logo'          => $this->system_model->getParam("logo"),
				'thresholdDays' => $thresholdDays,
			];
			$content = $this->load->view("_email/header",$emailData, true);
			$content .= $this->load->view("_email/developerSuspended",$emailData, true);
			$content .= $this->load->view("_email/footer",[], true);
			$this->Email_model3->save($dev->email,"Your developer account has been suspended",$content);
		}
	}

    public function sendStagingValidationReminders()
    {
        $this->db->query("SET @@session.time_zone = '+04:00'");

        $query = "SELECT svr.sprint_id,
                        svr.last_sent_on,
                        s.name AS sprint_name,
                        p.name AS project_name,
                        p.id AS project_id,
                        c.customer_id,
                        c.company_name,
                        COUNT(t.id) AS staging_count
                    FROM sprint_validation_reminders svr
                    JOIN sprints s ON s.id = svr.sprint_id
                    JOIN projects p ON p.id = s.project_id
                    JOIN customers c ON c.customer_id = p.customer_id
                    JOIN tasks t ON t.sprint_id = s.id
                    WHERE svr.ready_for_validation = 1
                      AND s.status = '1'
                      AND s.active = '1'
                      AND p.active = '1'
                      AND c.active = '1'
                      AND t.status = '1'
                      AND t.closed = '0'
                      AND t.stage = 'staging'
                    GROUP BY svr.sprint_id, svr.last_sent_on, s.name, p.name, p.id, c.customer_id, c.company_name
                    HAVING svr.last_sent_on IS NULL
                        OR svr.last_sent_on <= (NOW() - INTERVAL 7 DAY)";
        $candidates = $this->db->query($query)->result();

        if(empty($candidates)) return;

        $this->load->model("Email_model3");
        $this->load->model("system_model");

        foreach($candidates as $item){
            $recipients = $this->db->select("name,email")
                                ->from("customer_access")
                                ->where("customer_id", $item->customer_id)
                                ->where("status", "1")
                                ->where("email IS NOT NULL", null, false)
                                ->where("email !=", "")
                                ->get()->result();

            if(empty($recipients)) continue;

            $tasks_link = rtrim(site_url('portal/customers/tasks'), '/')
                . '?' . http_build_query([
                    'sprint_id' => (int) $item->sprint_id,
                    'stages' => 'staging',
                ]);
            $emailData = [
                "logo" => $this->system_model->getParam("logo"),
                "customer_name" => $item->company_name,
                "sprint_name" => $item->sprint_name,
                "project_name" => $item->project_name,
                "staging_count" => (int)$item->staging_count,
                "tasks_link" => $tasks_link
            ];

            $content = $this->load->view("_email/header", $emailData, true);
            $content .= $this->load->view("_email/stagingValidationReminder", $emailData, true);
            $content .= $this->load->view("_email/footer", [], true);
            $subject = "Reminder: Tasks awaiting validation for sprint {$item->sprint_name}";

            $sent = 0;
            foreach($recipients as $recipient){
                if(empty($recipient->email)) continue;
                $this->Email_model3->save($recipient->email, $subject, $content);
                $sent++;
            }

            if($sent > 0){
                $this->db->set("last_sent_on", "NOW()", false);
                $this->db->where("sprint_id", (int)$item->sprint_id);
                $this->db->update("sprint_validation_reminders");
            }
        }
    }

    public function fetchQuotes()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://zenquotes.io/api/quotes");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $this->data['quotes'] = curl_exec($curl);
        $quotes = json_decode($this->data['quotes']);
        curl_close($curl);

        $this->saveQuotes($quotes);
    }

    private function saveQuotes($quotes)
    {
        $this->db->set("deleted_on","NOW()",false)->where("deleted_on IS NULL")->update("quotes");

        foreach($quotes as $quote){
            $this->db->set("quote_text",$quote->q);
            $this->db->set("author_name",$quote->a);
            $this->db->set("character_count",$quote->c);
            $this->db->set("html",$quote->h);
            $this->db->set("fetched_on","NOW()",false);
            $this->db->set("quote_text",$quote->q);
            $this->db->insert("quotes");
        }
    }

}