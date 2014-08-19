<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');



class Question {
	
	protected static $table_name="questions";
	protected static $db_fields = array('id', 'user_id', 'title', 'description', 'keywords', 'created', 'last_updated', 'status', 'view_count', 'viewed', 'name', 'category_id', 'rated', 'thumbs_up', 'thumbs_down', 'question_id', 'message', 'reason', 'datetime', 'type', 'reported_id','answer_count');
	
	// Table: question
	
	public $id;
	public $user_id;
	public $title;
	public $description;
	public $keywords;
	public $created;
	public $last_updated;
	public $status;
	public $view_count;
	public $viewed;
	public $category_id;
	public $rated;
	public $thumbs_up;
	public $thumbs_down;
	public $answer_count;
	
	// Table: categories
	
	// public $id;
	public $name;
	// public $status;
	
	// Table: answers
	
	// public $id;
	// public $user_id;
	public $question_id;
	// public $status;
	public $message;
	// public $thumbs_up;
	// public $thumbs_down;
	
	
	// Table: reported
	
	// public $id;
	// public $user_id;
	public $reason;
	public $datetime;
	public $type;
	public $reported_id;
	
	public static function get_categories(){
		return self::find_by_sql("SELECT * FROM categories WHERE status = '1' ");
	}
	
	public static function get_question_listing($type,$id="",$title="",$category_name=""){
		if($type == "id"){
			$data = self::find_by_sql("SELECT * FROM question WHERE id = '{$id}' LIMIT 1 ");
		} else {
			$category_id = self::get_category_id_from_name($category_name);
			$data = self::find_by_sql("SELECT * FROM question WHERE title = '{$title}' AND category_id = '{$category_id}' LIMIT 1 ");
		}
		if($data != ""){
			$ip = $_SERVER['REMOTE_ADDR'];
			if(!in_array($ip, explode(",", $data[0]->viewed))){
				global $database;
				$new_count = $data[0]->view_count + 1;
				if($data[0]->viewed != ""){
					$viewed_array = explode(",", $data[0]->viewed);
					if(!in_array($ip, $viewed_array)){
						$new_viewed = implode(",", array_push($viewed_array, $ip) );
					}
				} else {
					$new_viewed = $ip;
				}				
				$database->query("UPDATE question SET view_count = '{$new_count}', viewed = '{$new_viewed}' WHERE id = '{$id}' ");
			}
			return $data;
		}
	}
	
	public static function get_started_questions($user_id){
		return self::find_by_sql("SELECT id,user_id,title,created,view_count,thumbs_up,thumbs_down,answer_count FROM question WHERE user_id = '{$user_id}' ");
	}
	
	public static function get_questions_answered($user_id){
		return self::find_by_sql("SELECT id,user_id,question_id,thumbs_up,thumbs_down,created FROM answers WHERE user_id = '{$user_id}' ");
	}
	
	public static function get_question_title($question_id){
		return self::find_by_sql("SELECT title,id FROM question WHERE id = '{$question_id}' ");
	}
	
	public static function get_category_name($id){
		$data = self::find_by_sql("SELECT name FROM categories WHERE id = '{$id}' LIMIT 1 ");
		return !empty($data) ? $data[0]->name : "unclassified";
	}
	
	public static function get_answers($id){
		return self::find_by_sql("SELECT * FROM answers WHERE question_id = '{$id}' ORDER BY thumbs_up DESC ");
	}
	
	public static function edit_question($id,$message,$admin=""){
		if($admin == "confirmed"){
			$flag = true;
		} else {
			$user_id = $_SESSION['masdyn']['answers']['user_id'];
			$data = self::find_by_sql("SELECT * FROM question WHERE id = '{$id}' LIMIT 1 ");
			if($user_id == $data[0]->user_id){
				$flag = true;
			}
		}
		if($flag == true){
			global $database;
			$session = new Session();
			$datetime = strftime("%Y-%m-%d %T", time());
			$database->query("UPDATE question SET description = '{$message}', last_updated = '{$datetime}' WHERE id = '{$id}' ");
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Pregunta Actualiada con Exito!.</div>");
			return "success";
		} else {
			return "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Algo ha salido mal, por favor, actualice e inténtelo de nuevo.</div>";
		}
	}
	
