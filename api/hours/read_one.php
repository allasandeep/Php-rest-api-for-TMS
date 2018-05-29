<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/hours.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare project object
$hours = new Hours($db);
 
// set ID property of project to be edited
$hours->id = isset($_GET['id']) ? $_GET['id'] : die();
 
// read the details of project to be edited
$hours->readOne();

$hour_arr=array();
$hour_arr["records"]=array();
 
// create array
$hour_item = array(
    "id" =>  $hours->id,
    "workdate" => $hours->work_date,
    "timefrom"=>$hours->worked_from,
    "timeto"=>$hours->worked_to
 
);

array_push($hour_arr["records"], $hour_item);

if(empty($hour_arr))
{
	echo json_encode(
        array("message" => "Hour Not Found.")
    );
}else
{	
// make it json format
 print_r(json_encode($hour_arr));
}
 


?>