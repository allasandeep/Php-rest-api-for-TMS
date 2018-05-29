<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/database.php';
 
// instantiate employee object
include_once '../objects/employee.php';
 
$database = new Database();
$db = $database->getConnection();
 
$employee = new Employee($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set employee property values
$employee->fname = $data->fname;
$employee->lname = $data->lname;
$employee->email = $data->email;
$employee->password = $data->password;
$employee->position = $data->position;
$employee->salary = $data->salary;
$employee->project_id = $data->project_id;
$employee->category_id = $data->category_id;
$employee->created = date('Y-m-d H:i:s');
 
// create the employee
if($employee->create()){
    echo '{';
        echo '"message": "Employee was created."';
    echo '}';
}
 
// if unable to create the employee, tell the user
else{
    echo '{';
        echo '"message": "Unable to create Employee."';
    echo '}';
}
?>