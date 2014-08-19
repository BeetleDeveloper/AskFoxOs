<?php require_once("includes/inc_files.php"); 

$current_page = "question";

if($session->is_logged_in()){
	$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
$profile_data = Profile::find_by_id($user->user_id);
}

if(isset($_GET['id'])){
	$question_id = clean_value($_GET['id']);
	$question_data = Question::get_question_listing("id",$question_id);
	if(empty($question_data)){
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>No se encontró pregunta alguna.</div>");
		redirect_to(WWW."search");
	}
	$question_data = $question_data[0];
	$category_name = Question::get_category_name($question_data->category_id);
	if(SEO_URLS == "ON"){
		$category_seo = str_replace(array(' ','.'), "-", strtolower($category_name));
		$title_seo = str_replace(array(' ','.'), "-", preg_replace("/[^0-9a-z_ ,.:(){}-]/i", "", strtolower($question_data->title)));
		$subs = str_replace("http://".$_SERVER['HTTP_HOST'], "", WWW);
		echo $_SERVER['REQUEST_URI'];
		if($_SERVER['REQUEST_URI'] == $subs."question?id=".clean_value($_GET['id'])){
			// redirects the user to the seo friendly url, if they are using the direct link.
			redirect_to(WWW.$category_seo."/".$title_seo.".html");
		}
	}	
	$author = User::get_author($question_data->user_id);
	if($user->user_id == $question_data->user_id){
		$owner = true;
	}
	if(!isset($owner)){
		$owner = false;
	}
	$answers = Question::get_answers($question_id);
} else if(isset($_GET['title'])){
	$category_seo = str_replace(array(' ','.'), "-", strtolower($_GET['category']));
	$title_seo = str_replace(array(' ','.'), "-", preg_replace("/[^0-9a-z_ ,.:(){}-]/i", "", strtolower($_GET['title'])));
	
	$question_data = Question::get_question_listing("name",null,$title_seo,$category_seo);
	if(empty($question_data)){
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>No se encontró pregunta alguna.</div>");
		redirect_to(WWW."search");
	}
	$question_data = $question_data[0];
	$category_name = Question::get_category_name($question_data->category_id);
	$author = User::get_author($question_data->user_id);
	$question_id = $question_data->id;
	if($user->user_id == $question_data->user_id){
		$owner = true;
	}
	if(!isset($owner)){
		$owner = false;
	}
	$answers = Question::get_answers($question_id);
} else {
	$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>No se encontró pregunta alguna.</div>");
	redirect_to(WWW."search");
}

?>

<?php $page_title = $question_data->title." - Question"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1><?php echo $question_data->title; ?></h1>
	<?php if($owner == true){ ?><a href="#edit_question" role="button" class="btn btn-primary" style="margin-bottom: 10px;" data-toggle="modal">Editar Pregunta</a><?php } ?>
	<?php if($user->staff == 1){ ?><a href="<?php echo WWW.ADMINDIR."question?id=".$question_data->id; ?>" class="btn btn-danger" style="margin-bottom: 10px;">Funciones de Moderador</a><?php } ?>
</div>

<?php echo output_message($message); ?>

<?php if($session->is_logged_in()){ ?>
<script>
$(document).ready(function(){
	$("#add_answer_btn").click(function(){
		var answer = $("#answer");
		var comment = escape(answer.val());
		if(comment){
			$.ajax({
				type: "POST",
				url: WWW+"data.php",
				data: "page=question&add_answer=<?php echo $question_data->id; ?>&answer="+comment,
				success: function(message){
					if(message != "success"){
						$(".answer_message").html(message);
						$("#add_answer_btn").html("Add Answer");
					} else {
						location.reload();
					}
				},
				beforeSend: function(){
					$("#add_answer_btn").html("Cargando...");
				}
			});
		} else {
			answer.addClass("error");
			$(".answer_message").html('<div class="alert alert-error" style="margin-bottom: 9px;"><button type="button" class="close" data-dismiss="alert">×</button>Por favor introduce una respuesta para nosotros agregar.</div>');
		}
	});
	var wall_message = $("#answer");
	var max_length = wall_message.attr('maxlength');
	if (max_length > 0) {
		wall_message.bind('keyup', function(e){
			length = new Number(wall_message.val().length);
			counter = max_length-length;
			$("#counter").text(counter);
		});
	}
});

function thumbs(id,type){
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=question&rate_answer="+id+"&type="+type,
		success: function(message){
			if(message != "failure"){
				$("#answer_"+id+" .rating .thumbs_"+type+"_count").html(message);
				message = '<div class="vote_message success">Gracias!</div>';
			} else {
				message = '<div class=\"vote_message failure\">Ya ha votado!</div>';
			}
			$("#answer_"+id+" .rating .vote_message").remove();
			$("#answer_"+id+" .rating").append(message);
		}
	});
}
function main_thumbs(type){
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=question&rate_question=<?php echo $question_data->id; ?>&type="+type,
		success: function(message){
			if(message != "failure"){
				$(".right_side .rating .thumbs_"+type+"_count").html(message);
				message = '<div class="vote_message success">Gracias!</div>';
			} else {
				message = '<div class=\"vote_message failure\">Ya ha votado!</div>';
			}
			$(".right_side .rating .vote_message").remove();
			$(".right_side .rating").append(message);
		}
	});
}

