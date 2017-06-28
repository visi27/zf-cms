<?php 

$proc_class = $_REQUEST ['loadClass'];	
$function = $_REQUEST ['method'];
$parameter = explode(";", $_REQUEST ['parameter']);

$s = new $proc_class ();
if (method_exists ( $s, $function )){
	call_user_func_array ( array (&$s, $function ), array($parameter) );
	$content = ob_get_contents ();
	echo (ob_end_clean ()==TRUE)? $content :"Error Displaying data";
}
else
	echo "ERROR: [$function] Not validated.";
	
		
