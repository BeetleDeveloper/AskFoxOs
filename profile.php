<?php require_once("includes/inc_files.php"); 

$current_page = "profile";

if(isset($_GET['username'])){
	$username = clean_value($_GET['username']);
	if(SEO_URLS == "ON"){
		$username = str_replace(array(' ','.'), "-", preg_replace("/[^0-9a-z_ ,.-]/i", "", strtolower($username)));
		$subs = str_replace("http://".$_SERVER['HTTP_HOST'], "", WWW);
		if($_SERVER['REQUEST_URI'] == $subs."profile.php?username=".$username){
			// redirects the user to the seo friendly url, instead of the search generated one.
			redirect_to(WWW."profile/".$username.".html");
		}
	}	
	$user_data = User::find_profile_data($username, "username");
	$profile_data = Profile::find_by_id($user_data->user_id);
	$profile_messages = Profile::get_profile_messages("unread", $profile_data->user_id);
	if($session->is_logged_in()) {
		$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);

		if($user->user_id == $user_data->user_id){
			$owner = true;
		} else {
			$owner = false;
		}

	} 
else {
	$user_id = "";
	}
	
	if(!isset($owner)){
		$owner = false;
	}
} else {
	redirect_to("profiles.php");
}

?>

<?php $page_title = $user_data->first_name." ".$user_data->last_name."Perfil "; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div id="message"><?php echo output_message($message); ?></div>

<script>
$(document).ready(function(){
	$("#submit_wall_message").click(function(){
		var wall_message = $("#wall_message");
		var message = escape(wall_message.val());
		if(message){
			$.ajax({
				type: "POST",
				url: "data.php",
				data: "page=profile&profile=<?php echo $profile_data->user_id; ?>&message="+message,
				success: function(html){
					if(html == "false"){
						$("#submit_wall_message").html("Agregar Mensaje");
					} else {
						wall_message.val("");
						$("#message").html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>Gracias, que ha publicado su mensaje en <?php echo $user_data->username; ?>\'s wall.</div>');
						wall_message.removeClass("error");
						$("#wall_message_counter").html("250");
						$("#profile_messages").html(html);
						$("#submit_wall_message").html("Agregar mensaje");
					}
				},
				beforeSend: function(){
					$("#submit_wall_message").html("Cargando...");
				}
			});
		} else {
			wall_message.addClass("error");
			$("#message").html('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Por favor, introduzca un mensaje de poder escribir en el <?php echo $user_data->username; ?>\'s wall.</div>');
		}
	});
	var wall_message = $("#wall_message");
	var max_length = wall_message.attr('maxlength');
	if (max_length > 0) {
		wall_message.bind('keyup', function(e){
			length = new Number(wall_message.val().length);
			counter = max_length-length;
			$("#wall_message_counter").text(counter);
		});
	}
	
});

function display_messages(type){
	var profile = <?php echo $profile_data->user_id; ?>;
	var limit = 5; // 5 for testing. This will be dynamically chosen by the user.
	var wall_message = $("#wall_message");
	// console.log(type);
	if(type == "unread"){
		$("#message_pills #all").removeClass("active");
		$("#message_pills #unread").addClass("active");
		limit = "";
	} else {
		$("#message_pills #all").addClass("active");
		$("#message_pills #unread").removeClass("active");
	}
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=profile&profile=<?php echo $profile_data->user_id; ?>&get="+type+"&limit="+limit,
		success: function(html){
			if(html != "false"){
				$("#profile_messages").html(html);
			}
		}
	});
}

function edit_message(id){
	$("#edit_comment #confirm").attr('onclick','confirm_edit('+id+');');
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=profile&get_message="+id,
		success: function(message){
			$("#edit_comment #textarea").html(message);
		}
	});
	$('#edit_comment').modal('show');
}
function confirm_edit(id){
	var message = $("#edit_comment #textarea").val();
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=profile&confirm_edit="+id+"&message="+message,
		success: function(){
			location.reload();
		},
		beforeSend: function(){
			$("#edit_comment #confirm").html("Cargando...");
		}
	});
}

