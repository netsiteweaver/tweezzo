

<?php

class Migration_Quotes extends CI_Migration
{
    function up()
    {
        $this->db->query("CREATE TABLE `quotes` (
                            `id` INT NOT NULL AUTO_INCREMENT , 
                            `quote_text` VARCHAR(100) NOT NULL , 
                            `author_name` VARCHAR(100) NOT NULL , 
                            `character_count` INT NOT NULL , 
                            `html` TEXT NOT NULL , 
                            `fetched_on` DATETIME NOT NULL , 
                            `deleted_on` DATETIME NULL , 
                            PRIMARY KEY (`id`))");
    }

    function down()
    {
        $this->db->query("DROP TABLE `quotes`");
    }
}