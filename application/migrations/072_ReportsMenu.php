<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Reports: freelance developer timesheet report and client timesheet report.
 */
class Migration_ReportsMenu extends CI_Migration
{
    public function up()
    {
        $existing = $this->db->select('id')->from('menu')
            ->where(['controller' => 'reports', 'action' => ''])->get()->row();
        if (!empty($existing)) {
            return;
        }

        $maxId = (int) $this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct;
        $parentId = $maxId + 1;
        $developerId = $maxId + 2;
        $clientId = $maxId + 3;

        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            ({$parentId}, 'menu', 'Reports', 'reports', '', '#e67e22', NULL, 'fa-bars', 7, 0, 1, 0, 1, 1, 0, 1, 0),
            ({$developerId}, 'menu', 'Developer', 'reports', 'developer', '', NULL, 'fa-user', 1, {$parentId}, 1, 0, 1, 1, 0, 1, 0),
            ({$clientId}, 'menu', 'Client', 'reports', 'client', '', NULL, 'fa-building', 2, {$parentId}, 1, 0, 1, 1, 0, 1, 0)");

        $timesheetsListing = $this->db->select('id')->from('menu')
            ->where(['controller' => 'timesheets', 'action' => 'listing'])->get()->row();
        if (empty($timesheetsListing)) {
            $timesheetsListing = $this->db->select('id')->from('menu')
                ->where(['controller' => 'developers', 'action' => 'listing'])->get()->row();
        }

        $menuIds = [$parentId, $developerId, $clientId];
        if (!empty($timesheetsListing)) {
            $users = $this->db->select('user_id')->from('permissions')
                ->where(['menu_id' => $timesheetsListing->id, 'read' => 1])->get()->result();
            foreach ($users as $user) {
                foreach ($menuIds as $menuId) {
                    $this->db->insert('permissions', [
                        'user_id' => $user->user_id,
                        'menu_id' => $menuId,
                        'create'  => 0,
                        'read'    => 1,
                        'update'  => 0,
                        'delete'  => 0,
                    ]);
                }
            }
            return;
        }

        $users = $this->db->select('id')->from('users')->where(['status' => '1'])->get()->result();
        foreach ($users as $user) {
            foreach ($menuIds as $menuId) {
                $this->db->insert('permissions', [
                    'user_id' => $user->id,
                    'menu_id' => $menuId,
                    'create'  => 0,
                    'read'    => 1,
                    'update'  => 0,
                    'delete'  => 0,
                ]);
            }
        }
    }

    public function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'reports')");
        $this->db->query("DELETE FROM menu WHERE controller = 'reports'");
    }
}
