<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');


class Admin {
	
	protected static $table_name="users";
	protected static $levels_table_name="user_levels";
	protected static $invites_table_name="invites";
	protected static $db_fields = array('id', 'user_id', 'first_name', 'last_name', 'gender', 'username', 'password', 'email', 'user_level', 'primary_group', 'activated', 'suspended', 'date_created', 'last_login', 'signup_ip', 'last_ip', 'country', 'whitelist', 'ip_whitelist', 'questions_posted', 'questions_answered', 'staff', 'created', 'expires', 'expiry_date', 'level_id', 'level_name', 'auto', 'datetime', 'ip_address', 'name', 'qty', 'status', 'redirect_page', 'access_time', 'time_type', 'amount', 'created', 'timed_access', 'expiry_date', 'expires', 'package_name','message','profile_picture','type','task');
	
	public $id;
	public $user_id;
	public $username;
	public $password;
	public $email;
	// public $user_level;
	// public $primary_group;
	public $activated;
	public $suspended;
	public $first_name;
	public $last_name;
	public $gender;
	public $date_created;
	public $last_login;
	// public $account_lock;
	public $signup_ip;
	public $last_ip;
	public $country;
	public $whitelist;
	public $ip_whitelist;
	public $credit;
	public $banked_credit;
	// public $level_expiry;
	// public $expiry_datetime;
	// public $invited_by;
	public $staff;
	public $questions_posted;
	public $questions_answered;
	
	// public $level_id;
	// public $level_name;
	// public $auto;
	
	// public $created;
	// public $timed_access;
	// public $expiry_date;
	// public $redirect_page;
	// public $access_time;
	// public $time_type;
	// public $amount;
	// public $expires;
	
	// public $package_name;
	
	public $datetime;
	public $ip_address;
	
	public $name;
	public $qty;
	public $status;
	
	public $message;
	public $profile_picture;
	
	// Table: user_activity
	
	// public $id;
	// public $user_id;
	// public $datetime;
	public $task;
	public $type;


	public function create_account($username, $password, $email, $first_name, $last_name, $plain_password, $signup_ip, $country, $gender, $user_level, $activated, $staff_notes, $alert_user, $whitelist, $ip_whitelist){
		global $database;
		$session = new Session();
		// Genetate the users ID.
		$user_id = generate_id();

		$flag = false;
		//until flag is false
		while ($flag == false){
			//check if the user id exists
			$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			//if it does try again till you find an id that does not exist
			if ($rows){
				$user_id = generate_id();
			}else{
				//if it does not exist, exit the loop
				$flag = true;
			}
		}
		if ($flag == true){
			//insert into db the data
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$first_name', '$last_name', '$gender', '$username', '$password', '$email', '$user_level', '0', '', '$activated', '0', '$datetime', '', '0', '$signup_ip', '', '$country', '0', '', '', '', '$invited_by')";
			$database->query($sql);
						
			// Send and email to the user.
			if($alert_user == "YES") {
				// Initialize functions.
				$email_class = new Email();

				// Email sent to the user if logged in.
				$from = SITE_EMAIL;
				$subject = "Welcome to ".SITE_NAME." ";
				
				$message = $email_class->email_template('registration_success', "$plain_password", "$username", "", "");
				$email_class->send_email($email, $from, $subject, $message);
			} else if($alert_user == "NO") {
				//$activation_hash = Activation::set_activation_link($email)
				// Activation::set_activation_link($plain_password, $username, $email);
			}
			
			if ($staff_notes != "") {
				self::create_staff_note($user_id, "On Account Creation", $staff_notes);
			}
			
			// Create the message that will be displayed on the login screen once the user has been redirected.
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The account has been created successfully.</div>");
			
			// redirect the user to the login page.
			redirect_to('users.php');
		}
	}

	public function update_account($value, $user_id, $username, $first_name, $last_name, $new_password, $email, $password, $country, $gender, $activated, $suspended, $staff){
			global $database;
						
			if ($value == 1) {
				$sql = "UPDATE ".self::$table_name." SET password = '{$new_password}', email = '{$email}', username = '{$username}', first_name = '{$first_name}', last_name = '{$last_name}', gender = '{$gender}', country = '{$country}', staff = '{$staff}' WHERE user_id = '{$user_id}'";
			} else if ($value == 2) {
				$sql = "UPDATE ".self::$table_name." SET email = '{$email}', username = '{$username}', first_name = '{$first_name}', last_name = '{$last_name}', gender = '{$gender}', country = '{$country}', activated = '{$activated}', suspended = '{$suspended}', staff = '{$staff}' WHERE user_id = '{$user_id}' ";
			} 
			$database->query($sql);

			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Settings successfully updated.</div>");
		
			redirect_to('user_settings.php?user_id='.$user_id.'');
	}
	
