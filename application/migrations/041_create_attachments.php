<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_attachments extends CI_Migration {

    public function up()
    {
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'uuid' => ['type' => 'VARCHAR', 'constraint' => 40],
            'meeting_note_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            'file_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
            'uploaded_at' => ['type' => 'TIMESTAMP', 'null' => TRUE],
            'uploaded_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE],
            
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (meeting_note_id) REFERENCES meeting_notes(id) ON DELETE CASCADE');
        $this->dbforge->create_table('attachments');
    }

    public function down()
    {
        $this->dbforge->drop_table('attachments',TRUE);
    }
}
