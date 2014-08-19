<?php


require_once("includes/inc_files.php");

if($session->is_logged_in()) {
  redirect_to("index");
}

if (isset($_POST['submit'])) { 
	
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	
	if ((!$username == '') && (!$password == '')) {
		$current_ip = $_SERVER['REMOTE_ADDR'];
		$remember_me = trim($_POST['remember_me']);
		$return = User::check_login($username, $password, $current_ip, $remember_me);
		if($return == "false"){
			redirect_to("login");
		} else {
			redirect_to($return);
		}
		
	} else {
		$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Por favor, rellene todos los campos obligatorios.</div>";
	}
  
} else { 
  $username = "";
  $password = "";
}

$current_page = "login";

?>

<?php $page_title = "Iniciar sesión"; require_once("includes/themes/".THEME_NAME."/header-login.php"); ?>

<div id="message"><?php echo output_message($message); ?></div>

<form action="login" method="post" >
	<div class="row-fluid">
		<div class="span12 center">
			<div class="row-fluid">
				<div class="span12">
			        <input type="text" class="span4" name="username" required="required" placeholder="Username" value="<?php echo htmlentities($username); ?>" />
				</div>
			</div>
			<br>
			<div class="row-fluid">
				<div class="span12">
			        <input type="password" class="span4" name="password" required="required" placeholder="Password" value="<?php echo htmlentities($password); ?>" />
				</div>
			</div><br><br>
			<div class="row-fluid">
				<div class="span12">
					<input type="checkbox" name="remember_me" value="yes" />
					<span>Acuerdate de mi? (Uses Cookies)</span>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<a href="reset_password.php">¿Olvidaste tu contraseña?</a>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<input class="btn btn-primary" type="submit" name="submit" value="Iniciar sesión" />
				</div>
			</div>
		</div>
	</div>
</form>

<?php if(OAUTH == "ON"){ ?>
<hr />
	
<div class="row-fluid">
	<div class="span12 center">
		<div class="span12" style="margin-bottom: 10px;">
			<a href="<?php echo WWW; ?>auth/facebook" class="zocial facebook">Iniciar sesión con Facebook</a>
			<a href="<?php echo WWW; ?>auth/twitter" class="zocial twitter">Iniciar sesión con Twitter</a>
		</div>
	</div>
</div>

<?php } ?>


<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>