	public function delete_account($user_id, $email) {
		global $database;
		// Delete the user from the users table.
		$sql = ("DELETE FROM ".self::$table_name." WHERE user_id = '{$user_id}'");
		$database->query($sql);
		// Check to see if their is an activation link outstanding for the user.
		$sql = ("SELECT * FROM ".self::$activation_table." WHERE email = '{$email}'");
		$query = $database->query($sql);
		$rows = $database->num_rows($query);
		if($rows) {
			// If their is a database entry.
			$sql = ("DELETE FROM ".self::$activation_table." WHERE email = '{$email}'");
			$database->query($sql);
		}
		// Check to see if the user's account has any password reset links outstanding.
		$sql = ("SELECT * FROM ".self::$password_table." WHERE email = '{$email}'");
		$query = $database->query($sql);
		$rows = $database->num_rows($query);
		if($rows) {
			// If their is a database entry.
			$sql = ("DELETE FROM ".self::$password_table." WHERE email = '{$email}'");
			$database->query($sql);
		}
		// Delete any questions
		$sql = "DELETE FROM question WHERE user_id = '{$user_id}'";
		$database->query($sql);
		// Delete any answers
		$sql = "DELETE FROM answers WHERE user_id = '{$user_id}'";
		$database->query($sql);
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>User deleted successfully.</div>");
		redirect_to('users.php');
	}
	
	private static function deactivate_lock($user_id, $location) {
		global $database;
		$sql = "UPDATE ".self::$table_name." SET account_lock = '0' WHERE user_id = '{$user_id}'";
		$database->query($sql);
		self::delete_unlock_code($user_id, $location);
	}
	
	private static function delete_unlock_code($user_id, $location) {
		// Delete activation link
		global $database;
		$sql = "DELETE FROM ".self::$account_lock_table." WHERE user_id = '{$user_id}'";
		$database->query($sql);
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Account settings have been unlocked.</div>");
		redirect_to($location);
	}
	
	// public static function check_lock_status($user_id, $location) {
	// 	global $database;
	// 	//check if the account is locked.
	// 	$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = {$user_id} AND account_lock = '1'";
	// 	$query = $database->query($sql);
	// 	$rows = $database->num_rows($query);
	// 	if ($rows) {
	// 		// Check their is an unlock code associated with the account
	// 		$sql = "SELECT * FROM ".self::$account_lock_table." WHERE user_id = '{$user_id}'";
	// 		$query = $database->query($sql);
	// 		$rows = $database->num_rows($query);
	// 		if ($rows) {
	// 			// Ok you can now deactivate the account lock.
	// 			self::deactivate_lock($user_id, $location);
	// 		} else {
	// 			// No unlock code found.
	// 			$session = new Session();
	// 			$session->message("<div class='notification-box error-notification-box'><p>The unlock code <strong>{$code}</strong> does not match the one we have in our database for your account.</p><a href='#' class='notification-close error-notification-close'>x</a></div><!--.notification-box .notification-box-error end-->
	// 			");
	// 			redirect_to($location);
	// 		}
	// 	} else {
	// 		// Throw error back to the user.
	// 		$session = new Session();
	// 		$session->message("<div class='notification-box error-notification-box'><p>The account could not be unlocked.</p><a href='#' class='notification-close error-notification-close'>x</a></div><!--.notification-box .notification-box-error end-->
	// 		");
	// 		redirect_to($location);
	// 	}
	// }
	
	public static function send_new_password($email, $location) {
		global $database;
		
		$plain_password = Reset_Password::generate_password();
		$new_password = md5($plain_password);
		
		$sql = "UPDATE ".self::$table_name." SET password = '{$new_password}' WHERE email = '{$email}'";
		$database->query($sql);
		
		// Initialize functions.
		$email_class = new Email();
		
		// Email sent to the user.
		$from = SITE_EMAIL;
		$subject = "Your New Password";
		
		// Send and email to the user.
		$content = $email_class->email_template('new_password', "$plain_password", "", "$email", "");
		
		$email_class->send_email($email, $from, $subject, $content);
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>A new password has been emailed to {$email}.</div>");
		
		redirect_to($location);
	}
	
	public static function email_user($email, $subject, $message) {
		global $database;
		
		// Initialize functions.
		$email_class = new Email();
		
		// Email sent to the user.
		$from = SITE_EMAIL;
		
		// Send and email to the user.
		$content = $email_class->email_template('custom_email', "", "", "$email", "", $message);
		
		$email_class->send_email($email, $from, $subject, $content);
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Email successfully sent to {$email}</div>");
		redirect_to("users.php");
	}
	
