<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Timesheets listing — browse developer time by developer, customer,
 * project, sprint, and task.
 */
class Migration_TimesheetsAdminMenu extends CI_Migration
{
    public function up()
    {
        $existing = $this->db->select('id')->from('menu')
            ->where(['controller' => 'timesheets', 'action' => ''])->get()->row();
        if (!empty($existing)) {
            return;
        }

        $maxId = (int) $this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct;
        $parentId = $maxId + 1;
        $listingId = $maxId + 2;

        // Place near Developers (display_order 6).
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
            ({$parentId}, 'menu', 'Timesheets', 'timesheets', '', '#2cbfc6', NULL, 'fa-clock', 6, 0, 1, 0, 1, 1, 0, 1, 0),
            ({$listingId}, 'menu', 'Listing', 'timesheets', 'listing', '', NULL, 'fa-list-ul', 1, {$parentId}, 1, 0, 1, 1, 0, 1, 0)");

        // Grant read to users who can already see Developers listing.
        $developersListing = $this->db->select('id')->from('menu')
            ->where(['controller' => 'developers', 'action' => 'listing'])->get()->row();
        if (empty($developersListing)) {
            $users = $this->db->select('id')->from('users')->where(['status' => '1'])->get()->result();
            foreach ($users as $user) {
                foreach ([$parentId, $listingId] as $menuId) {
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
            return;
        }

        $users = $this->db->select('user_id')->from('permissions')
            ->where(['menu_id' => $developersListing->id, 'read' => 1])->get()->result();
        foreach ($users as $user) {
            foreach ([$parentId, $listingId] as $menuId) {
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
    }

    public function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'timesheets')");
        $this->db->query("DELETE FROM menu WHERE controller = 'timesheets'");
    }
}