function edit_answer(id){
	$("#edit_answer #confirm").attr('onclick','confirm_edit_answer(\''+id+'\');');
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=question&get_answer="+id,
		success: function(message){
			$("#edit_answer #textarea").html(message);
		}
	});
	$('#edit_answer').modal('show');
}
function confirm_edit_answer(id){
	var message = $("#edit_answer #textarea").val();
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=question&confirm_edit="+id+"&message="+message,
		success: function(message){
			if(message == "success"){
				location.reload();
			} else {
				$("#edit_answer .message").html(message);
				$("#edit_answer #confirm").html("Confirm");
			}
		},
		beforeSend: function(){
			$("#edit_answer #confirm").html("Cargando...");
		}
	});
}
function report_answer(id){
	$("#report_answer #confirm").attr('onclick','confirm_report_answer(\''+id+'\');');
	$('#report_answer').modal('show');
}
function confirm_report_answer(id){
	var reason = $("#report_answer #reason").val();
	if(reason == ""){
		$("#report_answer .message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Por favor introduce una razón para reportar esta respuesta.</div>");
	} else {
		$.ajax({
			type: "POST",
			url: WWW+"data.php",
			data: "page=question&report_answer="+id+"&reason="+reason,
			success: function(message){
				if(message == "success"){
					location.reload();
				} else {
					$("#report_answer .message").html(message);
					$("#report_answer #confirm").html("Report");
				}
			},
			beforeSend: function(){
				$("#report_answer #confirm").html("Working...");
			}
		});
	}
}
function report_question(id){
	$("#report_question #confirm").attr('onclick','confirm_report_question(\''+id+'\');');
	$('#report_question').modal('show');
}
function confirm_report_question(id){
	var reason = $("#report_question #reason").val();
	if(reason == ""){
		$("#report_question .message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Por favor introduce una razón para reportar esta respuesta.</div>");
	} else {
		$.ajax({
			type: "POST",
			url: WWW+"data.php",
			data: "page=question&report_question="+id+"&reason="+reason,
			success: function(message){
				if(message == "success"){
					location.reload();
				} else {
					$("#report_question .message").html(message);
					$("#report_question #confirm").html("Report");
				}
			},
			beforeSend: function(){
				$("#report_question #confirm").html("Working...");
			}
		});
	}
}

</script>
<div id="edit_answer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Editar pregunta</h3>
	</div>
	<div class="modal-body">
		<div class="message"></div>
		<div class="row-fluid">
			<div class="span12">
		        <textarea class="span12" style="height: 80px;" id="textarea" maxlength="250" required="required"></textarea>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
		<button class="btn btn-danger" id="confirm">Confirmar</button>
	</div>
</div>
<div id="report_answer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Reportar pregunta</h3>
	</div>
	<div class="modal-body">
		<div class="message"></div>
		<div class="row-fluid">
			<div class="span12">
		        <textarea class="span12" style="height: 80px;" id="reason" maxlength="250" required="required" placeholder="Introduzca su razón para reportar esta respuesta"></textarea>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
		<button class="btn btn-danger" id="confirm">Reportar</button>
	</div>
</div>
<div id="report_question" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Informar pregunta</h3>
	</div>
	<div class="modal-body">
		<div class="message"></div>
		<div class="row-fluid">
			<div class="span12">
		        <textarea class="span12" style="height: 80px;" id="reason" maxlength="250" required="required" placeholder="Introduzca su razón para reportar esta respuesta"></textarea>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
		<button class="btn btn-danger" id="confirm">Reportar</button>
	</div>
</div>
<?php } //end login check ?>

