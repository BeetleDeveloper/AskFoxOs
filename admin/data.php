<?php


require_once("../includes/inc_files.php"); 

if(isset($_SESSION['masdyn']['answers']['user_id'])){
	$user_id = $_SESSION['masdyn']['answers']['user_id'];
} else {
	$user_id = "";
}

if(isset($_POST['page'])){
	// $page = $database->escape_value($_POST['page']);
	$page = clean_value($_POST['page']);
	if($page == "login"){
		if(isset($_POST['action'])){
			if($_POST['action'] == "login"){
				if(isset($_POST['username']) && isset($_POST['password'])){
					$username = $database->escape_value($_POST['username']);
					$password = $database->escape_value($_POST['password']);
					$remember_me = $database->escape_value($_POST['remember_me']);
					$current_ip = $_SERVER['REMOTE_ADDR'];
					$return = User::check_login($username, $password, $current_ip, $remember_me);
					if($return == "false"){
						echo "false";
					} else {
						echo $return;
					}
				} else {
					echo "false";
				}
			} else if($_POST['action'] == "update_msg"){
				echo output_message($message);
			}
		}
	} else if($page == "profile"){
		if(isset($_POST['profile']) && isset($_POST['message'])){
			$profile_id = clean_value($_POST['profile']);
			$message = $database->escape_value($_POST['message']);
			// $message = $_POST['message'];
			$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
			$datetime = strftime("%Y-%m-%d %H:%M:%S", time());
			Profile::create_profile_message($profile_id, $user->user_id, $message, $datetime);
			$type = "unread";
			echo Profile::display_profile_messages($type, $profile_id);
		} else if(isset($_POST['profile']) && isset($_POST['get']) && isset($_POST['limit'])){
			echo Profile::display_profile_messages($_POST['get'], $_POST['profile'], $_POST['limit']);
		} else if(isset($_POST['get_message'])){
			$id = clean_value($_POST['get_message']);
			echo Profile::get_profile_message_data($id);
		} else if(isset($_POST['confirm_edit']) && isset($_POST['message'])){
			$id = clean_value($_POST['confirm_edit']);
			$message = $database->escape_value($_POST['message']);
			Profile::update_message($id,$message);
		} else if(isset($_POST['delete_message'])){
			$id = clean_value($_POST['id']);
			if(Profile::check_message_owner($id)){
				echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>The message has been deleted.</div>";
			} else {
				$session->message("<div class='alert alert-failure'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but something has gone wrong. Please try again.</div>");
				echo "failure";
			}
		} else if(isset($_POST['update_profile'])){
			$id = clean_value($_POST['update_profile']);
			$profile_message = $database->escape_value($_POST['profile_message']);
			$about_me = clean_value($_POST['about_me']);
			echo Profile::update_profile($id,$profile_message,$about_me);
		}
	} else if($page == "settings"){
		if(isset($_POST['name'])){
			$name = $_POST['name'];
			$value = $_POST['value'];
			$allowed = array('first_name','last_name','gender','username','password', 'email','country');
			if(in_array($name, $allowed)){
				if($name == "username" || $name == "email"){
					$user_id = $_SESSION['masdyn']['answers']['user_id'];
					$data = User::find_by_sql("SELECT ".$name." FROM users WHERE ".$name." = '{$value}' LIMIT 1 ");
					if(!empty($data)){
						$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, that ".$name." has already been taken. Please choose another.</div>");
						echo "failure";
						$flag = false;
					} else {
						$flag = true;
					}
				} else {
					$flag = true;
				}
				if($flag == true){
					User::update_setting($name,$value);
					$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your ".$name." has successfully updated.</div>");
					echo "success";
				}
			}
		} if(isset($_POST['password'])){
			$password = $_POST['password'];
			User::update_setting("password",$password);
		} else if(isset($_POST['get_select'])){
			$select = clean_value($_POST['get_select']);
			if($select == "gender"){
				$gender = clean_value($_POST['gender']);
				echo '<select name="gender" id="gender" class="span12 chzn-select" required="required" value="<?php echo $gender ?>"><option value="Male" ';
					if($gender == 'Male') { echo 'selected="selected"';} else { echo ''; }
				echo '>Male</option><option value="Female"';
					if($gender == 'Female') { echo 'selected="selected"';} else { echo ''; }
				echo '>Female</option></select>';
			} else if($select == "country"){
				$country = clean_value($_POST['country']);
				echo '<select name="country" id="country" data-placeholder="Choose a Country..." class="span12 chzn-select" tabindex="2" value="<?php echo $country ?>">
					<option value="'.$country.'" selected="selected">'.$country.'</option>
					<option value="Afghanistan">Afghanistan</option> 
					<option value="Albania">Albania</option> 
					<option value="Algeria">Algeria</option> 
					<option value="American Samoa">American Samoa</option> 
					<option value="Andorra">Andorra</option> 
					<option value="Angola">Angola</option> 
					<option value="Anguilla">Anguilla</option> 
					<option value="Antarctica">Antarctica</option> 
					<option value="Antigua and Barbuda">Antigua and Barbuda</option> 
					<option value="Argentina">Argentina</option> 
					<option value="Armenia">Armenia</option> 
					<option value="Aruba">Aruba</option> 
					<option value="Australia">Australia</option> 
					<option value="Austria">Austria</option> 
					<option value="Azerbaijan">Azerbaijan</option> 
					<option value="Bahamas">Bahamas</option> 
					<option value="Bahrain">Bahrain</option> 
					<option value="Bangladesh">Bangladesh</option> 
					<option value="Barbados">Barbados</option> 
					<option value="Belarus">Belarus</option> 
					<option value="Belgium">Belgium</option> 
					<option value="Belize">Belize</option> 
					<option value="Benin">Benin</option> 
					<option value="Bermuda">Bermuda</option> 
					<option value="Bhutan">Bhutan</option> 
					<option value="Bolivia">Bolivia</option> 
					<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
					<option value="Botswana">Botswana</option> 
					<option value="Bouvet Island">Bouvet Island</option> 
					<option value="Brazil">Brazil</option> 
					<option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
					<option value="Brunei Darussalam">Brunei Darussalam</option> 
					<option value="Bulgaria">Bulgaria</option> 
					<option value="Burkina Faso">Burkina Faso</option> 
					<option value="Burundi">Burundi</option> 
					<option value="Cambodia">Cambodia</option> 
					<option value="Cameroon">Cameroon</option> 
					<option value="Canada">Canada</option> 
					<option value="Cape Verde">Cape Verde</option> 
					<option value="Cayman Islands">Cayman Islands</option> 
					<option value="Central African Republic">Central African Republic</option> 
					<option value="Chad">Chad</option> 
					<option value="Chile">Chile</option> 
					<option value="China">China</option> 
					<option value="Christmas Island">Christmas Island</option> 
					<option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
					<option value="Colombia">Colombia</option> 
					<option value="Comoros">Comoros</option> 
					<option value="Congo">Congo</option> 
					<option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
					<option value="Cook Islands">Cook Islands</option> 
					<option value="Costa Rica">Costa Rica</option> 
					<option value="Cote D\'ivoire">Cote D\'ivoire</option> 
					<option value="Croatia">Croatia</option> 
					<option value="Cuba">Cuba</option> 
					<option value="Cyprus">Cyprus</option> 
					<option value="Czech Republic">Czech Republic</option> 
					<option value="Denmark">Denmark</option> 
					<option value="Djibouti">Djibouti</option> 
					<option value="Dominica">Dominica</option> 
					<option value="Dominican Republic">Dominican Republic</option> 
					<option value="Ecuador">Ecuador</option> 
					<option value="Egypt">Egypt</option> 
					<option value="El Salvador">El Salvador</option> 
					<option value="Equatorial Guinea">Equatorial Guinea</option> 
					<option value="Eritrea">Eritrea</option> 
					<option value="Estonia">Estonia</option> 
					<option value="Ethiopia">Ethiopia</option> 
					<option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
					<option value="Faroe Islands">Faroe Islands</option> 
					<option value="Fiji">Fiji</option> 
					<option value="Finland">Finland</option> 
					<option value="France">France</option> 
					<option value="French Guiana">French Guiana</option> 
					<option value="French Polynesia">French Polynesia</option> 
					<option value="French Southern Territories">French Southern Territories</option> 
					<option value="Gabon">Gabon</option> 
					<option value="Gambia">Gambia</option> 
					<option value="Georgia">Georgia</option> 
					<option value="Germany">Germany</option> 
					<option value="Ghana">Ghana</option> 
					<option value="Gibraltar">Gibraltar</option> 
					<option value="Greece">Greece</option> 
					<option value="Greenland">Greenland</option> 
					<option value="Grenada">Grenada</option> 
					<option value="Guadeloupe">Guadeloupe</option> 
					<option value="Guam">Guam</option> 
					<option value="Guatemala">Guatemala</option> 
					<option value="Guinea">Guinea</option> 
					<option value="Guinea-bissau">Guinea-bissau</option> 
					<option value="Guyana">Guyana</option> 
					<option value="Haiti">Haiti</option> 
					<option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
					<option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
					<option value="Honduras">Honduras</option> 
					<option value="Hong Kong">Hong Kong</option> 
					<option value="Hungary">Hungary</option> 
					<option value="Iceland">Iceland</option> 
					<option value="India">India</option> 
					<option value="Indonesia">Indonesia</option> 
					<option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
					<option value="Iraq">Iraq</option> 
					<option value="Ireland">Ireland</option> 
					<option value="Israel">Israel</option> 
					<option value="Italy">Italy</option> 
					<option value="Jamaica">Jamaica</option> 
					<option value="Japan">Japan</option> 
					<option value="Jordan">Jordan</option> 
					<option value="Kazakhstan">Kazakhstan</option> 
					<option value="Kenya">Kenya</option> 
					<option value="Kiribati">Kiribati</option> 
					<option value="Korea, Democratic People\'s Republic of">Korea, Democratic People\'s Republic of</option> 
					<option value="Korea, Republic of">Korea, Republic of</option> 
					<option value="Kuwait">Kuwait</option> 
					<option value="Kyrgyzstan">Kyrgyzstan</option> 
					<option value="Lao People\'s Democratic Republic">Lao People\'s Democratic Republic</option> 
					<option value="Latvia">Latvia</option> 
					<option value="Lebanon">Lebanon</option> 
					<option value="Lesotho">Lesotho</option> 
					<option value="Liberia">Liberia</option> 
					<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
					<option value="Liechtenstein">Liechtenstein</option> 
					<option value="Lithuania">Lithuania</option> 
					<option value="Luxembourg">Luxembourg</option> 
					<option value="Macao">Macao</option> 
					<option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
					<option value="Madagascar">Madagascar</option> 
					<option value="Malawi">Malawi</option> 
					<option value="Malaysia">Malaysia</option> 
					<option value="Maldives">Maldives</option> 
					<option value="Mali">Mali</option> 
					<option value="Malta">Malta</option> 
					<option value="Marshall Islands">Marshall Islands</option> 
					<option value="Martinique">Martinique</option> 
					<option value="Mauritania">Mauritania</option> 
					<option value="Mauritius">Mauritius</option> 
					<option value="Mayotte">Mayotte</option> 
					<option value="Mexico">Mexico</option> 
					<option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
					<option value="Moldova, Republic of">Moldova, Republic of</option> 
					<option value="Monaco">Monaco</option> 
					<option value="Mongolia">Mongolia</option> 
					<option value="Montserrat">Montserrat</option> 
					<option value="Morocco">Morocco</option> 
					<option value="Mozambique">Mozambique</option> 
					<option value="Myanmar">Myanmar</option> 
					<option value="Namibia">Namibia</option> 
					<option value="Nauru">Nauru</option> 
					<option value="Nepal">Nepal</option> 
					<option value="Netherlands">Netherlands</option> 
					<option value="Netherlands Antilles">Netherlands Antilles</option> 
					<option value="New Caledonia">New Caledonia</option> 
					<option value="New Zealand">New Zealand</option> 
					<option value="Nicaragua">Nicaragua</option> 
					<option value="Niger">Niger</option> 
					<option value="Nigeria">Nigeria</option> 
					<option value="Niue">Niue</option> 
					<option value="Norfolk Island">Norfolk Island</option> 
					<option value="Northern Mariana Islands">Northern Mariana Islands</option> 
					<option value="Norway">Norway</option> 
					<option value="Oman">Oman</option> 
					<option value="Pakistan">Pakistan</option> 
					<option value="Palau">Palau</option> 
					<option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
					<option value="Panama">Panama</option> 
					<option value="Papua New Guinea">Papua New Guinea</option> 
					<option value="Paraguay">Paraguay</option> 
					<option value="Peru">Peru</option> 
					<option value="Philippines">Philippines</option> 
					<option value="Pitcairn">Pitcairn</option> 
					<option value="Poland">Poland</option> 
					<option value="Portugal">Portugal</option> 
					<option value="Puerto Rico">Puerto Rico</option> 
					<option value="Qatar">Qatar</option> 
					<option value="Reunion">Reunion</option> 
					<option value="Romania">Romania</option> 
					<option value="Russian Federation">Russian Federation</option> 
					<option value="Rwanda">Rwanda</option> 
					<option value="Saint Helena">Saint Helena</option> 
					<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
					<option value="Saint Lucia">Saint Lucia</option> 
					<option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
					<option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
					<option value="Samoa">Samoa</option> 
					<option value="San Marino">San Marino</option> 
					<option value="Sao Tome and Principe">Sao Tome and Principe</option> 
					<option value="Saudi Arabia">Saudi Arabia</option> 
					<option value="Senegal">Senegal</option> 
					<option value="Serbia and Montenegro">Serbia and Montenegro</option> 
					<option value="Seychelles">Seychelles</option> 
					<option value="Sierra Leone">Sierra Leone</option> 
					<option value="Singapore">Singapore</option> 
					<option value="Slovakia">Slovakia</option> 
					<option value="Slovenia">Slovenia</option> 
					<option value="Solomon Islands">Solomon Islands</option> 
					<option value="Somalia">Somalia</option> 
					<option value="South Africa">South Africa</option> 
					<option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
					<option value="Spain">Spain</option> 
					<option value="Sri Lanka">Sri Lanka</option> 
					<option value="Sudan">Sudan</option> 
					<option value="Suriname">Suriname</option> 
					<option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
					<option value="Swaziland">Swaziland</option> 
					<option value="Sweden">Sweden</option> 
					<option value="Switzerland">Switzerland</option> 
					<option value="Syrian Arab Republic">Syrian Arab Republic</option> 
					<option value="Taiwan, Province of China">Taiwan, Province of China</option> 
					<option value="Tajikistan">Tajikistan</option> 
					<option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
					<option value="Thailand">Thailand</option> 
					<option value="Timor-leste">Timor-leste</option> 
					<option value="Togo">Togo</option> 
					<option value="Tokelau">Tokelau</option> 
					<option value="Tonga">Tonga</option> 
					<option value="Trinidad and Tobago">Trinidad and Tobago</option> 
					<option value="Tunisia">Tunisia</option> 
					<option value="Turkey">Turkey</option> 
					<option value="Turkmenistan">Turkmenistan</option> 
					<option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
					<option value="Tuvalu">Tuvalu</option> 
					<option value="Uganda">Uganda</option> 
					<option value="Ukraine">Ukraine</option> 
					<option value="United Arab Emirates">United Arab Emirates</option> 
					<option value="United Kingdom">United Kingdom</option> 
					<option value="United States">United States</option> 
					<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
					<option value="Uruguay">Uruguay</option> 
					<option value="Uzbekistan">Uzbekistan</option> 
					<option value="Vanuatu">Vanuatu</option> 
					<option value="Venezuela">Venezuela</option> 
					<option value="Viet Nam">Viet Nam</option> 
					<option value="Virgin Islands, British">Virgin Islands, British</option> 
					<option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
					<option value="Wallis and Futuna">Wallis and Futuna</option> 
					<option value="Western Sahara">Western Sahara</option> 
					<option value="Yemen">Yemen</option> 
					<option value="Zambia">Zambia</option> 
					<option value="Zimbabwe">Zimbabwe</option>
				</select>';
			}
		}
	} else if($page == "profile_picture"){
		 if(isset($_POST['delete_image'])){
			$image_name = clean_value($_POST['name']);
			if(Profile::delete_image($image_name)){
				echo "success";
			} else {
				echo "<div class='alert alert-failure'><button type='button' class='close' data-dismiss='alert'>×</button>You must set another image as your thumbnail before you can delete this image.</div>";
			}
		} else if(isset($_POST['set_thumbnail'])){
			$image_name = clean_value($_POST['name']);
			if(Profile::set_thumbnail($image_name)){
				echo "success";
			} else {
				echo "<div class='alert alert-failure'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but something has gone wrong, please refresh and try again.</div>";
			}
		}
	} else if($page == "project_theme"){
		if(isset($_POST['delete_theme'])){
			$id = clean_value($_POST['delete_theme']);
			$image_name = clean_value($_POST['name']);
			if(Investments::delete_custom_theme($id,$image_name)){
				echo "success";
			} else {
				echo "<div class='alert alert-failure'><button type='button' class='close' data-dismiss='alert'>×</button>You must set another theme before you can delete this one.</div>";
			}
		} else if(isset($_POST['set_theme'])){
			$id = clean_value($_POST['set_theme']);
			$image_name = clean_value($_POST['name']);
			if(Investments::set_custom_theme($id,$image_name)){
				echo "success";
			} else {
				echo "<div class='alert alert-failure'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but something has gone wrong, please refresh and try again.</div>";
			}
		}
	} else if($page == "question"){
		if(isset($_POST['edit_question'])){
			$id = clean_value($_POST['edit_question']);
			$message = $database->escape_value($_POST['message']);
			echo Question::edit_question($id,$message,"confirmed");
		} else if(isset($_POST['rate_answer'])){
			$id = clean_value($_POST['rate_answer']);
			$type = clean_value($_POST['type']);
			echo Question::rate_answer($id,$type);
		} else if(isset($_POST['rate_question'])){
			$id = clean_value($_POST['rate_question']);
			$type = clean_value($_POST['type']);
			echo Question::rate_question($id,$type);
		} else if(isset($_POST['add_answer'])){
			$id = clean_value($_POST['add_answer']);
			$answer = $database->escape_value($_POST['answer']);
			$user_id = $_SESSION['masdyn']['answers']['user_id'];
			echo Question::add_answer($id, $user_id, $answer);
		} else if(isset($_POST['get_answer'])){
			$id = clean_value($_POST['get_answer']);
			echo Question::get_answer($id);
		} else if(isset($_POST['confirm_edit']) && isset($_POST['message'])){
			$id = clean_value($_POST['confirm_edit']);
			$message = $database->escape_value($_POST['message']);
			echo Question::update_answer($id,$message,"confirmed");
		} else if(isset($_POST['report_answer'])){
			$id = clean_value($_POST['report_answer']);
			$reason = $database->escape_value($_POST['reason']);
			echo Question::submit_report($id,$reason,1);
		} else if(isset($_POST['report_question'])){
			$id = clean_value($_POST['report_question']);
			$reason = $database->escape_value($_POST['reason']);
			echo Question::submit_report($id,$reason,0);
		} else if(isset($_POST['delete'])){
			$type = clean_value($_POST['delete']);
			$id = clean_value($_POST['id']);
			echo Question::delete($type,$id);
		}
	} else if($page == "create_question"){
		if(isset($_POST['title'])){
			$title = clean_value($_POST['title']);
			$category = clean_value($_POST['category']);
			$question = $database->escape_value($_POST['question']);
			echo Question::ask_question($title,$category,$question);
		}
	} else if($page == "misc"){
		if($_POST['action'] == "update_msg"){
			echo output_message($message);
		}
	} else if($page == "report"){
		 if(isset($_POST['delete'])){
			$id = clean_value($_POST['delete']);
			echo Question::delete_report($id);
		}
	} else if($page == "category"){
		 if(isset($_POST['delete'])){
			$id = clean_value($_POST['delete']);
			echo Question::delete_category($id);
		} else if(isset($_POST['modify'])){
			$id = clean_value($_POST['modify']);
			$name = clean_value($_POST['name']);
			$status = clean_value($_POST['status']);
			if($status == "Hidden"){
				$status = 0;
			} else {
				$status = 1;
			}
			echo Question::modify_category($id,$name,$status);
		} else if(isset($_POST['create'])){
			$name = clean_value($_POST['name']);
			$status = clean_value($_POST['status']);
			if($status == "Hidden"){
				$status = 0;
			} else {
				$status = 1;
			}
			echo Question::create_category($name,$status);
		}
	} else {
		echo "false";
	}
}

?>