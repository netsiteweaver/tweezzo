<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
*
* Reeaz Ramoly
* 19 Sep 2016
*
*/
class Native_session {

	public function __construct()
	{
		
	}

    public function set( $key, $value )
    {
        $_SESSION[$key] = $value;
    }	

    public function get( $key )
    {
    	return isset($_SESSION[ $key ]) ? $_SESSION[$key] : null ;
    }

    public function clear($key)
    {
    	unset( $_SESSION[$key] );
    }

    /* FLASH SESSION VARIABLES */
    public function set_flashdata($key, $value)
    {
    	$this->set('flash_'.$key, $value);
    }

    public function get_flashdata($key)
    {
    	$flashdata = $this->get('flash_'.$key);
    	if(isset($flashdata)){
    		$result = $this->get('flash_'.$key);
    		$this->clear('flash_'.$key);
    	}else{
    		$result = null;
    	}
    	return $result;

    }
}

