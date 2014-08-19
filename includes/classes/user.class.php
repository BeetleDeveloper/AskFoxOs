<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');


class User {
	
	protected static $table_name="users";
	protected static $levels_table_name="user_levels";
	protected static $invites_table_name="invites";
	protected static $db_fields = array('id', 'user_id', 'first_name', 'last_name', 'gender', 'username', 'password', 'email', 'user_level', 'primary_group', 'activated', 'suspended', 'date_created', 'last_login', 'signup_ip', 'last_ip', 'country', 'whitelist', 'ip_whitelist', 'questions_posted', 'questions_answered', 'staff', 'created', 'expires', 'expiry_date', 'level_id', 'level_name', 'auto', 'datetime', 'ip_address', 'name', 'qty', 'status', 'redirect_page', 'access_time', 'time_type', 'amount', 'created', 'timed_access', 'expiry_date', 'expires', 'package_name','message','profile_picture','type','task','oauth_provider','oauth_uid');
	
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
	public $oauth_provider;
	public $oauth_uid;
	
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
	
  	public function full_name() {
	    if(isset($this->first_name) && isset($this->last_name)) {
	      return $this->first_name . " " . $this->last_name;
	    } else {
	      return "";
	    }
  	}

	public static function authenticate($username="", $password="") {
    global $database;
    $username = $database->escape_value($username);
    $password = $database->escape_value(encrypt_password($password));

    $sql  = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND password = '{$password}' LIMIT 1";
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function check_user($table, $entry) {
	    global $database;
		 $table = $database->escape_value($table);
	    $entry = $database->escape_value($entry);

	    $sql  = "SELECT * FROM ".self::$table_name." WHERE {$table} = '{$entry}' LIMIT 1";
	    $result_array = self::find_by_sql($sql);
		 return !empty($result_array) ? true : false;
	}
	
	public static function check_activation($username) {
    global $database;
	$username = $database->escape_value($username);

    // $sql  = "SELECT '{$username}' FROM users WHERE {$table} = {$entry} LIMIT 1";
	$sql = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND activated = '1' LIMIT 1";
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_if_suspended($username) {
    global $database;
	$username = $database->escape_value($username);

    // $sql  = "SELECT '{$username}' FROM users WHERE {$table} = {$entry} LIMIT 1";
	$sql = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND suspended = '1' LIMIT 1";
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_current_password($username, $password) {
    global $database;
	$username = $database->escape_value($username);
	$password = $database->escape_value($password);

	// $sql = "SELECT * FROM users WHERE username = '{$username}' AND password = {$password}";
	$sql = "SELECT * FROM  ".self::$table_name." WHERE username = '{$username}' AND password = '{$password}' LIMIT 1";
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_whitelist($username) {
    global $database;
	$username = $database->escape_value($username);

    // $sql  = "SELECT '{$username}' FROM users WHERE {$table} = {$entry} LIMIT 1";
	$sql = "SELECT * FROM ".self::$table_name." WHERE username = '{$username}' AND whitelist = '1' LIMIT 1";
	
    $result_array = self::find_by_sql($sql);
		return !empty($result_array) ? true : false;
	}
	
	public static function check_login($username, $password, $current_ip, $remember) {
		// instantiate Session Class
		$session = new Session();
		
	    // Check database to see if username/password exist.
		$found_user = self::authenticate($username, $password);

		// lets see if the users account has been activated
	    $activated = self::check_activation($username);
		// lets see if the users account has been suspended
	    $suspended = self::check_if_suspended($username);

	  if ($found_user) {
	   	 if($activated){
		   	if(!$suspended){
				global $database;
				// echo "success";
				$session->login($found_user, $remember);
				$sql = "UPDATE ".self::$table_name." SET last_ip = '{$current_ip}' WHERE username = '{$username}' ";
				$database->query($sql);
				// redirect_to("index.php");
				// return "true";
				$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
				// return User::get_login_redirect($user->primary_group);
				return "index";
			 } else {
				$session->message("<div class='alert alert-error'>Su cuenta ha sido suspendida, por favor póngase en contacto con apoyo.</div>");
				return "false";
			 }
		 } else {
			$session->message("<div class='alert alert-info'>Su cuenta aún no se ha activado, por favor revise su correo electrónico. Para volver a enviar el código <a href='activate'>haga clic aquí.</a></div>");
			return "false";
		 }
	  } else {
	    // username/password combo was not found in the database
	    $session->message("<div class='alert alert-error'>Username/Password combinacion incorrecta.</div>");
		 return "false";
	  }
    }

	// public static function get_login_redirect($primary_group){
	// 	$data = self::find_by_sql("SELECT redirect_page FROM user_levels WHERE level_id = '{$primary_group}' LIMIT 1");
	// 	return $data[0]->redirect_page;
	// }
	
	public function create_account($username, $password, $email, $first_name, $last_name, $plain_password, $signup_ip, $country, $gender, $invite_code){
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
			if(VERIFY_EMAIL == "NO"){$activated = 1;} else if(VERIFY_EMAIL == "YES"){$activated = 0;}
			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$first_name', '$last_name', '$gender', '$username', '$password', '$email', '$activated', '0', '$datetime', '$datetime', '$signup_ip', '$signup_ip', '$country', '0', '', '','','')";
			$database->query($sql);
			
			if($gender == "Male"){
				$profile_picture = "male.jpg";
			} else {
				$profile_picture = "female.jpg";
			}
			
			$database->query("INSERT INTO profile VALUES ('', '$user_id', '0', '', '', '$profile_picture')");
						
			// Send and email to the user.
			if(VERIFY_EMAIL == "NO") {
				// Initialize functions.
				$email_class = new Email();

				// Email sent to the user if logged in.
				$from = SITE_EMAIL;
				$subject = "Welcome to ".SITE_NAME." ";
				
				$message = $email_class->email_template('registration_success', "$plain_password", "$username", "", "");
				$email_class->send_email($email, $from, $subject, $message);
			} else if(VERIFY_EMAIL == "YES") {
				//$activation_hash = Activation::set_activation_link($email)
				Activation::set_activation_link($plain_password, $username, $email);
			}

			// Create the message that will be displayed on the login screen once the user has been redirected.
			$session->message("<div class='alert alert-success'>Su cuenta ha sido creada con éxito. Por favor revise su correo electrónico para el enlace de activación.</div>");
			
			// redirect the user to the login page.
			redirect_to('login');
		}
	}
	
	public function create_oauth_account($username, $email, $first_name, $last_name, $gender, $oauth_provider, $oauth_uid){
		global $database;
		$session = new Session();
		// Genetate the users ID.
		$user_id = generate_id();
		
		$flag = false;
		while ($flag == false){
			$sql = "SELECT * FROM ".self::$table_name." WHERE user_id = '{$user_id}'";
			$query = $database->query($sql);
			$rows = $database->num_rows($query);
			if ($rows){
				$user_id = generate_id();
			}else{
				$flag = true;
			}
		}
		if ($flag == true){
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			$sql = "INSERT INTO ".self::$table_name." VALUES ('', '$user_id', '$first_name', '$last_name', '$gender', '$username', '$password', '$email', '1', '0', '$datetime', '$datetime', '$signup_ip', '$signup_ip', '$country', '0', '', '','{$oauth_provider}','{$oauth_uid}')";
			$database->query($sql);
			
			if($gender == "Male"){
				$profile_picture = "male.jpg";
			} else {
				$profile_picture = "female.jpg";
			}
			
			$database->query("INSERT INTO profile VALUES ('', '$user_id', '0', '', '', '$profile_picture')");
		}
	}
	
	public function update_account($value, $first_name, $last_name, $password, $email, $plain_password, $country, $gender){
			global $database;
			// Initialize functions.
			$email_class = new Email();
			$session = new Session();

			// Email sent to the user if logged in.
			$from = SITE_EMAIL;
			$subject = "Account Settings Changed";
			
			if(($whitelist == 1) && (empty($ip_whitelist))){
				$whitelist = 0;
			}
			
			if ($value == 1) {
				$sql = "UPDATE ".self::$table_name." SET password = '{$password}', email = '{$email}', first_name = '{$first_name}', last_name = '{$last_name}', gender = '{$gender}', country = '{$country}' WHERE user_id = '{$this->user_id}'";
				
				// HTML Message Content.
				$message = $email_class->email_template('update_all_settings', $plain_password, "");
				
			} else if ($value == 2) {
				$sql = "UPDATE ".self::$table_name." SET email = '{$email}', first_name = '{$first_name}', last_name = '{$last_name}', gender = '{$gender}', country = '{$country}' WHERE user_id = '{$this->user_id}' ";
				
				// HTML Message Content.
				$message = $email_class->email_template('update_settings', "", "");
				
			} 
			$database->query($sql);
			// $session = new Session();
			$session->message("<div class='alert alert-success'>Ajustes actualizado correctamente.</div>");
			
			// Finally send the email to the user.
			$email_class->send_email($email, $from, $subject, $message);
			
			redirect_to('settings');
	}
	
	public static function convert_time_type($type){
		if($type == 0){
			return "Days";
		} else if($type == 1){
			return "Weeks";
		} else if($type == 2){
			return "Months";
		}
	}
	
	public static function get_access_logs($user_id){
		return self::find_by_sql("SELECT * FROM access_logs WHERE user_id = '{$user_id}' ORDER BY datetime DESC ");
  	}
	
	public static function count_all_levels() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM user_levels";
    $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}
	
	public static function find_profile_message_owner($id=0) {
		global $database;
		// $id = 815788;
		$sql = "SELECT users.user_id, users.username, users.first_name, users.last_name, profile.user_id, profile.profile_picture FROM users, profile WHERE users.user_id = '{$id}'";
		return self::find_by_sql($sql);
		// return self::find_by_sql("SELECT user_id,first_name,last_name,username FROM users WHERE user_id = '{$id}' LIMIT 1 ");
    }

	public static function get_author($id) {
		$sql = "SELECT first_name,last_name,username,country FROM users WHERE user_id = '{$id}' LIMIT 1 ";
		return self::find_by_sql($sql);
    }
	
	public static function update_setting($name, $value){
		global $database;
		if($name == "password"){
			$value = encrypt_password($value);
		}
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		$database->query("UPDATE users SET $name = '{$value}' WHERE user_id = '{$user_id}' ");
		return "true";
	}
	
	public static function get_user_details($user_id) {
    $result_array = self::find_by_sql("SELECT user_id,username,first_name,last_name,country FROM ".self::$table_name." WHERE user_id = '{$user_id}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }
	
	public static function get_top_posters($max = 50){
		return self::find_by_sql("SELECT first_name,last_name,username,gender,country,questions_posted,questions_answered FROM users ORDER BY questions_answered DESC LIMIT $max ");
	}
	
	
	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  	}
  	
	public static function count_by_sql($sql) {
	  global $database;
	  // $sql = "SELECT COUNT(*) FROM user_levels";
     $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	public static function find_id_by_username($username) {
    $result_array = self::find_by_sql("SELECT user_id FROM ".self::$table_name." WHERE username = '{$username}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
   }

	public static function find_profile_data($username) {
    $result_array = self::find_by_sql("SELECT user_id,username,first_name,last_name,country FROM ".self::$table_name." WHERE username = '{$username}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }

  	public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE user_id = '{$id}'  LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }
  	
	public static function find_by_username($username) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE username = '{$username}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
    }
	
	public static function find_username_by_id($id) {
    $result_array = self::find_by_sql("SELECT username FROM ".self::$table_name." WHERE user_id = '{$id}' LIMIT 1");
		return !empty($result_array) ? $result_array[0]->username : false;
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