	public static function rate_answer($id,$type){
		$answer = self::find_by_sql("SELECT * FROM answers WHERE id = '{$id}' LIMIT 1 ");
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		if($answer[0]->rated == ""){
			$voters = array();
		} else {
			$voters = explode(',', $answer[0]->rated);
		}
		if(in_array($user_id, $voters)){
			return "failure";
		} else {
			array_push($voters, $user_id);
			$new_rated = implode(",", $voters);
			if($type == "up"){
				$new = $answer[0]->thumbs_up + 1;
				$extra = ", thumbs_up = '{$new}' ";
			} else if($type == "down"){
				$new = $answer[0]->thumbs_down + 1;
				$extra = ", thumbs_down = '{$new}' ";
			}
			global $database;
			$database->query("UPDATE answers SET rated = '{$new_rated}' $extra WHERE id = '{$id}' ");	
			return $new;
		}
	}
	
	public static function rate_question($id,$type){
		$question = self::find_by_sql("SELECT * FROM question WHERE id = '{$id}' LIMIT 1 ");
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		if($question[0]->rated == ""){
			$voters = array();
		} else {
			$voters = explode(',', $question[0]->rated);
		}
		if(in_array($user_id, $voters)){
			return "failure";
		} else {
			array_push($voters, $user_id);
			$new_rated = implode(",", $voters);
			if($type == "up"){
				$new = $question[0]->thumbs_up + 1;
				$extra = ", thumbs_up = '{$new}' ";
			} else if($type == "down"){
				$new = $question[0]->thumbs_down + 1;
				$extra = ", thumbs_down = '{$new}' ";
			}
			global $database;
			$database->query("UPDATE question SET rated = '{$new_rated}' $extra WHERE id = '{$id}' ");	
			return $new;
		}
	}
	
	public static function get_latest($id,$limit=6){
		if($id == "-"){
			return self::find_by_sql("SELECT id,title,created,thumbs_up,thumbs_down,answer_count FROM question WHERE id != '{$id}' ORDER BY created DESC LIMIT $limit ");
		} else {
			return self::find_by_sql("SELECT id,title,created,thumbs_up,thumbs_down,answer_count FROM question ORDER BY created DESC LIMIT $limit ");
		}
	}
	
	public static function get_top($limit=6){
		return self::find_by_sql("SELECT id,title,created,thumbs_up,thumbs_down,answer_count FROM question ORDER BY thumbs_up DESC LIMIT $limit ");
	}
	
	public static function add_answer($id,$user_id,$answer){
		global $database;
		$datetime = strftime("%Y-%m-%d %T", time());
		if($database->query("INSERT INTO answers VALUES('','{$user_id}','{$id}','1','{$answer}','0','0','0','{$datetime}') ")){
			$data = self::find_by_sql("SELECT answer_count FROM question WHERE id = '{$id}' LIMIT 1 ");
			$new_count = $data[0]->answer_count + 1;
			$database->query("UPDATE question SET answer_count = '{$new_count}' WHERE id = '{$id}' ");
			
			$user = User::find_by_id($user_id);
			$new_qa = $user->questions_answered + 1;
			$database->query("UPDATE users SET questions_answered = '{$new_qa}' WHERE user_id = '{$user_id}' ");
			
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Su respuesta ha sido añadida.</div>");
			return "success";
		} else {
			return "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Algo ha salido mal, por favor, actualice e inténtelo de nuevo.</div>";
		}
	}
	
	public static function get_answer($id){
		$data = self::find_by_sql("SELECT message FROM answers WHERE id = '{$id}' LIMIT 1 ");
		return $data[0]->message;
	}
	
