<?php 


require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

if(!$session->is_logged_in()) {redirect_to("../login.php");}

$admin = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);

$active_page = "reported";
$current_page = "";

$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = PAGINATION_PER_PAGE;
$total_count = Question::count_all_reported();
$pagination = new Pagination($page, $per_page, $total_count);
$sql = "SELECT * FROM reported LIMIT {$per_page} OFFSET {$pagination->offset()}";
$query_data = Question::find_by_sql($sql);

?>

<?php $page_title = "Reported Questions and Answers"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<script>
function delete_report(id){
	$("#delete_report #confirm").attr('onclick','confirm_delete(\''+id+'\');');
	$('#delete_report').modal('show');
}
function confirm_delete(id){
	$.ajax({
		type: "POST",
		url: "data.php",
		data: "page=report&delete="+id,
		success: function(){
			location.reload();
		},
		beforeSend: function(){
			$("#delete_report #confirm").html("Working...");
		}
	});
}
</script>

<div class="row-fluid">
	<?php require_once("../includes/global/admin_nav.php"); ?>
</div>
<div class="row-fluid">
<div class="span12">
	<div class="title">
		<h1><?php echo $page_title; ?></h1>
	</div>
		<?php if(!empty($query_data)){ ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Reported ID</th>
					<th>User ID</th>
					<th>Reason</th>
					<th>Date</th>
					<th>Type</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($query_data as $report) : ?>
				<tr>
					<td><?php echo $report->id; ?></td>
					<td><?php echo $report->reported_id; ?></td>
					<td><?php echo $report->user_id; ?></td>
					<td><?php echo $report->reason; ?></td>
					<td><?php echo $report->datetime; ?></td>
					<td><?php if($report->type == 0){echo "Question";}else{echo "Answer";} ?></td>
					<td><a href="<?php echo WWW.ADMINDIR."question.php?id=".$report->reported_id; ?>" target="_blank">View</a> - <a href="#" onclick="delete_report('<?php echo $report->id; ?>'); return false;"> Delete</a></td>					
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
						echo " <li><a href=\"reported.php?page={$i}\">{$i}</a></li> "; 
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

<div id="delete_report" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel">Delete Report</h3>
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

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>