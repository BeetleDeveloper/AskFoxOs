<?php


require_once("includes/inc_files.php");

if($session->is_logged_in()) {$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);}
$profile_data = Profile::find_by_id($user->user_id);
$location = "contact.php";

$current_page = "contact";

if (isset($_POST['submit'])) { 
		
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$subject = trim($_POST['subject']);
	$mess = trim($_POST['mess']);
	
	if ((!$name == '') && (!$email == '') && (!$subject == '') && (!$mess == '')) {
		// $message = "Success";
		
		$headers = "From: {$email}\r\n".
		"Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		$current_ip = $_SERVER['REMOTE_ADDR'];
		
		$html_message = nl2br($mess);
		
		$sub = "CONTACT FORM: ".$subject;
		
		//send email
		$to = SITE_EMAIL;
		$the_mess = "IP: ".$current_ip." <br />
				FROM: ".$email."<br />
				MESSAGE: <p />"."$html_message";
					
		mail($to, $sub, $the_mess, $headers);
	
		$message = "<div class='notification-box success-notification-box'><p>Thank you, your message has been sent successfully.</p><a href='#' class='notification-close success-notification-close'>x</a></div><!--.notification-box .notification-box-success end-->";		
		
	} else {
		$message = "<div class='notification-box error-notification-box'><p>Please complete all required fields.</p><a href='#' class='notification-close error-notification-close'>x</a></div><!--.notification-box .notification-box-error end-->";
	}
  
} else {
  $name = "";
  $email = "";
  $subject = "";
  $mess = "";
  $message = "";
}

?>

<?php $page_title = "Nosotros"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1>Póngase en contacto con nosotros</h1>
	<br><br>
</div>

	<?php echo output_message($message); ?>

	<div class="container-fluid" id="contact">
	<div class="container dark">
		<div class="span5">			
			<form action="<?php echo $location; ?>" method="post" >
				  <div class="control-group">
				    <label class="control-label" for="inputName">Nombre</label>
				    <div class="controls">
				      <input type="text" name="name" class="span4" required="required" placeholder="Nombre Completo" value="<?php echo htmlentities($name); ?>" />
				    </div>
				  </div>
				  <div class="control-group">
				    <label class="control-label" for="inputEmail">Email</label>
				    <div class="controls">
				      <input type="email" name="email" class="span4" required="required" placeholder="Email" value="<?php echo htmlentities($email); ?>" />
				    </div>
				  </div>
				   <div class="control-group">
				    <label class="control-label" for="inputEmail">Asunto</label>
				    <div class="controls">
				      <input type="text" name="subject" class="span4" required="required" placeholder="Asunto" value="<?php echo htmlentities($subject); ?>" />
				    </div>
				  </div>
				  <div class="control-group">
				    <label class="control-label" for="inputContact">Mensaje</label>
				    <div class="controls">
				     <textarea type="text" class="span4" name="mess" placeholder="Tu mensaje" required="required"><?php echo htmlentities($mess); ?></textarea>
				    </div>
				  </div>
				  <div class="control-group">
				    <div class="controls">
				      <button type="submit" class="btn btn-danger">Enviar</button>
				    </div>
				  </div>
			</form>
		</div>
	
		<div class="span4">	
				<h2>AskFoxOS</h2>
				
				<p>Toda la libertad de hacer tus preguntas!</p>
				 
				<address>
				  <strong>Administrador (superUsuario)</strong><br>
				  <a href="mailto:jdladnazaball@gmail.com">jdladnazaball@gmail.com"</a>
				</address>		
		</div>		
	</div>
</div>
<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>