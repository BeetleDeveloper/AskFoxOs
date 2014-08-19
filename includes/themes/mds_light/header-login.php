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
	<div class="title text-center">
	<a href="<?php echo WWW; ?>" class="brand"><img src="<?php echo WWW; ?>includes/themes/<?php echo THEME_NAME; ?>/img/logo-seccion.jpg" width="448" height="52" alt="Logo"></a>
	<br><br><br><br>
	<div class="container">
			
	<!-- Header End -->