function delete_message(id){
	// console.log("Delete:"+id);
	$("#confirm_delete #confirm").attr('onclick','confirm_delete('+id+');');
	$('#confirm_delete').modal('show');
}
function confirm_delete(id){
	// console.log("Confmed:"+id);
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=profile&delete_message=<?php echo $profile_data->user_id; ?>&id="+id,
		success: function(data){
			if(data == "failure"){
				location.reload();
			} else {
				$("#message").html(data);
				$("#message"+id).remove();
				$('#confirm_delete').modal('hide');
			}
		},
		beforeSend: function(){
			$("#confirm_delete #confirm").html("Cargando...");
		}
	});
}

function update_profile(){
	var profile_message = $("#edit_profile #profile_message").val();
	var about_me = $("#edit_profile #textarea").val();
	if(profile_message == ""){
		$("#edit_profile #message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Por favor, complete todos los campos obligatorios.</div>")
	} else {
		$.ajax({
			type: "POST",
			url: "data.php",
			data: "page=profile&update_profile=<?php echo $profile_data->user_id; ?>&profile_message="+profile_message+"&about_me="+about_me,
			success: function(data){
				if(data == "success"){
					// $("#edit #update_message").html("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Your updates to this project have been saved. Please refresh the page to see them.</div>");
					// $("#edit #submit").html("Update Project");
					location.reload();
				} else {
					$("#edit_profile #message").html(data);
				}
			},
			beforeSend: function(){
				$("#edit_profile #update").html("Cargando...");
			}
		});
	}
}

</script>


<div class="title">
	<h1><?php echo $user_data->first_name." ".$user_data->last_name; ?>'s Profile</h1>
</div>


