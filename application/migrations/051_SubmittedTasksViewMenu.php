<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_SubmittedTasksViewMenu extends CI_Migration
{
    function up()
    {
        $parent = $this->db->select('id')->from('menu')->where(['controller' => 'submitted_tasks', 'parent_menu' => '0'])->get()->row();
        if (empty($parent)) {
            return;
        }
        $maxId = (int) $this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct;
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            (" . ($maxId + 1) . ", 'menu', 'View', 'submitted_tasks', 'view', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0)");

        $users = $this->db->select('id')->from('users')->where('user_type', 'regular')->get()->result();
        foreach ($users as $user) {
            $this->db->insert('permissions', [
                'user_id' => $user->id,
                'menu_id' => $maxId + 1,
                'create' => 0,
                'read'   => 1,
                'update' => 0,
                'delete' => 0,
            ]);
        }
    }

    function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'submitted_tasks' AND action = 'view')");
        $this->db->query("DELETE FROM menu WHERE controller = 'submitted_tasks' AND action = 'view'");
    }
}
