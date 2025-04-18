<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tables_model extends CI_Model{

    public function get($table, $fields="")
    {
        if(!empty($fields)){
            if(is_array($fields)){
                $allfields = "";
                foreach($fields as $field){
                    $allfields .= $field . ',';
                }
                $allfields = substr($allfields, 0, strlen($allfields)-1);
                $this->db->select($allfields);
            }else{
                $this->db->select($fields);
            }
        }
        // $this->db->where("status !=", "DELETED");
        return $this->db->get($table)->result();
    }

    public function getAll($table)
    {
    	$result = $this->db->get($table)->result();
    	return $result;
    }

    public function getById($id)
    {
        $this->db->where("id","$id");
        return $this->db->get("users")->row();
    }

    public function insert($data)
    {

        $this->db->set('name',$data['name']);
        $this->db->set('email',$data['email']);
        $this->db->set('password',md5($data['password']), true);
        $this->db->insert("users");
        return;

    }

    public function delete($id)
    {
        $this->db->set("status","DELETED");
        $this->db->where("id",$id);
        $this->db->update("users    ");
        return;
    }

    public function update($userDetails) //edit user
    {

        $pswd   = $userDetails['pswd'];
        $this->db->set("name",$userDetails['name']);
        $this->db->set("email",$userDetails['email']);
        if(!empty($pswd)) $this->db->set("password",md5($userDetails['pswd']));

        $this->db->where('id',$userDetails['id']);
        $this->db->update('users');

        return true;

    }

	public function getColumnNames($table){
	    $sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :table";
	    try {
	        $core = Core::getInstance();
	        $stmt = $core->dbh->prepare($sql);
	        $stmt->bindValue(':table', $table, PDO::PARAM_STR);
	        $stmt->execute();
	        $output = array();
	        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	            $output[] = $row['COLUMN_NAME'];                
	        }
	        return $output; 
	    }

	    catch(PDOException $pe) {
	        trigger_error('Could not connect to MySQL database. ' . $pe->getMessage() , E_USER_ERROR);
	    }
	}    
}

