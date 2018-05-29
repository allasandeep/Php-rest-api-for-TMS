<?php
class Supervisor{
 
    // database connection and table name
    private $conn;
    private $table_name = "supervisors";
 
    // object properties
    public $super_id;
    public $fname;
    public $lname;
   
 
    public function __construct($db){
        $this->conn = $db;
    }
 
    // used by select drop-down list
    public function readAll(){
        //select all data
        $query = "SELECT
                    id, concat(fname,' ', lname) as name,fname,lname, email, position,salary
                FROM
                    " . $this->table_name . "
                ORDER BY
                    name";
 
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();
 
        return $stmt;
    }
	
	// used by select drop-down list
public function read(){
 
    //select all data
    $query = "SELECT
                id, concat(fname,' ', lname) as name ,fname,lname,email, position,salary
            FROM
                " . $this->table_name . "
            ORDER BY
                name";
 
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
 
    return $stmt;
}
}
?>