<?php

class Menu_model extends CI_Model {

    var $user_level;
    var $user_id;
    var $modules;

    function __construct() {

        global $modules;
        parent::__construct();
        $this->user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
        $this->user_level = isset($_SESSION['user_level']) ? $_SESSION['user_level'] : '';
    }

    function get_all_root() {

        $this->db->where('parent_menu', 0);
        $this->db->where('type', 'menu');
        $this->db->where('visible', '1');
        $this->db->where('status', '1');
        $obj = $this->db->get('menu');
        return $obj->result();
    }

    function get_glyphicons() {

        $this->db->order_by('classname');
        $obj = $this->db->get('glyphicons');
        return $obj->result();
    }

    function get($id = 0,$backoffice=0) {

        $backoffice = ((isset($_SESSION['backoffice']))&&($_SESSION['backoffice']=='on')) ? 1 : 0;
        $this->db->from("menu m");
        $this->db->select("p.id id, m.id mid, m.nom, m.class, m.controller, m.action, m.color");
        $this->db->join("permissions p", "p.menu_id = m.id");
        $this->db->where('m.parent_menu', $id);
        $this->db->where('p.user_id', $this->user_id);
        $this->db->where('m.type', 'menu');
        $this->db->where('m.visible', '1');
        $this->db->where('m.status', '1');
        $this->db->where('p.read', 1);
        $this->db->where('backoffice', $backoffice);
        if ($this->user_level == "Root"){
            $this->db->where('m.Root', 1);
        }
        if ($this->user_level == "Admin"){
            $this->db->where('m.Admin', 1);
            $this->db->where_in('m.module', $this->modules);
        }
        if ($this->user_level == "Normal"){
            $this->db->where('m.Normal', 1);
            $this->db->where_in('m.module', $this->modules);
        }
        
        // if ($this->user_level != "Root")
        //     $this->db->where_in('m.module', $this->modules);
        $this->db->order_by('m.display_order');
        $obj = $this->db->get();
        // debug($this->db->last_query());
        // debug($obj->result());
        $items = $obj->result();

        foreach ($items as $item) {
            $menu[$item->id] = $item;

            $sub_items = $this->get_child($item->mid);

            if ($sub_items) {

                $sub_menu[$item->id] = $sub_items;
            }
        }

        $menus = array(
            'main' => (isset($menu)) ? $menu : null,
            'sub' => (isset($sub_menu)) ? $sub_menu : null
        );

        return $menus;
    }

    function getforlisting($id = 0) {

        $this->db->from("menu m");
        $this->db->select("m.id mid, m.nom, m.class, m.controller, m.action, m.visible, m.Root, m.Admin, m.Normal, m.display_order, m.type");
        // $this->db->join("permissions p", "p.menu_id = m.id","left");
        $this->db->where('m.parent_menu', $id);
        // $this->db->where('p.user_id', $this->user_id);
        $this->db->where('m.type', 'menu');
        // $this->db->where('m.visible', '1');
        $this->db->where('m.status', '1');
        // $this->db->where('p.read', 1);
        // if ($this->user_level != "Root")
        //     $this->db->where_in('m.module', $this->modules);
        $this->db->order_by('m.display_order');
        $obj = $this->db->get();
        $items = $obj->result();

        foreach ($items as $item) {
            $menu[$item->mid] = $item;

            $sub_items = $this->get_child($item->mid);

            if ($sub_items) {

                $sub_menu[$item->mid] = $sub_items;
            }
        }

        $menus = array(
            'main' => (isset($menu)) ? $menu : null,
            'sub' => (isset($sub_menu)) ? $sub_menu : null
        );

        return $menus;
    }

    function get_child($parent_id) {

        $this->db->select("*, menu.id mid");
        $this->db->join("permissions", "permissions.menu_id = menu.id");

        $this->db->where('parent_menu', $parent_id);
        $this->db->where('permissions.user_id', $this->user_id);
        $this->db->where('type', 'menu');
        $this->db->where('visible', '1');
        $this->db->where('status', '1');
        $this->db->where('permissions.read', 1);
        /* if ($this->user_level == "Root"){
            $this->db->where('menu.Root', 1);
        }
        if ($this->user_level == "Admin"){
            $this->db->where('menu.Admin', 1);
            $this->db->where_in('menu.module', $this->modules);
        }
        if ($this->user_level == "Normal"){
            $this->db->where('menu.Normal', 1);
            $this->db->where_in('menu.module', $this->modules);
        } */

        // if ($this->user_level != "Root")
        //     $this->db->where_in('menu.module', $this->modules);

        $this->db->order_by('display_order');
        $obj = $this->db->get('menu');
        // debug($obj->result());
        if ($obj) {
            return $obj->result();
        }
    }