	public static function update_answer($id,$message,$admin=""){
		global $database;
		$answer = self::find_by_sql("SELECT user_id FROM answers WHERE id = '{$id}' LIMIT 1 ");
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		if($admin == "confirmed"){
			$flag = true;
		} else {
			if($answer[0]->user_id == $user_id){
				$flag = true;
			} else {
				$flag = true;
			}
		}		
		if($flag == true){
			$database->query("UPDATE answers SET message = '{$message}' WHERE id = '{$id}' ");
			$session = new Session();
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Su respuesta a sido actualizada Correctamente!.</div>");
			return "success";
		} else {
			return "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Algo ha salido mal, por favor, actualice e inténtelo de nuevo.</div>";
		}
	}
	
	public static function submit_report($id,$reason,$type){
		global $database;
		$datetime = strftime("%Y-%m-%d %T", time());
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		$data = self::find_by_sql("SELECT * FROM reported WHERE reported_id = '{$id}' AND type = '{$type}' ");
		if($type == 0){
			$type_name = "question";
		} else {
			$type_name = "answer";
		}
		if(empty($data)){
			if($database->query("INSERT INTO reported VALUES('','{$user_id}','{$reason}','{$datetime}','{$type}','{$id}') ")){
				$session = new Session();
				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Gracias por informar de este $type_name. Si es necesario, nos pondremos en contacto.</div>");
				return "success";
			} else {
				return '<div class="alert alert-error"><button type="button" class="close" class="btn btn-link" data-dismiss="alert">×</button><img src="'.WWW.'img/icons/alerts/error.png" width="31" height="31" alt="Error">Algo ha salido mal, por favor, actualice e inténtelo de nuevo.</div>';
			}
		} else {
			return '<div class="alert alert-error"><button type="button" class="close" class="btn btn-link" data-dismiss="alert">×</button><img src="'.WWW.'img/icons/alerts/error.png" width="31" height="31" alt="Error">Este mensaje ya ha sido reportado.</div>';
		}
	}
	
	public static function ask_question($title,$category,$question){
		global $database;
		$datetime = strftime("%Y-%m-%d %T", time());
		$user_id = $_SESSION['masdyn']['answers']['user_id'];
		$data = self::find_by_sql("SELECT * FROM question WHERE title = '{$title}' AND category_id = '{$category}' ");
		$session = new Session();
		if(empty($data)){
			if($database->query("INSERT INTO question VALUES('','{$user_id}','{$title}','{$question}','{$datetime}','{$datetime}','0','','{$category}','0','0','0','0') ")){
				$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Su pregunta ha sido creada con éxito.</div>");
				$user = User::find_by_id($user_id);
				$new_qp = $user->questions_posted + 1;
				$database->query("UPDATE users SET questions_posted = '{$new_qp}' WHERE user_id = '{$user_id}' ");
				$data = self::find_by_sql("SELECT id FROM question WHERE title = '{$title}' LIMIT 1 ");
				return WWW."question?id=".$data[0]->id;
			} else {
				$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Algo ha salido mal, por favor, actualice e inténtelo de nuevo.</div>");
				return "failure";
			}
		} else {
			$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Lo sentimos, pero ya hay una pregunta con este título en la categoría que ha seleccionado.</div>");
			return "failure";
		}
	}
	
	public static function delete($type,$id){
		global $database;
		$session = new Session();
		
		if($type == "question"){
			$database->query("DELETE FROM question WHERE id = '{$id}' ");
			$database->query("DELETE FROM answers WHERE question_id = '{$id}' ");
			$database->query("DELETE FROM reported WHERE reported_id = '{$id}' ");
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>La pregunta ha sido eliminado con éxito.</div>");
		} else {
			$database->query("DELETE FROM answers WHERE id = '{$id}' ");
			$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>La respuesta ha sido eliminado con éxito.</div>");
		}
		return "success";
	}
	
	public static function delete_report($id){
		global $database;
		$session = new Session();
		
		$database->query("DELETE FROM reported WHERE id = '{$id}' ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>El informe se ha eliminado correctamente.</div>");
	}
	
