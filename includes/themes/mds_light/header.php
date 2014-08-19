<?php require_once("includes/global/header.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="BeetleDeveloper - JDLA">
	
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/main.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/main.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script>var WWW = "<?php echo WWW ?>";</script>
	<script src="<?php echo WWW ?>includes/global/js/main.js"></script>
	<script>
	$(document).ready(function() {
		$(function() {
			$('.dropdown-toggle').dropdown();
			$('.dropdown, .dropdown input, .dropdown label').click(function(e) {
				e.stopPropagation();
			});
		});
	});
	$(function(){
		$("[rel='tooltip']").tooltip();
	});
	</script>
	
	
</head>

<body>
	<div id="header-wrapper">
		<div class="container">
			<header>
				<div class="row-fluid">
					<div class="span12">
						<div class="navbar navbar-fixed-top">
						<div class="navbar">
                              <div class="navbar-inner">
                                <div class="container" style="width: auto;">
                                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                                       <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                         <span class="icon-bar"></span>
                                          </a>
                                          <a href="<?php echo WWW; ?>" class="brand mobile"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/logo.jpg" alt="Logo"></a>
                                           <div class="nav-collapse">
                                           <ul class="nav">
                                           <li<?php echo ($current_page == "home") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>index">Inicio</a></li>
                                             <li<?php echo ($current_page == "scoreboard") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>scoreboard">AskRantig</a></li>
											<li<?php echo ($current_page == "contact") ? " class='active'" : "" ?>><a href="<?php echo WWW; ?>contact">Nosotros</a></li>
                                            
                                              <li class="dropdown">
												<a href="#" class="dropdown-toggle" data-toggle="dropdown">Buscar <b class="caret"></b></a>
												<ul class="dropdown-menu">
                                                        <form action="<?php echo WWW; ?>search.php" method="GET" id="search_form" style="margin-bottom: -8px;">
														<div class="row-fluid">
															<div class="span9 center">
														        <input type="text" class="span12" name="search" required="required" placeholder="Buscar...">
															</div>
															<div class="span3">
														        <button class="btn btn-danger" style="margin: -1px 0px 0px -18px;height: 32px;padding: 0px 20px;"><i class="fa fa-search"></i></button>
															</div>
														</div>
														<input type="hidden" class="span12" name="category" value="all">
													</form>
													<div style="text-align:center;margin-top: 3px;margin-bottom: 3px;"><a href="<?php echo WWW; ?>search">Buscar en todas las Preguntas</a></div>
												          </ul>
											              </li>
											              <a href="<?php echo WWW; ?>create-question" class="btn btn-danger btn-small" >Crear una Pregunta »</a>
                                                           </ul>
                                                             <ul class="nav pull-right">
                                                  <?php if($user->staff == 1){ echo '<li><a href="'.WWW.ADMINDIR.'">Admin Area</a></li>'; } ?>

                                                                        <li class="divider-vertical"></li>

                                                      <?php if($session->is_logged_in()) { ?>
												<li class="dropdown perfil" >
												<div class="perfil-div" >

															<?php if($profile_data->profile_picture == "male.jpg" || $profile_data->profile_picture == "female.jpg"){ ?>
				<img class="perfil-img" id="the_thumbnail" src="<?php echo WWW; ?><?php echo "/assets/img/profile/".$profile_data->profile_picture; ?>" alt="Thumbnail" />
			<?php } else { ?>
				<img class="perfil-img" id="the_thumbnail" src="<?php echo WWW; ?><?php echo "/assets/img/profile/".$user->user_id."/".$profile_data->profile_picture; ?>"  alt="Thumbnail" />
			<?php } ?>	</div>
										          <a href="" class="dropdown-toggle btn btn-success btn-small perfil-boton" data-toggle="dropdown"><?php echo $user->username; ?>  <b class="caret"></b></a>
										          <ul class="dropdown-menu perfil-menu">
														<li><a href="<?php echo WWW; ?>settings">Ajustes</a></li>
														<li><a href="<?php echo WWW; ?>profile?username=<?php echo $user->username; ?>">Perfil</a></li>
														<li><a href="<?php echo WWW; ?>my_questions">Mis Preguntas</a></li>
														<li class="divider"></li>
														<li><a href="<?php echo WWW; ?>logout">Salir</a></li>
										          </ul>
										        </li>
											<?php } else { ?>
												<form class="navbar-search pull-left" action="">
          <a class="btn btn-success btn-small" href="<?php echo WWW; ?>login"><i class="fa fa-sign-in fa-inverse"></i> Iniciar Seccion </a>
          <a class="divider-vertical"></a>
          <a class="btn btn-warning btn-small" href="<?php echo WWW; ?>register"><i class="fa fa-paper-plane fa-inverse"></i> Registrarse</a>
          </form>

											<?php } ?>
										</ul>


                                                      </div><!-- /.nav-collapse -->
                                                   </div>
                                               </div><!-- /navbar-inner -->
                                            </div><!-- /navbar -->
                                           </div>
                                      </div><!-- /.nav-collapse -->
                                    </div>
	                           </div>
	                      </div>
	                  </div>
				</div>
			</header>
		</div>
	</div>
	<br><br>
	<div class="container">
		<div id="content" class="settings">