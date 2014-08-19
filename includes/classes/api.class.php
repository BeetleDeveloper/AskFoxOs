<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

$api_version = "1.0";

class Api {
	
	public static function get_user_data($username){
		global $api_version;
		
		$username = clean_value($username);
		
		$data = User::find_by_sql("SELECT username,first_name,last_name,gender,last_login,country,questions_posted,questions_answered FROM users WHERE username = '{$username}' LIMIT 1 ");
		
		if(isset($data[0])){
			$array = array("api_version" => $api_version, "data" => array("username" => $data[0]->username,"first_name" => $data[0]->first_name,"last_name" => $data[0]->last_name,"gender" => $data[0]->gender,"last_login" => $data[0]->last_login, "country" => $data[0]->country, "questions_posted" => $data[0]->questions_posted, "questions_answered" => $data[0]->questions_answered) );
			return json_encode($array);
		} else {
			return json_encode("No user found");
		}		
	}
	
	public static function get_user_questions($username){
		global $api_version;
		
		$user_id = User::find_id_by_username($username);
		$user_id->user_id = clean_value($user_id->user_id);
		$data = Question::find_by_sql("SELECT id,title,description,created,view_count,category_id,thumbs_up,thumbs_down,answer_count FROM question WHERE user_id = '{$user_id->user_id}' ");
		
		$multi_array = array();
		$counter = 0;
		foreach($data as $data){
			$multi_array[$counter] = array(
				"id" => $data->id,
				"name" => $data->name,
				"description" => $data->description,
				"created" => $data->created,
				"view_count" => $data->view_count,
				"category_id" => $data->category_id,
				"thumbs_up" => $data->thumbs_up,
				"thumbs_down" => $data->thumbs_down,
				"answer_count" => $data->answer_count
			);
			$counter++;
		}
		
		if(isset($data)){
			$array = array("api_version" => $api_version, "data" => $multi_array);
			return json_encode($array);
		} else {
			return json_encode("No user found");
		}		
	}
	
	public static function get_question($id){
		global $api_version;
		
		$id = clean_value($id);
		$data = Question::find_by_sql("SELECT id,title,description,created,view_count,category_id,thumbs_up,thumbs_down,answer_count FROM question WHERE id = '{$id}' ");
		
		$multi_array = array();
		$counter = 0;
		foreach($data as $data){
			$multi_array[$counter] = array(
				"id" => $data->id,
				"name" => $data->name,
				"description" => $data->description,
				"created" => $data->created,
				"view_count" => $data->view_count,
				"category_id" => $data->category_id,
				"thumbs_up" => $data->thumbs_up,
				"thumbs_down" => $data->thumbs_down,
				"answer_count" => $data->answer_count
			);
			$counter++;
		}
		
		if(isset($data)){
			$array = array("api_version" => $api_version, "data" => $multi_array);
			return json_encode($array);
		} else {
			return json_encode("No user found");
		}		
	}
	

}

?>