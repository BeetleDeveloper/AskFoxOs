<?php
require_once("includes/inc_files.php");


if(isset($_GET['request'])){
	$request = clean_value($_GET['request']);
} else {
	echo "No request given. Please refer to the <a href='developer_api.php'>Developer API Documentation</a> for the commands";
	exit;
}

if($request == "user_details"){
	$username = clean_value($_GET['username']);
	if($username == ""){
		echo json_encode("No user entered");
	} else {
		echo Api::get_user_data($username);
	}
} else if($request == "user_questions"){
	$username = clean_value($_GET['username']);
	if($username == ""){
		echo json_encode("No user entered");
	} else {
		echo Api::get_user_questions($username);
	}
} else if($request == "question_details"){
	$id = clean_value($_GET['id']);
	if($id == ""){
		echo json_encode("No id entered");
	} else {
		echo Api::get_question($id);
	}
}

?>