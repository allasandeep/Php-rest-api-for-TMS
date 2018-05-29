<?php
// required header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/supervisor.php';
 
// instantiate database and category object
$database = new Database();
$db = $database->getConnection();
 
// initialize object
$supervisor = new Supervisor($db);
 
// query categorys
$stmt = $supervisor->read();
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // products array
    $supervisors_arr=array();
    $supervisors_arr["records"]=array();
 
    // retrieve our table contents
    // fetch() is faster than fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
        $supervisor_item=array(
            "id" => $id,
            "name" => $name,
			"fname" => $fname,
			"lname" => $lname,
			"email" => $email,
			"position" => $position,
			"salary" => $salary
            
        );
 
        array_push($supervisors_arr["records"], $supervisor_item);
    }
 
    echo json_encode($supervisors_arr);
}
 
else{
    echo json_encode(
        array("message" => "No Supervisors found.")
    );
}
?>