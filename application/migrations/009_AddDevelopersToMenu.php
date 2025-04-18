<?php

class Migration_AddDevelopersToMenu extends CI_Migration
{
    function up()
    {
        $maxId = intval($this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct);
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
                            ($maxId+1, 'menu', 'Developers', 'developers', '', '#FFFFFF', NULL, 'fa-users', 6, 0, 1, 0, 1, 1, 0, 1, 0),
                            ($maxId+2, 'menu', 'Listing', 'developers', 'listing', '', NULL, 'fa-list-ul', 1, $maxId+1, 1, 1, 1, 1, 0, 1, 0),
                            ($maxId+3, 'menu', 'Add', 'developers', 'add', '', NULL, 'fa-plus-square', 2, $maxId+1, 1, 0, 1, 1, 0, 1, 0),
                            ($maxId+4, 'menu', 'Edit', 'developers', 'edit', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0),
                            ($maxId+5, 'menu', 'Delete', 'developers', 'delete', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0),
                            ($maxId+6, 'menu', 'Permission', 'developers', 'permission', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0),
                            ($maxId+7, 'menu', 'My Profile', 'developers', 'myprofile', '', NULL, '', 999, 0, 0, 1, 1, 1, 0, 1, 0);");
        
        $users = $this->db->select("id")->from("users")->where("user_type","regular")->get()->result();
        foreach($users as $user){
            for($i=$maxId+1; $i<=$maxId+7; $i++){
                $this->db->insert("permissions",array(
                    'user_id'   =>  $user->id,
                    'menu_id'   =>  $i,
                    'create'    =>  0,
                    'read'      =>  1,
                    'update'    =>  0,
                    'delete'    =>  0
                ));
            }
        }
    }

    function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'developers')");
        $this->db->query("DELETE FROM menu WHERE controller = 'developers'");
    }
}