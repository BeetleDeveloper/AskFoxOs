<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');


class Profile {
	
	protected static $table_name="profile";
	protected static $db_fields = array('id', 'user_id', 'profile_status', 'profile_msg', 'about_me', 'profile_picture', 'profile_id', 'status', 'message', 'sent', 'type');
	
	public $id;
	public $user_id;
	public $profile_status;
	public $profile_msg;
	public $about_me;
	public $profile_picture;
	
	public $profile_id;
	public $status;
	public $message;
	public $sent;
	public $type;
	
	public static function update_profile($id,$profile_msg,$about_me){
		global $database;
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		if($id == $user_id){
			$database->query("UPDATE profile SET profile_msg = '{$profile_msg}', about_me = '{$about_me}' WHERE user_id = '{$id}' ");
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>La configuración de este perfil han sido actualizados con éxito.</div>");
			return "success";
		} else {
			return "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Lo sentimos, pero algo ha ido mal. Por favor, actualice la página y vuelva a intentarlo.</div>";
		}
	}
	
	public static function get_profile_messages($type, $profile_id){
		if($type == "unread"){
			return self::find_by_sql("SELECT * FROM profile_messages WHERE type = '0' AND profile_id = '{$profile_id}' ORDER BY sent DESC ");
		} else if($type == "all"){
			return self::find_by_sql("SELECT * FROM profile_messages WHERE profile_id = '{$profile_id}' ORDER BY sent DESC ");
		}
	}
		
	public static function display_profile_messages($type, $profile_id, $limit = null){
		if($limit != null){
			$limit = "LIMIT ".$limit;
		}
		if($type == "unread"){
			$profile_messages = self::find_by_sql("SELECT * FROM profile_messages WHERE type = '0' AND profile_id = '{$profile_id}' ORDER BY sent DESC $limit");
		} else if($type == "all"){
			$profile_messages = self::find_by_sql("SELECT * FROM profile_messages WHERE profile_id = '{$profile_id}' ORDER BY sent DESC $limit");
		}
		$messages = "";
		
		if(isset($_SESSION['masdyn']['answers']['user_id'])){
			$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
		} else {
			$user = "";
			$user_id = "";
		}
		
		foreach($profile_messages as $message): $message_owner = User::find_profile_message_owner($message->user_id);
		
		if($profile_id == $user->user_id){
			$action_buttons = '<br /><span class="right"><a href="JavaScript:void(0);" onclick="edit_message('.$message->id.');">Editar</a> - <a href="JavaScript:void(0);" onclick="delete_message('.$message->id.');">Eliminar</a></span>';
		} else {
			if($message_owner[0]->user_id == $user->user_id){
				$action_buttons = '<br /><span class="right"><a href="JavaScript:void(0);" onclick="edit_message('.$message->id.');">Editar</a></span>';
			} else {
				$action_buttons = "";
			}
		}
		
		if($message_owner[0]->profile_picture == "male.jpg" || $message_owner[0]->profile_picture == "female.jpg"){
			$profile_image = '<img src="assets/img/profile/'.$message_owner[0]->profile_picture.'" alt="Profile Picture">';
		} else { 
			$profile_image = '<img src="assets/img/profile/'.$message_owner[0]->user_id."/".$message_owner[0]->profile_picture.'" alt="Profile Picture">';
		} 
		
		$messages .= '<div class="profile_message_container" id="message'.$message->id.'">
			<div class="span2 center">
				'.$profile_image.'
			</div>
			<div class="span10">
				<a href="'.WWW.'profile?username='.$message_owner[0]->username.'" style="font-size: 16px;">'.$message_owner[0]->first_name.' '.$message_owner[0]->last_name.'</a>
				<span class="right">'.datetime_to_text($message->sent).'</span>
				<div class="clearfix" style="height: 6px;"></div>
				'.nl2br($message->message).'
				<br />
				'.$action_buttons.'
			</div>
			<div class="clearfix"></div>
		</div>';
		endforeach;
		return $messages;
	}
	
	public static function get_profile_picture($user_id){
		$data = self::find_by_sql("SELECT profile_picture FROM profile WHERE user_id = '{$user_id}' ");
		return $data[0]->profile_picture;
	}
	
	public static function create_profile_message($profile_id, $user_id, $message, $datetime){
		global $database;
		// $datetime = strftime("%Y-%m-%d %H:%M:%S", time());
		$database->query("INSERT INTO profile_messages (id,user_id,profile_id,status,message,sent,type) VALUES ('','{$user_id}','{$profile_id}','0','{$message}','{$datetime}','0') ");
	}
	
	public static function check_message_owner($id){
		global $database;
		$data = self::find_by_sql("SELECT user_id,profile_id FROM profile_messages WHERE id = '{$id}'");
		$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
		if($user->user_id == $data[0]->user_id || $user->user_id == $data[0]->profile_id){
			// $database->query("DELETE FROM profile_messages WHERE id = '{$id}' ");
			return true;
		} else {
			return false;
		}
	}
	
	public static function delete_message($id){
		if(self::check_message_owner($id)){
			$database->query("DELETE FROM profile_messages WHERE id = '{$id}' ");
			return true;
		} else {
			return false;
		}
	}
	
	public static function get_profile_message_data($id){
		$data = self::find_by_sql("SELECT message FROM profile_messages WHERE id = '{$id}' LIMIT 1");
		return $data[0]->message;
	}
	
	public static function update_message($id,$message){
		global $database;
		$session = new Session();
		if(self::check_message_owner($id)){
			$database->query("UPDATE profile_messages SET message = '{$message}' WHERE id = '{$id}' ");
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>El mensaje ha sido actualizado.</div>");
			return true;
		} else {
			$session->message("<div class='alert alert-failure'><button type='button' class='close' data-dismiss='alert'>×</button>Lo sentimos, pero algo ha ido mal. Por favor, inténtelo de nuevo.</div>");
			return false;
		}
	}
	
	public static function delete_image($image_name){
		global $database;
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		$data = self::find_by_sql("SELECT profile_picture FROM profile WHERE user_id = '{$user_id}'");
		if($image_name == $data[0]->profile_picture){
			return false;
		} else {
			unlink("./assets/img/profile/".$user_id."/".$image_name);
			return true;
		}
	}
	
	public static function set_thumbnail($name){
		global $database;
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		$database->query("UPDATE profile SET profile_picture = '{$name}' WHERE user_id = '{$user_id}' ");
		return true;
	}
	
	
	// Common Database Methods
	
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}
  
	public static function find_by_id($id) {
    	$result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE user_id = '{$id}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }
  	
  	public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    return $object_array;
   }

	public static function count_all() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".self::$table_name;
     $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
     return array_shift($row);
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
    	$object = new self;
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}

}

?>