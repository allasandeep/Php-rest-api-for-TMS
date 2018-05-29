<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/project.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare project object
$project = new Project($db);
 
// set ID property of project to be edited
$project->id = isset($_GET['id']) ? $_GET['id'] : die();
 
// read the details of project to be edited
$stmt = $project->readEmployees();
$num = $stmt->rowCount();

if($num>0){

$employee_arr=array();
$employee_arr["records"]=array();

 while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	 extract($row);
// create array
$employee_item = array(
    "id" => $employee_id,   
    "employee_name" => $employee_name,	
    "email" => $email,    
    "position" => $position,
    "salary" => $salary,
    "project_id"=>$project_id,
    "project_name"=>$project_name,
    "super_id"=>$super_id
	
 
);

array_push($employee_arr["records"], $employee_item);

 }
 
// make it json format
echo json_encode($employee_arr);
}
 
else{
    echo json_encode(
        array("message" => "No Employee is Assigned to you.")
    );
	}
?>