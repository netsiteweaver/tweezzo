<?php

class Migration_AdditionalEmailForCustomer extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `customer_access` (
                        `id` int NOT NULL AUTO_INCREMENT,
                        `name` varchar(50) NOT NULL,
                        `job_description` varchar(25) NOT NULL,
                        `phone_number1` varchar(30) NOT NULL,
                        `phone_number2` varchar(30) NOT NULL,
                        `customer_id` int NOT NULL,
                        `email` varchar(100) NOT NULL,
                        `password` varchar(100) NOT NULL,
                        `created_on` DATETIME NOT NULL,
                        `created_by` int null,
                        `created_by_customer` int null,
                        `created_by_type` enum('user','customer') not null,
                        PRIMARY KEY (`id`),
                        KEY `fk_access_customer` (`customer_id`),
                        CONSTRAINT `fk_access_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`)
                        ) ENGINE=InnoDB");
        $customers = $this->db->select("customer_id, full_name, email, password")->from("customers")->where("status","1")->get()->result();
        foreach($customers as $customer){
            $var = array(
                "id"            =>  $customer->customer_id,
                "name"          =>  $customer->full_name,
                "job_description"=>  'migration 019',
                "password"      =>  $customer->password,
                "email"         =>  $customer->email,
                "customer_id"   =>  $customer->customer_id,
                "created_on"    =>  date("Y-m-d H:i:s"),
                "created_by"    =>  $_SESSION['user_id'],
                "created_by_type"=> 'user'
            );
            $this->db->insert("customer_access",$var);
        }
    }

    function down()
    {
        $this->db->query("DROP TABLE `customer_access`");
    }
}