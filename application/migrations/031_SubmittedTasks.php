<?php

class Migration_SubmittedTasks extends CI_Migration
{
    function up()
    {
        $maxId = intval($this->db->query("SELECT MAX(id) AS ct FROM menu")->row()->ct);
        $maxDisplayOrder = intval($this->db->query("SELECT MAX(display_order) AS displayOrder FROM menu WHERE `parent_menu` = '0' AND `visible` = '1' AND `status` = '1'")->row()->displayOrder);
        $this->db->query("INSERT INTO `menu` (`id`, `type`, `nom`, `controller`, `action`, `color`, `url`, `class`, `display_order`, `parent_menu`, `visible`, `Normal`, `Admin`, `Root`, `module`, `status`, `backoffice`) VALUES
                            ($maxId+1, 'menu', 'Submitted Tasks', 'submitted_tasks', '', '#6f42c1', NULL, 'fa-tasks', $maxDisplayOrder+1 , 0, 1, 0, 1, 1, 0, 1, 0),
                            ($maxId+2, 'menu', 'Listing', 'submitted_tasks', 'listing', '', NULL, 'fa-list-ul', 1, $maxId+1, 1, 1, 1, 1, 0, 1, 0),
                            ($maxId+3, 'menu', 'Add', 'submitted_tasks', 'add', '', NULL, 'fa-plus-square', 2, $maxId+1, 1, 0, 1, 1, 0, 1, 0),
                            ($maxId+4, 'menu', 'Edit', 'submitted_tasks', 'edit', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0),
                            ($maxId+5, 'menu', 'Delete', 'submitted_tasks', 'delete', '', NULL, '', 999, 0, 0, 0, 1, 1, 0, 1, 0)");
        
        $users = $this->db->select("id")->from("users")->where("user_type","regular")->get()->result();
        foreach($users as $user){
            for($i=$maxId+1; $i<=$maxId+5; $i++){
                $this->db->insert("permissions",array(
                    'user_id'   =>  $user->id,
                    'menu_id'   =>  $i,
                    'create'    =>  0,
                    'read'      =>  1,
                    'update'    =>  0,
                    'delete'    =>  0
                ));
            }
        }

        $this->db->query("ALTER TABLE `submitted_tasks` CHANGE `created_by_customer` `created_by_customer` int NOT NULL AFTER `created_by`");
        $this->db->query("ALTER TABLE `submitted_tasks` CHANGE `created_by` `created_by` INT NULL");
        $this->db->query("ALTER TABLE `submitted_tasks` CHANGE `created_by_customer` `created_by_customer` INT NULL");

        $this->db->query("UPDATE submitted_tasks SET created_by = NULL where created_by = '0'");
        $this->db->query("UPDATE submitted_tasks SET created_by_customer = NULL where created_by_customer = '0'");

        $this->db->query("ALTER TABLE `submitted_tasks` ADD CONSTRAINT `fk_st_customer` FOREIGN KEY (`created_by_customer`) REFERENCES `customers`(`customer_id`)");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD CONSTRAINT `fk_st_user` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)");

        $this->db->query("ALTER TABLE `submitted_tasks` ADD CONSTRAINT `fk_st_sprint` FOREIGN KEY (`sprint_id`) REFERENCES `sprints`(`id`)");

        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `validated_on` DATETIME NULL");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `validated_by` INT NULL");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD CONSTRAINT `fk_st_valid_user` FOREIGN KEY (`validated_by`) REFERENCES `users`(`id`)");

        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `rejected_on` DATETIME NULL");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD COLUMN `rejected_by` INT NULL");
        $this->db->query("ALTER TABLE `submitted_tasks` ADD CONSTRAINT `fk_st_reject_user` FOREIGN KEY (`rejected_by`) REFERENCES `users`(`id`)");


    }

    function down()
    {
        $this->db->query("DELETE FROM permissions WHERE menu_id IN (SELECT id FROM menu WHERE controller = 'submitted_tasks')");
        $this->db->query("DELETE FROM menu WHERE controller = 'submitted_tasks'");

        $this->db->query("ALTER TABLE submitted_tasks DROP FOREIGN KEY `fk_st_customer`");
        $this->db->query("ALTER TABLE submitted_tasks DROP FOREIGN KEY `fk_st_user`");
        $this->db->query("ALTER TABLE submitted_tasks DROP FOREIGN KEY `fk_st_sprint`");

        $this->db->query("ALTER TABLE `submitted_tasks` CHANGE `created_by` `created_by` INT NOT NULL");
        $this->db->query("ALTER TABLE `submitted_tasks` CHANGE `created_by_customer` `created_by_customer` INT NOT NULL");

        $this->db->query("ALTER TABLE submitted_tasks DROP FOREIGN KEY `fk_st_valid_user`");
        $this->db->query("ALTER TABLE `submitted_tasks` DROP `validated_on`");
        $this->db->query("ALTER TABLE `submitted_tasks` DROP `validated_by`");

        $this->db->query("ALTER TABLE submitted_tasks DROP FOREIGN KEY `fk_st_reject_user`");
        $this->db->query("ALTER TABLE `submitted_tasks` DROP `rejected_on`");
        $this->db->query("ALTER TABLE `submitted_tasks` DROP `rejected_by`");

    }
}