	public static function delete_category($id){
		global $database;
		$session = new Session();
		
		$database->query("DELETE FROM categories WHERE id = '{$id}' ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>La categoría se ha eliminado correctamente.</div>");
	}
	
	public static function modify_category($id,$name,$status){
		global $database;
		$session = new Session();
		$database->query("UPDATE categories SET name = '{$name}', status = '{$status}' WHERE id = '{$id}' ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>La categoría se ha actualizado correctamente.</div>");
	}
	
	public static function create_category($name,$status){
		global $database;
		$session = new Session();
		$database->query("INSERT INTO categories VALUES('','{$name}','{$status}') ");
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>La categoría ha sido creado con éxito.</div>");
		return "success";
	}
	
	public static function count_all_reported() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM reported";
      $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
     return array_shift($row);
	}
	
	public static function count_all_categories() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM categories";
      $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
     return array_shift($row);
	}
	
	public static function get_total_rank($top_posters){
		$counts = array();
		foreach($top_posters as $key=>$subarr){
			if(isset($counts[$subarr->questions_answered])){
				$counts[$subarr->questions_answered]++;
			} else {
				$counts[$subarr->questions_answered] = 1;
				$counts[$subarr->questions_answered] = isset($counts[$subarr->questions_answered]) ? $counts[$subarr->questions_answered]++ : 1;
			}
		}
		return $counts;
	}
	
	public static function get_badge($count){
		if($count < 50){
			return '<a href="#" rel="tooltip" title="0-50 Respuestas"><img src="'.WWW.'assets/img/icons/medals/bronze-1.png" width="20" height="20" alt="0-50 Respuestas"></a>';
		} else if($count < 100){
			return '<a href="#" rel="tooltip" title="51-100 Respuestas"><img src="'.WWW.'assets/img/icons/medals/bronze-1.png" width="20" height="20" alt="51-100 Respuestas"></a>';
		} else if($count < 500){
			return '<a href="#" rel="tooltip" title="101-500 Respuestas"><img src="'.WWW.'assets/img/icons/medals/bronze-1.png" width="20" height="20" alt="101-500 Respuestas"></a>';
		} else if($count < 1000){
			return '<a href="#" rel="tooltip" title="501-1000 Respuestas"><img src="'.WWW.'assets/img/icons/medals/bronze-1.png" width="20" height="20" alt="501-1000 Respuestas"></a>';
		} else if($count < 5000){
			return '<a href="#" rel="tooltip" title="1001-5000 Respuestas"><img src="'.WWW.'assets/img/icons/medals/silver-2.png" width="20" height="20" alt="1001-5000 Respuestas"></a>';
		} else if($count < 10000){
			return '<a href="#" rel="tooltip" title="5001-10000 Respuestas"><img src="'.WWW.'assets/img/icons/medals/silver-3.png" width="20" height="20" alt="5001-10000 Respuestas"></a>';
		} else if($count < 10000){
			return '<a href="#" rel="tooltip" title="10001-20000 RespuestasRespuestas"><img src="'.WWW.'assets/img/icons/medals/gold-1.png" width="20" height="20" alt="10001-20000 Respuestas"></a>';
		} else if($count < 20000){
			return '<a href="#" rel="tooltip" title="20001-40000 Respuestas"><img src="'.WWW.'assets/img/icons/medals/gold-2.png" width="20" height="20" alt="20001-40000 Respuestas"></a>';
		} else if($count < 40000){
			return '<a href="#" rel="tooltip" title="40001+ Respuestas"><img src="'.WWW.'assets/img/icons/medals/gold-3.png" width="20" height="20" alt="40001+ Respuestas"></a>';
		}
	}
	
	public static function get_category_id_from_name($name){
		$data = self::find_by_sql("SELECT * FROM categories WHERE seo_name = '{$name}' LIMIT 1 ");
		return $data[0]->id;
	}
	
	// Common Database Methods
	
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