<?php
require_once("includes/inc_files.php");



$current_page = "create_question";

if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['user_id']);
$profile_data = Profile::find_by_id($user->user_id);
} else {
	redirect_to("login");
}

if(isset($_POST['submit'])){
	$title = clean_value($_POST['title']);
	$description = clean_value($_POST['description']);
	
	if($title == "" || $description == ""){
		$message = "<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please Complete All Required Fields.</div>";
	} else {
		echo "submitted";
	}
} else {
	$title = "";
	$description = "";
}


?>
<?php require_once("includes/templates/header.php"); ?>

<div class="hero-unit">
	<h3>Hacer Pregunta!</h3>
<br>
<p>*Favor mantener un lenguaje cortés, civilizado y educado al formular sus preguntas.<p>
<p>*Favor no borre las preguntas de los demas, coloque sus preguntas al final del documento sin modificar las de los demas.<p>
	<hr />

	<?php echo output_message($message); ?>

	<form action="ask_question" method="post" class="form-horizontal">
	
	<div class="row-fluid">
		<div class="span12">
			<input type="text" name="title" class="span12" required="required" placeholder="Titulo" value="<?php echo htmlentities($title); ?>" />
		</div>
	</div>
	<br />
	<div class="row-fluid">
		<div class="span12">
			<textarea type="text" class="span12" style="height:111px;" name="description" placeholder="Su pregunta" required="required"><?php echo htmlentities($description); ?></textarea>
		</div>
	</div>

	<div class="clear"></div>
	<div class="form-actions" style="text-align: center;">
		<input class="btn btn-primary" type="submit" name="submit" value="Ask Question" />
	</div>

	</form>

</div>

<?php require_once("includes/templates/footer.php"); ?>