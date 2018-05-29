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
$project->readProjects();

$project_arr=array();
$project_arr["records"]=array();
 
// create array
$project_item = array(
    "id" =>  $project->id,   
    "employee_name" => $project->employee_name,	
    "email" => $project->email,    
    "position" => $project->position,
    "salary" => $project->salary,
    "project_id"=>$project->project_id,
    "project_name"=>$project->project_name,
    "super_id"=>$project->super_id,
	"supervisor_name"=>$project->supervisor_name,
 
);

array_push($project_arr["records"], $project_item);


if(empty($project_arr))
{
	echo json_encode(
        array("message" => "You are not assigned to any projects.")
    );
}else
{	
// make it json format
 print_r(json_encode($project_arr));
}
?>