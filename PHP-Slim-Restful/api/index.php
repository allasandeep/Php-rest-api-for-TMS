<?php
require 'config.php';
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$app->post('/login','login'); /* User login */
$app->post('/employeesignup','employeesignup');
$app->post('/employeeedit','employeeedit');
$app->post('/addHours','addHours');
$app->post('/readTotalHours','readTotalHours');
$app->post('/readTotalHoursMonthly','readTotalHoursMonthly');
$app->post('/readHoursBetween','readHoursBetween');
$app->post('/readHoursMonthly','readHoursMonthly');
$app->post('/addEmployees','addEmployees');
$app->post('/createproject','createproject');
$app->post('/deleteop','deleteop');
$app->post('/projectdeleteop','projectdeleteop');
$app->post('/projectEdit','projectEdit');
$app->post('/hoursEdit','hoursEdit');
$app->post('/removeEmployee','removeEmployee');




$app->run();

/************************* USER LOGIN *************************************/
/* ### User login ### */
function login() {
    
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
		
    
    try {
        
        $db = getDB();
        $userData ='';
        $sql = "SELECT id, concat(fname,' ',lname) as name,email, username, type FROM employees WHERE (username=:username or email=:username) and password=:password ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $data->username, PDO::PARAM_STR);
        $password=hash('sha256',$data->password);		
        $stmt->bindParam("password", $password, PDO::PARAM_STR);
        $stmt->execute();
        $mainCount=$stmt->rowCount();
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
		
        
        if(!empty($userData))
        {
            $id=$userData->id;
            $userData->token = apiToken($id);
        }
        
        $db = null;
         if($userData){
               $userData = json_encode($userData);			  
                echo '{"userData": ' .$userData . '}';
				
            } else {
               echo '{"error":{"text":"Bad request wrong username and password"}}';
            }

           
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}





function employeesignup() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());	
	$firstname = $data->fname;
    $lastname = $data->lname;
	$username = $data->username;
	$email=$data->email;
	$password =$data->password;
    $empPosition=$data->position;
    $salary=$data->salary;
	$project_id =$data->selectedProjectId;
	$type = "Employee";
	
    
    try {	
		
        
        $username_check = preg_match('~^[A-Za-z0-9_]{3,20}$~i', $username);
        $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
        $password_check = preg_match('~^[A-Za-z0-9!@#$%^&*()_]{6,20}$~i', $password);
        
        //echo $email_check.'<br/>'.$email.'<br/>';
       // echo $username_check.'<br/>'.$username.'<br/>';
       // echo $password_check.'<br/>'.$password.'<br/>';
        
        if (strlen(trim($username))>0 && strlen(trim($password))>0 && strlen(trim($email))>0 && $email_check>0 && $password_check>0 && $username_check>0)
        {
            
			echo $tablename;
            $db = getDB();
            $userData = '';
            $sql = "SELECT id FROM employees WHERE email=:email";
            $stmt = $db->prepare($sql);
            
            $stmt->bindParam("email", $email,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            $created=date('Y-m-d H:i:s');
            if($mainCount==0)
            {
               
                //Inserting user values
                $sql1="INSERT INTO employees (fname,lname,username,email,password,position,salary,project_id,type,created)VALUES(:fname,:lname,:username,:email,:password,:position,:salary,:project_id,:type,:created)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam(":fname", $firstname,PDO::PARAM_STR);
				$stmt1->bindParam(":lname", $lastname,PDO::PARAM_STR);
				$stmt1->bindParam(":username", $username,PDO::PARAM_STR);
				$stmt1->bindParam(":email", $email,PDO::PARAM_STR);
                $password=hash('sha256',$data->password);
                $stmt1->bindParam(":password", $password,PDO::PARAM_STR);
                $stmt1->bindParam(":position", $empPosition,PDO::PARAM_STR);
                $stmt1->bindParam(":salary", $salary,PDO::PARAM_STR);
				$stmt1->bindParam(":project_id", $project_id,PDO::PARAM_STR);
				$stmt1->bindParam(":type", $type,PDO::PARAM_STR);
                $stmt1->bindParam(":created", $created,PDO::PARAM_STR);				
                $stmt1->execute();
                
                $userData=internalUserDetails($email);
                
            }
			else{
				echo json_encode(
        array("message" => "Employee with this username already exists.")
    );
			}
            
            $db = null;
         

            if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":{"text":"Enter valid data bb"}}';
            }

           
        }
        else{
            echo '{"error":{"text":"Enter valid data dd"}}';
        }
		
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}



