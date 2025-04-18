<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Reeaz Ramoly
 * This class manages the menu items in the system. 
 */
class Menu extends MY_Controller {

    var $data;

    function __construct()
    {
        parent::__construct();
        $this->load->model('menu_model');
        $this->data['page_title'] = "Menu Items";
        $this->load->model("accesscontrol_model");
        $this->mybreadcrumb->add('Menu Items', base_url('menu/listing'));
    }

    public function index()
    {
        redirect(base_url('menu/listing'));
    }

    public function listing()
    {

       if(!isAuthorised('menu','listing')) return;

        $this->load->model("accesscontrol_model");
        $this->data['perms']['can_delete'] = $this->accesscontrol_model->authorised('menu', "delete",false);
        $this->data['perms']['can_edit'] = $this->accesscontrol_model->authorised('menu', "edit",false);
        $this->data['perms']['can_add'] = $this->accesscontrol_model->authorised('menu', "add",false);

        $this->data['resources'] = $this->menu_model->getforlisting();
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/menu/listing",$this->data,true);
        $this->load->view("/layouts/default",$this->data);            
    }

    public function submenu()
    {

        if(!isAuthorised('menu','listing')) return;

        $this->data['page_title'] = "Sub Menu Items";

        $this->data['perms']['can_delete'] = canAccess($this, "delete");
        $this->data['perms']['can_edit'] = canAccess($this, "edit");
        $this->data['perms']['can_add'] = canAccess($this, "add");
        $this->data['perms']['can_sort'] = canAccess($this, "sort");

        $this->data['resources'] = $this->menu_model->get_child($this->uri->segment(3));
        $this->data['parent_name'] = $this->menu_model->getMenuName($this->uri->segment(3));

        $this->mybreadcrumb->add('Sub Menu', base_url('menu/submenu'.$this->uri->segment(3)));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/menu/listing-submenu",$this->data,true);
        $this->load->view("/layouts/default",$this->data);            
    }

