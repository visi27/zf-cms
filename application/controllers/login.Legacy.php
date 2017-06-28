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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
	<title>HR - Authentication Panel</title>
	<script type="text/javascript" src="js/md5.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui-1.9.2.min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/block/jquery.blockUI.js"></script>
	
	<link rel="stylesheet" href="css/themes/ui-lightness/jquery-ui-1.9.2.custom.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/index.css" type="text/css" media="screen" />
	<link rel="SHORTCUT ICON" href="images/icons/flag.ico" /> 
	
<style>
		body { font-size: 72.5%; }
		select {width:174px;}
		label, input { display:block; }
		input.text { margin-bottom:12px; width:95%; padding: .4em; }
		fieldset { padding:0; border:0; margin-top:25px; }
		.ui-dialog .ui-state-error { padding: .3em; }
		.error{		
			color:red;
			margin: 5px 0px;		
		}
</style>
</head>

<body>
<noscript>
		<h1 align="center"><br/><br/><b>Unavailable because JavaScript is disabled on your computer. <br/>
		Please enable JavaScript and refresh this page.</b></h1>
</noscript>

<!-- modal dialog -->
<script type="text/javascript">
//the main javascript
	$(function() {
		// remove any existing dialog with the same id
		$( "#userLogin" ).dialog({
			title: "Paneli i autentifikimit",
			closeOnEscape: false,
			autoOpen: true,
			height: "auto",
			width: "270",
			show: "blind",
			stack: true,
			modal: true,
			open: function(event, ui) {
				$(".ui-dialog-titlebar-close").hide();
			}
		});
		
		$("#username").focus();	
		
		$("body").keydown(function(event) {
			if (event.keyCode == "13") {
			    $("#userLogin button[id=\'submit_form\']").click();
			}
		});
	
	});// end jQuery function
</script>

<div id="userLogin">
	<?=$theLoginForm?>
</div>
</body>
</html>