<?php 

if($session->is_logged_in()) { 
	$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
	if($user->suspended == "1") { 
		redirect_to('logout?msg=suspended'); 
	}	
} else {
	$user = "";
}

?>