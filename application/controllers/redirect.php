<?php 
// destroy the session's data, in any case :))
session_unset();
// Expire the cookie (session) by seting its lifetime to the past.
setcookie(session_name(), session_id(), mktime(0, 0, 0, 1, 1, 1980), "/"); 		

if ((Authenticate::getErrorMsg() == Authenticate::SESSION_EXPIRED OR
	Authenticate::getErrorMsg() == Authenticate::SESSION_ERROR) AND 
	($_REQUEST['format'] == 'json')){	
		$returnMessage = Utility_Functions::_toJson( array("id" => md5(session_id())) );
		
}else{		
	$returnMessage = '
	<script>
		$(function() {			
			noty({
				"text":"Your session has expired due to inactivity! Redirecting to the login... "+
						   "<img width=\"16px\" height=\"16px\" src=\"images/ajax-loader.gif\"/>",
				"layout":"center",
				"type":"warning",
				"speed":10,
				"timeout":2900
			});
			
			setTimeout(function() {
				window.location  = "index.php";
			}, 2000);
		
		});
	</script>';
}

echo $returnMessage;
?>