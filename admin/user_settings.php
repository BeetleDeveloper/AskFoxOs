<?php 


require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../login.php");}

$admin = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
$user_id = $_GET['user_id'];
$location = "user_settings.php?user_id=".$user_id;

$access_logs = User::get_access_logs($user_id);

$current_page = "";
$active_page = "users";

if(empty($_GET['user_id'])) {
	$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>No User ID was provided.</div>";
}

if(empty($_GET['user_id'])){
    $user_id = "";
	$current_user = "";
	$admin_user = "";
    
  } else {
	$user_id = $_GET['user_id'];
	$current_user = User::find_by_id($user_id);
	$admin_user = Admin::find_by_id($_SESSION['masdyn']['answers']['user_id']);
	if(!$current_user) {
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>User could not be found.</div>");
		redirect_to('users.php');
	}
}

if (isset($_POST['submit'])) { 

	$username = $current_user->username;
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$repeat_password = trim($_POST['repeat_password']);
	$email = trim($_POST['email']);
	$gender = $_POST['gender'];
	$country = $_POST['country'];
	$activated = $_POST['activated'];
	$suspended = $_POST['suspended'];
	$staff = $_POST['staff'];
	
	$staff_username = $admin->username;
	
	$check_email = User::check_user('email', $email);
	
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		if ($first_name != "" && $last_name != "" && $email != "") {
		
			if ($password != "" && $repeat_password != "") {
				// if new password fields are not empty, check to see if they match.
				if ($password == $repeat_password) {
					// new password match
					$new_password = encrypt_password($password);
					$admin_user->update_account('1', $user_id, $username, $first_name, $last_name, $new_password, $email, $password, $country, $gender, $activated, $suspended, $staff);
				} else {
					$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Passwords don't match.</div>";
				}
			} else {
				// if new password fields are empty
				$admin_user->update_account('2', $user_id, $username, $first_name, $last_name, '', $email, $password, $country, $gender, $activated, $suspended, $staff);
				// $message = "Settings 2 Updated";
			}

		} else {
			$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
		}
	}
	
} else { // Form has not been submitted.
	if (!$user_id == ""){
		$username = $current_user->username;
		$password = "";
		$current_password = "";
		$repeat_password = "";
		$first_name = $current_user->first_name;
		$last_name = $current_user->last_name;
		$email = $current_user->email;
		$activated = $current_user->activated;
		$suspended = $current_user->suspended;
		$staff = $current_user->staff;
	}
}

