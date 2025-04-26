<?php

/**
 * Description of Email
 *
 * @author Reeaz Ramoly <reeaz@netsiteweaver.com>
 */
class Email_model3 extends CI_Model{
    var $data;

    public function __construct() {
        parent::__construct();
        $this->load->model("system_model");
        $this->smtp_settings = $this->system_model->getParam("smtp_settings",true);
        $this->load->config("mailer");
        $this->mailer_endpoint = $this->config->item("mailer_endpoint");
        $this->mailer_type = $this->config->item("mailer_type");
        $this->token = $this->config->item("token");
    }

    public function save($recipient,$subject,$content)
    {
        if(ENVIRONMENT == "development")
        {
            $subject = "**" . $subject;
        }elseif(ENVIRONMENT == "staging")
        {
            $subject = "##" . $subject;
        }
        $httpcode = 0;
        $var = array(
            'uuid'          =>  gen_uuid(),
            'date_created'  =>  date("Y-m-d H:i:s"),
            'recipients'    =>  $recipient,
            'subject'       =>  $subject,
            'content'       =>  $content,
            'date_sent'     =>  NULL,
        );

        if($this->mailer_type == "local")
        {
            $this->db->insert("email_queue",$var);
        }
        else
        {
            $ch = curl_init($this->mailer_endpoint . "queueEmail/");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($var));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$this->token}",
                "Origin: https://tweezzo.com"
            ]);
            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Get HTTP status code
            $err = curl_error($ch); // Check for curl error
            curl_close($ch);    
            // debug($response);
            return $httpcode;
        }
    }

}