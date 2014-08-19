<?php 


require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

login_check();

$active_page = "settings";
$current_page = "";

$settings = Core_Settings::find_by_sql("SELECT * FROM core_settings");

if(isset($_POST['update_settings'])){
	
	if(DEMO_MODE == "OFF"){
		// $max = count($settings);
		// for($i=1;$i <= $max; $i++){
		// 	$array =  (array) $settings[$i-1];
		// 	$$array['name'] = $_POST[$array['name']];
		// 	$sql = "UPDATE core_settings SET data = '".$data."' WHERE name = '".$name."' ";
		// 	$database->query($sql);
		// }
		
		foreach($settings as $setting) {
			$array =  (array) $setting;
			$$array['name'] = $_POST[$array['name']];
			
			// echo $$array['name']."<hr />";
			$database->query("UPDATE core_settings SET data = '".$$array['name']."' WHERE name = '".$array['name']."' ");
		}

		$database->query("UPDATE core_settings SET data = 'OFF' WHERE name = 'DEMO_MODE' ");

		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Settings have been successfully updated.</div>");
	} else {
		$session->message("<div class='alert alert-warning'><button type='button' class='close' data-dismiss='alert'>×</button>Sorry, but you can't do that while demo mode is enabled.</div>");
	}
	
	redirect_to("settings.php");
} else {
	foreach($settings as $setting) {
		$array =  (array) $setting;
		$$array['name'] = $array['data'];
	}
}

?>

<?php $page_title = "Site Settings"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<div class="row-fluid">
	<?php require_once("../includes/global/admin_nav.php"); ?>
</div>
<div class="row-fluid">
<div class="span12">
	<div class="title">
		<h1><?php echo $page_title; ?></h1>
	</div>
	
	<div id="message"><?php echo output_message($message); ?></div>
	
		<form action="settings.php" method="POST">
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
				<div class="span3">
					<label>Admin Directory</label>
					<input type="text" name="ADMINDIR" class="span12" required="required" value="<?php echo htmlentities($ADMINDIR); ?>" />
				</div>
				<div class="span3">
					<label>Site Email</label>
					<input type="text" name="SITE_EMAIL" class="span12" required="required" value="<?php echo htmlentities($SITE_EMAIL); ?>" />
				</div>
				<div class="span2">
					<label>Verify Email</label>
			      <select name="VERIFY_EMAIL" class="span12" required="required" value="<?php echo $VERIFY_EMAIL ?>">
						<option value="YES" <?php if($VERIFY_EMAIL == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
						<option value="NO" <?php if($VERIFY_EMAIL == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
					</select>
				</div>
				<div class="span2">
					<label>Pagination</label>
					<input type="text" name="PAGINATION_PER_PAGE" class="span12" required="required" value="<?php echo htmlentities($PAGINATION_PER_PAGE); ?>" />
				</div>
				<div class="span2">
					<label>Allow Registrations</label>
			      <select name="ALLOW_REGISTRATIONS" class="span12" required="required" value="<?php echo $ALLOW_REGISTRATIONS ?>">
						<option value="YES" <?php if($ALLOW_REGISTRATIONS == 'YES') { echo 'selected="selected"';} else { echo ''; } ?>>Yes</option>
						<option value="NO" <?php if($ALLOW_REGISTRATIONS == 'NO') { echo 'selected="selected"';} else { echo ''; } ?>>No</option> 
					</select>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span2">
					<label>SEO Friendly URLS</label>
					<select name="SEO_URLS" class="span12" required="required" value="<?php echo $SEO_URLS ?>">
						<option value="ON" <?php if($SEO_URLS == 'ON') { echo 'selected="selected"';} else { echo ''; } ?>>On</option>
						<option value="OFF" <?php if($SEO_URLS == 'OFF') { echo 'selected="selected"';} else { echo ''; } ?>>Off</option> 
					</select>
				</div>
				<div class="span2">
					<label>Database Salt</label>
					<input type="text" name="DATABASE_SALT" class="span12" required="required" value="<?php echo htmlentities($DATABASE_SALT); ?>" />
				</div>
				<div class="span3">
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
				<div class="span3">
					<label>Theme Name</label>
					<input type="text" name="THEME_NAME" class="span12" required="required" value="<?php echo htmlentities($THEME_NAME); ?>" />
				</div>
				<div class="span2">
					<label>OAuth</label>
					<select name="OAUTH" class="span12" required="required" value="<?php echo $OAUTH ?>">
						<option value="ON" <?php if($OAUTH == 'ON') { echo 'selected="selected"';} else { echo ''; } ?>>On</option>
						<option value="OFF" <?php if($OAUTH == 'OFF') { echo 'selected="selected"';} else { echo ''; } ?>>Off</option> 
					</select>
				</div>
			</div>

			<div class="form-actions" style="text-align: center;">
				<input class="btn btn-primary" type="submit" name="update_settings" value="Update Settings" />
			</div>
		</form>
	
	</div>

	</div>

</div>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>