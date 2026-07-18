<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The reminder stage lists move out of System Params onto their own
 * Settings > Reminders page, so they need a menu entry of their own.
 * Read access is granted to whoever can already see Settings > Params.
 */
class Migration_RemindersSettingsMenu extends CI_Migration
{
    public function up()
    {
        $existing = $this->db->select('id')->from('menu')
                        ->where(['controller' => 'settings', 'action' => 'reminders'])->get()->row();
        if ( ! empty($existing)) {
            return;
        }

        $parent = $this->db->select('id')->from('menu')
                    ->where(['controller' => 'settings', 'parent_menu' => '0'])->get()->row();
        $params = $this->db->select('id')->from('menu')
                    ->where(['controller' => 'settings', 'action' => 'params'])->get()->row();
        if (empty($parent) || empty($params)) {
            return;
        }

        $id = (int) $this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct + 1;
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            ({$id}, 'menu', 'Reminders', 'settings', 'reminders', '', NULL, 'fa-angle-right', 60, {$parent->id}, 1, 0, 1, 1, 0, 1, 0)");

        // Mirror the audience that can already reach Settings > Params.
        $users = $this->db->select('user_id')->from('permissions')
                    ->where(['menu_id' => $params->id, 'read' => 1])->get()->result();
        foreach ($users as $user) {
            $this->db->insert('permissions', [
                'user_id' => $user->user_id,
                'menu_id' => $id,
                'create'  => 0,
                'read'    => 1,
                'update'  => 0,
                'delete'  => 0,
            ]);
        }
    }

    public function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'settings' AND action = 'reminders')");
        $this->db->query("DELETE FROM menu WHERE controller = 'settings' AND action = 'reminders'");
    }
}
