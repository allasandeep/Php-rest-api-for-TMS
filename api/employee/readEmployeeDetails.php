<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/employee.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare employee object
$employee = new Employee($db);
 
// set ID property of employee to be edited
$employee->id = isset($_GET['id']) ? $_GET['id'] : die();
 
// read the details of employee to be edited
$employee->readEmployeeDetails();

$employee_arr=array();
$employee_arr["records"]=array();
 
// create array
$employee_item = array(
    "id" =>  $employee->id,
    "employee_name"=>$employee->employee_name,
    "email" => $employee->email,
    "type" => $employee->type
    
 
);
array_push($employee_arr["records"], $employee_item);

// make it json format
print_r(json_encode($employee_arr));
?>