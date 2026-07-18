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
        $query = $this->dueTasksQuery($days);
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

    /**
     * Guard for the dev-only diagnostic routes: they are unauthenticated and one of them
     * can dispatch real email, so they must never be reachable in production.
     */
    private function blockOnProduction()
    {
        if (ENVIRONMENT === 'production') {
            show_404();
        }
    }

    /**
     * The single source of truth for the "tasks due in N days" query.
     * Used by both getDueTasks() (which sends) and dryRunDueTasks() (which only reports).
     *
     * $stagesParam selects which configured stage list applies — the "due today" run is
     * scoped separately from the 3/2/1-day-ahead run.
     */
    private function dueTasksQuery($days, $stagesParam = null)
    {
        $days = (int)$days;
        if($stagesParam === null){
            $stagesParam = ($days === 0) ? "due_today_reminder_stages" : "due_reminder_stages";
        }
        $stageList = $this->stageInList($stagesParam);

        return "select t.uuid, t.id, t.task_number, t.name, t.stage, t.source, t.description, t.section, t.due_date, t.estimated_hours, s.name as sprint_name, p.name as project_name, c.company_name, u.name developer_name, u.email as developer_email
                from tasks t
                left join sprints s on s.id = t.sprint_id
                left join projects p on p.id = s.project_id
                left join task_user tu on tu.task_id = t.id
                left join customers c on c.customer_id = p.customer_id
                left join users u on u.id = tu.user_id
                where due_date = CURDATE() + INTERVAL $days DAY
                and t.stage in({$stageList})
                and u.email IS NOT NULL
                and u.status = '1'
                and u.user_type = 'developer'
                and t.status = '1'
                and t.closed = '0'
                and s.active = '1'
                and p.active = '1'
                and c.active = '1'
                order by u.email";
    }

    // --- DEV DEBUG (disabled). Uncomment to re-enable. ---
    // /**
     // * Dry run: prints who WOULD receive the due-task reminder for each day offset,
     // * without sending or queueing anything. Hit via URL: cron/dryRunDueTasks
     // * Optionally pass a single day offset: cron/dryRunDueTasks/2
     // */
    // public function dryRunDueTasks()
    // {
        // $this->blockOnProduction();
        // $this->db->query("SET @@session.time_zone = '+04:00'");
        // $arg  = $this->uri->segment(3);
        // $days = ($arg === null || $arg === '') ? [3, 2, 1] : [(int)$arg];

        // header("Content-Type: text/plain; charset=utf-8");
        // $today = $this->db->query("SELECT CURDATE() AS d")->row()->d;
        // echo "DRY RUN — no emails sent/queued.\n";
        // echo "DB CURDATE(): {$today}\n";
        // echo str_repeat("=", 60) . "\n\n";

        // foreach($days as $d){
            // $target = $this->db->query("SELECT CURDATE() + INTERVAL " . (int)$d . " DAY AS d")->row()->d;
            // echo "Day offset: +{$d}  (matches tasks due exactly {$target})\n";

            // $rows = $this->db->query($this->dueTasksQuery($d))->result();
            // echo "  Matched task rows: " . count($rows) . "\n";

            // if(!empty($rows)){
                // $grouped = [];
                // foreach($rows as $r){
                    // if(empty($r->developer_email)) continue;
                    // $grouped[$r->developer_email][] = $r->task_number . " (" . $r->name . ")";
                // }
                // echo "  Recipients: " . count($grouped) . "\n";
                // foreach($grouped as $email => $tasks){
                    // echo "    - {$email}: " . count($tasks) . " task(s) -> " . implode(", ", $tasks) . "\n";
                // }
            // }

            // // Show any tasks due on that date that were EXCLUDED by the developer/active filters,
            // // to explain a "no result" when tasks exist but recipients don't qualify.
            // $diag = $this->db->query(
                // "select t.task_number, t.name, t.stage, t.status, t.closed,
                        // u.email, u.status as user_status, u.user_type,
                        // s.active as sprint_active, p.active as project_active, c.active as customer_active
                 // from tasks t
                 // left join sprints s on s.id = t.sprint_id
                 // left join projects p on p.id = s.project_id
                 // left join task_user tu on tu.task_id = t.id
                 // left join customers c on c.customer_id = p.customer_id
                 // left join users u on u.id = tu.user_id
                 // where t.due_date = CURDATE() + INTERVAL " . (int)$d . " DAY"
            // )->result();
            // if(!empty($diag)){
                // echo "  All task_user rows due on {$target} (before filters): " . count($diag) . "\n";
                // foreach($diag as $x){
                    // echo "      #{$x->task_number} stage={$x->stage} t.status={$x->status} closed={$x->closed} "
                       // . "| user=" . ($x->email ?: 'NONE') . " u.status=" . ($x->user_status ?? '-')
                       // . " type=" . ($x->user_type ?? '-')
                       // . " | sprint_active={$x->sprint_active} project_active={$x->project_active} customer_active={$x->customer_active}\n";
                // }
            // }
            // echo "\n";
        // }
        // echo "Done.\n";
    // }

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

    /**
     * Stages a reminder cron covers, configured in Settings > System Params > Reminders.
     * Each reminder has its own param: 'overdue_reminder_stages', 'due_reminder_stages'
     * and 'due_today_reminder_stages'. Falls back to the historical default — everything
     * except 'completed' and 'on_hold' — when the param is missing or unreadable.
     */
    private function reminderStages($key)
    {
        $this->load->model("system_model");
        $stages = $this->system_model->getParam($key, true);
        if(!is_array($stages)){
            return ['new','in_progress','testing','staging','validated','stopped'];
        }
        return array_values(array_filter(array_map('strval', $stages), 'strlen'));
    }

    /**
     * Renders a configured stage list as an escaped SQL IN() body. An empty selection
     * means nobody should be reminded; "''" keeps the query shape valid and matches nothing.
     */
    private function stageInList($key)
    {
        $stages = $this->reminderStages($key);
        return empty($stages)
            ? "''"
            : implode(",", array_map([$this->db, 'escape'], $stages));
    }

    /**
     * Single source of truth for the overdue-tasks query.
     * Used by sendOverdueTaskReminders() (sends) and dryRunOverdueTasks() (reports only).
     */
    private function overdueTasksQuery()
    {
        $stageList = $this->stageInList("overdue_reminder_stages");

        return "select t.uuid, t.id, t.task_number, t.name, t.stage, t.source, t.description, t.section, t.due_date, t.estimated_hours,
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
                and t.stage in({$stageList})
                and u.email IS NOT NULL
                and u.status = '1'
                and u.user_type = 'developer'
                and t.status = '1'
                and t.closed = '0'
                and s.active = '1'
                and p.active = '1'
                and c.active = '1'
                order by u.email, c.company_name, p.name, t.due_date ASC";
    }

    // --- DEV DEBUG (disabled). Uncomment to re-enable. ---
    // /**
     // * Dry run: prints who WOULD receive the overdue reminder, without sending or
     // * queueing anything. Hit via URL: cron/dryRunOverdueTasks
     // */
    // public function dryRunOverdueTasks()
    // {
        // $this->blockOnProduction();
        // $this->db->query("SET @@session.time_zone = '+04:00'");
        // header("Content-Type: text/plain; charset=utf-8");

        // $this->config->load("mailer", false, true);
        // $mailerType = $this->config->item("mailer_type");
        // $mailerEndpoint = $this->config->item("mailer_endpoint");

        // // Pass 'send' as the 3rd URI segment to ALSO dispatch the real emails:
        // //   cron/dryRunOverdueTasks/send
        // $liveSend = ($this->uri->segment(3) === 'send');

        // $today = $this->db->query("SELECT CURDATE() AS d")->row()->d;
        // echo ($liveSend ? "LIVE SEND — report below, then emails are dispatched.\n" : "DRY RUN — no emails sent/queued.\n");
        // echo "DB CURDATE(): {$today}\n";
        // echo "ENVIRONMENT: " . ENVIRONMENT . "  |  mailer_type: " . ($mailerType ?: '(empty)')
           // . "  |  endpoint set: " . (!empty($mailerEndpoint) ? 'yes' : 'NO') . "\n";
        // if(ENVIRONMENT != 'production'){
            // echo "NOTE: non-production, so real sends get a '[TESTING MODE]' subject prefix.\n";
        // }
        // echo str_repeat("=", 60) . "\n\n";

        // $rows = $this->db->query($this->overdueTasksQuery())->result();
        // echo "Matched task rows (pass all filters): " . count($rows) . "\n";

        // if(!empty($rows)){
            // $grouped = [];
            // foreach($rows as $r){
                // if(empty($r->developer_email)) continue;
                // $grouped[$r->developer_email][] = "#{$r->task_number} ({$r->name}) due {$r->due_date}, {$r->days_overdue}d overdue";
            // }
            // echo "Recipients (emails that would be sent): " . count($grouped) . "\n\n";
            // foreach($grouped as $email => $tasks){
                // echo "  {$email}: " . count($tasks) . " task(s)\n";
                // foreach($tasks as $t){ echo "      - {$t}\n"; }
            // }
            // echo "\n";
        // }

        // // Pre-filter breakdown: any task past due, showing WHY rows are excluded.
        // $diag = $this->db->query(
            // "select t.task_number, t.stage, t.status, t.closed, t.due_date,
                    // u.email, u.status as user_status, u.user_type,
                    // s.active as sprint_active, p.active as project_active, c.active as customer_active
             // from tasks t
             // left join sprints s on s.id = t.sprint_id
             // left join projects p on p.id = s.project_id
             // left join task_user tu on tu.task_id = t.id
             // left join customers c on c.customer_id = p.customer_id
             // left join users u on u.id = tu.user_id
             // where t.due_date < CURDATE()
             // and t.stage not in('completed','on_hold')
             // and t.status = '1' and t.closed = '0'
             // order by t.due_date"
        // )->result();
        // echo "Active past-due task_user rows BEFORE user/active filters: " . count($diag) . "\n";
        // foreach($diag as $x){
            // $reasons = [];
            // if(empty($x->email)) $reasons[] = "no assignee";
            // if(($x->user_status ?? null) !== '1') $reasons[] = "user_status=" . ($x->user_status ?? '-');
            // if(($x->user_type ?? null) !== 'developer') $reasons[] = "user_type=" . ($x->user_type ?? '-');
            // if($x->sprint_active !== '1') $reasons[] = "sprint_active={$x->sprint_active}";
            // if($x->project_active !== '1') $reasons[] = "project_active={$x->project_active}";
            // if($x->customer_active !== '1') $reasons[] = "customer_active={$x->customer_active}";
            // $verdict = empty($reasons) ? "INCLUDED" : "EXCLUDED: " . implode(", ", $reasons);
            // echo "   #{$x->task_number} due {$x->due_date} user=" . ($x->email ?: 'NONE') . "  => {$verdict}\n";
        // }

        // if($liveSend){
            // echo "\n" . str_repeat("-", 60) . "\n";
            // echo "SEND MODE: dispatching real emails via sendOverdueTaskReminders()...\n";
            // $this->sendOverdueTaskReminders();
            // echo "Dispatched. Check the mail relay / inbox.\n";
        // }

        // echo "\nDone.\n";
    // }

    // /**
     // * Sends ONE tiny test email through the real Email_model3->save() and prints the
     // * relay's HTTP response, to verify the mailer handshake. Usage:
     // *   cron/testMailerSend/you@example.com
     // */
    // public function testMailerSend()
    // {
        // $this->blockOnProduction();
        // header("Content-Type: text/plain; charset=utf-8");
        // // Email addresses contain '@' which CI's URI filter rejects, so pass the address
        // // hex-encoded: cron/testMailerSend/<hex>. Generate hex with: bin2hex("you@example.com")
        // $arg = $this->uri->segment(3);
        // $to = ($arg && ctype_xdigit($arg)) ? hex2bin($arg) : $arg;
        // if(empty($to)){
            // echo "Usage: cron/testMailerSend/<hex-encoded-email>  (bin2hex the address)\n";
            // return;
        // }
        // $this->config->load("mailer", false, true);
        // echo "mailer_type: " . $this->config->item("mailer_type") . "\n";
        // echo "endpoint: " . $this->config->item("mailer_endpoint") . "\n";
        // echo "Sending test to: {$to}\n";

        // $this->load->model("Email_model3");
        // $result = $this->Email_model3->save($to, "Mailer handshake test", "<p>This is a mailer test from tweezzo dev.</p>");
        // echo "Email_model3->save() returned: " . var_export($result, true) . "\n";
        // echo "(remote mode returns the relay's HTTP status code; 200/201 = accepted. local mode returns null and inserts into email_queue.)\n";
    // }

    // /**
     // * Renders the overdue email HTML for the first recipient and echoes it (no send).
     // * Usage: cron/previewOverdueEmail  — inspect/measure the generated markup.
     // */
    // public function previewOverdueEmail()
    // {
        // $this->blockOnProduction();
        // $this->db->query("SET @@session.time_zone = '+04:00'");
        // $result = $this->db->query($this->overdueTasksQuery())->result();
        // $this->load->model("system_model");
        // $grouped = array();
        // foreach($result as $row){
            // if(empty($row->developer_email)) continue;
            // $key = $row->company_name . '|' . $row->project_name . '|' . $row->customer_id . '|' . $row->project_id;
            // if(!isset($grouped[$row->developer_email][$key])){
                // $grouped[$row->developer_email][$key] = [
                    // 'customer_name' => $row->company_name, 'project_name' => $row->project_name,
                    // 'customer_id' => $row->customer_id, 'project_id' => $row->project_id, 'tasks' => [],
                // ];
            // }
            // $grouped[$row->developer_email][$key]['tasks'][] = $row;
        // }
        // $first = reset($grouped);
        // if(empty($first)){ echo "No recipients."; return; }
        // $emailData = ['logo' => $this->system_model->getParam("logo"), 'customerProjects' => $first, 'show_lifecycle' => false];
        // $content  = $this->load->view("_email/header", $emailData, true);
        // $content .= $this->load->view("_email/overdueTasks", $emailData, true);
        // $content .= $this->load->view("_email/footer", [], true);
        // echo $content;
    // }

    public function sendOverdueTaskReminders()
    {
        // Send reminders for tasks that are past due
        $this->db->query("SET @@session.time_zone = '+04:00'");
        $result = $this->db->query($this->overdueTasksQuery())->result();
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
                $this->Email_model3->save($developerEmail, "Reminder: You have tasks past their due date", $content);
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
                        (SELECT COUNT(t.id)
                           FROM tasks t
                          WHERE t.sprint_id = s.id
                            AND t.status = '1'
                            AND t.closed = '0'
                            AND t.stage = 'staging') AS staging_count
                    FROM sprint_validation_reminders svr
                    JOIN sprints s ON s.id = svr.sprint_id
                    JOIN projects p ON p.id = s.project_id
                    JOIN customers c ON c.customer_id = p.customer_id
                    WHERE svr.ready_for_validation = 1
                      AND s.status = '1'
                      AND s.active = '1'
                      AND p.active = '1'
                      AND c.active = '1'
                      AND (svr.last_sent_on IS NULL
                           OR svr.last_sent_on <= (NOW() - INTERVAL 7 DAY))
                      AND (SELECT COUNT(t2.id)
                             FROM tasks t2
                            WHERE t2.sprint_id = s.id
                              AND t2.status = '1'
                              AND t2.closed = '0'
                              AND t2.stage = 'staging') > 0";
        $candidates = $this->db->query($query)->result();

        if(empty($candidates)) return;

        $this->load->model("Email_model3");
        $this->load->model("system_model");

        foreach($candidates as $item){
            if ((int) $item->staging_count <= 0) {
                continue;
            }
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