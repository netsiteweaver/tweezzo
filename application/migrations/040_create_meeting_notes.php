<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_meeting_notes extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'customer_id' => ['type' => 'INT', 'constraint' => 11],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'meeting_datetime' => ['type' => 'DATETIME'],
            'lieu' => ['type' => 'VARCHAR', 'constraint' => 255], // Lieu of meeting
            'attendees' => ['type' => 'TEXT'],
            'notes' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => TRUE]
        ]);
        $this->dbforge->add_key('id', TRUE);
        // $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE');
        $this->dbforge->create_table('meeting_notes');

        $menuQuery = "INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
                    (500, 'menu', 'Meeting Notes', 'meeting_notes', '', '#68bdcb', NULL, 'fa-edit', 5, 0, 1, 1, 1, 1, 0, 1, 0),
                    (501, 'menu', 'Listing', 'meeting_notes', 'index', '', NULL, 'fa-list-ul', 1, 500, 1, 1, 1, 1, 0, 1, 0),
                    (502, 'menu', 'Add', 'meeting_notes', 'add', '', NULL, 'fa-plus-square', 2, 500, 1, 1, 1, 1, 0, 1, 0),
                    (503, 'menu', 'Edit', 'meeting_notes', 'edit', '', NULL, '', 999, 0, 0, 1, 1, 1, 0, 1, 0),
                    (504, 'menu', 'View', 'meeting_notes', 'View', '', NULL, '', 999, 0, 0, 1, 1, 1, 0, 1, 0),
                    (505, 'menu', 'Delete', 'meeting_notes', 'delete', '', NULL, '', 999, 0, 0, 1, 1, 1, 0, 1, 0)";
        $this->db->query($menuQuery);
        $this->db->insert_batch("permissions",array(
            array(
                'user_id'   =>  $_SESSION['user_id'],
                'menu_id'   =>  '500',
                'read'      =>  1
            ),
            array(
                'user_id'   =>  $_SESSION['user_id'],
                'menu_id'   =>  '501',
                'read'      =>  1
            ),
            array(
                'user_id'   =>  $_SESSION['user_id'],
                'menu_id'   =>  '502',
                'read'      =>  1
            ),
            array(
                'user_id'   =>  $_SESSION['user_id'],
                'menu_id'   =>  '503',
                'read'      =>  1
            ),
            array(
                'user_id'   =>  $_SESSION['user_id'],
                'menu_id'   =>  '504',
                'read'      =>  1
            ),
            array(
                'user_id'   =>  $_SESSION['user_id'],
                'menu_id'   =>  '505',
                'read'      =>  1
            ),
            
        ));
    }

    public function down()
    {
        $this->dbforge->drop_table('meeting_notes',TRUE);
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'meeting_notes')");
        $this->db->query("DELETE FROM menu WHERE controller = 'meeting_notes'");
    }
}