function employeeedit() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
	$id = $data->id;
	$firstname = $data->fname;
    $lastname = $data->lname;			
    $empPosition=$data->position;
    $salary=$data->salary;
	$project_id = $data->selectedProjectId;	
   
    
    try {    
            
            $db = getDB();
            $userData = ''; 
           			
                    
                //Inserting user values
                $sql1="UPDATE employees set fname = '".$firstname."',lname= '".$lastname."',position='".$empPosition."',salary='".$salary."',project_id='".$project_id."' WHERE id='".$id."' ";
                $stmt1 = $db->prepare($sql1);
                $sql2="select username from employees where id='".$id."'";
				$stmt2 = $db->prepare($sql2);
				
                $stmt1->execute();     
				$stmt2->execute();
				
				$row1 = $stmt2->fetch(PDO::FETCH_ASSOC);
	            $username =  $row1['username'];
				
				$userData=internalUserDetails($username);			
                $db = null;
				
				if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":{"text":"Enter valid data bb"}}';
            }
     
        
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function createproject() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $projectname= $data->projectname;    
    $employee_id = $data->selectedEmployeeId;
	$budget = $data->budget;
    $type = "Supervisor";
	//$consent = $data->consent;
    try {     
       
        
        //echo $email_check.'<br/>'.$email.'<br/>';
       // echo $username_check.'<br/>'.$username.'<br/>';
       // echo $password_check.'<br/>'.$password.'<br/>';
        
        if (strlen(trim($projectname))>0)
        {
            
            $db = getDB();
            $userData = '';
			
			/*if($consent != 'true')
			{
			$sql0 = "SELECT type FROM employees WHERE id='".$employee_id."'";
            $stmt0 = $db->prepare($sql0);           
            $stmt0->execute();
			$row = $stmt0->fetch(PDO::FETCH_ASSOC);
	        $employeeType =  $row['type'];
			if($employeeType == 'Supervisor')
			{
				 echo json_encode(
        array("typeMessage" => "This Employee is already a Supervisor for a different project, Do you wanna assign him for this project too?")
    );
			}
			else{
				$consent = 'true';
			}
			}*/
			
			
            $sql = "SELECT id FROM projects WHERE name=:projectname";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("projectname", $projectname,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            if($mainCount==0)
            {               		      
			 // if($consent == 'true')
			 // {
                /*Inserting user values*/
                $sql1="INSERT INTO projects(name,budget,super_id)VALUES(:projectname,:budget,:employee_id)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam("projectname", $projectname,PDO::PARAM_STR);  
                $stmt1->bindParam("budget",$budget,PDO::PARAM_STR);				
                $stmt1->bindParam("employee_id",$employee_id,PDO::PARAM_STR);   
				                       
				
				if($stmt1->execute())
			   { 
		          $sql2 = "SELECT id FROM projects WHERE name='".$projectname."'";
                  $stmt2 = $db->prepare($sql2);
				  $stmt2->execute();
				  $row = $stmt2->fetch(PDO::FETCH_ASSOC);
				  $project_id = $row['id'];
		   
		        $sql3="UPDATE employees set project_id = '".$project_id."', type = '".$type."'  WHERE id='".$employee_id."' ";
				$stmt3 = $db->prepare($sql3);
		          
		         
				
				if($stmt3->execute())
			   {
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "error 3")
    );
			   }
			   }else{
				   echo json_encode(
        array("message" => "error 2")
    );
			   }
			//}
			}

                 else
			{
				 echo json_encode(
        array("message" => "Project Already exists")
    );				
			}
            
            $db = null;
         

           
}
           
        
        else{
            echo '{"error":{"text":"Enter valid project name"}}';
        }
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function projectEdit() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
	$id = $data->id;
	$projectname = $data->project_name;			
    $budget=$data->budget;
    $project_id = $data->id;
    $selectedSupervisorId = $data->selectedSupervisorId;	
    $previousSupervisorId = $data->previousSupervisorId;
    $etype = "Employee";
	$stype = "Supervisor";
	
    try {    
            
            $db = getDB();
             
           			$projectName='';
                    
                //Inserting user values
                $sql1="UPDATE projects set budget ='".$budget."',super_id='".$selectedSupervisorId."' WHERE id='".$id."' ";
                $stmt1 = $db->prepare($sql1);     
					
						
                
				
				if($stmt1->execute()){
					
					
				$sql2="UPDATE employees set type = '".$etype."'  WHERE id='".$previousSupervisorId."' ";
				$stmt2 = $db->prepare($sql2);			
			            
													
               if($stmt2->execute())
			   { 
				   $sql3="UPDATE employees set project_id = '".$project_id."', type = '".$stype."'  WHERE id='".$selectedSupervisorId."' ";
				$stmt3 = $db->prepare($sql3);
				
				if($stmt3->execute())
			   {
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	$db = null;
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "error 3")
    );
			   }
			   }else{
				   echo json_encode(
        array("message" => "error 2")
    );
			   }
			   
            } else {
               echo json_encode(
        array("message" => "error")
    );
            }
     
        
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}


