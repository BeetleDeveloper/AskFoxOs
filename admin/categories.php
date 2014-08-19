<?php 


require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../login.php");}

$admin = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);

$active_page = "categories";
$current_page = "";

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = PAGINATION_PER_PAGE;
$total_count = Question::count_all_categories();
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM categories LIMIT {$per_page} OFFSET {$pagination->offset()}";
$query_data = Question::find_by_sql($sql);

?>

<?php $page_title = "Categories"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<script>
function delete_category(id){
	$("#delete_category #confirm").attr('onclick','confirm_delete(\''+id+'\');');
	$('#delete_category').modal('show');
}
function confirm_delete(id){
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=category&delete="+id,
		success: function(){
			location.reload();
		},
		beforeSend: function(){
			$("#delete_category #confirm").html("Working...");
		}
	});
}
function modify_category(id){
	$("#modify_category #confirm").attr('onclick','confirm_modify_category(\''+id+'\');');
	$("#name").val($("#cat_"+id+" .name").html());
	$("#status").val($("#cat_"+id+" .status").html());
	$('#modify_category').modal('show');
}
function confirm_modify_category(id){
	var name = $("#name").val();
	var status = $("#status option:selected ").val();
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=category&modify="+id+"&name="+name+"&status="+status,
		success: function(){
			location.reload();
		},
		beforeSend: function(){
			$("#modify_category #confirm").html("Working...");
		}
	});
}
function create_category(){
	var name = $("#create_category #name").val();
	var status = $("#create_category #status option:selected ").val();
	if(name == ""){
		$("#create_category #name").addClass('error');
		$("#create_category .message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please complete all required fields.</div>");
	} else {
		$.ajax({
			type: "POST",
			url: "data.php",
			data: "page=category&create=true&name="+name+"&status="+status,
			success: function(data){
				if(data == "success"){
					location.reload();
				} else {
					$("#create_category .message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Something has gone wrong, Please refresh and try again.</div>");
				}
			},
			beforeSend: function(){
				$("#create_category #confirm").html("Working...");
			}
		});
	}
}
</script>

<div class="row-fluid">
	<?php require_once("../includes/global/admin_nav.php"); ?>
</div>
<div class="row-fluid">
<div class="span12">
	<div class="title">
		<h1><?php echo $page_title; ?></h1>
		<button class="btn btn-small btn-primary" data-toggle="modal" href="#create_category" style="margin-bottom: 10px;">Create Category</button>
	</div>
		<?php if(!empty($query_data)){ ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($query_data as $cat) : ?>
				<tr id="cat_<?php echo $cat->id; ?>">
					<td class="id"><?php echo $cat->id; ?></td>
					<td class="name"><?php echo $cat->name; ?></td>
					<td class="status"><?php if($cat->status == 0){echo "Hidden";}else{echo "Visible";} ?></td>
					<td><a href="#" onclick="modify_category('<?php echo $cat->id; ?>'); return false;">Modify</a> - <a href="#" onclick="delete_category('<?php echo $cat->id; ?>'); return false;">Delete</a></td>					
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
			if($pagination->total_pages() > 1) {
			echo "<div class='pagination pagination-centered'><ul>";

				for($i=1; $i <= $pagination->total_pages(); $i++) {
					if($i == $page) {
						echo " <li class='active'><a>{$i}</a></li> ";
					} else {
						echo " <li><a href=\"categories.php?page={$i}\">{$i}</a></li> "; 
					}
				}

			}

			echo "</ul>";
		?>
	<?php } else { ?>
		
	<strong>No questions or answers have been reported.</strong>
	
	<?php } ?>
	
	</div>
</div>

<div class="clear"><!-- --></div>

<div id="delete_category" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Delete Category</h3>
	</div>
	<div class="modal-body">
		<div class="message"></div>
		<div class="row-fluid">
			<strong>Do you really want to delete this report? This action can't be reversed!</strong>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button class="btn btn-danger" id="confirm">Confirm</button>
	</div>
</div>
<div id="modify_category" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Modify Category</h3>
	</div>
	<div class="modal-body">
		<div class="message"></div>
		<div class="row-fluid">
			<label>Name</label>
			<input type="text" required="required" class="span12" name="name" id="name" value="" />
			<label>Status</label>
			<select name="status" id="status" class="span12">
				<option value="Hidden">Hidden</option>
				<option value="Visible">Visible</option>
			</select>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button class="btn btn-danger" id="confirm">Modify</button>
	</div>
</div>
<div id="create_category" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Create Category</h3>
	</div>
	<div class="modal-body">
		<div class="message"></div>
		<div class="row-fluid">
			<label>Name</label>
			<input type="text" required="required" class="span12" name="name" id="name" value="" />
			<label>Status</label>
			<select name="status" id="status" class="span12">
				<option value="Hidden">Hidden</option>
				<option value="Visible">Visible</option>
			</select>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		<button class="btn btn-danger" id="confirm" onclick="create_category();">Create</button>
	</div>
</div>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>