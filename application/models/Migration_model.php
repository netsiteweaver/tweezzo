<?php

class Migration_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function import($sql_file)
    {
        $folder = './sql/';
        if(!file_exists($folder.$sql_file))
        {
            return false;
        }

        $temp_line = '';
        $lines = file($folder.$sql_file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
        foreach($lines as $line){
            if( (substr($line,0,2)=="--") || ($line=="") )continue;

            $temp_line .= $line;

            //if last character is semi column, its end of statement, so execute it
            if(substr(trim($line),-1)==';'){
                $this->db->query($temp_line);
                $temp_line = "";
            }

        }
    }
}