function addHours(){
	$request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $project_id= $data->project_id;    
    $employee_id = $data->empid;
	$timefrom = $data->timefrom;
	$timeto = $data->timeto;
	$workdate = $data->workdate;
    
    try {                
			
            $db = getDB();
            $userData = '';
            
            
                          
                /*Inserting user values*/
                $sql1="INSERT INTO hours(project_id,employee_id,worked_from,worked_to,work_date)VALUES(:project_id,:employee_id,:worked_from,:worked_to,:work_date)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam("project_id", $project_id,PDO::PARAM_STR);  
                $stmt1->bindParam("employee_id",$employee_id,PDO::PARAM_STR);				
                $stmt1->bindParam("worked_from",$timefrom,PDO::PARAM_STR);
				$stmt1->bindParam("worked_to",$timeto,PDO::PARAM_STR);
				$stmt1->bindParam("work_date",$workdate,PDO::PARAM_STR);
                           
                
				$sql2="UPDATE hours SET hours_diff = TIMEDIFF(worked_to, worked_from) where employee_id = '".$employee_id."' and project_id= '".$project_id."'";
				$stmt2 =$db->prepare($sql2);
				
				
				if($stmt1->execute() && $stmt2->execute() )
			   {
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "Unable to Add ")
    );
			   }
                
            
			
            
            $db = null;  
             
       
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function hoursEdit() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
	$id = $data->id;
	$timefrom = $data->timefrom;
	$timeto = $data->timeto;
	$workdate = $data->workdate;
   
    
    try {    
            
            $db = getDB();                      			
                    
                //Inserting user values
                $sql1="UPDATE hours set worked_from = '".$timefrom."', worked_to = '".$timeto."', work_date = '".$workdate."' WHERE id='".$id."' ";
                $stmt1 = $db->prepare($sql1);
                
				$sql2="UPDATE hours SET hours_diff = TIMEDIFF(worked_to, worked_from) where id= '".$id."'";
				$stmt2 =$db->prepare($sql2);
					

				if($stmt1->execute() && $stmt2->execute() )
			   {
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "error ")
    );
			   }
				                
											
							
                $db = null;
				
				
     
        
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function addEmployees(){
	$request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $project_id= $data->selectedProjectId;    
    $employee_id = $data->selectedEmployeeId;
	
    
    try {     
            
			$db = getDB();
					
            $sql = "SELECT id FROM employees WHERE project_id=:project_id and id=:employee_id";
            $stmt = $db->prepare($sql);            
			$stmt->bindParam("project_id", $project_id,PDO::PARAM_STR);
			$stmt->bindParam("employee_id", $employee_id,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            if($mainCount==0)
            {               
                /*Inserting user values*/
                $sql1="UPDATE employees set project_id = '".$project_id."'  WHERE id='".$employee_id."' ";
                $stmt1 = $db->prepare($sql1);                	                
                   

              if($stmt1->execute())
			   {
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	$db = null;
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "Unable to add the employee ")
    );
			   }				
                
                
            }
			else
			{
				echo json_encode(
        array("message" => "Employee already Assigned to that project")
    );
			}
            
            $db = null;  
             
       
    }
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

	
function removeEmployee(){
	$request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $id = $data->del_id;	
	$tablename = $data->type;
	
	
	try{
	$db = getDB();
	
	
    $query = "Update ".$tablename." set project_id = '14' WHERE id = :id";
	$stmt = $db->prepare($query);
    $stmt->bindParam("id", $id,PDO::PARAM_STR);   
	 
            
    // execute query
    if($stmt->execute() )
			   {
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	$db = null;
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "error ")
    );
			   }
	}
	  catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
	} 
	
	function deleteop(){
	$request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $id = $data->del_id;	
	$tablename = $data->type;
	
	
	try{
	$db = getDB();
	
	
    $query = "DELETE FROM  ".$tablename." WHERE id = :id";
	$stmt = $db->prepare($query);
    $stmt->bindParam("id", $id,PDO::PARAM_STR);   
	 
            
    // execute query
    if($stmt->execute() )
			   {
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	$db = null;
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "error ")
    );
			   }
	}
	  catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
	} 
	
