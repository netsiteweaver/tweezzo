<?php

/**
 * Description of Email
 *
 * @author Reeaz Ramoly <reeaz@netsiteweaver.com>
 */
class Email_model2 extends CI_Model{
    var $data;

    public function __construct() {
        parent::__construct();
        $this->load->model("system_model");
        $this->smtp_settings = $this->system_model->getParam("smtp_settings",true);
    }

    public function save($recipient,$subject,$content)
    {
        $this->db->set("date_created",'NOW()',FALSE);
        $var = array(
            'uuid'          =>  gen_uuid(),
            'recipients'     =>  $recipient,
            'subject'       =>  $subject,
            'content'       =>  $content,
            'date_sent'     =>  NULL,
        );
        $this->db->insert("email_queue",$var);
    }

    public function sendFromQueue($to,$subject,$message)
    {

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
            $this->email->to($to);
            $this->email->subject($subject);
            $this->email->message($message);

            if($this->email->send(true)){
                return array(true);
            }else{
                // echo $this->email->print_debugger();
                return array(false,$this->email->print_debugger());
            }
        }catch(Exception $ex){
            debug($ex);
        }
            
    } 

}