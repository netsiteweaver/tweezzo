<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Backoffice extends MY_Controller 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function on()
    {
        $_SESSION['backoffice'] = TRUE;
        redirect(base_url());
    }

    public function off()
    {
        $_SESSION['backoffice'] = FALSE;
        redirect(base_url());
    }

}