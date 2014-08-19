<?php require_once("includes/inc_files.php"); 
?>
<?php	
    $session->logout();
	
	$msg = $_GET['msg'];
	
	if (isset($_GET['msg'])) {
		if ($msg == "suspended") {
			$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Su cuenta ha sido suspendida, por favor póngase en contacto con apoyo..</div>");
		} else if ($msg == "not_found") {
			$session->message("<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>Lo sentimos, pero no podemos encontrar su cuenta, por favor póngase en contacto con soporte.</div>");
		} else if ($msg == "maintenance") {
			$session->message("<div class='alert alert-info'><button type='button' class='close' data-dismiss='alert'>×</button>Lo sentimos, pero estamos haciendo actualmente algunos trabajos de mantenimiento</div>");
		}
	} else {
		$session->message("<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>×</button>Usted ha cerrado la sesión con éxito </div>");
	}

	redirect_to("login");
?>