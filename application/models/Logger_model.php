<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Log_model
 *
 * @author Your Name <Reeaz Ramoly at Netsiteweaver>
 */
class Logger_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
    }
    
    public function save($notes="")
    {
        if(!isset($_SESSION['user_id'])) return;
        $data = array(
            "created_on"=> date('Y-m-d H:i:s'),
            "user_id"   => isset($_SESSION['user_id'])?$_SESSION['user_id']:0,
            "uuid"      =>  gen_uuid(),
            "ip"        => $this->input->ip_address(),
            "controller"=> $this->uri->segment(1,'dashboard'),
            "method"    => $this->uri->segment(2,'index'),
            "uri"       =>  $this->uri->uri_string(),
            "notes"    => !isset($_SESSION['user_id'])?"Not Logged In":"",
            "meta"      =>  $notes
        );
        $this->db->insert("log",$data);
    }

    public function getPaged($page=1,$rows_per_page=50,$filter_user="",$filter_ip="")
    {
        if( (empty($page)) || ($page <= 0) ) $page =1;
        $offset = ( ($page-1)*$rows_per_page);  

        $this->db->select('l.created_on,l.ip,l.controller,l.method,l.notes,u.name');
        $this->db->from('log l');
        $this->db->join('users u','u.id=l.user_id','left');
        if(!empty($filter_user)) $this->db->where('l.user_id',$filter_user);
        $this->db->limit($rows_per_page,$offset);
        $this->db->order_by('created_on','desc');
        $obj = $this->db->get();
        $rows = $obj->result();

        $qry = 'SELECT DISTINCT(user_id) AS id,COUNT(user_id) AS ct, u.name,u.status FROM log AS l
        JOIN users AS u ON u.id = l.user_id
        GROUP BY user_id
        ORDER BY ct DESC';
        $users = $this->db->query($qry)->result();

        $qry = 'SELECT DISTINCT(ip),COUNT(ip) AS ct FROM log
        GROUP BY ip
        ORDER BY ct DESC';
        $ips = $this->db->query($qry)->result();

        if(!empty($filter_user)){
            $total = $this->db->query("SELECT COUNT(id) AS ct FROM log WHERE user_id = $filter_user")->row();
        }else{
            $total = $this->db->query("SELECT COUNT(id) AS ct FROM log")->row();
        }
        

        return array(
            'rows'  =>  $rows,
            'total' =>  $total->ct,
            'users' =>  $users,
            'ips'   =>  $ips
        );
    }
}
