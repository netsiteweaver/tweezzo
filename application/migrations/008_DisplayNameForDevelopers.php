<?php

class Migration_DisplayNameForDevelopers extends CI_Migration
{
    function up()
    {
        $query = "ALTER TABLE `users` ADD COLUMN `display_name` VARCHAR(5) NULL";
        $this->db->query($query);

        $developers = $this->db->select('SUBSTR(name,1,1) as prefix, id')->from("users")->where("user_type", "developer")->get()->result();
        foreach($developers as $i => $d)
        {
            $display_name = strtoupper($d->prefix) . str_pad(($i+1),2,'0',STR_PAD_LEFT);
            $this->db->set("display_name",$display_name);
            $this->db->where("id",$d->id);
            $this->db->update("users");
        }
    }

    function down()
    {
        $query = "ALTER TABLE `users` DROP `display_name`";
        $this->db->query($query);
    }
}