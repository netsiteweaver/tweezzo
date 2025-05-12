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

    public function getDueTasks()
    {
        $days = $this->uri->segment(3);
        if(empty($days)){
            $days = 7;
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
                and t.stage not in('completed','staging','validated')
                and u.email IS NOT NULL
                order by u.email";
        $result = $this->db->query($query)->result();
        $grouped = array();
        if(!empty($result)){
            foreach($result as $row){
                if(!in_array($row->developer_email,$grouped)){
                    $grouped[$row->developer_email][] = array(
                        "email" => $row->developer_email,
                        "tasks" => $row
                    );
                }
            }
        }

        $this->load->model("Email_model3");
        $this->load->model("system_model");

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
            $this->Email_model3->save($tasks[0]['email'],"Tasks Due Reminder",$content);
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