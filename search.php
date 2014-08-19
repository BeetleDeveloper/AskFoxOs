<?php
require_once("includes/inc_files.php");

if($session->is_logged_in()) {
	$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);
	$profile_data = Profile::find_by_id($user->user_id);
}

$current_page = "search";

if(isset($_GET['search'])){
	$search = true;
	
	if(SEO_URLS == "ON"){
		$query = str_replace(array(' ','.'), "-", preg_replace("/[^0-9a-z_ ,.-]/i", "", strtolower($_GET['search'])));
		$category = str_replace(array(' ','.'), "+", preg_replace("/[^0-9a-z_ ,.-]/i", "", strtolower($_GET['category'])));
		$subs = str_replace("http://".$_SERVER['HTTP_HOST'], "", WWW);
		if($_SERVER['REQUEST_URI'] == $subs."search.php?search=".$query."&category=".$category){
			// redirects the user to the seo friendly url, instead of the search generated one.
			redirect_to(WWW."search/".$category."/".$query.".html");		
		}
	}
	
	$query = preg_replace("/[^0-9a-z_ ,.-]/i", "", $_GET['search']);
	$category_seo = str_replace(array(' ','.'), "-", strtolower($_GET['category']));
	
	if($_GET['category'] == "all"){
		$sql = "SELECT * FROM question WHERE title LIKE '%$query%'";
	} else {
		$category_id = Question::get_category_id_from_name($category);
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

// preprint($query_data);

?>

<?php $page_title = "Buscar"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1>Búsqueda de Preguntas</h1>
</div>

<div id="message"><?php echo output_message($message); ?></div>

<form action="<?php echo WWW; ?>search.php" method="GET" class="form-search">
	<input type="text" placeholder="Buscar..." name="search" class="input-xxlarge" value="<?php echo $query; ?>">
	<select name="category">
		<option value="all" <?php if(isset($_GET['category']) && $_GET['category'] == "all"){echo "selected=\"selected\" ";} ?>>Todas las Categorias</option>
		<?php foreach(Question::get_categories() as $category){ ?>
			<option value="<?php echo $category->name; ?>" <?php if(isset($_GET['category']) && $_GET['category'] == $category->name){echo "selected=\"selected\" ";} ?>><?php echo $category->name; ?></option>
		<?php } ?>
	</select>
	<button type="submit" class="btn btn-primary">Buscar</button>
</form>

<div class="row-fluid search">
	
	<div class="span3">
		<div class="well" style="max-width: 340px; padding: 8px 0;margin-left: -11px; border-radius: 0px; -webkit-border-radius: 0px; -moz-border-radius: 0px; box-shadow: none; -webkit-box-shadow: none; -moz-box-shadow: none;  ">
			<ul class="nav nav-list">
				<li class="nav-header">Categorias</li>
				<?php foreach(Question::get_categories() as $category){ ?>
				<li <?php if(isset($_GET['category']) && $_GET['category'] == $category->name){echo "class=\"active\" ";} ?>><a href="<?php echo WWW."search.php?search=&category=".$category->name; ?>"><?php echo $category->name; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	
	<div class="span9">
	
	<?php if(empty($query_data)){ ?>
		<strong>Lo sentimos, que donde no pueden encontrar a cualquier pregunta que responden a su búsqueda.</strong>
	<?php } else { ?>
		
		<?php foreach($query_data as $data): ?>
		
		<a href="<?php echo WWW."question?id=".$data->id; ?>">
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
					echo " <li><a href=\"search?search={$query}&amp;category={$_GET['category']}&amp;page={$i}\">{$i}</a></li> "; 
				}
			}
		
		}

		echo "</ul>";
	?>

	<?php } ?>
	
	</div>
	
</div>

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>