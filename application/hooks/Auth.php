<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{

		parent::__construct();

	}
	
	public function check()
	{
		
		$userid = isset($_SESSION['user_id'])?$_SESSION['user_id']:'';
		$controller = $this->uri->segment(1);
		$method = $this->uri->segment(2);

		if($controller == "portal") {
			if( ($this->uri->segment(2) == "customers") && ($this->uri->segment(3) != "signin") ){
				if (empty($_SESSION['customer_access_id'])){
					
				}
			}elseif( ($this->uri->segment(2) == "developers") && ( ($this->uri->segment(3) == "authenticate") || ($this->uri->segment(3) == "forgotPassword")  || ($this->uri->segment(3) == "processForgotPassword")) ){
				
			}elseif( ($this->uri->segment(2) == "developers") && ($this->uri->segment(3) != "signin") ){
				if (empty($_SESSION['developer_id'])){
					redirect( base_url( "portal/developers/signin" ));
				}

			}elseif( ($this->uri->segment(2) == "customers") && ( ($this->uri->segment(3) == "authenticate") || ($this->uri->segment(3) == "forgotPassword")  || ($this->uri->segment(3) == "processForgotPassword")) ){

			}

		}else{
			if(empty($userid)) {
				if( 
					( ($controller == "users") && (in_array($method,['signin','authenticate','forget-password','forget_password_process','forget_password','check_user_level','isUserPermanent'])) ) || 
					($controller == 'api') || 
					($controller == 'cron') || 
					($controller == 'migrate')){
	
				}else{
					$s1=$this->uri->segment(1);$s2=$this->uri->segment(2);$s3=$this->uri->segment(3);
					$_SESSION['expired_url'] = $s1. ((!empty($s2))?"/".$s2.((!empty($s3))?"/".$s3:""):"");
					if($s1=='ajax'){
						echo json_encode(array(
							"result"=>false,
							"reason"=>"login"));
						exit;
					}
					redirect( base_url("users/signin") );
				}
			}
		}
		
		
	}
}
