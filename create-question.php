<?php



require_once("includes/inc_files.php");

if($session->is_logged_in()) {$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);}else{redirect_to("login");}
$profile_data = Profile::find_by_id($user->user_id);
$location = "create-question.php";

$current_page = "create-question";

?>

<?php $page_title = "Crear una pregunta"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1><?php echo $page_title; ?></h1>
</div>
<p>*Favor mantener un lenguaje cortés, civilizado y educado al formular sus preguntas.<p>
<hr>
<div id="message"><?php echo output_message($message); ?></div>

<div class="row-fluid">
	<div class="span6">
		<label>Titulo</label>
		<input type="text" name="title" id="title" class="span12" required="required" value="" />
	</div>
	<div class="span6">
		<label>Categoria</label>
		<select name="category" id="category" class="span12 chzn-select" required="required" value="">
			<?php foreach(Question::get_categories() as $category){ ?>
			<option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="row-fluid">
	<label>Su pregunta</label>
	<div class="span12" style="margin-left: 0;">
		<textarea type="text" class="span12" style="height:111px;" name="question" id="question" required="required"></textarea>
	</div>
</div>

<div class="form-actions" style="text-align: center;margin: 10px -10px -10px;">
	<button class="btn btn-primary" name="ask_question" id="ask_question">Preguntar ahora!</button>
</div>

<script>
$(document).ready(function(){
	$("#ask_question").click(function(){
		var title = $("#title").val();
		var category = $("#category option:selected").val();
		var question = $("#question").val();
		if(title == "" || question == ""){
			$("#title").addClass('error').removeClass('success');
			$("#question").addClass('error').removeClass('success');
			$("#message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Por favor, complete todos los campos necesarios para continuar.</div>");
		} else {
			$("#title").removeClass('error').addClass('success');
			$("#question").removeClass('error').addClass('success');
			$("#message").html("");
			$.ajax({
				type: "POST",
				url: WWW+"data.php",
				data: "page=create_question&title="+title+"&category="+category+"&question="+question,
				success: function(data){
					if(data == "failure"){
						update_msg();
					} else {
						window.location.replace(data);
					}
				}
			});
		}
	});
});

</script>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>