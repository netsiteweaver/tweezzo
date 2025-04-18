<?php

class Audittrail_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }

    public function get($id)
    {
        $this->db->select('l.id,l.uuid,SUBSTRING(l.created_on,1,16) AS date,u.name,l.ip,l.controller,l.method,l.uri,l.meta');
        $this->db->from('log l');
        $this->db->join('users u','u.id=l.user_id','left');
        $this->db->where('l.id',$id);
        $this->db->order_by('created_on','desc');
        $obj = $this->db->get();
        return $obj->row();
    }

    
    public function getPaged($page=1,$rows_per_page=50)
    {
        if( (empty($page)) || ($page <= 0) ) $page =1;
        $offset = ( ($page-1)*$rows_per_page);  

        $filter_start_date = isset($_SESSION['filter_start_date'])?$_SESSION['filter_start_date']:"";
        $filter_end_date = isset($_SESSION['filter_end_date'])?$_SESSION['filter_end_date']:"";
        $filter_user = isset($_SESSION['filter_user'])?$_SESSION['filter_user']:"";
        $filter_ip = isset($_SESSION['filter_ip'])?$_SESSION['filter_ip']:"";
        $filter_controller = isset($_SESSION['filter_controller'])?$_SESSION['filter_controller']:"";
        $filter_method = isset($_SESSION['filter_method'])?$_SESSION['filter_method']:"";

        $this->db->select('l.id,l.uuid,SUBSTRING(l.created_on,1,16) AS date,u.name,l.ip,l.controller,l.method,l.meta');
        $this->db->from('log l');
        $this->db->join('users u','u.id=l.user_id','left');
        if(!empty($filter_start_date)) $this->db->where("l.created_on >=",$filter_start_date." 00:00:00");
        if(!empty($filter_end_date)) $this->db->where("l.created_on <=",$filter_end_date." 23:59:59");
        if(!empty($filter_user)) $this->db->where("u.name",$filter_user);
        if(!empty($filter_ip)) $this->db->where("l.ip",$filter_ip);
        if(!empty($filter_controller)) $this->db->where("l.controller",$filter_controller);
        if(!empty($filter_method)) $this->db->where("l.method",$filter_method);
        $this->db->limit($rows_per_page,$offset);
        $this->db->order_by('created_on','desc');
        $obj = $this->db->get();
        $rows = $obj->result();

        return $rows;
    }

    public function getUsers()
    {
        return $this->db->select('u.id,u.name')->distinct('u.name')
        ->from('log l')
        ->join('users u','u.id=l.user_id','left')
        ->order_by('u.name')
        ->get()
        ->result();
    }

    public function getIPs()
    {
        return $this->db->select('u.ip')->distinct('u.ip')
        ->from('log l')
        ->join('users u','u.id=l.user_id','left')
        ->order_by('u.ip')
        ->get()
        ->result();
    }

    public function totalRows()
    {
        $filter_start_date = isset($_SESSION['filter_start_date'])?$_SESSION['filter_start_date']:"";
        $filter_end_date = isset($_SESSION['filter_end_date'])?$_SESSION['filter_end_date']:"";
        $filter_user = isset($_SESSION['filter_user'])?$_SESSION['filter_user']:"";
        $filter_ip = isset($_SESSION['filter_ip'])?$_SESSION['filter_ip']:"";
        $filter_controller = isset($_SESSION['filter_controller'])?$_SESSION['filter_controller']:"";
        $filter_method = isset($_SESSION['filter_method'])?$_SESSION['filter_method']:"";

        $this->db->select('count(l.id) AS ct');
        $this->db->from('log l');
        $this->db->join('users u','u.id=l.user_id','left');
        if(!empty($filter_start_date)) $this->db->where("l.created_on >=",$filter_start_date." 00:00:00");
        if(!empty($filter_end_date)) $this->db->where("l.created_on <=",$filter_end_date." 23:59:59");
        if(!empty($filter_user)) $this->db->where("u.name",$filter_user);
        if(!empty($filter_ip)) $this->db->where("l.ip",$filter_ip);
        if(!empty($filter_controller)) $this->db->where("l.ip",$filter_controller);
        if(!empty($filter_method)) $this->db->where("l.ip",$filter_method);

        return $this->db->get()->row('ct');
    }

}