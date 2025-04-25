<?php

class Migration_ManageNotes extends CI_Migration
{
    function up()
    {
        $maxId = intval($this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct);
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
                            ($maxId+1, 'menu', 'Notes', 'notes', '', '#FFFFFF', NULL, 'fa-comments', 6, 0, 1, 0, 1, 1, 0, 1, 0),
                            ($maxId+2, 'menu', 'Listing', 'notes', 'listing', '', NULL, 'fa-list-ul', 1, $maxId+1, 1, 1, 1, 1, 0, 1, 0),
                            ($maxId+3, 'menu', 'Edit', 'notes', 'edit', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0),
                            ($maxId+4, 'menu', 'Delete', 'notes', 'delete', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0)");
        
        $users = $this->db->select("id")->from("users")->where("user_type","regular")->get()->result();
        foreach($users as $user){
            for($i=$maxId+1; $i<=$maxId+4; $i++){
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
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'notes')");
        $this->db->query("DELETE FROM menu WHERE controller = 'notes'");
    }
}