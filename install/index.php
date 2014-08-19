<?php 

$script_name = "Answers";
$installer_version = "2.2";

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>AskFoxOs Installer </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="BeetleDeveloper - JDLA">

    <!-- The styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
	 <link href="css/chosen.css" rel="stylesheet">
	
	<style>
	body{
		margin-top: 5%;
	}
	</style>
	
    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- the fav and touch icons -->
    <link rel="shortcut icon" href="ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
	
	<script src="js/jquery.js"></script>
	<script src="js/custom.js"></script>
	<script src="js/jquery.jcarousel.min.js"></script>
	<script src="js/jquery.pikachoose.js"></script>

  </head>

  <body>

	<div class="container">


<div class="hero-unit">

	<h3>AskFoxOs Installation</h3>
	
	<hr />
	
	<div class="row-fluid center">
		<div class="span12">
			<img src="logo.jpg" alt="Logo">
		</div>
	</div>
	
	<hr />

	<div class="row-fluid center">
		<div class="span6" style="margin-top: -21px;margin-bottom: -21px;padding: 15px;float: left;">
			<!-- <a href="#upgrade" data-toggle="modal" class="btn btn-link"><h3 style="color: rgb(99, 99, 99);">Upgrade</h3></a> -->
			<h3 style="color: rgb(162, 162, 162);margin-top: 15px;">No Upgrade Available</h3>
		</div>
		<div class="span6" style="border-left: 1px solid lightgray;margin-top: -21px;margin-bottom: -21px;padding: 15px;float: right;">
			<a href="#install" data-toggle="modal" class="btn btn-link"><h3 style="color: rgb(99, 99, 99);">Fresh Install</h3></a>
		</div>
	</div>
	
</div>

<div id="install" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Fresh Install</h3>
  </div>
  <div class="modal-body">
	Before continuing please make sure that you have done the following:
	<ul>
		<li>Backed Up the Target Database if it is not empty.</li>
	</ul>
	<strong>This installer WILL delete all data inside of the target database, so make sure no other script is using that database.</strong><br />
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <a href="install.php" class="btn btn-primary">Install</a>
  </div>
</div>


      		<footer class="well wellf text-center">
		&copy; 2014 BeetleDeveloper.<span style="float:right"><a href="http://forum.beetledeveloper.tk"></a></span>
		<a href="<?php echo WWW; ?>developer_api">Developer API AskFoxOs</a>
		        <p class="pull-right "><a href="#top"><i class="fa fa-arrow-circle-o-up fa-3x"></i></a></p>
        <div class="links text-center">
          <a href="http://forum.beetledeveloper.tk" onclick="pageTracker._link(this.href); return false;">Forums</a>
          <a href="https://twitter.com/thomashpark">Twitter</a>
          <a href="https://github.com/beetledeveloper">GitHub</a>
          <a href="#">Donate</a>
        </div>
        Developer by <a href="mailto:jdlandazaball@gmail.com" rel="nofollow">JDLA</a><br>
        General Public License, <a href="http://opensource.org/licenses/gpl-3.0.html" rel="nofollow">version 3 (GPL-3.0)</a>.<br>
        Based on <a href="http://getbootstrap.com/2.3.2/" rel="nofollow">Bootstrap</a>
        Icons from <a href="http://fortawesome.github.com/Font-Awesome/" rel="nofollow">Font Awesome</a>. 
        Web fonts from <a href="http://www.google.com/webfonts" rel="nofollow">Google</a>
			
		</footer>

    </div> <!-- /container -->

    <!-- The javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
    <script src="js/google-code-prettify/prettify.js"></script>
    <script src="js/application.js"></script>
	<script src="js/bootstrap.min.js"></script>
 	<script src="js/chosen.jquery.min.js"></script>
	<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
  </body>
</html>