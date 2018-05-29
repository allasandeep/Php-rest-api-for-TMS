<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/hours.php';
 
// instantiate database and hours object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$hours = new Hours($db);

// set ID property of project to be edited
$hours->id = isset($_GET['id']) ? $_GET['id'] : die();
 
// query employees
$stmt = $hours->readEmployeeHours();
$num = $stmt->rowCount();
 
// check if more than 0 record found
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
 
        $hour_item=array(            
            "project_name" => $project_name,
			"employee_id" =>$employee_id,
			"employee_name" => $employee_name,
			"supervisor_name" => $hours->supervisor_name,
            "position" => html_entity_decode($position),
            "salary" => $salary,
            "project_id"=>$project_id,
            "worked_from"=>$worked_from,
			"worked_to"=>$worked_to,
			"work_date"=>$work_date
			
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
?>