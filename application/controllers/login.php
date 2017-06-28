<?php
// create the zend form object
$form = new Form_Login (array( 'id'=>md5(session_id()) ));

// set the form action, to the current controller
$form->setAction("index.php?c=".basename(__FILE__, '.php'));

// check form validation
if ($_POST['submit_form'] && $form->isValid ( $_POST ) ){
	
			
	if(Authenticate::database( 	$form->getElement("username")->getValue(),
								$form->getElement("password")->getValue() )){		
		// successful authentication
		header("Location: index.php");	
		
	}	
}
// get any error in authentication
$authResponse = "<label class='error'>" .Authenticate::getErrorMsg()."</label>";

// get the form render
$theLoginForm = $form->render(new Zend_View()).$authResponse;
 

?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Paneli i Administrimit | Identifikohuni</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8">
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="template/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="template/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="template/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="template/layout/css/login.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="template/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="template/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="template/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="template/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="template/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->

<script type="text/javascript" src="js/md5.js"></script>

<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">
	<a href="#">
	<img src="template/layout/img/logo.png" alt=""/>
	</a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
	<!-- BEGIN LOGIN FORM -->
	
		<h3 class="form-title">Identifikohu</h3>

		
		<?=$theLoginForm?>
		
		<!--
		<div class="form-group">
			
			<label class="control-label visible-ie8 visible-ie9">Username</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Password</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
		</div>
		-->
		<div class="form-actions">
			
			<a href="javascript:;" id="forget-password" class="forget-password">Keni Harruar Fjalekalimin?</a>
			
		</div>

		
		

	<!-- END LOGIN FORM -->
	<!-- BEGIN FORGOT PASSWORD FORM -->
	<form class="forget-form" action="index.html" method="post">
		<h3>Keni Harruar Fjalekalimin?</h3>
		<p>
			 Vendosni Adresen email
		</p>
		<div class="form-group">
			<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"/>
		</div>
		<div class="form-actions">
			<button type="button" id="back-btn" class="btn btn-default">Back</button>
			<button type="submit" class="btn btn-success uppercase pull-right">Submit</button>
		</div>
	</form>
	<!-- END FORGOT PASSWORD FORM -->
	
</div>
<div class="copyright">
	 Paneli i Administrimit
</div>
<!-- END LOGIN -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="template/global/plugins/respond.min.js"></script>
<script src="template/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="template/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="template/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="template/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="template/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="template/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>

<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="template/global/scripts/metronic.js" type="text/javascript"></script>
<script src="template/layout/scripts/layout.js" type="text/javascript"></script>
<script src="template/layout/scripts/demo.js" type="text/javascript"></script>
<script src="template/layout/scripts/login.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
jQuery(document).ready(function() {     
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout

	Demo.init();
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>