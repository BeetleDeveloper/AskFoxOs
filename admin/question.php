<?php 


require_once("../includes/inc_files.php"); 
require_once("../includes/classes/admin.class.php");

$active_page = "questions";
$current_page = "";

if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
} else {
	redirect_to("../login.php");
}

if(isset($_GET['id'])){
	$question_id = clean_value($_GET['id']);
	$question_data = Question::get_question_listing($question_id);
	if(empty($question_data)){
		$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>No question found.</div>");
		redirect_to("questions.php");
	}
	$question_data = $question_data[0];
	$author = User::get_author($question_data->user_id);
	$owner = true;
	$answers = Question::get_answers($question_id);
} else {
	$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>No question found.</div>");
	redirect_to("questions.php");
}

?>

<?php $page_title = "Question: ".$question_data->title; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<div class="row-fluid">
	<?php require_once("../includes/global/admin_nav.php"); ?>
</div>
<div class="row-fluid">
	<div class="span12">
	
		<div class="title">
			<h1><?php echo $question_data->title; ?></h1>
			<a href="<?php echo WWW."question.php?id=".$question_data->id; ?>" role="button" class="btn btn-primary" style="margin-bottom: 10px;" data-toggle="modal">Main Listing</a>
			<a href="#edit_question" role="button" class="btn btn-warning" style="margin-bottom: 10px;" data-toggle="modal">Edit</a>
			<a href="#delete_question" role="button" class="btn btn-danger" style="margin-bottom: 10px;" data-toggle="modal">Delete</a>
		</div>

		<?php echo output_message($message); ?>

		<script>
		
		function thumbs(id,type){
			$.ajax({
				type: "POST",
				url: "data.php",
				data: "page=question&rate_answer="+id+"&type="+type,
				success: function(message){
					if(message != "failure"){
						$("#answer_"+id+" .rating .thumbs_"+type+"_count").html(message);
						message = '<div class="vote_message success">Thanks!</div>';
					} else {
						message = '<div class=\"vote_message failure\">Already Voted!</div>';
					}
					$("#answer_"+id+" .rating .vote_message").remove();
					$("#answer_"+id+" .rating").append(message);
				}
			});
		}
		function main_thumbs(type){
			$.ajax({
				type: "POST",
				url: "data.php",
				data: "page=question&rate_question=<?php echo $question_data->id; ?>&type="+type,
				success: function(message){
					if(message != "failure"){
						$(".right_side .rating .thumbs_"+type+"_count").html(message);
						message = '<div class="vote_message success">Thanks!</div>';
					} else {
						message = '<div class=\"vote_message failure\">Already Voted!</div>';
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
				url: "data.php",
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
				url: "data.php",
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
					$("#edit_answer #confirm").html("Working...");
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
				$("#report_answer .message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter a reason for reporting this answer.</div>");
			} else {
				$.ajax({
					type: "POST",
					url: "data.php",
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
				$("#report_question .message").html("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Please enter a reason for reporting this question.</div>");
			} else {
				$.ajax({
					type: "POST",
					url: "data.php",
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
		function delete_answer(id){
			$("#delete_answer #confirm").attr('onclick','delete_qa(\'answer\' ,\''+id+'\');');
			$('#delete_answer').modal('show');
		}
		function delete_qa(type,id){
			$.ajax({
				type: "POST",
				url: "data.php",
				data: "page=question&delete="+type+"&id="+id,
				success: function(message){
					if(message == "success"){
						if(type == "question"){
							window.location.replace("questions.php");
						} else {
							location.reload();
						}
					} else {
						location.reload();
					}
				},
				beforeSend: function(){
					$("#delete_"+type+" #confirm").html("Working...");
				}
			});
		}
		</script>
		<div id="edit_answer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Edit Answer</h3>
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
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-danger" id="confirm">Confirm</button>
			</div>
		</div>
		<div id="report_answer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Report Answer</h3>
			</div>
			<div class="modal-body">
				<div class="message"></div>
				<div class="row-fluid">
					<div class="span12">
				        <textarea class="span12" style="height: 80px;" id="reason" maxlength="250" required="required" placeholder="Please enter your reason for reporting this answer"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-danger" id="confirm">Report</button>
			</div>
		</div>
		<div id="report_question" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Report Question</h3>
			</div>
			<div class="modal-body">
				<div class="message"></div>
				<div class="row-fluid">
					<div class="span12">
				        <textarea class="span12" style="height: 80px;" id="reason" maxlength="250" required="required" placeholder="Please enter your reason for reporting this question"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-danger" id="confirm">Report</button>
			</div>
		</div>

		<div class="question">

			<div class="row-fluid">

				<div class="span9">
					<div class="question_container">
						<?php echo nl2br($question_data->description); ?>
						<div class="quote_99"></div>
					</div>
					<span>This question was posted <strong><?php echo ago_time($question_data->created); ?></strong> in <strong><?php echo Question::get_category_name($question_data->category_id); ?></strong> and has received <strong><?php echo $question_data->view_count; ?></strong> <?php if($question_data->view_count == 1){echo "view";}else{echo "views";}?>
				</div>
				<div class="span3 right_side">
					<div class="rating_container">
						<div class="rating">
							<span class="thumbs_up_count"><?php echo $question_data->thumbs_up; ?></span>
							<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="main_thumbs('up');"><img src="../includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_up.png" width="26" height="26" alt="Tumbs Up" class="button"></button>
							<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="main_thumbs('down');"><img src="../includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_down.png" width="26" height="26" alt="Tumbs Down" class="button"></button>
							<span class="thumbs_down_count"><?php echo $question_data->thumbs_down; ?></span>
						</div>
					</div>
					<hr />
					<label style="font-size: 12px;"><em>This question was posted by:</em></label>
					<a href="<?php echo WWW."profile.php?username=".$author[0]->username; ?>"><label style="font-size: 20px;"><?php echo $author[0]->first_name." ".$author[0]->last_name." (".$author[0]->username.")"; ?></label></a>
					<label><?php echo $author[0]->country; ?></label>

				</div>
				<div class="clearfix"></div>

			</div>

			<hr />

			<div class="row-fluid">
				<div class="span7">
					<?php $top_answer = array_shift($answers); if(!empty($top_answer)){ ?>
					<h3>Top Answer</h3>
					<div id="answer_<?php echo $top_answer->id; ?>" class="answer_container top_answer">
						<a href="<?php echo $username = User::find_username_by_id($top_answer->user_id); ?>"><h4><?php echo $username ?></h4></a>
						<div class="rating">
							<span class="thumbs_up_count"><?php echo $top_answer->thumbs_up; ?></span>
							<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $top_answer->id; ?>','up');"><img src="../includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_up.png" width="26" height="26" alt="Tumbs Up" class="button"></button>
							<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $top_answer->id; ?>','down');"><img src="../includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_down.png" width="26" height="26" alt="Tumbs Down" class="button"></button>
							<span class="thumbs_down_count"><?php echo $top_answer->thumbs_down; ?></span>
						</div>
						<p><?php echo nl2br($top_answer->message); ?></p>
						<span style="font-size: 12px; font-style: italic;">This answer was posted <strong><?php echo ago_time($top_answer->created); ?></strong> - <a href="#" role="button" data-toggle="modal" onclick="report_answer('<?php echo $top_answer->id; ?>'); return false;">Report</a>  - <a href="#" style="padding:0;margin0;" onclick="edit_answer('<?php echo $top_answer->id ?>'); return false;">Edit</a> - <a href="#" role="button" data-toggle="modal" onclick="delete_answer('<?php echo $top_answer->id; ?>'); return false;">Delete</a> - Question ID: <strong><?php echo $top_answer->id; ?></strong></span>
					</div>
					<?php } ?>

					<!-- Regular Answers -->
					<?php if(!empty($answers)){ ?>
					<h3>Answers</h3>
					<?php foreach($answers as $answer){ ?>
					<div id="answer_<?php echo $answer->id; ?>" class="answer_container">
					<a href="<?php $username = User::find_username_by_id($answer->user_id); echo WWW."profile.php?username=".$username; ?>"><h4><?php echo $username ?></h4></a>
						<div class="rating">
							<span class="thumbs_up_count"><?php echo $answer->thumbs_up; ?></span>
							<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $answer->id; ?>','up');"><img src="../includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_up.png" width="26" height="26" alt="Tumbs Up" class="button"></button>
							<button class="btn btn-link" style="margin: 0; padding: 0;" onclick="thumbs('<?php echo $answer->id; ?>','down');"><img src="../includes/themes/<?php echo THEME_NAME ?>/img/icons/tumbs_down.png" width="26" height="26" alt="Tumbs Down" class="button"></button>
							<span class="thumbs_down_count"><?php echo $answer->thumbs_down; ?></span>					
						</div>
						<p><?php echo nl2br($answer->message); ?></p>
						<span style="font-size: 12px; font-style: italic;">This answer was posted <strong><?php echo ago_time($answer->created); ?></strong> - <a href="#" role="button" data-toggle="modal" onclick="report_answer('<?php echo $answer->id; ?>'); return false;">Report</a> - <a href="#" style="padding:0;margin0;" onclick="edit_answer('<?php echo $answer->id ?>'); return false;">Edit</a> - <a href="#" role="button" data-toggle="modal" onclick="delete_answer('<?php echo $answer->id; ?>'); return false;">Delete</a> - Question ID: <strong><?php echo $top_answer->id; ?></strong></span>
					</div>
					<?php } } ?>
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

					<h3>Latest Questions</h3>

					<?php foreach($latest as $question){ ?>
					<a href="<?php echo WWW."question.php?id=".$question->id; ?>">
						<div class="link_container">
							<?php echo $question->title ?> <span>- <?php echo $question->answer_count; if($question->answer_count == 1){echo " Answer";}else{echo " Answers";} ?> - <?php echo ago_time($question->created); ?> - <span style="color: rgb(103, 177, 23);"><?php echo $question->thumbs_up; ?> Up</span> - <span style="color: rgb(207, 0, 0);"><?php echo $question->thumbs_down; ?> Down</span><span>
						</div>
					</a>

					<?php } } ?>

				</div>
			</div>

		</div>

		<script>
		function edit_question(){
			var message = $("#edit_question #answer").val();
			$.ajax({
				type: "POST",
				url: "data.php",
				data: "page=question&edit_question=<?php echo $question_data->id; ?>&message="+message,
				success: function(data){
					if(data == "success"){
						location.reload();
					} else {
						$("#edit_question .message").html(data);
						$("#edit_question #confirm").html("Confirm Update");
					}
				},
				beforeSend: function(){
					$("#edit_question #confirm").html("Working...");
				}
			});
		}
		</script>
		<div id="edit_question" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Edit Question</h3>
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
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-danger" id="confirm" onclick="edit_question();">Confirm Update</button>
			</div>
		</div>
		<div id="delete_question" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Delete Question</h3>
			</div>
			<div class="modal-body">
				<div class="message"></div>
				<div class="row-fluid">
					<strong>Do you really want to delete this question? This action can't be reversed!</strong>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-danger" id="confirm" onclick="delete_qa('question','<?php echo $question_data->id; ?>');">Confirm</button>
			</div>
		</div>
		<div id="delete_answer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">Delete Answer</h3>
			</div>
			<div class="modal-body">
				<div class="message"></div>
				<div class="row-fluid">
					<strong>Do you really want to delete this answer? This action can't be reversed!</strong>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-danger" id="confirm">Confirm</button>
			</div>
		</div>
		
	</div>
</div>

<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>