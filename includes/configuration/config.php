<?php

if (__FILE__ == $_SERVER["SCRIPT_FILENAME"]) exit("No direct access allowed.");


ob_start();
ob_clean();
session_start();
defined("DB_SERVER") ? null : define("DB_SERVER", "mysql.nixiweb.com");
defined("DB_USER")   ? null : define("DB_USER", "u231603265_askfo");
defined("DB_PASS")   ? null : define("DB_PASS", "juandavid_1o");
defined("DB_NAME")   ? null : define("DB_NAME", "u231603265_ask");
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
