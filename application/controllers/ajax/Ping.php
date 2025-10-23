<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ping extends CI_Controller
{
    /**
     * Ping endpoint to check if user session is still active
     * Called every 5 seconds by main.js isLoggedIn() function
     * 
     * If user is not logged in, the Auth hook will catch it and return error
     * If we reach here, user is logged in, so return success
     */
    function index()
    {
        echo json_encode(array(
            "result" => true,
            "user_id" => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            "timestamp" => date('Y-m-d H:i:s')
        ));
    }
}