function projectdeleteop(){
	$request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $id = $data->del_id;	
	$tablename = $data->type;
	
	
	try{
	$db = getDB();
	
	
    $query = "DELETE FROM  ".$tablename." WHERE id = :id";
	$stmt = $db->prepare($query);
    $stmt->bindParam("id", $id,PDO::PARAM_STR);
      
    $query1 = "update employees set project_id = '14', type='Employee' WHERE project_id = :id";
	$stmt1 = $db->prepare($query1);
    $stmt1->bindParam("id", $id,PDO::PARAM_STR);
            
    // execute query
    if($stmt->execute() && $stmt1->execute())
			   {
				   
				   
               echo json_encode(
        array("successMessage" => "success")
    );
	$db = null;
			   }
			   
			   else{
				   echo json_encode(
        array("message" => "error ")
    );
			   }
	}
	  catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
	}

function email() {
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $email=$data->email;

    try {
       
        $email_check = preg_match('~^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$~i', $email);
       
        if (strlen(trim($email))>0 && $email_check>0)
        {
            $db = getDB();
            $userData = '';
            $sql = "SELECT id FROM emailUsers WHERE email=:email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam("email", $email,PDO::PARAM_STR);
            $stmt->execute();
            $mainCount=$stmt->rowCount();
            $created=time();
            if($mainCount==0)
            {
                
                /*Inserting user values*/
                $sql1="INSERT INTO emailUsers(email)VALUES(:email)";
                $stmt1 = $db->prepare($sql1);
                $stmt1->bindParam("email", $email,PDO::PARAM_STR);
                $stmt1->execute();
                
                
            }
            $userData=internalEmailDetails($email);
            $db = null;
            if($userData){
               $userData = json_encode($userData);
                echo '{"userData": ' .$userData . '}';
            } else {
               echo '{"error":{"text":"Enter valid dataaaa"}}';
            }
        }
        else{
            echo '{"error":{"text":"Enter valid data"}}';
        }
    }
    
    catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function readHoursBetween(){
 
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $date_from = $data->fromdate;
	$date_to = $data->todate;
	$id = $data->id;
	
		
	try
	{
	$db = getDB();
	$userdata = [];
    // query to read single record
    $query = "SELECT
                hr.project_id, hr.work_date, ((hour(hours_diff)) * 60) + (minute(hours_diff)) as total_minutes, hr.worked_from,hr.worked_to, em.id as employee_id, concat(em.fname,' ',em.lname) as employee_name, pr.name as project_name, em.salary,em.position FROM
                hours hr
				JOIN 
				employees em 
				ON em.id = hr.employee_id
				JOIN 
				projects pr 
				ON pr.id = hr.project_id
                WHERE
                pr.super_id = '".$id."' and (type= 'Employee' or type = 'Admin') and (hr.work_date between '".$date_from."' and '".$date_to."') order by hr.work_date dESC";
		
    $query1 = "select concat(fname,' ',lname) as supervisor_name from employees where id = '".$id."'";		
	
	$stmt = $db->prepare($query);
    $stmt1 = $db->prepare($query1);
    $stmt->execute();
	$stmt1->execute();
	$num = $stmt->rowCount();
	
	$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	$supervisor_name =  $row1['supervisor_name'];
	
	if($num>0){
 
   // employees array
    $hour_arr=array();
    $hour_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
		
		$total_hours = intval($total_minutes/60);
        $minutes = $total_minutes - ($total_hours* 60);
	    $hours_worked = $total_hours.' Hour '.$minutes.' Minutes';
 
        $hour_item=array(            
            "project_name" => $project_name,
			"employee_id" =>$employee_id,
			"employee_name" => $employee_name,
			"supervisor_name" => $supervisor_name,
			"position" => $position,
            "salary" => $salary,
            "project_id"=>$project_id,
            "worked_from"=>$worked_from,
			"worked_to"=>$worked_to,
			"work_date"=>$work_date,
			"total_hours"=>$hours_worked
			
        );
 
        array_push($hour_arr["records"], $hour_item);
    }
 
    echo json_encode($hour_arr);
}
 
else{
    echo json_encode(
        array("message" => "No hours found.")
    );
}
	
    
	
	
    }
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	
}

