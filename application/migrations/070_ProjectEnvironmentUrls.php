<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Projects can record where they are deployed, so customers can jump straight to the
 * running site from the portal top bar. Both URLs are optional — a project may have
 * neither, one, or both.
 */
class Migration_ProjectEnvironmentUrls extends CI_Migration
{
    public function up()
    {
        $fields = [];
        if ( ! $this->db->field_exists('staging_url', 'projects')) {
            $fields['staging_url'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE, 'after' => 'end_date'];
        }
        if ( ! $this->db->field_exists('production_url', 'projects')) {
            $fields['production_url'] = ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE, 'after' => 'staging_url'];
        }
        if ( ! empty($fields)) {
            $this->dbforge->add_column('projects', $fields);
        }
    }

    public function down()
    {
        foreach (['staging_url', 'production_url'] as $field) {
            if ($this->db->field_exists($field, 'projects')) {
                $this->dbforge->drop_column('projects', $field);
            }
        }
    }
}
