<?php 

ob_start();
ob_clean();
session_start();

$location = "install.php";
$script_name = "AskFoxOs";
$installer_version = "2.2";

function preprint($data){
	echo "<pre>";
	print_r($data);
	echo "</pre>";
}

function clean_value($value){
	global $database;
	return preg_replace("/[^0-9a-z_ ,.(){}-]/i", "", trim($value));
}

if(!isset($message)){
	$message = "";
}

if(isset($_SESSION['current_step'])){
	if(isset($_GET['step'])){
		$step = clean_value($_GET['step']);
	} else {
		$step = 1;
	}
	if($_SESSION['current_step'] != $step){
		header("Location: ".$location."?step=".$_SESSION['current_step']);
	    exit;
	}
	
	if($step == 1){
		$location .= "?step=1";
		if(isset($_POST['submit'])){
			$host = trim($_POST['host']);
			$user = trim($_POST['user']);
			$pass = trim($_POST['pass']);
			$name = trim($_POST['name']);
			
			if($host == "" || $user == "" || $pass == "" || $name == ""){
				$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>";
			} else {
				
				$ready = false;
				
				if (function_exists('mysqli_connect')) {
				 	$link = mysqli_connect($host, $user, $pass, $name);

					if (mysqli_connect_errno()) {
					    printf("Connect failed: %s\n", mysqli_connect_error());
					    exit();
					}
					
					if(mysqli_query($link, "DROP DATABASE `".$name."`")){
						while (mysqli_next_result($link)) {;}
						if(mysqli_query($link, "CREATE DATABASE `".$name."`")){
							$ready = true;
						} else {
							$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>There was an error when we tried to create ".$name.".</div>";
						}
					} else {
						if(mysqli_query($link, "CREATE DATABASE `".$name."`")){
							$ready = true;
						} else {
							$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>There was an error when we tried to create ".$name.".</div>";
						}
					}
					mysqli_close($link);
				} else {
					$link = mysql_connect($host, $user, $pass);
					if (!$link) {
					    die('Could not connect: ' . mysql_error());
					}
					$db_selected = mysql_select_db($name, $link);
					if (!$db_selected) {
					    die ('Can\'t use '.$name.' : ' . mysql_error());
					}
					
					if(mysql_query("DROP DATABASE ".$name)){
						if(mysql_query("CREATE DATABASE `".$name."`")){
							$ready = true;
						} else {
							$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>There was an error when we tried to create ".$name.".</div>";
						}
					} else {
						if(mysql_query("CREATE DATABASE `".$name."`")){
							$ready = true;
						} else {
							$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>There was an error when we tried to create ".$name.".</div>";
						}
					}
					mysql_close($link);
				}
				
				if($ready == true){
					
					$database_installed = false;
					
					if (function_exists('mysqli_connect')) {
					 	$link = mysqli_connect($host, $user, $pass, $name);

						if (mysqli_connect_errno()) {
						    printf("Connect failed: %s\n", mysqli_connect_error());
						    exit();
						}
						
						$query = file_get_contents("fresh_database.sql");
						if(mysqli_multi_query($link, $query)){
							$database_installed = true;
						}
						
					} else {
						$link = mysql_connect($host, $user, $pass);
						if (!$link) {
						    die('Could not connect: ' . mysql_error());
						}
						$db_selected = mysql_select_db($name, $link);
						if (!$db_selected) {
						    die ('Can\'t use '.$name.' : ' . mysql_error());
						}
						$the_file = file("fresh_database.sql");
						foreach ($the_file as $query) {
						    mysql_query($query);
							// echo $query." -- end of query --<br />";
						}
						$database_installed = true;
					}

					if($database_installed == true){
						
						$filename = '../includes/configuration/config.php';
$content = '<?php

if (__FILE__ == $_SERVER["SCRIPT_FILENAME"]) exit("No direct access allowed.");

ob_start();
ob_clean();
session_start();
defined("DB_SERVER") ? null : define("DB_SERVER", "'.$host.'");
defined("DB_USER")   ? null : define("DB_USER", "'.$user.'");
defined("DB_PASS")   ? null : define("DB_PASS", "'.$pass.'");
defined("DB_NAME")   ? null : define("DB_NAME", "'.$name.'");
require("core_settings.class.php");
$core_settings = Core_Settings::find_by_sql("SELECT * FROM core_settings");
$count = count($core_settings);
for($i=0;$i <= $count-1;$i++){
	defined($core_settings[$i]->name) ? null : define($core_settings[$i]->name, $core_settings[$i]->data);
}
defined("IMAGES") ? null : define("IMAGES", WWW."assets/img/"); 
date_default_timezone_set(TIMEZONE);

defined("AUTHPATH")   ? null : define("AUTHPATH", "/auth/");
defined("AUTHSALT")   ? null : define("AUTHSALT", "PASTE_RANDOM_CODE_HERE");

defined("FACEBOOK_APP_ID")   ? null : define("FACEBOOK_APP_ID", "HERE");
defined("FACEBOOK_APP_SECRET")   ? null : define("FACEBOOK_APP_SECRET", "HERE");

defined("TWITTER_CONSUMER_KEY")   ? null : define("TWITTER_CONSUMER_KEY", "HERE");
defined("TWITTER_CONSUMER_SECRET")   ? null : define("TWITTER_CONSUMER_SECRET", "HERE");

?>
';

										if (!$handle = fopen($filename, 'w')) {
										echo "Cannot open file ($filename)";
										exit;
										}

										if (fwrite($handle, $content) === FALSE) {
										echo "Cannot write to file ($filename)";
										exit;
										}

										fclose($handle);

										$filename = 'database_cons.php';

$content = '<?php
defined("DB_SERVER") ? null : define("DB_SERVER", "'.$host.'");
defined("DB_USER")   ? null : define("DB_USER", "'.$user.'");
defined("DB_PASS")   ? null : define("DB_PASS", "'.$pass.'");
defined("DB_NAME")   ? null : define("DB_NAME", "'.$name.'"); ?>';

										if (!$handle = fopen($filename, 'w')) {
										echo "Cannot open file ($filename)";
										exit;
										}

										if (fwrite($handle, $content) === FALSE) {
										echo "Cannot write to file ($filename)";
										exit;
										}

										fclose($handle);
						
						
						
						$_SESSION['current_step'] = 2;
						header("Location: install.php?step=2");
						exit;
					} else {
						if (function_exists('mysqli_connect')){
							mysqli_error($link);
						} else {
							mysql_error($link);
						}
					}					
					
				} else {
					$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>There was a problem when we tried to create or delete the database. Please refresh and try again.</div>";
				}
				
				if (function_exists('mysqli_connect')){
					mysqli_close($link);
				} else {
					mysql_close($link);
				}
			}

		} else {
			$host = "localhost";
			$user = "";
			$pass = "";
			$name = "";
		}
		
		
	} else if($step == 2){
		$location .= "?step=2";
		$timezones = array (
		  '(GMT-12:00) International Date Line West' => 'Pacific/Wake',
		  '(GMT-11:00) Midway Island' => 'Pacific/Apia',
		  '(GMT-11:00) Samoa' => 'Pacific/Apia',
		  '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
		  '(GMT-09:00) Alaska' => 'America/Anchorage',
		  '(GMT-08:00) Pacific Time (US &amp; Canada); Tijuana' => 'America/Los_Angeles',
		  '(GMT-07:00) Arizona' => 'America/Phoenix',
		  '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
		  '(GMT-07:00) La Paz' => 'America/Chihuahua',
		  '(GMT-07:00) Mazatlan' => 'America/Chihuahua',
		  '(GMT-07:00) Mountain Time (US &amp; Canada)' => 'America/Denver',
		  '(GMT-06:00) Central America' => 'America/Managua',
		  '(GMT-06:00) Central Time (US &amp; Canada)' => 'America/Chicago',
		  '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
		  '(GMT-06:00) Mexico City' => 'America/Mexico_City',
		  '(GMT-06:00) Monterrey' => 'America/Mexico_City',
		  '(GMT-06:00) Saskatchewan' => 'America/Regina',
		  '(GMT-05:00) Bogota' => 'America/Bogota',
		  '(GMT-05:00) Eastern Time (US &amp; Canada)' => 'America/New_York',
		  '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
		  '(GMT-05:00) Lima' => 'America/Bogota',
		  '(GMT-05:00) Quito' => 'America/Bogota',
		  '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
		  '(GMT-04:00) Caracas' => 'America/Caracas',
		  '(GMT-04:00) La Paz' => 'America/Caracas',
		  '(GMT-04:00) Santiago' => 'America/Santiago',
		  '(GMT-03:30) Newfoundland' => 'America/St_Johns',
		  '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
		  '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
		  '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
		  '(GMT-03:00) Greenland' => 'America/Godthab',
		  '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
		  '(GMT-01:00) Azores' => 'Atlantic/Azores',
		  '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
		  '(GMT) Casablanca' => 'Africa/Casablanca',
		  '(GMT) Edinburgh' => 'Europe/London',
		  '(GMT) Greenwich Mean Time : Dublin' => 'Europe/London',
		  '(GMT) Lisbon' => 'Europe/London',
		  '(GMT) London' => 'Europe/London',
		  '(GMT) Monrovia' => 'Africa/Casablanca',
		  '(GMT+01:00) Amsterdam' => 'Europe/Berlin',
		  '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
		  '(GMT+01:00) Berlin' => 'Europe/Berlin',
		  '(GMT+01:00) Bern' => 'Europe/Berlin',
		  '(GMT+01:00) Bratislava' => 'Europe/Belgrade',
		  '(GMT+01:00) Brussels' => 'Europe/Paris',
		  '(GMT+01:00) Budapest' => 'Europe/Belgrade',
		  '(GMT+01:00) Copenhagen' => 'Europe/Paris',
		  '(GMT+01:00) Ljubljana' => 'Europe/Belgrade',
		  '(GMT+01:00) Madrid' => 'Europe/Paris',
		  '(GMT+01:00) Paris' => 'Europe/Paris',
		  '(GMT+01:00) Prague' => 'Europe/Belgrade',
		  '(GMT+01:00) Rome' => 'Europe/Berlin',
		  '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
		  '(GMT+01:00) Skopje' => 'Europe/Sarajevo',
		  '(GMT+01:00) Stockholm' => 'Europe/Berlin',
		  '(GMT+01:00) Vienna' => 'Europe/Berlin',
		  '(GMT+01:00) Warsaw' => 'Europe/Sarajevo',
		  '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
		  '(GMT+01:00) Zagreb' => 'Europe/Sarajevo',
		  '(GMT+02:00) Athens' => 'Europe/Istanbul',
		  '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
		  '(GMT+02:00) Cairo' => 'Africa/Cairo',
		  '(GMT+02:00) Harare' => 'Africa/Johannesburg',
		  '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
		  '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
		  '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
		  '(GMT+02:00) Kyiv' => 'Europe/Helsinki',
		  '(GMT+02:00) Minsk' => 'Europe/Istanbul',
		  '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
		  '(GMT+02:00) Riga' => 'Europe/Helsinki',
		  '(GMT+02:00) Sofia' => 'Europe/Helsinki',
		  '(GMT+02:00) Tallinn' => 'Europe/Helsinki',
		  '(GMT+02:00) Vilnius' => 'Europe/Helsinki',
		  '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
		  '(GMT+03:00) Kuwait' => 'Asia/Riyadh',
		  '(GMT+03:00) Moscow' => 'Europe/Moscow',
		  '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
		  '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
		  '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
		  '(GMT+03:00) Volgograd' => 'Europe/Moscow',
		  '(GMT+03:30) Tehran' => 'Asia/Tehran',
		  '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
		  '(GMT+04:00) Baku' => 'Asia/Tbilisi',
		  '(GMT+04:00) Muscat' => 'Asia/Muscat',
		  '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
		  '(GMT+04:00) Yerevan' => 'Asia/Tbilisi',
		  '(GMT+04:30) Kabul' => 'Asia/Kabul',
		  '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
		  '(GMT+05:00) Islamabad' => 'Asia/Karachi',
		  '(GMT+05:00) Karachi' => 'Asia/Karachi',
		  '(GMT+05:00) Tashkent' => 'Asia/Karachi',
		  '(GMT+05:30) Chennai' => 'Asia/Calcutta',
		  '(GMT+05:30) Kolkata' => 'Asia/Calcutta',
		  '(GMT+05:30) Mumbai' => 'Asia/Calcutta',
		  '(GMT+05:30) New Delhi' => 'Asia/Calcutta',
		  '(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
		  '(GMT+06:00) Almaty' => 'Asia/Novosibirsk',
		  '(GMT+06:00) Astana' => 'Asia/Dhaka',
		  '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
		  '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
		  '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
		  '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
		  '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
		  '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
		  '(GMT+07:00) Jakarta' => 'Asia/Bangkok',
		  '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
		  '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
		  '(GMT+08:00) Chongqing' => 'Asia/Hong_Kong',
		  '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
		  '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
		  '(GMT+08:00) Kuala Lumpur' => 'Asia/Singapore',
		  '(GMT+08:00) Perth' => 'Australia/Perth',
		  '(GMT+08:00) Singapore' => 'Asia/Singapore',
		  '(GMT+08:00) Taipei' => 'Asia/Taipei',
		  '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
		  '(GMT+08:00) Urumqi' => 'Asia/Hong_Kong',
		  '(GMT+09:00) Osaka' => 'Asia/Tokyo',
		  '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
		  '(GMT+09:00) Seoul' => 'Asia/Seoul',
		  '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
		  '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
		  '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
		  '(GMT+09:30) Darwin' => 'Australia/Darwin',
		  '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
		  '(GMT+10:00) Canberra' => 'Australia/Sydney',
		  '(GMT+10:00) Guam' => 'Pacific/Guam',
		  '(GMT+10:00) Hobart' => 'Australia/Hobart',
		  '(GMT+10:00) Melbourne' => 'Australia/Sydney',
		  '(GMT+10:00) Port Moresby' => 'Pacific/Guam',
		  '(GMT+10:00) Sydney' => 'Australia/Sydney',
		  '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
		  '(GMT+11:00) Magadan' => 'Asia/Magadan',
		  '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
		  '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
		  '(GMT+12:00) Auckland' => 'Pacific/Auckland',
		  '(GMT+12:00) Fiji' => 'Pacific/Fiji',
		  '(GMT+12:00) Kamchatka' => 'Pacific/Fiji',
		  '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
		  '(GMT+12:00) Wellington' => 'Pacific/Auckland',
		  '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu',
		);
		
		if(isset($_POST['submit'])){
			$WWW = trim($_POST['WWW']);
			$SITE_NAME = trim($_POST['SITE_NAME']);
			$SITE_DESC = trim($_POST['SITE_DESC']);
			$SITE_KEYW = trim($_POST['SITE_KEYW']);
			$ADMINDIR = trim($_POST['ADMINDIR']);
			$SITE_EMAIL = trim($_POST['SITE_EMAIL']);
			$VERIFY_EMAIL = trim($_POST['VERIFY_EMAIL']);
			$ALLOW_REGISTRATIONS = trim($_POST['ALLOW_REGISTRATIONS']);
			$DATABASE_SALT = trim($_POST['DATABASE_SALT']);
			$PAGINATION_PER_PAGE = trim($_POST['PAGINATION_PER_PAGE']);
			$TIMEZONE = trim($_POST['TIMEZONE']);
			
			if($WWW != "" && $SITE_NAME != "" && $SITE_DESC != "" && $SITE_KEYW != "" && $ADMINDIR != "" && $SITE_EMAIL != "" && $VERIFY_EMAIL != "" && $ALLOW_REGISTRATIONS != "" && $DATABASE_SALT != "" && $TIMEZONE != ""){
				
				$user_id = '';
				for ($i=0;$i<6;$i++){
					$user_id .= rand(1, 9);
				}
				$salt = $DATABASE_SALT;
				$password = crypt("admin",$salt);
				for ($i = 0; $i < 10; ++$i){
				    $password = crypt($password."admin",$salt);
				}
				
				require 'database_cons.php';

				if (function_exists('mysqli_connect')) {
				 	$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

					/* check connection */
					if (mysqli_connect_errno()) {
					    printf("Connect failed: %s\n", mysqli_connect_error());
					    exit();
					}
					
				} else {
					$link = mysql_connect(DB_SERVER, DB_USER, DB_PASS);
					if (!$link) {
					    die('Could not connect: ' . mysql_error());
					}
					$db_selected = mysql_select_db(DB_NAME, $link);
					if (!$db_selected) {
					    die ('Can\'t use '.$name.' : ' . mysql_error());
					}
				}
				
				$query_successful = false;
				
				$query1 = "INSERT INTO core_settings VALUES('WWW','{$WWW}'),('SITE_NAME','{$SITE_NAME}'),('SITE_DESC','{$SITE_DESC}'),('SITE_KEYW','{$SITE_KEYW}'),('ADMINDIR','{$ADMINDIR}'),('SITE_EMAIL','{$SITE_EMAIL}'),('VERIFY_EMAIL','{$VERIFY_EMAIL}'),('DEMO_MODE','OFF'),('ALLOW_REGISTRATIONS','{$ALLOW_REGISTRATIONS}'),('DATABASE_SALT','{$DATABASE_SALT}'),('THEME_NAME','mds_light'),('PAGINATION_PER_PAGE','{$PAGINATION_PER_PAGE}'),('TIMEZONE','{$TIMEZONE}'),('SEO_URLS','OFF'),('OAUTH','OFF'); ";
				$query2 = "INSERT INTO `users` VALUES(1, 688969, 'Admin', 'Account', 'Male', 'admin', '{$password}', 'admin@example.com', '1', '0', '2013-02-01 00:00:00', '2013-02-27 22:43:26', 'SERVER', '127.0.0.1', 'United Kingdom', '1', 0, 0, '0', '');";
				
				if (function_exists('mysqli_connect')){
					if(mysqli_query($link, $query1) && mysqli_query($link, $query2)){
						$query_successful = true;
					} else {
						$error_message = mysqli_errno($link) . ": " . mysqli_error($link);
					}
				} else {
					if(mysql_query($query1) && mysql_query($query2)){
						$query_successful = true;
					} else {
						$error_message = mysql_errno($link) . ": " . mysql_error($link);
					}
				}
				
				if($query_successful){					
					$_SESSION['current_step'] = 3;
					header("Location: install.php?step=3");
					exit;
				} else {
					$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>The following error has accrued. ".$error_message."</div>";
				}			
				
			} else {
				$message = "<div class='alert alert-error'>Please complete all required fields.</div>";
			}
			
		} else {
			
			$aZ09 = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9)); 
		   $database_salt_gen =''; 
		   for($c=0;$c < 25;$c++) { 
		      $database_salt_gen .= $aZ09[mt_rand(0,count($aZ09)-1)]; 
		   } 
			
			$URI = str_replace( 'install/install.php?step=2', '', $_SERVER['REQUEST_URI'] );
			$WWW = "http://".$_SERVER["HTTP_HOST"].$URI."";
			$SITE_NAME = "AskFoxOs";
			$SITE_DESC = "The Description";
			$SITE_KEYW = "The,Key,Words";
			$ADMINDIR = "admin/";
			$SITE_EMAIL = "AskFoxOs <server@example.com>";
			$VERIFY_EMAIL = "YES";
			$ALLOW_REGISTRATIONS = "ALLOW_REGISTRATIONS";
			$DATABASE_SALT = $database_salt_gen;
			$PAGINATION_PER_PAGE = "20";
			$TIMEZONE = "Europe/London";
		}
	} else if($step == 3){
		$_SESSION['current_step'] = 1;
	}
	
} else {
	$_SESSION['current_step'] = 1;
	header("Location: install.php?step=1");
	exit;
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>AskFoxOs Installer - Beetledeveloper</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="BeetleDeveloper - JDLA">

    <!-- The styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
	 <link href="css/chosen.css" rel="stylesheet">
	
	<style>
	body{
		margin-top: 5%;
	}
	</style>
	
    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- the fav and touch icons -->
    <link rel="shortcut icon" href="ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
	
	<script src="js/jquery.js"></script>
	<script src="js/custom.js"></script>
	<script src="js/jquery.jcarousel.min.js"></script>
	<script src="js/jquery.pikachoose.js"></script>

  </head>

  <body>

	<div class="container">


<div class="hero-unit">

	<h3>AskFoxOs Installer  - Beetledeveloper</h3>
	
	<hr />
	
	<ul id="steps">
		<li class="<?php if($step == 1){echo "current";} ?>">Step 1<span>Database Setup</span></li>
		<li class="<?php if($step == 2){echo "current";} ?>">Step 2<span>Core Settings</span></li>
		<li class="<?php if($step == 3){echo "current";} ?>">Step 3<span>Installation Complete</span></li>
	</ul>
	
	<hr />
	
	<?php echo $message; ?>
	
	<?php if($step == 1) { ?>
		<form action="<?php echo $location; ?>" method="POST">
			<div class="row-fluid">
				<div class="span3">
					<label>Host</label>
					<input type="text" name="host" class="span12" required="required" value="<?php echo htmlentities($host); ?>" />
				</div>
				<div class="span3">
					<label>Username</label>
					<input type="text" name="user" class="span12" required="required" value="<?php echo htmlentities($user); ?>" />
				</div>
				<div class="span3">
					<label>Password</label>
					<input type="password" name="pass" class="span12" required="required" value="<?php echo htmlentities($pass); ?>" />
				</div>
				<div class="span3">
					<label>Database Name</label>
					<input type="text" name="name" class="span12" required="required" value="<?php echo htmlentities($name); ?>" />
				</div>
			</div>
			
			<div class="form-actions" style="text-align: center;">
				<input class="btn btn-primary" type="submit" name="submit" value="Configure Database" />
			</div>
		</form>
	<?php } else if($step == 2) { ?>
		<form action="<?php echo $location; ?>" method="POST">
			<div class="row-fluid">
				<div class="span3">
					<label>Site Domain</label>
			      <input type="text" name="WWW" class="span12" required="required" value="<?php echo htmlentities($WWW); ?>" />
				</div>
				<div class="span3">
					<label>Site Name</label>
		      	<input type="text" name="SITE_NAME" class="span12" required="required" value="<?php echo htmlentities($SITE_NAME); ?>" />
				</div>
				<div class="span3">
					<label>Site Description</label>
					<input type="text" name="SITE_DESC" class="span12" required="required" value="<?php echo htmlentities($SITE_DESC); ?>" />
				</div>
				<div class="span3">
					<label>Site Keywords</label>
					<input type="text" name="SITE_KEYW" class="span12" required="required" value="<?php echo htmlentities($SITE_KEYW); ?>" />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span4">
					<label>Admin Directory</label>
					<input type="text" name="ADMINDIR" class="span12" required="required" value="<?php echo htmlentities($ADMINDIR); ?>" />
				</div>
				<div class="span5">
					<label>Site Email</label>
					<input type="text" name="SITE_EMAIL" class="span12" required="required" value="<?php echo htmlentities($SITE_EMAIL); ?>" />
				</div>
				<div class="span3">
					<label>Verify Email</label>
			    	<select name="VERIFY_EMAIL" class="span12" required="required" value="<?php echo $VERIFY_EMAIL ?>">
						<option value="YES" <?php if($VERIFY_EMAIL == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
						<option value="NO" <?php if($VERIFY_EMAIL == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
					</select>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span3">
					<label>Allow New Registrations</label>
			    	<select name="ALLOW_REGISTRATIONS" class="span12" required="required" value="<?php echo $ALLOW_REGISTRATIONS ?>">
						<option value="YES" <?php if($ALLOW_REGISTRATIONS == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
						<option value="NO" <?php if($ALLOW_REGISTRATIONS == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
					</select>
				</div>
				<div class="span2">
					<label>Pagination Per Page</label>
					<input type="text" name="PAGINATION_PER_PAGE" class="span12" required="required" value="<?php echo htmlentities($PAGINATION_PER_PAGE); ?>" />
				</div>
				<div class="span3">
					<label>Database Salt</label>
					<input type="text" name="DATABASE_SALT" class="span12" required="required" value="<?php echo htmlentities($DATABASE_SALT); ?>" />
				</div>
				<div class="span4">
					<label>Timezone</label>
			      <select name="TIMEZONE" class="span12" required="required" value="<?php echo $TIMEZONE ?>">
						<?php
						
						foreach ($timezones as $key => $value) {
							if($value == $TIMEZONE){
								$selected = ' selected="selected"';
							} else {
								$selected = '';
							}
							echo '<option value="' .$value. '" '.$selected.' >' .$key. '</option>';
						}
						
						?>
					</select>
				</div>
			</div>
			<div class="form-actions" style="text-align: center;">
				<input class="btn btn-primary" type="submit" name="submit" value="Set Core Settings" />
			</div>
		</form>
	<?php } else if($step == 3) { ?>
		<div class="row-fluid center">
			<div class="span12">
				<label> AskFoxOs - has been successfully installed. If you have any questions please feel free to <a href="malito:jdlandazaball@gmail.com">Contact Us</a>.</label>
			</div>
		</div>
		
		<div class="row-fluid center">
			<div class="span12">
				<label>The following account has been setup and is ready for you to use. For security reasons, we recommend that you change the password right away.</label>
			</div>
		</div>
		
		<div class="row-fluid center">
			<div class="span12">
				<code style="padding-bottom: 4px;">Username: admin</code><br />
				<code>Password: admin</code>
			</div>
		</div>
		
		<hr />
		
		<div class="row-fluid center">
			<div class="span12">
				<strong>Please remove the "install" directory.</strong> <br /><br /><a href="../index" class="btn btn-primary">Visit Site</a>
			</div>
		</div>
	<?php } ?>
	
</div>

     <footer class="well wellf text-center">
		&copy; 2014 BeetleDeveloper.<span style="float:right"><a href="http://forum.beetledeveloper.tk"></a></span>
		<a href="<?php echo WWW; ?>developer_api">Developer API AskFoxOs</a>
		        <p class="pull-right "><a href="#top"><i class="fa fa-arrow-circle-o-up fa-3x"></i></a></p>
        <div class="links text-center">
          <a href="http://forum.beetledeveloper.tk" onclick="pageTracker._link(this.href); return false;">Forums</a>
          <a href="https://twitter.com/thomashpark">Twitter</a>
          <a href="https://github.com/beetledeveloper">GitHub</a>
          <a href="#">Donate</a>
        </div>
        Developer by <a href="mailto:jdlandazaball@gmail.com" rel="nofollow">JDLA</a><br>
        General Public License, <a href="http://opensource.org/licenses/gpl-3.0.html" rel="nofollow">version 3 (GPL-3.0)</a>.<br>
        Based on <a href="http://getbootstrap.com/2.3.2/" rel="nofollow">Bootstrap</a>
        Icons from <a href="http://fortawesome.github.com/Font-Awesome/" rel="nofollow">Font Awesome</a>. 
        Web fonts from <a href="http://www.google.com/webfonts" rel="nofollow">Google</a>
			
		</footer>

    </div> <!-- /container -->

	    <!-- The javascript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
	    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
	    <script src="js/google-code-prettify/prettify.js"></script>
	    <script src="js/application.js"></script>
		<script src="js/bootstrap.min.js"></script>
	 	<script src="js/chosen.jquery.min.js"></script>
		<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
	  </body>
	</html>