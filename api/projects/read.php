<?php
// required header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/project.php';
 
// instantiate database and category object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$project = new Project($db);
 
// query categorys
$stmt = $project->read();
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // products array
    $projects_arr=array();
    $projects_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
        $project_item=array(
            "id" => $id,
            "name" => $name,
            "budget" => $budget,
			"super_id"=>$super_id,
			"supervisor_name"=>$supervisor_name
        );
 
        array_push($projects_arr["records"], $project_item);
    }
 
    echo json_encode($projects_arr);
}
 
else{
    echo json_encode(
        array("message" => "No projects found.")
    );
}
?>