if (isset($_POST['send_new_password'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		Admin::send_new_password($email, $location);
	}
}

if (isset($_POST['delete_account'])) {
	if (DEMO_MODE == 'ON') {
		$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
	} else {
		Admin::delete_account($user_id, $email);
	}
}

if(isset($_POST['login_as_user'])){
	$session = new Session();
	$session->admin_login_as_user($current_user->user_id);
	redirect_to("../settings.php");
}

if(isset($_POST['send_email'])) {
	$email = $current_user->first_name." ".$current_user->last_name." <".$current_user->email.">";
	$subject = $_POST['subject'];
	$email_message = $_POST['message'];
	if(($subject != "") || ($message != "")) {
		if (DEMO_MODE == 'ON') {
			$message = "<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>";
		} else {
			Admin::email_user($email, $subject, $email_message);
		}
	} else {
		$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
	}
}

?>
	
<?php $page_title = "User"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<div class="row-fluid">
	<?php require_once("../includes/global/admin_nav.php"); ?>
</div>
<div class="row-fluid">
<div class="span12">
	<div class="title">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="message"><?php echo output_message($message); ?></div>
	
	<?php

	if($user_id == "") {
		echo "<a href='users.php' class='button'>Back to users</a>";
	} else { ?>

	<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="post" class="center">
		<input class="btn btn-small btn-primary" type="submit" name="send_new_password" value="Send New Password" />
		<input class="btn btn-small btn-warning" type="submit" name="login_as_user" value="Login as User" />
		<button class="btn btn-small btn-primary" data-toggle="modal" href="#access_logs" >Access Logs</button>
		<button class="btn btn-small btn-primary" data-toggle="modal" href="#email_user">Email User</button>
		<button class="btn btn-small btn-danger" data-toggle="modal" href="#delete_account">Delete Account</button>
	</form>
	<hr />

	<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="post">
		
		<h3>Account Information</h3>	
		
		<div class="row-fluid">
			<div class="span3">
				<label>First Name</label>
		      <input type="text" class="span12" name="first_name" required="required" value="<?php echo htmlentities($first_name); ?>" />
			</div>
			<div class="span3">
				<label>Last Name</label>
	      	<input type="text" class="span12" name="last_name" required="required" value="<?php echo htmlentities($last_name); ?>" />
			</div>
			<div class="span3">
				<label>Gender</label>
				<select name="gender" class="span12" required="required" value="<?php echo $gender ?>">
					<option value="Male" <?php if($current_user->gender == 'Male') { echo 'selected="selected"';} else { echo ''; } ?>>Male</option>
					<option value="Female" <?php if($current_user->gender == 'Female') { echo 'selected="selected"';} else { echo ''; } ?>>Female</option> 
				</select>
			</div>
			<div class="span3">
				<label>Country</label>
				<select name="country" class="span12" required="required" value="<?php echo $country ?>">
					<option value="<?php echo $current_user->country ?>" selected="selected"><?php echo $current_user->country ?></option> 
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
					<option value="Cote D'ivoire">Cote D'ivoire</option> 
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
					<option value="Korea, Democratic People's Republic of">Korea, Democratic People's Republic of</option> 
					<option value="Korea, Republic of">Korea, Republic of</option> 
					<option value="Kuwait">Kuwait</option> 
					<option value="Kyrgyzstan">Kyrgyzstan</option> 
					<option value="Lao People's Democratic Republic">Lao People's Democratic Republic</option> 
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
				</select>		
			</div>
		</div>	

		<div class="row-fluid">
			<div class="span3">
				<label>Email Address</label>
		      <input type="email" class="span12" name="email" required="required" value="<?php echo htmlentities($email); ?>" />
			</div>
			<div class="span3">
				<label>Username</label>
	      	<input type="text" class="span12" name="username" disabled="disabled" required="required" value="<?php echo htmlentities($username); ?>" />
			</div>
			<div class="span3">
				<label>New Password</label>
		      <input type="password" class="span12" name="password" value="<?php echo htmlentities($password); ?>" />
			</div>
			<div class="span3">
				<label>Repeat New Password</label>
	      	<input type="password" class="span12" name="repeat_password" value="<?php echo htmlentities($repeat_password); ?>" />
			</div>
		</div>

		<div class="row-fluid">
			<div class="span3">
            	<label>Activated</label>
				<select name="activated" class="span12" required="required" value="<?php echo $activated ?>">
					<option value ="1" <?php if($current_user->activated == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value ="0" <?php if($current_user->activated == '0') { echo 'selected="selected"';} else { echo ''; } ?>>No</option>
				</select>
			</div>
			<div class="span3">
            	<label>Suspended</label>
				<select name="suspended" class="span12" required="required" value="<?php echo $suspended; ?>">
					<option value ="1" <?php if($current_user->suspended == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value ="0" <?php if($current_user->suspended == '0') { echo 'selected="selected"';} else { echo ''; } ?>>No</option>
				</select>
			</div>
			<div class="span3">
            	<label>Staff Access</label>
				<select name="staff" class="span12" required="required" value="<?php echo $staff; ?>">
					<option value ="1" <?php if($current_user->staff == '1') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
					<option value ="0" <?php if($current_user->staff == '0') { echo 'selected="selected"';} else { echo ''; } ?>>No</option>
				</select>
			</div>
		</div>
		
		<h3>Account Settings and Statistics</h3>
		
		<div class="row-fluid">
			<div class="span3">
				<label>Signup Date</label>
			    <input type="text" class="span12" name="signup_date" disabled="disabled" value="<?php echo $current_user->date_created; ?>" />
			</div>
			<div class="span3">
	            <label>Last Login</label>
	           	<input type="text" class="span12" name="last_login" disabled="disabled" value="<?php echo $current_user->last_login; ?>" />
			</div>
			<div class="span3">
	            <label>Signup IP</label>
	            <input type="text" class="span12" name="signup_ip" disabled="disabled" value="<?php echo $current_user->signup_ip; ?>" />
			</div>
			<div class="span3">
	            <label>Last IP</label>
	            <input type="text" class="span12" name="last_ip" disabled="disabled" value="<?php echo $current_user->last_ip; ?>" />
			</div>
		</div>
		
		<div class="row-fluid">
			<div class="span3">
				<label>Questions Posted</label>
			    <input type="text" class="span12" name="questions_posted" disabled="disabled" value="<?php echo $current_user->questions_posted; ?>" />
			</div>
			<div class="span3">
	            <label>Questions Answered</label>
	           	<input type="text" class="span12" name="questions_answered" disabled="disabled" value="<?php echo $current_user->questions_answered; ?>" />
			</div>
			<div class="span3">
	            <label>Signup IP</label>
	            <input type="text" class="span12" name="signup_ip" disabled="disabled" value="<?php echo $current_user->signup_ip; ?>" />
			</div>
			<div class="span3">
	            <label>Last IP</label>
	            <input type="text" class="span12" name="last_ip" disabled="disabled" value="<?php echo $current_user->last_ip; ?>" />
			</div>
		</div>

		<div class="form-actions" style="text-align: center;">
			<input class="btn btn-primary" type="submit" name="submit" value="Update Settings" />
		</div>

	</form>

</div>

<div id="access_logs" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Access Logs</h3>
	</div>
	<div class="modal-body">
		<?php if(!empty($access_logs)){ ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Date and Time</th>
					<th>IP Address</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($access_logs as $log): ?>
				<tr>
					<td><?php echo datetime_to_text($log->datetime); ?></td>
					<td><?php if(DEMO_MODE == "ON"){echo "--Hidden In Demo--";}else{echo $log->ip_address;} ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php } else { ?>
		<strong>This user has not yet logged in.</strong>
		<?php } ?>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
	</div>
</div>

<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="POST" id="delete_account" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Delete Account</h3>
	</div>
	<div class="modal-body">
		<strong>Are you sure about deleting this account? This action can't be reversed.</strong>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="delete_account" value="Confirm" />
	</div>
</form>

<form action="user_settings.php?user_id=<?php echo $user_id ?>" method="POST" id="email_user" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Email User</h3>
	</div>
	<div class="modal-body">
		<label>Subject</label>
	   <input type="text" required="required" style="width: 98%;" name="subject" value="" />
		<label>Message</label>
		<textarea required="required" style="width: 98%;" name="message"></textarea>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal">Close</button>
		<input class="btn btn-danger" type="submit" name="send_email" value="Send Email" />
	</div>
</form>

</div>

<div class="clear"><!-- --></div>

<?php } ?>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>