<?php

function deleteMenuEntries($controller)
{
    echo "<br>Deleting menu entires for ".$controller;
    $CI = & get_instance();
    deletePermissions($controller);
    $CI->db->from("menu")->where("controller",$controller)->delete();
}

function createMenuEntries($controller,$display_name,$givePermissions=FALSE,$forBackOffice=FALSE)
{
    $backoffice = ($forBackOffice)?1:0;
    $CI = & get_instance();
    $display_order = $CI->db->select("MAX(display_order) AS display_order")->from("menu")->where("display_order !=",999)->get()->row('display_order');
    $id = $CI->db->select("MAX(id) AS id")->from("menu")->get()->row('id');
    $id = ceil($id/10)*10;
    $vars = array(
        array('id'=>$id, 'type'=>'menu', 'nom'=>$display_name,'controller'=>$controller,  'action'=>'','params'=>NULL, 'url'=>NULL, 'class'=>'fa-user', 'display_order'=>$display_order, 'parent_menu'=>0, 'visible'=>'1', 'Normal'=>'0', 'Admin'=>'1', 'Root'=>'1', 'module'=>'', 'status'=>'1','backoffice'=>$backoffice),
        array('id'=>$id+1, 'type'=>'menu', 'nom'=>'Listing','controller'=>$controller,  'action'=>'listing','params'=>NULL, 'url'=>NULL, 'class'=>'fa-list', 'display_order'=>'10', 'parent_menu'=>$id, 'visible'=>'1', 'Normal'=>'0', 'Admin'=>'1', 'Root'=>'1', 'module'=>'', 'status'=>'1','backoffice'=>$backoffice),
        array('id'=>$id+2, 'type'=>'menu', 'nom'=>'Add', 'controller'=>$controller,   'action'=>'add','params'=>NULL, 'url'=>NULL, 'class'=>'fa-plus-square-o', 'display_order'=>'20', 'parent_menu'=>$id, 'visible'=>'1', 'Normal'=>'0', 'Admin'=>'1', 'Root'=>'1', 'module'=>'', 'status'=>'1','backoffice'=>$backoffice),
        array('id'=>$id+3, 'type'=>'menu', 'nom'=>'Edit','controller'=>$controller,  'action'=>'edit','params'=>NULL, 'url'=>NULL, 'class'=>'', 'display_order'=>999, 'parent_menu'=>0, 'visible'=>'0', 'Normal'=>'0', 'Admin'=>'1', 'Root'=>'1', 'module'=>'', 'status'=>'1','backoffice'=>$backoffice),
        array('id'=>$id+4, 'type'=>'menu', 'nom'=>'Delete','controller'=>$controller,  'action'=>'delete','params'=>NULL, 'url'=>NULL, 'class'=>'', 'display_order'=>999, 'parent_menu'=>0, 'visible'=>'0', 'Normal'=>'0', 'Admin'=>'1', 'Root'=>'1', 'module'=>'', 'status'=>'1','backoffice'=>$backoffice),
    );
    $CI->db->insert_batch("menu",$vars);
    givePermissions([$id,$id+1,$id+2,$id+3,$id+4]);
}

function givePermissions($ids=array())
{
    $CI = & get_instance();
    if(!empty($ids)){
        $users = $CI->db->select("id")->from("users")->get()->result();
        foreach($users as $user){
            foreach($ids as $id){
                $CI->db->set("menu_id",$id);
                $CI->db->set("user_id",$user->id);
                $CI->db->set("create",'0');
                $CI->db->set("update",'0');
                $CI->db->set("read",'1');
                $CI->db->set("delete",'0');
                $CI->db->insert("permissions");
            }
        }
    }
}

function deletePermissions($controller)
{
    echo "<br>Deleting permissions for ".$controller;
    $CI = & get_instance();
    $ids = $CI->db->select("id")->from("menu")->where("controller",$controller)->get()->result();
    debug($ids);
    foreach($ids as $id){
        $CI->db->where("menu_id",$id)->delete("permissions");
    }
}

function createMenuEntry(String $display_name,String $controller,String $action,String $class,Int $display_order,Int $parent_menu,Bool $visible,Bool $normal,Bool $admin, Bool $root)
{
    $CI = & get_instance();
    $entry = array(
        'id'=>NULL,
        'type'=>'menu',
        'nom'=>$display_name,
        'controller'=>$controller,
        'action'=>$action,
        'params'=>NULL,
        'url'=>NULL,
        'class'=> $class,
        'display_order'=>$display_order,
        'parent_menu'=>$parent,
        'visible'=>$visible,
        'Normal'=>$normal,
        'Admin'=>$admin,
        'Root'=>$root,
        'module'=>'',
        'status'=>'1');

    $CI->db->insert("menu",$entry);
}
