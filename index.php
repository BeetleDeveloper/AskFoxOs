<?php require_once("includes/inc_files.php"); 


if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
	$profile_data = Profile::find_by_id($user->user_id);
}

$current_page = "home";

?>
<br><br>
<div class="jumbotron">
      <div class="container center">
        <h1>Bienvenidos a AskFoxOs!</h1>
        <p>Sientase libre de añadir sus preguntas</p>
        <p><a href="<?php echo WWW; ?>create-question" class="btn btn-primary btn-lg" role="button">Crear una Pregunta »</a></p>
      </div>
    </div><br><br>
<?php $page_title = "AskFoxOs"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div id="message"><?php echo output_message($message); ?></div>

<div class="row-fluid homepage">

	<div class="span6 left_side">

		<?php $latest = Question::get_latest("-",10); if($latest){ ?>

		<div class="text-center">
<h2><i class="fa fa-comments-o"></i> Ùltimas Preguntas</h2>
</div>
		<br>
		
		
		<?php foreach($latest as $question){ ?>
		<a href="<?php echo WWW."question?id=".$question->id; ?>">
			<div class="link_container">
				<?php echo $question->title ?> <span> - <?php echo ago_time($question->created); ?> - <span style="color: rgb(103, 177, 23);"><?php echo $question->thumbs_up; ?> <i class="fa fa-thumbs-up"></i></span> - <span style="color: rgb(207, 0, 0);"><?php echo $question->thumbs_down; ?> <i class="fa fa-thumbs-down"></i></span><span>
			</div>
		</a>
		
		<?php } } ?>

	</div>
	
	<div class="span6">

		<?php $top = Question::get_top(10); if($top){ ?>
				<div class="text-center">
<h2><i class="fa fa-bullhorn"></i> Preguntas Más Frecuentes</h2>
</div><br>
		
		<?php foreach($top as $question){ ?>
		<a href="<?php echo WWW."question?id=".$question->id; ?>">
			<div class="link_container">
				<?php echo $question->title ?> <span> - <?php echo ago_time($question->created); ?> - <span style="color: rgb(103, 177, 23);"><?php echo $question->thumbs_up; ?> <i class="fa fa-thumbs-up"></i></span> - <span style="color: rgb(207, 0, 0);"><?php echo $question->thumbs_down; ?> <i class="fa fa-thumbs-down"></i></span><span>
			</div>
		</a>
		
		<?php } } ?>

	</div>
	
</div>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>