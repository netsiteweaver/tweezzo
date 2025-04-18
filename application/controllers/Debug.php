<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Debug extends MY_Controller
{

    public $data;

    public function __construct()
    {
        parent::__construct();
        $this->data['controller']   = str_replace("-", "", $this->uri->segment(1, "dashboard"));
        $this->data['method']       = $this->uri->segment(2, "index");
    }

    public function renumberProducts()
    {
        $start = 1;
        $products = $this->db->select("*")->from("products")->get()->result();
        foreach($products as $p){
            $formatted = str_pad($start++,8,'0',STR_PAD_LEFT);
            $this->db->set("stockref",$formatted)->where("product_id",$p->product_id)->update("products");
        }
        $sr = $this->db->select("max(stockref) as sr")->from("products")->get()->row("sr");
        $sr = intval($sr);
        $this->db->set("value",$sr+1)->where("title","stockref_lastnumber")->update("params");
    }
}