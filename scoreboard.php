<?php


require_once("includes/inc_files.php");

if($session->is_logged_in()){$user = User::find_by_id($_SESSION['masdyn']['answers']['user_id']);}
$profile_data = Profile::find_by_id($user->user_id);
$location = "scoreboard.php";

$current_page = "scoreboard";

$top_posters = User::get_top_posters();

?>

<?php $page_title = "AskRantig"; require_once("includes/themes/".THEME_NAME."/header.php"); ?>

<div class="title">
	<h1>AskRantig</h1>
</div>

<div id="message"><?php echo output_message($message); ?></div>

<div class="row-fluid">
	<div class="span9">
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Nombre Completo</th>
					<th>Username</th>
					<th>Publicado</th>
					<th>Respuestas</th>
					<th>País</th>
				</tr>
			</thead>
			<tbody>
				<?php $counter = 1; $mini_counter = 1; $total_rank = Question::get_total_rank($top_posters); foreach($top_posters as $poster){ ?>
				<tr>
					<?php $total_count = $total_rank[$poster->questions_answered]; if($total_count > 1){?>
						<?php if($mini_counter == 1){ ?>
							<td rowspan="<?php echo $total_count; ?>"><?php echo $counter; ?></td>
						<?php } $mini_counter++; ?>
					<?php } else { ?>
						<td><?php echo $counter; $mini_counter = 1; ?></td>
					<?php } ?>
					<td><?php echo $poster->first_name." ".$poster->last_name; ?></td>
					<td><?php echo "<a href=\" ".WWW."profile?username=".$poster->username." \">".$poster->username."</a>"; ?></td>
					<td><?php echo $poster->questions_posted; ?></td>
					<td><?php echo $poster->questions_answered; ?></td>
					<td><?php echo $poster->country; ?></td>
				</tr>
				<?php $counter++; } ?>
			</tbody>
		</table>
	</div>
	<div class="span3">
		<h2 style="margin-bottom: 5px">Medallas</h2>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/bronze-1.png" width="20" height="20" alt="0-50"></td>
					<td><strong>0-50</strong> Respuestas</td>
				</tr>
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/bronze-2.png" width="20" height="20" alt="51-100"></td>
					<td><strong>51-100</strong> Respuestas</td>
				</tr>
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/bronze-3.png" width="20" height="20" alt="101-500"></td>
					<td><strong>101-500</strong> Respuestas</td>
				</tr>
				<!--  -->
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/silver-1.png" width="20" height="20" alt="501-1000"></td>
					<td><strong>501-1000</strong> Respuestas</td>
				</tr>
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/silver-2.png" width="20" height="20" alt="1001-5000"></td>
					<td><strong>1001-5000</strong> Respuestas</td>
				</tr>
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/silver-3.png" width="20" height="20" alt="5001-10,000"></td>
					<td><strong>5001-10,000</strong> Respuestas</td>
				</tr>
				<!--  -->
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/gold-1.png" width="20" height="20" alt="10,001-20,000"></td>
					<td><strong>10,001-20,000</strong> Respuestas</td>
				</tr>
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/gold-2.png" width="20" height="20" alt="20,001-40,000"></td>
					<td><strong>20,001-40,000</strong> Respuestas</td>
				</tr>
				<tr>
					<td style="text-align:center;"><img src="<?php echo WWW; ?>assets/img/icons/medals/gold-3.png" width="20" height="20" alt="40,001+"></td>
					<td><strong>40,001+</strong> Respuestas</td>
				</tr>
				
			</tbody>
		</table>
	</div>
</div>
	

<?php require_once("includes/themes/".THEME_NAME."/footer.php"); ?>