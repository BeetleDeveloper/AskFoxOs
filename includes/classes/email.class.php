<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) exit('No direct access allowed.');



$stylesheet = "
	<style type='text/css'>
		body {background: #f2f2f2}
		.email_container {width: 700px; margin: 20px auto 0; background: #fff}
		.email_content {width: 100%; border: #ddd 1px solid; padding: 5px 15px 5px; background: #fff}
		.logo {padding-top:6px}
	</style>
";

$header = "
	<body>
	<div class='email_container'>
		<div class='email_content'>
			<img src='".IMAGES."email_logo.jpg' class='logo' />
			<p />
";

$footer = "";

class Email{
	
	public function send_email($to, $from, $subject, $message) {
 		$headers = 'From: '.$from."\r\n".
		// Uncomment below line to have all emails Blind Caron Coppied to the site owners email address. (Warning: Site owner will recieve every email sent from the site including emails containing unencrypted passwords.)
		// 'Bcc: '.SITE_OWNER."\r\n" .
		"Content-Type: text/html; charset=ISO-8859-1\r\n" .
		'X-Mailer: PHP/' . phpversion();
		
		//send email
		mail($to, $subject, $message, $headers);
		
	}

	public function email_template($template_name, $plain_password="", $username="", $email="", $hash="", $message="") {
		global $stylesheet;
		global $header;
		global $footer;
		
		if ($template_name == "registration_success") {
			// registration success template.
			$message = "
			{$stylesheet}
			{$header}
					<p>Su cuenta en <a href='".WWW."'>".SITE_NAME."</a> ha sido creado con éxito.</p>
					<p>Username: {$username}
					<br />
					Password: {$plain_password} (Encrypted in our database.)</p>
			{$footer}";
			return $message;
		} else if ($template_name == "registration_activation") {
			// template asking the user to activate their account.
			$message = "
			{$stylesheet}
			{$header}
					<p>Su cuenta en at <a href='".WWW."'>".SITE_NAME."</a> ha sido creado con éxito.</p>
					<p>Username: {$username}
					<br />
					Password: {$plain_password} (Encrypted in our database.)</p>
					<p>Sin embargo, usted todavía necesita para activar su cuenta, lo que se puede hacer haciendo clic en el siguiente enlace: <br />
					<a href='".WWW."activate.php?email={$email}&hash={$hash}'>Activar cuenta</a></p>
					<p>Si no puede hacer clic en el enlace anterior todavía se puede activar su cuenta a continuación.</p>
					<p>URL: ".WWW."activate.php <br />
					Codigo de Confirmacion: {$hash}</p>
			{$footer}";
			return $message;
		} else if ($template_name == "resend_activation_code") {
			// template asking the user to activate their account.
			$message = "
			{$stylesheet}
			{$header}
					<p>Usted puede activar su cuenta haciendo clic en el siguiente enlace: <br />
					<a href='".WWW."activate.php?email={$email}&hash={$hash}'>Activar cuenta</a></p>
					<p>Si no puede hacer clic en el enlace anterior todavía se puede activar su cuenta a continuación.</p>
					<p>URL: ".WWW."activate.php <br />
					Codigo de Confirmacion: {$hash}</p>
			{$footer}";
			return $message;
		} else if ($template_name == "update_all_account_settings") {
			// all account settings updated.
			$message = "
			{$stylesheet}
			{$header}
					<p>Your account settings have been changed.</p>
					<p>Password: {$plain_password} (Encrypted in our database.)</p>
			{$footer}";
			return $message;
		} else if ($template_name == "update_account_settings") {
			// all account settings apart from new password updated.
			$message = "
			{$stylesheet}
			{$header}
					<p>Configuración de su cuenta se han cambiado.</p>
			{$footer}";
			return $message;
		} else if ($template_name == "reset_password") {
			// template asking the user to activate their account.
			$message = "
			{$stylesheet}
			{$header}
					<p>Una nueva contraseña ha sido solicitada de su cuenta en <a href='".WWW."'>".SITE_NAME."</a>.</p>
					<p>Sin embargo tendrá que confirmar esta acción haciendo clic en el siguiente enlace: <br />
					<a href='".WWW."reset_password.php?email={$email}&hash={$hash}'>Restablecer contraseña</a></p>
					<p>Si no puede hacer clic en el enlace de arriba todavía puede solicitar una nueva contraseña.</p>
					<p>URL: ".WWW."reset_password.php <br />
					Codigo de Confirmacion: {$hash}</p>
			{$footer}";
			return $message;
		} else if ($template_name == "new_password") {
			// template asking the user to activate their account.
			$message = "
			{$stylesheet}
			{$header}
					<p>Su nueva contraseña de la cuenta es: {$plain_password}</p>
			{$footer}";
			return $message;
		} else if ($template_name == "resend_password_reset_code") {
			// template asking the user to activate their account.
			$message = "
			{$stylesheet}
			{$header}
					<p>URL: ".WWW."reset_password.php <br />
					Codigo de Confirmacion: {$hash}</p>
			{$footer}";
			return $message;
		} else if ($template_name == "account_lock") {
			// template asking the user to activate their account.
			$message = "
			{$stylesheet}
			{$header}
					<p>Conforme a lo solicitado configuración de su cuenta se han bloqueado.<p />
					Codigo de Desbloqueo: {$hash}</p>
			{$footer}";
			return $message;
		} else if ($template_name == "resend_unlock_code") {
			// template asking the user to activate their account.
			$message = "
			{$stylesheet}
			{$header}
					<p>Codigo de Desbloqueo: {$hash}</p>
			{$footer}";
			return $message;
		} else if ($template_name == "custom_email") {
			// custom email template for email_user.php.
			$message = "
			{$stylesheet}
			{$header}
					{$message}
			{$footer}";
			return $message;
		}
		
	} // Email_Template end.

} // Class end.