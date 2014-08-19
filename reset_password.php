<?php
require_once("includes/inc_files.php");


if($session->is_logged_in()) {
  redirect_to("index");
}

$current_page = "forgot_password";

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
	Reset_Password::check_confirm_link($email, $hash);
}

if (isset($_POST['submit'])) { // Form has been submitted.

	$email = trim($_POST['email']);
	$hash = trim($_POST['hash']);
	
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Lo sentimos, no puedes hacer eso mientras que el modo de demostración está activada.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
	  	if ((!empty($email)) && (!empty($hash))) {
			Reset_Password::check_confirm_link($email, $hash);

		} else {
			$message = "<div class='notification-box warning-notification-box'><p>Nada Mención.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
		}
	}
  
} else { // Form has not been submitted.
	$email = "";
	$hash = "";
}

if (isset($_POST['send_code'])) { // Form has been submitted.

	$email = trim($_POST['email']);
	
	if (DEMO_MODE == 'ON') {
		$message = "<div class='notification-box warning-notification-box'><p>Lo sentimos, no puedes hacer eso mientras que el modo de demostración está activada.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
	} else {
	  	if (!empty($email)) {
			Reset_Password::set_confirm_email_link($email);

		} else {
			$message = "<div class='notification-box warning-notification-box'><p>Nada Mención.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
		}
	}
  
} else { // Form has not been submitted.
	$email = "";
}

// if (isset($_POST['resend_code'])) { // Form has been submitted.
// 
// 	$email = trim($_POST['email']);
// 	
// 	if (DEMO_MODE == 'ON') {
// 		$message = "<div class='notification-box warning-notification-box'><p>Sorry, you can't do that while demo mode is enabled.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
// 	} else {
// 	  	if (!empty($email)) {
// 			Reset_Password::check_resend_code($email);
// 
// 		} else {
// 			$message = "<div class='notification-box warning-notification-box'><p>Nothing Entered.</p><a href='#' class='notification-close warning-notification-close'>x</a></div><!--.notification-box .notification-box-warning end-->";
// 		}
// 	}
//   
// } else { // Form has not been submitted.
// 	$email = "";
// 	$hash = "";
// }

?>

<?php $page_title = "Olvidé mi contraseña"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1>Olvidé mi contraseña</h1>
	<br><br>
</div>

<div id="message"><?php echo output_message($message); ?></div>

<?php if((!isset($_GET['email'])) && (!isset($_GET['hash'])) ) : ?>
<div class="row-fluid">
	<form action="reset_password.php" method="post" >
		<div class="span6 center">
			<div class="span12">
		      <h3>Restablecer contraseña</h3>
			</div>
			<div class="span12">
		      <input type="text" class="span4" name="email" required="required" placeholder="Email " value="<?php echo htmlentities($email); ?>" />
			</div>
			<div class="span12">
		      <input type="text" class="span4" name="hash" required="required" placeholder="Codigo de Confirmarcion" value="<?php echo htmlentities($hash); ?>" />
			</div>
			<div class="span12">
				<input class="btn btn-primary" type="submit" name="submit" value="Restablecer contraseña" />
			</div>
		</div>
	</form>
	<form action="reset_password.php" method="post" >
		<div class="span6 center">
			<div class="span12">
		      <h3>Enviar Código</h3>
			</div>
			<div class="span12">
		      <input type="text" class="span4" name="email" required="required" placeholder="Email" value="<?php echo htmlentities($email); ?>" />
			</div>
			<div class="span12">
				<input class="btn btn-primary" type="submit" name="send_code" value="Enviar Confirmar Código" />
			</div>
		</div>
	</form>
</div>
<?php endif ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>