function readHoursMonthly(){
 
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $month = $data->month;
	$year = $data->year;
	$id = $data->id;
	
		
	try
	{
	$db = getDB();	
    // query to read single record
    $query = "SELECT
                hr.project_id, hr.work_date, ((hour(hours_diff)) * 60) + (minute(hours_diff)) as total_minutes, hr.worked_from,hr.worked_to, em.id as employee_id, concat(em.fname,' ',em.lname) as employee_name, pr.name as project_name, em.salary,em.position FROM
                hours hr
				JOIN 
				employees em 
				ON em.id = hr.employee_id
				JOIN 
				projects pr 
				ON pr.id = hr.project_id
                WHERE
                pr.super_id = '".$id."' and (type= 'Employee' or type = 'Admin') and (MONTH(hr.work_date) = '".$month."') and (YEAR(hr.work_date)='".$year."') order by hr.work_date dESC";
		
    $query1 = "select concat(fname,' ',lname) as supervisor_name from employees where id = '".$id."'";		
	
	$stmt = $db->prepare($query);
    $stmt1 = $db->prepare($query1);
    $stmt->execute();
	$stmt1->execute();
	$num = $stmt->rowCount();
	
	$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	$supervisor_name =  $row1['supervisor_name'];
	
	if($num>0){
 
   // employees array
    $hour_arr=array();
    $hour_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
		
		$total_hours = intval($total_minutes/60);
        $minutes = $total_minutes - ($total_hours* 60);
	    $hours_worked = $total_hours.' Hour '.$minutes.' Minutes';
 
        $hour_item=array(            
            "project_name" => $project_name,
			"employee_id" =>$employee_id,
			"employee_name" => $employee_name,
			"supervisor_name" => $supervisor_name,
			"position" => $position,
            "salary" => $salary,
            "project_id"=>$project_id,
            "worked_from"=>$worked_from,
			"worked_to"=>$worked_to,
			"work_date"=>$work_date,
			"total_hours"=>$hours_worked
			
        );
 
        array_push($hour_arr["records"], $hour_item);
    }
 
    echo json_encode($hour_arr);
}
 
else{
    echo json_encode(
        array("message" => "No hours found.")
    );
}
	
    
	
	
    }
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	
}

