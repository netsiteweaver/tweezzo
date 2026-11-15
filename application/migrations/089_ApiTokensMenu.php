<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Settings > Tokens (settings/tokens): staff create and revoke their personal API tokens (Yawmee desktop
 * app) and see their mobile app sign-ins. Same user levels as the Settings menu; granted to every active
 * staff user whose level allows it.
 */
class Migration_ApiTokensMenu extends CI_Migration {

    public function up()
    {
        $exists = $this->db->select('id')->from('menu')->where(['controller' => 'settings', 'action' => 'tokens'])->get()->row();
        $settings = $this->db->select('*')->from('menu')->where(['controller' => 'settings', 'action' => '', 'parent_menu' => 0])->order_by('id', 'asc')->limit(1)->get()->row_array();
        if (!empty($exists) || empty($settings)) {
            return;
        }
        $this->db->insert('menu', [
            'type'          => 'menu',
            'nom'           => 'Tokens',
            'controller'    => 'settings',
            'action'        => 'tokens',
            'color'         => '',
            'class'         => 'fa-angle-right',
            'display_order' => 70,
            'parent_menu'   => (int) $settings['id'],
            'visible'       => 1,
            'Normal'        => 0,
            'Admin'         => (int) $settings['Admin'],
            'Root'          => (int) $settings['Root'],
            'module'        => 0,
            'status'        => 1,
            'backoffice'    => 0,
        ]);
        $menuId = (int) $this->db->insert_id();

        $users = $this->db->select('id, user_level')->from('users')->where(['user_type' => 'regular', 'status' => 1])->get()->result();
        foreach ($users as $user) {
            if (!empty($settings[$user->user_level])) {
                $this->db->insert('permissions', ['user_id' => (int) $user->id, 'menu_id' => $menuId, 'create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1]);
            }
        }
    }

    public function down()
    {
        foreach ($this->db->select('id')->from('menu')->where(['controller' => 'settings', 'action' => 'tokens'])->get()->result() as $row) {
            $this->db->where('menu_id', (int) $row->id)->delete('permissions');
            $this->db->where('id', (int) $row->id)->delete('menu');
        }
    }
}
