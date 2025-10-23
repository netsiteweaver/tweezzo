<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_OnlineUsersTracking extends CI_Migration {

    public function up()
    {
        // Create online_users table to track active sessions
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => FALSE
            ],
            'user_type' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'customer', 'developer'],
                'null' => FALSE,
                'comment' => 'Type of user: admin (from users table), customer (from customer_access), developer (from developers)'
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'session_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE
            ],
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],
            'last_activity' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE
            ]
        ]);
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('user_type');
        $this->dbforge->add_key('last_activity');
        
        $this->dbforge->create_table('online_users', TRUE);
        
        // Add unique constraint on user_id + user_type combination
        $this->db->query("ALTER TABLE `online_users` ADD UNIQUE KEY `unique_user` (`user_id`, `user_type`)");
    }

    public function down()
    {
        $this->dbforge->drop_table('online_users', TRUE);
    }
}