function readTotalHours(){
 
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $date_from = $data->fromdate;
	$date_to = $data->todate;
	$id = $data->id;
	
		
	try
	{
	$db = getDB();
	$userdata = [];
    // query to read single record
    $query = "SELECT
                em.id as employee_id, concat(em.fname,' ',em.lname) as employee_name, pr.name as project_name, (sum(hour(hours_diff)) * 60) + sum(minute(hours_diff)) as total_minutes  FROM
                hours hr
				JOIN 
				employees em 
				ON em.id = hr.employee_id
				JOIN 
				projects pr 
				ON pr.id = hr.project_id
                WHERE
                pr.super_id = '".$id."' and (type= 'Employee' or type = 'Admin') and (hr.work_date between '".$date_from."' and '".$date_to."') group by em.id";
		
    $query1 = "select concat(fname,' ',lname) as supervisor_name from employees where id = '".$id."'";		
	
	$stmt = $db->prepare($query);
    $stmt1 = $db->prepare($query1);
    $stmt->execute();
	$stmt1->execute();
	$num = $stmt->rowCount();
	
	$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	$supervisor_name =  $row1['supervisor_name'];
	
	if($num>0){
 
   // employees array
    $hour_arr=array();
    $hour_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
			$total_hours = intval($total_minutes/60);
            $minutes = $total_minutes - ($total_hours* 60);
			$hours_worked = $total_hours.' Hour '.$minutes.' Minutes';
			
        $hour_item=array(            
            "project_name" => $project_name,
			"employee_id" =>$employee_id,
			"employee_name" => $employee_name,
			"supervisor_name" => $supervisor_name,
			"hours_worked"=> $hours_worked,
			
        );
 
        array_push($hour_arr["records"], $hour_item);
    }
 
    echo json_encode($hour_arr);
}
 
else{
    echo json_encode(
        array("message" => "No hours found.")
    );
}
	
    
	
	
    }
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	
}

function readTotalHoursMonthly(){
 
    $request = \Slim\Slim::getInstance()->request();
    $data = json_decode($request->getBody());
    $month = $data->month;
	$year = $data->year;
	$id = $data->id;
	
		
	try
	{
	$db = getDB();
	$userdata = [];
    // query to read single record
    $query = "SELECT
                em.id as employee_id, concat(em.fname,' ',em.lname) as employee_name, pr.name as project_name, (sum(hour(hours_diff)) * 60) + sum(minute(hours_diff)) as total_minutes  FROM
                hours hr
				JOIN 
				employees em 
				ON em.id = hr.employee_id
				JOIN 
				projects pr 
				ON pr.id = hr.project_id
                WHERE
                pr.super_id = '".$id."' and (type= 'Employee' or type = 'Admin') and (MONTH(hr.work_date) = '".$month."') and (YEAR(hr.work_date)='".$year."') group by em.id";
		
    $query1 = "select concat(fname,' ',lname) as supervisor_name from employees where id = '".$id."'";		
	
	$stmt = $db->prepare($query);
    $stmt1 = $db->prepare($query1);
    $stmt->execute();
	$stmt1->execute();
	$num = $stmt->rowCount();
	
	$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
	$supervisor_name =  $row1['supervisor_name'];
	
	if($num>0){
 
   // employees array
    $hour_arr=array();
    $hour_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
			$total_hours = intval($total_minutes/60);
            $minutes = $total_minutes - ($total_hours* 60);
			$hours_worked = $total_hours.' Hour '.$minutes.' Minutes';
			
        $hour_item=array(            
            "project_name" => $project_name,
			"employee_id" =>$employee_id,
			"employee_name" => $employee_name,
			"supervisor_name" => $supervisor_name,
			"hours_worked"=> $hours_worked,
			
        );
 
        array_push($hour_arr["records"], $hour_item);
    }
 
    echo json_encode($hour_arr);
}
 
else{
    echo json_encode(
        array("message" => "No hours found.")
    );
}
	
    
	
	
    }
	catch(PDOException $e)
	{
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
	
}

/* ### internal Username Details ### */
function internalUserDetails($input) {
    
    try {
        $db = getDB();
        $sql = "SELECT id, fname,lname, email, username ,type FROM employees WHERE username=:input or email=:input";
        $stmt = $db->prepare($sql);
        $stmt->bindParam("input", $input,PDO::PARAM_STR);
        $stmt->execute();
        $usernameDetails = $stmt->fetch(PDO::FETCH_OBJ);
        $usernameDetails->token = apiToken($usernameDetails->id);
        $db = null;
        return $usernameDetails;
        
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
    
}




?>
