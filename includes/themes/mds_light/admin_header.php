<?php require_once("../includes/global/header.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Lewis @ Masdyn Studio - Masdyn.com - dewsbury.co">
	
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/bootstrap.css" rel="stylesheet">
	<link href="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/css/main.css" rel="stylesheet">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script src="../assets/js/custom.js"></script>
	<script>
	$(document).ready(function() {
		$(function() {
			$('.dropdown-toggle').dropdown();
			$('.dropdown, .dropdown input, .dropdown label').click(function(e) {
				e.stopPropagation();
			});
		});
	});
	
	var WWW = "<?php echo WWW ?>";
	</script>
	
	
</head>

<body>
	<div id="header-wrapper">
		<div class="container">
			<header>
				<div class="row-fluid">
					<div class="span12">
						
						<div class="navbar">
							<div class="navbar-inner">
								<div class="container">
									<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-responsive-collapse">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</a>
									<a href="<?php echo WWW; ?>" class="brand"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/logo.jpg" width="248" height="52" alt="Logo"></a>
									<div class="nav-collapse collapse navbar-responsive-collapse">
										<ul class="nav">
											<li><a href="../index.php">Frontend</a></li>
										</ul>
										<ul class="nav pull-right">
											<?php if($session->is_logged_in()) { ?>
												<li class="dropdown">
										          <a href="" class="dropdown-toggle" data-toggle="dropdown"><?php echo $user->username; ?><b class="caret"></b></a>
										          <ul class="dropdown-menu">
														<li><a href="../settings.php">Settings</a></li>
														<li><a href="../profile.php?username=<?php echo $user->username; ?>">My Profile</a></li>
														<li><a href="../my_questions.php">My Questions</a></li>
														<li class="divider"></li>
														<li><a href="../logout.php">Sign Out</a></li>
										          </ul>
										        </li>
											<?php } else { ?>
												<li class="dropdown">
													<a href="#" class="dropdown-toggle" data-toggle="dropdown">Login <b class="caret"></b></a>
													<ul class="dropdown-menu">
														<div id="login_form" onkeypress="if(event.keyCode == 13){login()}">
															<div id="message"></div>
															<div class="row-fluid">
																<div class="span12 center">
															        <input type="text" class="span12" id="username" required="required" placeholder="Username">
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
															        <input type="password" class="span12" id="password" required="required" placeholder="Password">
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
																	<input type="checkbox" id="remember_me" />
																	<span>Remember Me? (Uses Cookies)</span>
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
																	<a href="reset_password.php">Forgot your Password?</a>
																</div>
															</div>
															<div class="row-fluid">
																<div class="span12">
																	<button class="btn btn-primary" type="submit" id="login_btn" onclick="login()" style="width: 100%">Login</button>
																</div>
															</div>

														</div>
													</ul>
												</li>
												<li><a href="register.php">Register</a></li>
											<?php } ?>
										</ul>
									</div><!-- /.nav-collapse -->
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</header>
		</div>
	</div>
	
	<div class="container">
		<div id="content" class="settings">
			
	<!-- Header End -->