	public static function count_all_users() {
		global $database;
		$sql = "SELECT COUNT(id) FROM ".self::$table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function count_users($var="", $var2="") {
		global $database;
		$sql = "SELECT COUNT($var) FROM ".self::$table_name." WHERE {$var} = '{$var2}'";
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function count_questions($type) {
		global $database;
		if($type == 0){
			$sql = "SELECT COUNT(*) FROM question WHERE answer_count = '0' ";
		} else {
			$sql = "SELECT COUNT(*) FROM question WHERE answer_count > '0' ";
		}
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	public static function count_all_users_in_group($user_level) {
		global $database;
		$sql = "SELECT COUNT(user_level) AS UserLevel FROM ".self::$table_name." WHERE user_level LIKE '%{$user_level}%'";
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}
	
	public static function count_all_groups() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$levels_table_name;
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function find_all_groups() {
		return self::find_by_sql("SELECT * FROM ".self::$levels_table_name);
  	}
	
	public function create_group($level_name, $auto, $redirect_page, $purchasable, $amount, $timed_access, $time_type, $access_time){
		global $database;
		$session = new Session();
		// Genetate the users ID.
		$level_id = generate_id();

		if ($auto == "YES") {
			$sql = "UPDATE user_levels SET auto = '0' WHERE auto = '1' ";
			$database->query($sql);
			$auto = "1";
		} else {
			$auto = "0";
		}
		
		$flag = false;
		//until flag is false
		while ($flag == false){
			//check if the user id exists
			$sql = "SELECT * FROM ".self::$levels_table_name." WHERE level_id = '{$level_id}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			if ($rows){
				$level_id = generate_id();
			}else{
				$flag = true;
			}
		}
		if ($flag == true){
			$database->query("INSERT INTO ".self::$levels_table_name." VALUES ('', '$level_id', '$level_name', '$auto', '$redirect_page', '$purchasable', '$amount', '$timed_access', '$time_type', '$access_time')");

			// Create the message that will be displayed on the login screen once the user has been redirected.
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Group has been created.</div>");
			
			// redirect the user to the login page.
			redirect_to('groups.php');
		}
	}
	
	public function update_group($group_id, $level_name, $signup, $redirect_page, $purchasable, $amount, $timed_access, $time_type, $access_time){
			global $database;
			
			if($signup == 1){
				$database->query("UPDATE user_levels SET auto = '0' WHERE auto = '1' ");
				// $database->query("UPDATE user_levels SET auto = '1' WHERE level_id = '{$level_id}' ");
			}
			
			$database->query("UPDATE user_levels SET level_name = '{$level_name}', auto = '{$signup}', redirect_page = '{$redirect_page}', purchasable = '{$purchasable}', amount = '{$amount}', timed_access = '{$timed_access}', time_type = '{$time_type}', access_time = '{$access_time}' WHERE level_id = '{$group_id}' ");
			
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Group successfully updated.</div>");
		
			redirect_to('group_settings.php?group_id='.$group_id.'');
	}
	
	public static function find_group_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$levels_table_name." WHERE level_id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
   }
	
	public function delete_group($level_id){
		global $database;
		
		$users = self::find_by_sql("SELECT * FROM users WHERE user_level LIKE '%{$level_id}%' ");
		$default_level = self::find_by_sql("SELECT * FROM user_levels WHERE auto = '1' LIMIT 1 ");
		
		if($users != ""){
			foreach($users as $user){
				$levels = explode(",", $user->user_level);
				if(($key = array_search($level_id, $levels)) !== false) {
				    unset($levels[$key]);
				}
				// Check and Delete Users "Levels" entry
				
				// End Check
				if(empty($levels)){
					$database->query("UPDATE users SET user_level = '{$default_level[0]->level_id}' WHERE user_id = '{$user->user_id}' ");
				} else {
					$database->query("UPDATE users SET user_level = '{$levels}' WHERE user_id = '{$user->user_id}' ");
				}
			}
		}

		$database->query("DELETE FROM user_levels WHERE level_id = '{$level_id}' ");
		
		$session = new Session();
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Group deleted successfully.</div>");
		redirect_to('groups.php');
		
	}
	
	public static function create_staff_note($user_id, $staff_username, $message) {
		global $database;
		$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
		
		$message = $database->escape_value($message);
		
		//insert into db the data
		$sql = "INSERT INTO ".self::$staff_notes_table." VALUES ('', '$user_id', '$staff_username', '$message', '$datetime')";
		$database->query($sql);
	}
	
	public static function delete_staff_note($confirm, $id, $user_id, $location) {
		global $database;
		if ($confirm == "yes") {
			$sql = "DELETE FROM ".self::$staff_notes_table." WHERE id = '{$id}' AND user_id = '{$user_id}'";
			$database->query($sql);
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Staff note has been deleted successfully.</div>");
		}
		redirect_to("$location");
	}
	
	public static function get_staff_notes($user_id=0) {
		return self::find_by_sql("SELECT * FROM staff_notes WHERE user_id= '{$user_id}'");
	}
	
	// public static function get_user_levels($user_id=0) {
	// 	return self::find_by_sql("SELECT * FROM levels WHERE user_id= '{$user_id}'");
	// }
	
	// Common
	
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}
  
  	public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE user_id={$id} LIMIT 1");
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
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}

} 	

?>