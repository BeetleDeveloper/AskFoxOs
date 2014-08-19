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

if(isset($_GET['search'])){
	$search = true;
	$query = preg_replace('#[^a-z 0-9?!]#i', '', $_GET['search']);
	
	if($_GET['category'] == "all"){
		$sql = "SELECT * FROM question WHERE title LIKE '%$query%'";
	} else {
		$category_id = $_GET['category'];
		$sql = "SELECT * FROM question WHERE title LIKE '%$query%' AND category_id = '{$category_id}' ";
	}
	
	$query_data = Question::find_by_sql($sql);
	
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$per_page = 20;
	$total_count = count($query_data);
	$pagination = new Pagination($page, $per_page, $total_count);
	$sql .= " LIMIT {$per_page} OFFSET {$pagination->offset()}";
	$query_data = Question::find_by_sql($sql);
} else {
	$search = false;
	$query_data = Question::find_by_sql("SELECT * FROM question");
	$page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
	$per_page = 20;
	$total_count = count($query_data);
	$pagination = new Pagination($page, $per_page, $total_count);
	$sql = "SELECT * FROM question LIMIT {$per_page} OFFSET {$pagination->offset()}";
	$query_data = Question::find_by_sql($sql);	
}

if(!isset($query)){
	$query = "";
}


?>

<?php $page_title = "Search Questions"; require_once("../includes/themes/".THEME_NAME."/admin_header.php"); ?>

<div class="row-fluid">
	<?php require_once("../includes/global/admin_nav.php"); ?>
</div>
<div class="row-fluid">
	<div class="span12">
	
		<div class="title">
			<h1><?php echo $page_title; ?></h1>
		</div>

		<div id="message"><?php echo output_message($message); ?></div>
		
		<form action="questions.php" method="GET" class="form-search">
			<input type="text" placeholder="Search..." name="search" class="input-xxlarge" value="<?php echo $query; ?>">
			<select name="category">
				<option value="all" <?php if(isset($_GET['category']) && $_GET['category'] == "all"){echo "selected=\"selected\" ";} ?>>All Categories</option>
				<?php foreach(Question::get_categories() as $category){ ?>
					<option value="<?php echo $category->id; ?>" <?php if(isset($_GET['category']) && $_GET['category'] == $category->id){echo "selected=\"selected\" ";} ?>><?php echo $category->name; ?></option>
				<?php } ?>
			</select>
			<button type="submit" class="btn btn-primary">Search</button>
		</form>

		<div class="row-fluid search">

			<div class="span3">
				<div class="well" style="max-width: 340px; padding: 8px 0;margin-left: -11px; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px; box-shadow: none; -webkit-box-shadow: none; -moz-box-shadow: none;  ">
					<ul class="nav nav-list">
						<li class="nav-header">Categories</li>
						<?php foreach(Question::get_categories() as $category){ ?>
						<li <?php if(isset($_GET['category']) && $_GET['category'] == $category->id){echo "class=\"active\" ";} ?>><a href="<?php echo WWW.ADMINDIR."questions.php?search=&category=".$category->id; ?>"><?php echo $category->name; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>

			<div class="span9">

			<?php if(empty($query_data)){ ?>
				<strong>Sorry, we where unable to find any questions which match your search.</strong>
			<?php } else { ?>

				<?php foreach($query_data as $data): ?>

				<a href="<?php echo WWW.ADMINDIR."question.php?id=".$data->id; ?>">
					<div class="link_container">
						<?php echo $data->title; ?> <span>- <?php echo $data->answer_count; if($data->answer_count == 1){echo " Answer";}else{echo " Answers";} ?>, posted <?php echo ago_time($data->created); ?> - <span style="color: rgb(103, 177, 23);"><?php echo $data->thumbs_up; ?> Up</span> - <span style="color: rgb(207, 0, 0);"><?php echo $data->thumbs_down; ?> Down</span><span>
					</div>
				</a>

				<?php endforeach; ?>

			<?php
				if($pagination->total_pages() > 1) {
				echo "<div class='pagination pagination-centered'><ul>";

					for($i=1; $i <= $pagination->total_pages(); $i++) {
						if($i == $page) {
							echo " <li class='active'><a>{$i}</a></li> ";
						} else {
							echo " <li><a href=\"questions.php?search={$query}&amp;category={$_GET['category']}&amp;page={$i}\">{$i}</a></li> "; 
						}
					}

				}

				echo "</ul>";
			?>

			<?php } ?>

			</div>

		</div>

	</div>
</div>

<div class="clear"><!-- --></div>

<?php require_once("../includes/themes/".THEME_NAME."/footer.php"); ?>