    public function add()
    {
        
        if(!isAuthorised('menu','add')) return;

        $this->data['page_title'] = "Add Menu Item";
        $this->load->model("menu_model");
        $this->load->model("system_model");
        $this->data['resource'] = $this->menu_model->fetchById($this->uri->segment(4));
        $this->data['parent_controller'] = $this->uri->segment(3);
        $this->data['all_root_menu'] = $this->menu_model->get_all_root();
        $this->data['glyphicons'] = $this->menu_model->get_glyphicons();
        $this->data['controllers'] = getControllers();

        $this->mybreadcrumb->add('Add', base_url('menu/add'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/menu/add",$this->data,true);
        $this->load->view("/layouts/default",$this->data);            
    }

    public function crud()
    {
        
        if(!isAuthorised('menu','crud')) return;

        $this->data['page_title'] = "Add CRUD Set";
        $this->load->model("menu_model");
        $this->load->model("system_model");
        $this->data['resource'] = $this->menu_model->fetchById($this->uri->segment(4));
        $this->data['controllers'] = getControllers();
        $this->data['icons'] = $this->menu_model->get_glyphicons();
        $this->data['next_order'] = $this->menu_model->getNextOrder();

        $this->mybreadcrumb->add('Add', base_url('menu/crud'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/menu/crud",$this->data,true);
        $this->load->view("/layouts/default",$this->data);            
    }

    public function save_crud()
    {
        if(!isAuthorised('menu','crud')) return;
        $parent_menu = $this->input->post('nom');
        $display_order = $this->input->post('display_order');
        $controller = $this->input->post('controller');
        $icon = $this->input->post('icon');
        $admin = !empty($this->input->post('admin'))?$this->input->post('admin'):'0';
        $normal = !empty($this->input->post('normal'))?$this->input->post('normal'):'0';

        $this->load->model("menu_model");
        $menu_id = $this->menu_model->getNextId();

        $items = array(
            array('id'=>$menu_id,'type'=>'menu','nom'=>$parent_menu,'controller'=>$controller,'action'=>'','class'=>$icon,'display_order'=>$display_order,'parent_menu'=>'0','root'=>'1','admin'=>$admin,'normal'=>$normal,'visible'=>'1'),
            array('id'=>$menu_id+1,'type'=>'menu','nom'=>'Listing','controller'=>$controller,'action'=>'listing','class'=>'fa-list','display_order'=>$display_order,'parent_menu'=>$menu_id,'root'=>'1','admin'=>$admin,'normal'=>$normal,'visible'=>'1'),
            array('id'=>$menu_id+2,'type'=>'menu','nom'=>'Add','controller'=>$controller,'action'=>'add','class'=>'fa-plus-square-o','display_order'=>$display_order,'parent_menu'=>$menu_id,'root'=>'1','admin'=>$admin,'normal'=>$normal,'visible'=>'1'),
            array('id'=>$menu_id+3,'type'=>'menu','nom'=>'Edit','controller'=>$controller,'action'=>'edit','class'=>'','display_order'=>'999','parent_menu'=>'0','root'=>'1','admin'=>$admin,'normal'=>$normal,'visible'=>'0'),
            array('id'=>$menu_id+4,'type'=>'menu','nom'=>'Delete','controller'=>$controller,'action'=>'delete','class'=>'','display_order'=>'999','parent_menu'=>'0','root'=>'1','admin'=>$admin,'normal'=>$normal,'visible'=>'0'),
        );

        $this->db->insert_batch('menu',$items);

        redirect(base_url('menu/listing'));
        
    }

    public function edit($type = "")
    {

        if(!isAuthorised('menu','edit')) return;

        $id = $this->uri->segment(3);
        $this->data['main_menu'] = $this->uri->segment(4);
        $this->data['parent_menu'] = $this->uri->segment(4);

        if (empty($id)) {
            redirect(base_url('/menu/listing'));
            return;
        }

        $this->data['page_title'] = "Edit Menu Item";

        $this->load->model("menu_model");
        $this->load->model("system_model");
        $this->data['resource'] = $this->menu_model->fetchById($this->uri->segment(3));

        if(empty($this->data['resource'])) {
            flashDanger("Unable to retrieve menu item");
            redirect(base_url('/menu/listing'));
            return;
        }
        $this->data['all_root_menu'] = $this->menu_model->get_all_root();
        $this->data['glyphicons'] = $this->menu_model->get_glyphicons();
        $this->data['controllers'] = getControllers();

        $this->mybreadcrumb->add('Edit', base_url('menu/edit'));
        $this->data['breadcrumbs'] = $this->mybreadcrumb->render();

        $this->data["content"]=$this->load->view("/menu/edit",$this->data,true);
        $this->load->view("/layouts/default",$this->data);            
    }

    public function save()
    {
        $id = $this->input->post("id");
        $main_menu = $this->uri->segment(4);

        if(empty($id)) {
            if(!isAuthorised('menu','add')) return;
        }else{
            if(!isAuthorised('menu','edit')) return;
        }

        $controller = $this->input->post('controller');
        $action = $this->input->post('action');
        $visible = $this->input->post('visible');

        $nom = htmlentities($this->input->post('nom'), ENT_QUOTES, 'utf-8');
        $controller = $this->input->post('controller');
        $action = $this->input->post('action');
        $class = $this->input->post('icon');
        $display_order = $this->input->post('display_order');
        $parent_menu = $this->input->post('parent_menu');
        $visible = $this->input->post('visible');
        $backoffice = ( (null !== $this->input->post('backoffice')) && (!empty($this->input->post('backoffice'))) )?'1':'0';

        $vars = array(
            'type' => 'menu',
            'nom' => $nom,
            'controller' => $controller,
            'module' => '0',
            'action' => $action,
            'class' => $class,
            'display_order' => $display_order,
            'parent_menu' => ($visible=='0')?'0':$parent_menu,
            'root' => $this->input->post('root'),
            'admin' => $this->input->post('admin'),
            'normal' => $this->input->post('normal'),
            'visible' => $visible,
            'backoffice'    =>  $backoffice
        );

        if(empty($id)) {
            $this->db->insert('menu', $vars);
            flashSuccess( "Menu <strong>$nom</strong> has been added successfully!");
        }else{
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('menu', $vars);
            flashSuccess("Menu <strong>$nom</strong> has been updated successfully!");
        }
        if($parent_menu!=0){
            redirect(base_url('menu/submenu/'.$parent_menu));
        }else{
            redirect(base_url('menu/listing'));
        }
    }

    public function delete()
    {

        if(!isAuthorised('menu','delete')) return;

        $id = $this->input->post('id');
        $this->load->model('menu_model');
        $result = $this->menu_model->delete($id);
        echo json_encode(array('result' => $result));
    }

    public function deleteAjax()
    {
        if(!isAuthorised('menu','delete')) return;

        $id = $this->input->post("id");
        $this->menu_model->deleteAjax($id);
        echo json_encode(array("result"=>true));
    }

    public function reorder()
    {
        $this->data['rows'] = $this->db->select()->from("menu")->where(array("parent_menu"=>"0","visible"=>"1","status"=>"1"))->order_by("display_order")->get()->result();
        // debug($rows);

        $this->data["content"]=$this->load->view("/menu/reorder",$this->data,true);
        $this->load->view("/layouts/default",$this->data);         

    }
}
