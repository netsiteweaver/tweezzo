<?php

class Migration_MenuItemsIconsAndColors extends CI_Migration
{
    function up()
    {
        $this->db->query("UPDATE menu SET class = 'fa-bars' WHERE `controller` = 'tasks' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET class = 'fa-list-ol' WHERE `controller` = 'sprints' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET class = 'fa-list' WHERE `controller` = 'projects' AND `parent_menu` = '0' and `visible` = '1'");

        $this->db->query("UPDATE menu SET color = '#1c8be6' WHERE `controller` = 'tasks' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET color = '#44ab8e' WHERE `controller` = 'sprints' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET color = '#98c363' WHERE `controller` = 'projects' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET color = '#f36930' WHERE `controller` = 'users' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET color = '#c44866' WHERE `controller` = 'customers' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET color = '#4e67c7' WHERE `controller` = 'settings' AND `parent_menu` = '0' and `visible` = '1'");
        $this->db->query("UPDATE menu SET color = '#4e67c7' WHERE `controller` = 'developers' AND `parent_menu` = '0' and `visible` = '1'");
    }

    function down()
    {
    }
}