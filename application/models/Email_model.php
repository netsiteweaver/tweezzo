<?php

/**
 * Description of Email
 *
 * @author Reeaz Ramoly <reeaz@netsiteweaver.com>
 */
class Email_model extends CI_Model{
    var $data;

    public function __construct() {
        parent::__construct();
        $this->load->model("system_model");
        $this->smtp_settings = $this->system_model->getParam("smtp_settings",true);
    }

    public function send_admin($subject, $message)
    {

        $this->load->model("system_model");
        $idJson = $this->system_model->getParam("send_enquiries_to");
        $ids = json_decode($idJson);
        if(empty($ids)) return;

        $sql = "select id, email from users where id in (";
        foreach($ids as $id){
            $sql = $sql.strval($id).", ";
        }
        $sql = substr($sql, 0, strlen($sql) - 2).")";
        $qry = $this->db->query($sql)->result();
        foreach($qry as $q){
            $this->send($q->email, $subject, $message);
        }
        
    }

    public function send($to,$subject,$message) 
    {
        $this->save($to,$subject,$message);
        return true;
    }

    public function send2($data) {
        
        $this->load->model('system_model');
        $data['companyInfo'] = $this->system_model->getCompanyInfo();
        $content = $this->load->view("email/".$data['email_template'],$data, true);

        $mailer = $this->config->item('mailer');
        $this->load->library('email');
        $this->email->from($mailer);
        $this->email->to($data['to']);
        $this->email->subject($data['subject']);
        $this->email->message($content);

        if($this->email->send(true)){
            return true;
        }else{
            return false;
        }
        
    }    

    public function resetPassword($data) {
      
        $this->load->model('system_model');
        $data['companyInfo'] = $this->system_model->getCompanyInfo();
        $content = $this->load->view("email/".$data['email_template'],$data, true);

        $mailer = $this->config->item('mailer');
        $this->load->library('email');
        $this->email->from($mailer);
        $this->email->to($data['to']);
        $this->email->subject($data['subject']);
        $this->email->message($content);
        
        if($this->email->send(true)){
            return true;
        }else{
            return false;
        }
        
    }

    private function save($recipient,$subject,$content)
    {
        $this->load->model("system_model");
        $testing_mode = $this->system_model->getParam("testing_mode");
        $this->db->set("date_created",'NOW()',FALSE);
        $var = array(
            'uuid'          =>  gen_uuid(),
            'recipients'     =>  $recipient,
            'subject'       =>  $subject,
            'content'       =>  $content,
            'date_sent'     =>  NULL,
            'stage'         =>  ($testing_mode=='yes')?'sent':NULL
        );

        $this->db->insert("email_queue",$var);
    }

    public function processOrder($stage,$data)
    {
        //get users
        // $users = $this->db->select("id,email,name,username")->from("users")->where("status",1)->get()->result();

        //get notifications from params
        $this->load->model("system_model");
        $notifications = json_decode($this->system_model->getParam("notifications"));

        if(empty($notifications)) return;

        $email_list = "";
        foreach($notifications as $n){
            if($n->stage == $stage->id){
                $user = $this->db->select("id,email,name,username")->from("users")->where(["status"=>1,"id"=>$n->user])->get()->row();
                $email_list .= $user->email.",";
            }
        }
        $email_list = substr($email_list,0,strlen($email_list)-1);

        //get logo
        $this->load->model("system_model");
        $data['logo'] = $this->system_model->getParam("logo");

        $content = $this->load->view("email/orders/process",$data, true);

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = (($this->smtp_settings->port=='465')?'ssl://':'') . $this->smtp_settings->hostname;
        $config['smtp_user'] = $this->smtp_settings->username;
        $config['smtp_pass'] = $this->smtp_settings->password;
        $config['smtp_port'] = $this->smtp_settings->port;

        $config['charset'] = 'utf-8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = "html";
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";

        try{
            $this->load->library('email');
            $this->email->initialize($config);
            $this->email->from($this->smtp_settings->from,$this->smtp_settings->displayname);
            $this->email->to($email_list);
            $this->email->subject('Order '.$data['documentnumber'].' is Being Processed');
            $this->email->message($content);

            if(ENVIRONMENT!="development"){
                if($this->email->send(true)){
                    // return array(true);
                }else{
                    echo $this->email->print_debugger();
                    // return array(false,$this->email->print_debugger());
                }
                $this->email->clear();
            }

        }catch(Exception $ex){
            debug($ex);
        }
    }

}