<div class="question">
	
	<div class="row-fluid">

		<div class="span9">
			<div class="question_container">
				<?php echo nl2br($question_data->description); ?>
				<div class="quote_99"></div>
			</div>
			<span>Esta pregunta fue publicada <strong><?php echo ago_time($question_data->created); ?></strong> en <strong><?php echo $category_name; ?></strong> y ha recibido <strong><?php echo $question_data->view_count; ?></strong> <?php if($question_data->view_count == 1){echo "vista";}else{echo "vistas";}?> <?php if($session->is_logged_in()){ ?> - <span class="label label-primary" href="#" role="button" data-toggle="modal" onclick="report_question('<?php echo $question_data->id; ?>'); return false;">Reportar pregunta!</span></span><?php } ?>
			<br /> El enlace directo a esta pregunta es: <code><?php echo WWW."question.php?id=".$question_id; ?></code>
		</div>
		<div class="span3 right_side">
			<div class="rating_container">
				<div class="rating">
					<span class="thumbs_up_count"><?php echo $question_data->thumbs_up; ?></span>
					<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="main_thumbs('up');"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_up.png" width="26" height="26" alt="Tumbs Up" class="button"></button>
					<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="main_thumbs('down');"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_down.png" width="26" height="26" alt="Tumbs Down" class="button"></button>
					<span class="thumbs_down_count"><?php echo $question_data->thumbs_down; ?></span>
				</div>
			</div>
			<hr />
			<label style="font-size: 12px;"><em>Esta pregunta fue publicada por:</em></label>
			<a href="<?php echo WWW."profile?username=".$author[0]->username; ?>"><label style="font-size: 20px;"><?php echo $author[0]->first_name." ".$author[0]->last_name." (".$author[0]->username.")"; ?></label></a>
			<label><?php echo $author[0]->country; ?></label>
			<label><?php echo Question::get_badge($author[0]->questions_answered); ?></label>
		</div>
		<div class="clearfix"></div>
		
	</div>
	
	<hr />
	
	<div class="row-fluid">
		<div class="span7">
			<?php if(empty($answers)){ ?>
				<?php if($session->is_logged_in()){ ?>
				<h3>Responder a esta pregunta</h3>

				<div class="answer_container">
					<div class="row-fluid">
						<div class="span12">
							<div class="answer_message"></div>
							<textarea class="span12" style="height: 80px;" id="answer" maxlength="250" required="required" placeholder="Por favor escriba su respuesta"></textarea>
							<span id="counter">250</span> Caracteres Restantes
							<button class="btn btn-success right" id="add_answer_btn">Añadir respuesta</button>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
				<?php } ?>
				<br />
				<strong>Actualmente no hay respuestas para esta pregunta.</strong>
			<?php } else { ?>
			<?php $top_answer = array_shift($answers); if(!empty($top_answer)){ ?>
			<h3>Top respuesta</h3>
			<div id="answer_<?php echo $top_answer->id; ?>" class="answer_container top_answer">
				<a href="<?php echo WWW; ?>profile?username=<?php echo $username = User::find_username_by_id($top_answer->user_id); ?>"><h4><?php echo $username ?></h4></a>
				<div class="rating">
					<span class="thumbs_up_count"><?php echo $top_answer->thumbs_up; ?></span>
					<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $top_answer->id; ?>','up');"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_up.png" width="26" height="26" alt="Tumbs Up" class="button"></button>
					<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $top_answer->id; ?>','down');"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_down.png" width="26" height="26" alt="Tumbs Down" class="button"></button>
					<span class="thumbs_down_count"><?php echo $top_answer->thumbs_down; ?></span>
				</div>
				<p><?php echo nl2br($top_answer->message); ?></p>
				<span style="font-size: 12px; font-style: italic;">Esta respuesta fue publicada hace <strong><?php echo ago_time($top_answer->created); ?></strong><?php if($session->is_logged_in()){ ?> - <a href="#" role="button" data-toggle="modal" onclick="report_answer('<?php echo $top_answer->id; ?>'); return false;">Reportar</a> <?php if($user->user_id == $top_answer->user_id){ echo "- <a href=\"#\" style=\"padding:0;margin0;\" onclick=\"edit_answer('".$top_answer->id."'); return false; \">Editar</a>"; } ?></span><?php } ?>
			</div>
			<?php } ?>
			<!-- Answer Question -->
			
			<?php if($session->is_logged_in()){ ?>
			<br>
			<h3>Responder a esta pregunta</h3>
			
			<div class="answer_container">
				<div class="row-fluid">
					<div class="span12">
						<div class="answer_message"></div>
						<textarea class="span12" style="height: 80px;" id="answer" maxlength="250" required="required" placeholder="Por favor escriba su respuesta"></textarea>
						<span id="counter">250</span> Caracteres Restantes
						<button class="btn btn-success right" id="add_answer_btn">Añadir respuesta</button>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			<?php } ?>
			<!-- Regular Answers -->
			<?php if(!empty($answers)){ ?>
			<h3>Respuestas</h3>
			<?php foreach($answers as $answer){ ?>
			<div id="answer_<?php echo $answer->id; ?>" class="answer_container">
				<a href="<?php $username = User::find_username_by_id($answer->user_id); echo WWW."profile?username=".$username; ?>"><h4><?php echo $username ?></h4></a>
				<div class="rating">
					<span class="thumbs_up_count"><?php echo $answer->thumbs_up; ?></span>
					<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $answer->id; ?>','up');"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_up.png" width="26" height="26" alt="Tumbs Up" class="button"></button>
					<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $answer->id; ?>','down');"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_down.png" width="26" height="26" alt="Tumbs Down" class="button"></button>
					<span class="thumbs_down_count"><?php echo $answer->thumbs_down; ?></span>					
				</div>
				<p><?php echo nl2br($answer->message); ?></p>
				<span style="font-size: 12px; font-style: italic;">Esta respuesta fue publicada hace<strong><?php echo ago_time($answer->created); ?></strong><?php if($session->is_logged_in()){ ?> - <a href="#" role="button" data-toggle="modal" onclick="report_answer('<?php echo $answer->id; ?>'); return false;">Reportar</a> <?php if($user->user_id == $answer->user_id){ echo "- <a href=\"#\" style=\"padding:0;margin0;\" onclick=\"edit_answer('".$answer->id."'); return false; \">Edit</a>"; } ?></span><?php } ?>
			</div>
			<?php } } ?>
			<?php } ?>
		</div>
		
		<div class="span5">
			<!-- <h3>Related Questions</h3>
			
			<a href="#">
				<div class="link_container">
					Question Title Here <span>- 0 Answers, posted NUM minutes ago<span>
				</div>
			</a>
			<a href="#">
				<div class="link_container">
					Question Title Here
				</div>
			</a>
			<a href="#">
				<div class="link_container">
					Question Title Here
				</div>
			</a>
			<a href="#">
				<div class="link_container">
					Question Title Here
				</div>
			</a> -->
			
			<!-- latest -->
			<?php $latest = Question::get_latest($question_data->id); if($latest){ ?>
			
			<h3>Ùltimas Preguntas</h3>
			
			<?php foreach($latest as $question){ ?>
			<a href="<?php echo WWW."question?id=".$question->id; ?>">
				<div class="link_container">
					<?php echo $question->title ?> <span>- <?php echo $question->answer_count; if($question->answer_count == 1){echo " respuesta";}else{echo " respuestas";} ?> - <?php echo ago_time($question->created); ?> - <span style="color: rgb(103, 177, 23);"><?php echo $question->thumbs_up; ?></span> | <span style="color: rgb(207, 0, 0);"><?php echo $question->thumbs_down; ?></span><span>
				</div>
			</a>
			
			<?php } } ?>

		</div>
	</div>
	
</div>

<?php if($owner == true){ ?>
<script>
function edit_question(){
	var message = $("#edit_question #answer").val();
	$.ajax({
		type: "POST",
		url: WWW+"data.php",
		data: "page=question&edit_question=<?php echo $question_data->id; ?>&message="+message,
		success: function(data){
			if(data == "success"){
				location.reload();
			} else {
				$("#edit_question .message").html(data);
				$("#edit_question #confirm").html("Confirmar Actualización");
			}
		},
		beforeSend: function(){
			$("#edit_question #confirm").html("Cargando...");
		}
	});
}
</script>
<div id="edit_question" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Editar Pregunta</h3>
	</div>
	<div class="modal-body">
		<div class="message"></div>
		<div class="row-fluid">
			<div class="span12">
		        <input type="text" class="span12" id="question" disabled="disabled" value="<?php echo htmlentities($question_data->title); ?>" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
		        <textarea class="span12" style="height: 80px;" id="answer" maxlength="250" required="required"><?php echo htmlentities($question_data->description); ?></textarea>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
		<button class="btn btn-danger" id="confirm" onclick="edit_question();">Confirmar Actualización</button>
	</div>
</div>
<?php } ?>
<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>