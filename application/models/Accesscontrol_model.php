<?php

class Accesscontrol_model extends CI_Model {

    var $user_level;
    var $user_id;
    var $modules;

    function __construct() {
        parent::__construct();
        $this->user_level = isset($_SESSION['user_level'])?$_SESSION['user_level']:"";//$_SESSION['user_level'];
        $this->user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:"";//$_SESSION['user_id'];
    }

    public function authorised($controller = "", $method = "") {

        $controller = (empty($controller)) ? $this->uri->segment(1) : $controller;
        $action     = (empty($method)) ? $this->uri->segment(2) : $method;

        $user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:"";
        $user = isset($_SESSION['user_name'])?$_SESSION['user_name']:"";
        $user_level = isset($_SESSION['user_level'])?$_SESSION['user_level']:"Normal";

        if ((empty($user_id)) && ( ($controller == 'resources') && ($action == "edit") )) {
            return false;
        }

        $vars['user_id'] = $user_id;
        $vars['controller'] = $controller;
        $vars[$user_level] = 1;
        if (!empty($action)) {
            $vars['action'] = $action;
        }
        
        $this->db->select("read");
        $this->db->join('permissions', 'permissions.menu_id = menu.id');
        $this->db->where($vars);
        $obj = $this->db->get('menu');
        $result = false;
        if ($obj->num_rows() > 0) {
            $result = $obj->row("read");
        }
        return $result;
    }

    public function check($permission = "read", $section = "", $controller = "") {

        // $this->load->model('log_model');

        $controller = (empty($controller)) ? $this->uri->segment(1) : $controller;
        $action = (!empty($section)) ? $section : $this->uri->segment(2);

        $user_id = $_SESSION['user_id'];
        $user = $_SESSION['user_name'];
        $user_level = $_SESSION['user_level'];
        if (empty($user_level))
            $user_level = "Normal";

        if ((empty($user_id)) && ( ($controller == 'menu') && ($action == "edit") )) {
            return false;
        }

        $this->db->from('menu');
        $this->db->join('permissions', 'permissions.menu_id = menu.id');

        $vars['user_id'] = $user_id;
        $vars['controller'] = $controller;
        // $vars[$user_level] = 1;
        if (!empty($action)) {
            $vars['action'] = $action;
        }
        $this->db->where($vars);
        $result = $this->db->get()->row();
        if (!empty($result)) {
            if (strtolower($permission) == "create") {
                return $result->create;
            } elseif (strtolower($permission) == "read") {
                return $result->read;
            } elseif (strtolower($permission) == "update") {
                return $result->update;
            } elseif (strtolower($permission) == "delete") {
                return $result->delete;
            } elseif (strtolower($permission) == "all") {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function read($permission = "read") {
        $this->check($permission);
    }

}
