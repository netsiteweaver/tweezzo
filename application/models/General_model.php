<?php

class General_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generic function to get number of rows of a table
     * @author reeaz@ramoly.info
     * @version 1.0
     * @since 1.0
     * @param table The name of the table to count the rows
     * @param where Is an array of conditions to apply to the table
     */
    public function getCount($table,$where=array("status"=>'1'))
    {
        $this->db->select("count(id) AS ct")->from($table);
        $this->db->where($where);
        return $this->db->get()->row("ct");
    }

    /**
     * Generic function to get a list of rows of a table for lookup
     * @author reeaz@ramoly.info
     * @version 1.0
     * @since 1.0
     * @param table The name of the table to count the rows
     * @param where Is an array of conditions to apply to the table
     */
    public function lookup($table,$where=array("status"=>"1"),$options=array())
    {
        return $this->db->select()
        ->from($table)
        ->where($where)
        ->get()
        ->result();
    }

    /** 
     * Unlock table row (after editing has been completed or cancelled)
     * @author reeaz@ramoly.info
     * @version 1.0
     * @since 1.0
     * @param 
     */
    public function unlockTable($table_name,$row_uuid)
    {
        $this->db->where(array(
            "row_uuid"      =>  $row_uuid,
            "table_name"    =>  $table_name
        ))->delete("locks");

        if($this->db->affected_rows()>0){
            return array(
                "result"    =>  true
            );
        }else{
            return array(
                "result"    =>  false,
                "reason"    =>  "Unable to save this record. Either your editing time has expired or an admin has unlocked the row, hence preventing from saving the record you were editing"
            );
        }
    }

    /** 
     * Lock rows when editing. This function has been to avoid that more than one persion is editing the same record simultaneously
     * @author reeaz@ramoly.info
     * @version 1.0
     * @since 1.0
     * @param 
     */
    public function lockTable($table_name,$row_uuid)
    {
        $locked = $this->db->select("l.*,u.name as user_name")
                        ->from("locks l")
                        ->join("users u","u.id=l.locked_by")
                        ->where(array(
                            "row_uuid"      =>  $row_uuid,
                            "table_name"    =>  $table_name,
                            "locked_by !="  =>  $_SESSION['user_id']
                        ))->get()->result();
        if(empty($locked)){
            $this->db->set("table_name",$table_name);
            $this->db->set("row_uuid",$row_uuid);
            $this->db->set("locked_on","NOW()",false);
            $this->db->set("locked_by",$_SESSION['user_id']);
            $this->db->set("locked_by_name",$_SESSION['user_name']);
            $this->db->insert("locks");
            return array(
                "result"    =>  true
            );
        }else{
            return array(
                "result"    =>  false,
                "reason"    =>  $locked[0]->user_name . " is editing this record. Please try again later"
            );
        }
    }

}