<?php

defined('BASEPATH') or exit('No direct script access allowed');

class History extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function commitParser($filename="commits")
    {
        $filename = (empty($filename))?"commits":$filename;
        $fullname = realpath(".").DIRECTORY_SEPARATOR.$filename;
        if(file_exists($fullname)){
            $fh = fopen($filename,"r");
            $commitObj = new stdClass;
            if(!$fh){
                echo "Cannot read from file";
            }else{
                $this->db->truncate("commits");
                $row = 1;
                while(!feof($fh)){
                    $line = stream_get_line($fh, 1000000, "\n"); 
                    if($line == "") continue;

                    if(substr($line,0,6)=='commit') {
                        $commitObj->commit = substr($line,8);
                    }elseif(substr($line,0,6)=='Author') {
                        $commitObj->author = substr($line,8);
                    }elseif(substr($line,0,4)=='Date') {
                        $commitObj->date = strftime('%Y-%m-%dT%H:%M:%S', strtotime(substr($line,6)));
                    }else{
                        $commitObj->details = trim($line);
                    }
                    if(($row % 4) == 0){
                        $this->db->insert("commits",$commitObj);
                    }
                    $row++;
                }
            }
            fclose($fh);
            $this->db->query("UPDATE params SET value = (SELECT COUNT(1) FROM commits) WHERE `title` = 'current_version'");
        }else{
            die("Commit file not found");
        }
    }
}