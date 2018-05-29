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
$stmt = $project->readSupervisorProjects();

$num = $stmt->rowCount();

if($num>0){
	
$project_arr=array();
$project_arr["records"]=array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	 extract($row);
 
// create array
$project_item = array(
    "super_id" =>  $super_id,      
    "project_id"=>$project_id,
    "project_name"=>$project_name,
    
 
);


array_push($project_arr["records"], $project_item);
}
echo json_encode($project_arr);
}
else{
	echo json_encode(
        array("message" => "You are not assigned to any projects.")
    );
}



?>