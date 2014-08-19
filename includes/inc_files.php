<?php


if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');

if(is_dir("install")){
	echo "Please go <a href='install/'>here</a> to install this script. If you have already gone though the install process, please delete the install directory and refresh this page. ";
    exit;
}

require_once("configuration/config.php");
require_once("classes/database.class.php");
require_once("classes/functions.class.php");
require_once("classes/pagination.class.php");
require_once("classes/session.class.php");
require_once("classes/user.class.php");
require_once("classes/email.class.php");
require_once("classes/activation.class.php");
require_once("classes/reset_password.class.php");
require_once("classes/profile.class.php");
require_once("classes/question.class.php");
require_once("classes/api.class.php");

require_once("language/en_language.php");

?>