<div class="row-fluid">
	<div class="span3 center">
		<?php if($profile_data->profile_picture == "male.jpg" || $profile_data->profile_picture == "female.jpg"){ ?>
		<img src="<?php echo WWW; ?>assets/img/profile/<?php echo $profile_data->profile_picture; ?>" alt="Profile Picture">
		<?php } else { ?>
		<img src="<?php echo WWW; ?>assets/img/profile/<?php echo $profile_data->user_id."/".$profile_data->profile_picture; ?>" alt="Profile Picture">
		<?php } ?>
		<br /><br />
		<ul class="nav nav-tabs nav-stacked" style="text-align: left;">
			<?php if($owner == true): ?>
			<li><a href="<?php echo WWW; ?>profile_picture">Editar Foto del perfil</a></li>
			<li><a href="#edit_profile" data-toggle="modal">Editar Perfil</a></li>
			<?php endif; // profile owner check ?>
		</ul>
	</div>
	<div class="span9">
		<label style="font-size: 20px;"><?php echo $user_data->first_name." ".$user_data->last_name." (".$user_data->username.")"; ?></label>
		<label><?php echo $user_data->country; ?></label>
		<label><?php echo Question::get_badge($user_data->questions_answered); ?></label>
		<br />
		<ul id="myTab" class="nav nav-tabs">
			<li class="active"><a href="#wall" data-toggle="tab">Muro</a></li>
			<li class=""><a href="#about_me" data-toggle="tab">Acerca de mí</a></li>
			<li class=""><a href="#questions_started" data-toggle="tab">Preguntas Hechas</a></li>
			<li class=""><a href="#my_answers" data-toggle="tab">Mis Respuestas</a></li>
		</ul>
		<div id="message"></div>
		<div id="myTabContent" class="tab-content" style="overflow: hidden;">
			<div class="tab-pane fade active in" id="wall">
				<?php if($session->is_logged_in()) : ?>
				<div class="create_wp_container">
					<label>Escribe en tu muro!</label>
					<textarea class="span12" style="height: 80px;" id="wall_message" maxlength="250" required="required"></textarea>
					<span id="wall_message_counter">250</span> Caracteres Restantes
					<button class="btn btn-success right" id="submit_wall_message">Agregar Mensaje</button>
					<div class="clearfix"></div>
				</div>
				<?php endif; ?>
				
				<div class="clear"></div>
				<div id="profile_messages" style="margin-top: 1px;">
				<?php echo $profile_messages = Profile::display_profile_messages("unread", $profile_data->user_id); if(empty($profile_messages)){echo "<strong>Actualmente Este usuario no tiene mensajes de perfil en su muro.</strong>";} ?>
				</div>
			</div>
			<div class="tab-pane fade" id="about_me">
				<?php if($profile_data->about_me != ""){ echo nl2br($profile_data->about_me); } else { echo "Lo sentimos, ".$user_data->username." no ha entrado nada para esta sección de su perfil."; } ?>
			</div>
			<div class="tab-pane fade" id="questions_started">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Titulo</th>
							<th>Creado</th>
							<th>Respuestas</th>
							<th>Contador de visitas</th>
							<th>Thumbs Up</th>
							<th>Thumbs Down</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($questions = Question::get_started_questions($profile_data->user_id) as $question): ?>
						<tr>
							<td><a href="<?php echo WWW."question?id=".$question->id; ?>"><?php echo $question->title; ?></a></td>
							<td><?php echo ago_time($question->created); ?></td>
							<td><?php echo $question->answer_count; ?></td>
							<td><?php echo $question->view_count; ?></td>
							<td style="color: rgb(103, 177, 23);"><?php echo $question->thumbs_up; ?></td>
							<td style="color: rgb(207, 0, 0);"><?php echo $question->thumbs_down; ?></td>
						</tr>
						<?php endforeach; if(empty($questions)){ ?>
							<tr>
								<td colspan="6">No hay preguntas Encontradas</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="tab-pane fade" id="my_answers">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Titulo de Preguntas</th>
							<th>Publicado</th>
							<th>Thumbs Up</th>
							<th>Thumbs Down</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($answers = Question::get_questions_answered($profile_data->user_id) as $answer): $question_data = Question::get_question_title($answer->question_id); ?>
						<tr>
							<td><a href="<?php echo WWW."question?id=".$question_data[0]->id; ?>"><?php echo $question_data[0]->title; ?></a></td>
							<td><?php echo ago_time($answer->created); ?></td>
							<td style="color: rgb(103, 177, 23);"><?php echo $answer->thumbs_up; ?></td>
							<td style="color: rgb(207, 0, 0);"><?php echo $answer->thumbs_down; ?></td>
						</tr>
						<?php endforeach; if(empty($answers)){ ?>
							<tr>
								<td colspan="4">No hay respuestas encontradas</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		
	</div>
</div>

 
<?php if($session->is_logged_in()){ ?>
<!-- Edit Comment - Modal -->
<div id="edit_comment" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Editar Comentario</h3>
	</div>
	<div class="modal-body">
		<textarea id="textarea" class="span12" style="height: 100px; width: 97%;"></textarea>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
		<button id="confirm" class="btn btn-primary">Actualizar</button>
	</div>
</div>

<!-- Confirm Delete - Comment - Modal -->
<div id="confirm_delete" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Confirmar la Eliminacion</h3>
	</div>
	<div class="modal-body">
		<p>¿Está seguro de borrar este comentario? <br /> <strong>Esta acción no se puede deshacer!</strong></p>
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button id="confirm" class="btn btn-danger">Confirmar</button>
	</div>
</div>

<!-- Edit Profile - Modal -->
<div id="edit_profile" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Editar Perfil</h3>
	</div>
	<div class="modal-body">
		<div id="message"></div>
		<label>Estado</label>
		<input type="text" id="profile_message" required="required" style="width: 97%;" value="<?php echo $profile_data->profile_msg; ?>" />
		<label>Acerca de mí</label>
		<textarea id="textarea" class="span12" style="height: 100px; width: 97%;"><?php echo $profile_data->about_me; ?></textarea>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button id="update" class="btn btn-primary" onclick="update_profile();">Actualizar</button>
	</div>
</div>

<?php } ?>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>