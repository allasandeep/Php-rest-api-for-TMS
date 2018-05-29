<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/employee.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare product object
$employee = new Employee($db);
 
// get id of product to be edited
$data = json_decode(file_get_contents("php://input"));
 
// set ID property of product to be edited
$employee->id = $data->id;
 
// set product property values
$employee->fname = $data->fname;
$employee->lname = $data->lname;
$employee->salary = $data->salary;
$employee->position = $data->position;
$employee->category_id = $data->category_id;
 
// update the product
if($employee->update()){
    echo '{';
        echo '"message": "employee was updated."';
    echo '}';
}
 
// if unable to update the product, tell the user
else{
    echo '{';
        echo '"message": "Unable to update employee."';
    echo '}';
}
?>