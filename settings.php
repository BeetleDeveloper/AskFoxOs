<?php require_once("includes/inc_files.php"); 


if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
$profile_data = Profile::find_by_id($user->user_id);
} else {
	redirect_to("login");
}

$current_page = "settings";

$location = "settings.php";

?>

<?php $page_title = "Ajustes"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<script>
function edit(id){
	if(id == "gender" || id == "country"){
		if(id == "gender"){
			var value = '<?php echo $user->gender; ?>';
		} else {
			var value = '<?php echo $user->country; ?>';
		}
		$.ajax({
			type: "POST",
			url: WWW+"data.php",
			data: "page=settings&get_select="+id+"&"+id+"="+value,
			success: function(data){
				$(".settings #"+id+" .setting").attr('style', 'display:none').after('<td class="setting input">'+data+'</td>');
				$(".settings #"+id+" .action").html('<button class="btn btn-link" onclick="save(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/tick.png" width="31" height="31" alt="Tick"></button> <button class="btn btn-link" onclick="cancel(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/cross.png" width="31" height="31" alt="Cross"></button>');
			}
		});
	} else {
		if(id == "password"){
			var value = "";
		} else {
			var value = $(".settings #"+id+" .setting").html();
		}
		$(".settings #"+id+" .setting").attr('style', 'display:none').after('<td class="setting input"><input type="text" class="span12" id="'+id+'" required="required" value="'+value+'"></td>');
		$(".settings #"+id+" .action").html('<button class="btn btn-link" onclick="save(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/tick.png" width="31" height="31" alt="Tick"></button> <button class="btn btn-link" onclick="cancel(\''+id+'\')"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/icons/cross.png" width="31" height="31" alt="Cross"></button>');
	}
}
function save(id){
	if(id == "gender" || id == "country"){
		var value = $("#"+id+" .setting.input #"+id+" option:selected ").val();
	} else {
		var value = $("#"+id+" .setting.input #"+id).val();
	}
	if(value != ""){
		$.ajax({
			type: "POST",
			url: WWW+"data.php",
			data: "page=settings&name="+id+"&value="+value,
			success: function(data){
				if(data == "failure"){
					update_msg();
				} else {
					$("#"+id+" .setting").html(value);
					cancel(id);
					update_msg();
				}
			}
		});
	} else {
		$("#message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>This setting can't be left blank.</div>")
	}
}
function cancel(id){
	$(".settings #"+id+" .setting").removeAttr('style');
	$(".settings #"+id+" .setting.input").remove();
	$(".settings #"+id+" .action").html('<button class="btn btn-inverse" onclick="edit(\''+id+'\');"></i> Editar</button>');
}
</script>

<div class="title">
	<h1>Ajuste de Perfil de <?php echo $user->first_name; ?></h1>
</div>

<div id="message"><?php echo output_message($message); ?></div>

<div class="row-fluid">
	<div class="span6">
		<table class="settings table">
			<thead>
				<tr>
					<th colspan="2"><h2><?php echo lang_settings; ?></h2></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="name" colspan="2"><?php echo lang_first_name; ?></td>
				</tr>
				<tr id="first_name">
					<td class="setting"><?php echo $user->first_name; ?></td>
					<td class="action"><button class="btn btn-inverse" onclick="edit('first_name');"></i> Editar</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2"><?php echo lang_last_name; ?></td>
				</tr>
				<tr id="last_name">
					<td class="setting"><?php echo $user->last_name; ?></td>
					<td class="action"><button class="btn btn-inverse" onclick="edit('last_name');"></i> Editar</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2"><?php echo lang_username; ?></td>
				</tr>
				<tr id="username">
					<td class="setting" colspan="2"><?php echo $user->username; ?></td>
					<td class="action"><button class="btn btn-inverse" onclick="edit('username');"></i> Editar</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2"><?php echo lang_password; ?></td>
				</tr>
				<tr id="password">
					<td class="setting">**********</td>
					<td class="action"><button class="btn btn-inverse" onclick="edit('password');"></i> Editar</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2"><?php echo lang_email_address; ?></td>
				</tr>
				<tr id="email">
					<td class="setting"><?php echo $user->email; ?></td>
					<td class="action"><button class="btn btn-inverse" onclick="edit('email');"></i> Editar</button></td>
				</tr>				
				<!--  -->
				<tr>
					<td class="name" colspan="2"><?php echo lang_gender; ?></td>
				</tr>
				<tr id="gender">
					<td class="setting"><?php echo $user->gender; ?></td>
					<td class="action"><button class="btn btn-inverse" onclick="edit('gender');"></i> Editar</button></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name" colspan="2"><?php echo lang_country; ?></td>
				</tr>
				<tr id="country">
					<td class="setting"><?php echo $user->country; ?></td>
					<td class="action"><button class="btn btn-inverse" onclick="edit('country');"></i> Editar</button></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="span6">					
		<table class="settings table">
			<thead>
				<tr>
					<th colspan="2"><h2><?php echo lang_statistics; ?></h2></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="name"><?php echo lang_last_login; ?></td>
				</tr>
				<tr>
					<td class="setting"><?php echo ago_time($user->last_login)." ".lang_from." ".$user->last_ip; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name"><?php echo lang_signed_up; ?></td>
				</tr>
				<tr>
					<td class="setting"><?php echo ago_time($user->date_created)." ".lang_from." ".$user->signup_ip; ?></td>
				</tr>
				<!--  -->
				<tr>
					<td class="name"><?php echo lang_questions; ?></td>
				</tr>
				<tr>
					<td class="setting"><strong><?php echo $user->questions_posted; ?></strong> <?php echo lang_posted; ?>, <strong><?php echo $user->questions_answered; ?></strong> <?php echo lang_answered; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>