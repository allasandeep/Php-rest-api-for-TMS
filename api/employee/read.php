<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/employee.php';
 
// instantiate database and employee object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$employee = new Employee($db);
 
// query employees
$stmt = $employee->read();
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // employees array
    $employee_arr=array();
    $employee_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
        $employee_item=array(
            "id" => $id,
            "fname" => $fname,
            "lname" => $lname,
			"employee_name" => $employee_name,
            "email" => $email,
            "position" => html_entity_decode($position),
            "salary" => $salary,
            "project_id"=>$project_id,
            "project_name"=>$project_name,
			"type"=>$type,
        );
 
        array_push($employee_arr["records"], $employee_item);
    }
 
    echo json_encode($employee_arr);
}
 
else{
    echo json_encode(
        array("message" => "No employees found.")
    );
}
?>