<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/core.php';
include_once '../shared/utilities.php';
include_once '../config/database.php';
include_once '../objects/employee.php';
 
// utilities
$utilities = new Utilities();
 
// instantiate database and employee object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$employee = new Employee($db);
 
// query employees
$stmt = $employee->readPaging($from_record_num, $records_per_page);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // employees array
    $employees_arr=array();
    $employees_arr["records"]=array();
    $employees_arr["paging"]=array();
 
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
            "email" => $email,
            "password" =>$password,
            "position" => html_entity_decode($position),
            "salary" => $salary,
            "project_id"=>$project_id,
            "category_id" => $category_id,
            "category_name" => $category_name
        );
 
        array_push($employees_arr["records"], $employee_item);
    }
 
 
    // include paging
    $total_rows=$employee->count();
    $page_url="{$home_url}employee/read_paging.php?";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $employees_arr["paging"]=$paging;
 
    echo json_encode($employees_arr);
}
 
else{
    echo json_encode(
        array("message" => "No employees found.")
    );
}
?>