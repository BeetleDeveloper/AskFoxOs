<?php


require_once("includes/inc_files.php");

if($session->is_logged_in()) {
  redirect_to("index");
}

if((empty($_GET['email'])) || (empty($_GET['hash']))) {
    $message = "";
    
  } else {
	$email = trim($_GET['email']);
	$hash = trim($_GET['hash']);
	
	global $database;
	
	// Escape email and hash values to help prevent sql injection.
	$email = $database->escape_value($email);
	$hash = $database->escape_value($hash);
	
	// Check if the provided information is in the database
	Activation::check_activation($email, $hash);
}

if (isset($_POST['submit'])) { // Form has been submitted.
	$email = trim($_POST['email']);
	$hash = trim($_POST['hash']);
	
	if ((!$email == "") && (!$hash == "")) {
		Activation::check_activation($email, $hash);
	} else {
		$message = "<div class='notification-box warning-notification-box'><p>Nada Mención.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}
} else { // Form has not been submitted.
	$email = "";
	$hash = "";
}

	
if (isset($_POST['resend_code'])) { // Form has been submitted.
	$email = trim($_POST['email']);
	
	if (!$email == "") {
		Activation::check_resend_code($email);
	} else {
		$message = "<div class='notification-box warning-notification-box'><p>Nada Mención.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	}
} else { // Form has not been submitted.
	$email = "";
	$hash = "";
}

$current_page = "forgot_password";

?>

<?php $page_title = "Activate"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>

<div id="message"><?php echo output_message($message); ?></div>

<?php if((!isset($_GET['email'])) && (!isset($_GET['hash'])) ) : ?>
<div class="row-fluid">
	<form action="activate.php" method="post" >
		<div class="span6 center">
			<div class="span12">
		      <h3>Activar Cuenta</h3>
			</div>
			<div class="span12">
		      <input type="text" class="span4" name="email" required="required" placeholder="Email" value="<?php echo htmlentities($email); ?>" />
			</div>
			<div class="span12">
		      <input type="text" class="span4" name="hash" required="required" placeholder="Codigo de Corfimacion" value="<?php echo htmlentities($hash); ?>" />
			</div>
			<div class="span12">
				<br />
				<input class="btn btn-primary" type="submit" name="submit" value="Activate" />
			</div>
		</div>
	</form>
	<form action="activate.php" method="post" >
		<div class="span6 center">
			<div class="span12">
		      <h3>Reenviar Codigo de Activacion</h3>
			</div>
			<div class="span12">
		      <input type="text" class="span4" name="email" required="required" placeholder="Email" value="<?php echo htmlentities($email); ?>" />
			</div>
			<div class="span12">
				<br />
				<input class="btn btn-primary" type="submit" name="resend_code" value="Resetear Codigo" />
			</div>
		</div>
	</form>
</div>
<?php endif ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>