    function fetchById($id){
		$this->db->where('id', $id);
        $this->db->where('status', '1');
		$obj = $this->db->get("menu");
		if( ($obj) && ($obj->num_rows()==1) ){
			return $obj->row();
		}else{
			return false;
		}

	}
        
    function fetchAll($id = 2) {

        $this->db->select("*, menu.id mid, permissions.id perm_id");
        $this->db->join("permissions", "permissions.menu_id = menu.id", "left");
        $this->db->where('visible', '1');
        $this->db->where('status', '1');
        $this->db->order_by('menu.id');
        $obj = $this->db->get('menu');
        echo $this->db->last_query();
        $items = $obj->result();
        $result = array();
        foreach ($items as $key => $item) {
            if (($item->user_id == $id) || ($item->user_id == null)) {
                $result[$key] = $item;
            }
        }
        return $result;
    }

    function listAll($id = 2, $user_level = 'Normal') {

        $parent_menus = $this->getMenus($user_level, 0, 1);
        foreach ($parent_menus as $parent_menu) {

            $perms = $this->getPermissions($id, $parent_menu->id);
            $item = $parent_menu;
            $item->permission_id = (!empty($perms)) ? $perms->id : null;
            $item->read = (!empty($perms)) ? $perms->read : 0;
            $item->type = "main";
            $result[] = $item;

            $submenus = $this->getMenus($user_level, $parent_menu->id, 1);
            foreach ($submenus as $submenu)
            {
                $perms = $this->getPermissions($id, $submenu->id);
                $item = $submenu;
                $item->permission_id = (!empty($perms)) ? $perms->id : null;
                $item->read = (!empty($perms)) ? $perms->read : 0;
                $item->type = "sub";
                $result[] = $item;

                if (!empty($submenu->controller)) {

                    $hidden_items = $this->getMenus($user_level, 0, 0, $submenu->controller);

                    foreach ($hidden_items as $hiddenitem) {

                        $perms = $this->getPermissions($id, $hiddenitem->id);
                        $item = $hiddenitem;
                        $item->permission_id = (!empty($perms)) ? $perms->id : null;
                        $item->read = (!empty($perms)) ? $perms->read : 0;
                        $item->type = "hidden";
                        $result[] = $item;
                    }
                }
            }
        }
        return $result;
    }

    private function getPermissions($user_id = "", $menu_id, $profile_id = "") {
        $this->db->select("id,read");
        $this->db->from("permissions");
        if (!empty($profile_id)) {
            $this->db->where('profile_id', $profile_id);
        } else {
            $this->db->where('user_id', $user_id);
        }

        $this->db->where('menu_id', $menu_id);
        $obj = $this->db->get();
        $items = ($obj) ? $obj->row() : false;
        return $items;
    }

    private function getMenus($user_level = 'Normal', $parent_menu = 0, $visible = 1, $controller = '') {
        $this->db->select('id,type,nom,controller,action,class,display_order,parent_menu,visible,Normal,Admin,Root,status');
        $this->db->order_by('type', 'asc');
        $this->db->order_by('display_order');
        switch ($user_level) {
            case 'Root':
                $this->db->where('Root', 1);
                break;
            case 'Admin':
                $this->db->where('Admin', 1);
                break;
            case 'Normal':
                $this->db->where('Normal', 1);
                break;
        }
        $this->db->where('parent_menu', $parent_menu);
        $this->db->where('visible', $visible);
        if (!empty($controller)) {
            $this->db->where('controller', $controller);
        }
        $this->db->where('status', '1');
        $obj = $this->db->get('menu');
        $items = ($obj) ? $obj->result() : false;
        return $items;
    }

    public function rearrange($id, $display_order) {
        $this->db->where('id', $id);
        $vars = array(
            'display_order' => $display_order
        );
        $this->db->update('menu', $vars);
    }

    public function delete($id){

        $this->db->where('id',$id);
        $result = $this->db->delete('menu');

        $this->db->where('menu_id',$id);
        $this->db->delete('permissions');

        return true;

    }    

    public function getMenuName($id)
    {
        $this->db->select("id,nom, controller");
        $this->db->where("id",$id);
        $obj = $this->db->get("menu");
        $result = $obj->row();
        return $result;
    }

    public function deleteAjax($id)
    {

        $this->db->set("status","0");
        $this->db->where("id",$id);
        $this->db->update("menu");

        $this->db->where("menu_id",$id);
        $this->db->delete("permissions");

        return;
    }

    public function getNextOrder()
    {
        $id = $this->db->select("max(display_order) AS new_id")->from('menu')->where(array("visible"=>1,"parent_menu"=>0))->get()->result();
        return ceiling($id[0]->new_id,10);
    }

    public function getNextId()
    {
        $id = $this->db->select("max(id) AS new_id")->from('menu')->get()->result();
        return ceiling($id[0]->new_id,10);
    }

}
