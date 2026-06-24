<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_FabricOrdersFrontMenu extends CI_Migration {

    public function up()
    {
        $this->db->query("UPDATE `menu` SET `backoffice` = 0 WHERE `controller` = 'fabric_orders' AND (`action` IS NULL OR `action` = '') AND `parent_menu` = 0 AND `visible` = 1");
    }

    public function down()
    {
        $this->db->query("UPDATE `menu` SET `backoffice` = 1 WHERE `controller` = 'fabric_orders' AND (`action` IS NULL OR `action` = '') AND `parent_menu` = 0 AND `visible` = 1");
    }
}
