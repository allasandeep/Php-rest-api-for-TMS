<?php
class Employee{
 
    // database connection and table name
    private $conn;
    private $table_name = "employees";
 
    // object properties
    public $id;
    public $fname;
    public $lname;
    public $email;
    public $password;
	public $position;
    public $salary;
    public $project_id;
    public $project_name;
    public $category_id;
    public $category_name;
    public $created;
	public $type;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
	
	// read products
function read(){
 
    // select all query
    $query = "SELECT
                pr.name as project_name, p.id, concat(p.fname,' ', p.lname) as employee_name,p.fname,p.lname, p.email, p.position, p.salary,p.project_id, type, p.created
            FROM
                " . $this->table_name . " p 
                
                 JOIN       
                    projects pr 
                        ON p.project_id = pr.id 
            ORDER BY
                p.created DESC";
 
    // prepare query statement
    $stmt = $this->conn->prepare($query);
 
    // execute query
    $stmt->execute();
 
    return $stmt;
}

function readEmployeesOnly(){
 
    // select all query
    $query = "SELECT
                pr.name as project_name, p.id, concat(p.fname,' ', p.lname) as employee_name,p.fname,p.lname, p.email, p.position, p.salary,p.project_id, type, p.created
            FROM
                " . $this->table_name . " p 
                
                 JOIN       
                    projects pr 
                        ON p.project_id = pr.id where p.type = 'Employee' or p.type = 'Admin' 
            ORDER BY
                p.created DESC";
 
    // prepare query statement
    $stmt = $this->conn->prepare($query);
 
    // execute query
    $stmt->execute();
 
    return $stmt;
}

// create product
function create(){
 
    // query to insert record
    $query = "INSERT INTO
                " . $this->table_name . "
            SET
                fname=:fname, lname=:lname, email=:email,password=:password, position=:position, salary=:salary, project_id=:project_id,created=:created";
 
    // prepare query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->fname=htmlspecialchars(strip_tags($this->fname));
    $this->lname=htmlspecialchars(strip_tags($this->lname));
    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->password=htmlspecialchars(strip_tags($this->password));
    $this->position=htmlspecialchars(strip_tags($this->position));
    $this->salary=htmlspecialchars(strip_tags($this->salary));
    $this->project_id=htmlspecialchars(strip_tags($this->project_id));    
    $this->created=htmlspecialchars(strip_tags($this->created));
 
    // bind values
    $stmt->bindParam(":fname", $this->fname);
    $stmt->bindParam(":lname", $this->lname);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":password", $this->password);
    $stmt->bindParam(":position", $this->position);
    $stmt->bindParam(":salary", $this->salary);
    $stmt->bindParam(":project_id", $this->project_id);   
    $stmt->bindParam(":created", $this->created);
 
    // execute query
    if($stmt->execute()){
        return true;
    }
 
    return false;
     
}

// used when filling up the update product form
function readOne(){
 
    // query to read single record
    $query = "SELECT
                pr.name as project_name, p.id, p.fname,p.lname, p.email, p.position, p.salary, p.project_id, p.created
            FROM
                " . $this->table_name . " p
               JOIN
                        projects pr
                        ON p.project_id = pr.id
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
    $this->fname = $row['fname'];
    $this->lname = $row['lname'];
    $this->email = $row['email'];
    $this->position = $row['position'];
    $this->salary = $row['salary'];
    $this->project_id = $row['project_id'];
    $this->project_name = $row['project_name'];
    
}

function readEmployeeDetails(){
	
	 // query to read single record
    $query = "SELECT
                  pr.id, concat(pr.fname,' ',pr.lname) as employee_name, pr.email,pr.type
            FROM
                " . $this->table_name . " pr
               		   
            WHERE
                pr.id = ? 
            ";
			
	
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
    
    // bind id of product to be updated
    $stmt->bindParam(1, $this->id);
   
    // execute query
    $stmt->execute();
    
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // set values to object properties
   
    $this->employee_name = $row['employee_name'];
    $this->email = $row['email'];
    $this->type = $row['type'];
	
}


// update the product
function update(){
    
       // update query
       $query = "UPDATE
                   " . $this->table_name . "
               SET
                   fname = :fname,
                   lname=:lname,
                   position = :position,
                   salary = :salary,
                   category_id = :category_id
               WHERE
                   id = :id";
    
       // prepare query statement
       $stmt = $this->conn->prepare($query);
    
       // sanitize
       $this->fname=htmlspecialchars(strip_tags($this->fname));
       $this->lname=htmlspecialchars(strip_tags($this->lname));
       $this->position=htmlspecialchars(strip_tags($this->position));
       $this->salary=htmlspecialchars(strip_tags($this->salary));
       $this->category_id=htmlspecialchars(strip_tags($this->category_id));
       $this->id=htmlspecialchars(strip_tags($this->id));
    
       // bind new values
       $stmt->bindParam(':fname', $this->fname);
       $stmt->bindParam(':lname', $this->lname);
       $stmt->bindParam(':position', $this->position);
       $stmt->bindParam(':salary', $this->salary);
       $stmt->bindParam(':category_id', $this->category_id);
       $stmt->bindParam(':id', $this->id);
    
       // execute the query
       if($stmt->execute()){
           return true;
       }
    
       return false;
   }

// delete the product
function delete(){
 
    // delete query
    $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
 
    // prepare query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $this->id=htmlspecialchars(strip_tags($this->id));
 
    // bind id of record to delete
    $stmt->bindParam(1, $this->id);
 
    // execute query
    if($stmt->execute()){
        return true;
    }
 
    return false;
     
}

// search products
function search($keywords){
 
    // select all query
    $query = "SELECT
                c.name as category_name, p.id, p.fname,p.lname,p.email,p.position, p.salary, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            WHERE
                p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
            ORDER BY
                p.created DESC";
 
    // prepare query statement
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    $keywords=htmlspecialchars(strip_tags($keywords));
    $keywords = "%{$keywords}%";
 
    // bind
    $stmt->bindParam(1, $keywords);
    $stmt->bindParam(2, $keywords);
    $stmt->bindParam(3, $keywords);
 
    // execute query
    $stmt->execute();
 
    return $stmt;
}

// read products with pagination
public function readPaging($from_record_num, $records_per_page){
 
    // select query
    $query = "SELECT
                c.name as category_name, p.id, p.fname,p.lname,p.email, p.position, p.salary, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                LEFT JOIN
                    categories c
                        ON p.category_id = c.id
            ORDER BY p.created DESC
            LIMIT ?, ?";
 
    // prepare query statement
    $stmt = $this->conn->prepare( $query );
 
    // bind variable values
    $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
    $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
 
    // execute query
    $stmt->execute();
 
    // return values from database
    return $stmt;
}

// used for paging products
public function count(){
    $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";
 
    $stmt = $this->conn->prepare( $query );
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
    return $row['total_rows'];
}
}