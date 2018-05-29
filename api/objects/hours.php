<?php
class Hours{
 
    // database connection and table name
    private $conn;
    private $table_name = "hours";
 
    // object properties
    public $id;
	public $project_id;
	public $employee_id;
	public $employee_name;
	public $supervisor_name;
	public $project_name;
	public $salary;
	public $position;
    public $worked_from;
    public $worked_to;
	public $work_date;
   
 
    public function __construct($db){
        $this->conn = $db;
    }
 

 function readOne(){
 
    // query to read single record
    $query = "SELECT
                p.work_date,p.worked_from,p.worked_to
            FROM
                " . $this->table_name . " p
            WHERE
                p.id = ?
            LIMIT
                0,1";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
    $stmt->bindParam(1, $this->id);
 
    // execute query
    $stmt->execute();
 
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // set values to object properties
    
    $this->work_date = $row['work_date'];
    $this->worked_from = $row['worked_from'];
    $this->worked_to = $row['worked_to'];
    
}


function readEmployeeHours(){
 
    // query to read single record
    $query = "SELECT
                hr.project_id, hr.work_date, hr.worked_from,hr.worked_to, em.id as employee_id, concat(em.fname,' ',em.lname) as employee_name, pr.name as project_name, em.salary,em.position FROM
                hours hr
				JOIN 
				employees em 
				ON em.id = hr.employee_id
				JOIN 
				projects pr 
				ON pr.id = hr.project_id
                WHERE
                pr.super_id = ? and (type= 'Employee' or type = 'Admin') order by hr.work_date dESC";
				
	$query1 = "select concat(fname,' ',lname) as supervisor_name from employees where id = ?";
	
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
	$stmt1 = $this->conn->prepare( $query1 );
    // bind id of product to be updated
    $stmt->bindParam(1, $this->id);
    $stmt1->bindParam(1, $this->id);
    // execute query
    $stmt->execute();
	$stmt1->execute();
    
	$row = $stmt1->fetch(PDO::FETCH_ASSOC);
 
    // set values to object properties
    $this->supervisor_name = $row['supervisor_name'];
	
    return $stmt;
	
}


}
?>