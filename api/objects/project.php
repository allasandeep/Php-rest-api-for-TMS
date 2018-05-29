<?php
class Project{
 
    // database connection and table name
    private $conn;
    private $table_name = "projects";
 
    // object properties
    public $id;
    public $name;
    public $supervisor_id;
   
 
    public function __construct($db){
        $this->conn = $db;
    }
 
    // used by select drop-down list
    public function readAll(){
        //select all data
        $query = "SELECT
                    id, name, description
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
                pr.id, pr.name, pr.budget,pr.super_id, concat(em.fname,' ',em.lname) as supervisor_name
            FROM
                " . $this->table_name . " pr
				JOIN 
				employees em 
				ON em.id = pr.super_id 
				
            ORDER BY
                name";
 
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
 
    return $stmt;
}

function readOne(){
 
    // query to read single record
    $query = "SELECT
                p.name as project_name, p.id, p.budget ,p.super_id as selectedSupervisorId
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
    
    $this->budget = $row['budget'];
    $this->project_id = $row['id'];
    $this->project_name = $row['project_name'];
    $this->supervisor_id = $row['selectedSupervisorId'];
}

function readProjects(){
 
    // query to read single record
    $query = "SELECT
                 p.super_id, p.name as project_name, pr.id, concat(pr.fname,' ',pr.lname) as employee_name, pr.email, pr.position, pr.salary, pr.project_id 
            FROM
                " . $this->table_name . " p
               JOIN
                        employees pr
                        ON p.id = pr.project_id			   
            WHERE
                (pr.id = ?)
            ";
			
	$query2= "select concat(fname,' ',lname) as supervisor_name from employees where project_id =(select project_id from employees where id = ?) and type='Supervisor'";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
    $stmt2 = $this->conn->prepare( $query2 );
    // bind id of product to be updated
    $stmt->bindParam(1, $this->id);
    $stmt2->bindParam(1, $this->id);
    // execute query
    $stmt->execute();
    $stmt2->execute();
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    // set values to object properties
   
    $this->employee_name = $row['employee_name'];
    $this->email = $row['email'];
    $this->position = $row['position'];
    $this->salary = $row['salary'];
    $this->project_id = $row['project_id'];
    $this->project_name = $row['project_name'];
	$this->super_id = $row['super_id'];
    $this->supervisor_name=$row2['supervisor_name'];
    
}

function readSupervisorProjects(){
 
    // query to read single record
    $query = "SELECT
                  p.name as project_name, p.id as project_id, p.super_id
            FROM
                " . $this->table_name . " p               		   
            WHERE
                p.super_id = ? 
            ";
			
	
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
    
    // bind id of product to be updated
    $stmt->bindParam(1, $this->id);
    
    // execute query
    $stmt->execute();
    
    // get retrieved row
    
   
    // set values to object properties
   

     return $stmt;
	
    
    
}

function readEmployees(){
 
    // query to read single record
    $query = "SELECT
                p.super_id, p.name as project_name, pr.id as employee_id, concat(pr.fname,' ',pr.lname) as employee_name, pr.email, pr.position, pr.salary, pr.project_id
            FROM
                employees pr

               JOIN
           			   projects p
                        ON p.id = pr.project_id
			   
            WHERE
                p.super_id = ? and (type='Employee' or type='Admin')
            ";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
    $stmt->bindParam(1, $this->id);
 
    // execute query
    $stmt->execute();
 
    
    return $stmt;
}



function readEmployeeHours(){
 
    // query to read single record
    $query = "SELECT id as hour_id,
                work_date, worked_from,worked_to  FROM
                hours 
               WHERE
                employee_id = ? order by work_date dESC";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind id of product to be updated
    $stmt->bindParam(1, $this->id);
 
    // execute query
    $stmt->execute();
 
    
    return $stmt;
}
}
?>