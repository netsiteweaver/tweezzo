<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Hidden Edit / Delete actions for admin timesheets.
 * Root gets access by default; other users with Timesheets listing get
 * permission rows with read=0 so access can be granted per user.
 */
class Migration_TimesheetsEditDeletePerms extends CI_Migration
{
    public function up()
    {
        $existing = $this->db->select('id')->from('menu')
            ->where(['controller' => 'timesheets', 'action' => 'edit'])->get()->row();
        if (!empty($existing)) {
            return;
        }

        $parent = $this->db->select('id')->from('menu')
            ->where(['controller' => 'timesheets', 'action' => '', 'parent_menu' => '0'])->get()->row();
        $listing = $this->db->select('id')->from('menu')
            ->where(['controller' => 'timesheets', 'action' => 'listing'])->get()->row();
        if (empty($parent) || empty($listing)) {
            return;
        }

        $maxId = (int) $this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct;
        $editId = $maxId + 1;
        $deleteId = $maxId + 2;

        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            ({$editId}, 'menu', 'Edit', 'timesheets', 'edit', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0),
            ({$deleteId}, 'menu', 'Delete', 'timesheets', 'delete', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0)");

        $users = $this->db->select('p.user_id, u.user_level')
            ->from('permissions p')
            ->join('users u', 'u.id = p.user_id', 'left')
            ->where(['p.menu_id' => $listing->id, 'p.read' => 1])
            ->get()->result();

        foreach ($users as $user) {
            $canManage = in_array($user->user_level, ['Root', 'root'], true) ? 1 : 0;
            foreach ([$editId, $deleteId] as $menuId) {
                $exists = $this->db->select('id')->from('permissions')
                    ->where(['user_id' => $user->user_id, 'menu_id' => $menuId])->get()->row();
                if (!empty($exists)) {
                    continue;
                }
                $this->db->insert('permissions', [
                    'user_id' => $user->user_id,
                    'menu_id' => $menuId,
                    'create'  => 0,
                    'read'    => $canManage,
                    'update'  => 0,
                    'delete'  => 0,
                ]);
            }
        }
    }

    public function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'timesheets' AND action IN ('edit','delete'))");
        $this->db->query("DELETE FROM menu WHERE controller = 'timesheets' AND action IN ('edit','delete')");
    }
}
