<?php

class Migration_PermissionsForTaskImport extends CI_Migration
{
    function up()
    {
        //get menu id for tasks
        $taskId = $this->db->select('id')->from("menu")->where(array("controller"=>"tasks","parent_menu"=>'0','visible'=>'1'))->get()->row()->id;
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
                            (null, 'menu', 'Import', 'tasks', 'import', '', NULL, 'fa-upload', 3, $taskId, 1, 0, 1, 1, 0, 1, 0)");
        
        $users = $this->db->select("id")->from("users")->where("user_type","regular")->get()->result();
        // foreach($users as $user){
        //     // for($i=$maxId+1; $i<=$maxId+7; $i++){
        //         $this->db->insert("permissions",array(
        //             'user_id'   =>  $user->id,
        //             'menu_id'   =>  $this->db->insert_id(),
        //             'create'    =>  0,
        //             'read'      =>  1,
        //             'update'    =>  0,
        //             'delete'    =>  0
        //         ));
        //     // }
        // }
    }

    function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE `controller` = 'tasks' AND `action` = 'import')");
        $this->db->query("DELETE FROM menu WHERE `controller` = 'tasks' AND `action` = 'import'");
    }
}