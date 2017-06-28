
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="NO-CACHE" />
	<meta http-equiv="PRAGMA" content="NO-CACHE" />
	
	<title><?=$pageTitle?></title>
	
	<link rel="SHORTCUT ICON" href="images/icons/flag.ico" /> 
	<link type="text/css" rel="stylesheet" href="css/themes/ui-lightness/jquery-ui-1.9.2.custom.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="css/index.css" /> 
	<link type="text/css" rel="stylesheet" href="css/plugins/noty/jquery.noty.css"/>
	<link type="text/css" rel="stylesheet" href="css/plugins/tree/ui.dynatree.css" />
	<link type="text/css" rel="stylesheet" href="css/plugins/layout/layout-default-latest.css" />
	<link type="text/css" rel="stylesheet" href="css/noty_theme_default.css"/>
	<link type="text/css" rel="stylesheet" href="css/pagination.css" />
	<link type="text/css" rel="stylesheet" href="css/jquery.contextMenu.css" />
	<link rel="stylesheet" type="text/css" href="css/archive.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.fancybox.css" />
	<link rel="stylesheet" type="text/css" href="css/jquery.fancybox-buttons.css" />

	
	<style type="text/css">
	html, body {
		background:	#666;
		width:		100%;
		height:		100%;					
		padding:	0;
		margin:		0;
		overflow:	auto; /* when page gets too small */
	}
	#container {
		background:	#999;
		height:		100%;
		margin:		0 auto;
		width:		100%;
		max-width:	100%;
		min-width:	920px;
		_width:		920px; /* min-width for IE6 */
	}
	.pane {
		display:	none; /* will appear when layout inits */
	}
	.ui-autocomplete
    {
        position:absolute;
        cursor:default;
        z-index:4000 !important
    }
	</style>
	
	<script type="text/javascript" src="js/md5.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-1.8.3.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui-1.9.2.min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/layout/jquery.layout-latest.min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/tree/jquery.dynatree.min.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/block/jquery.blockUI.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/noty/jquery.noty.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/contextmenu/jquery.contextMenu.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.fancybox-buttons.js"></script>
	
	
			
			<!-- broadcast section only in homepage-->
<?php 
Zend_Registry::get('applog')->log("REQUEST: ".$_SERVER['REQUEST_URI']);
if (strcmp ($_SERVER['REQUEST_URI'],"/index.php")==0) { 
	
	$broadcast_msg_time = Zend_Registry::get('config')->broadcast_msg_time;	
?>
	<link href="css/banner-message.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery/banner-message-min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
					$.ajax({
			            url:"index.php?c=ajax",
			            type: "POST",
			            data: {
			        		  loadClass: "Ajax_Response_Utility",
			        		  method: "getMessages",
				        	  parameter: <?=$broadcast_msg_time?>
			        	},
			        	context: this,
			        	success: function(response){
				        	    // shfaq banner nese ka msg
	                            if (response != '' ) {	                            		        	    
					 		        $().showRibbonMessage({ 
							        	color: 'green',
								        message : response
									});
	                            }
	                            else {
					 		        $().hideRibbonMessage();
	                            }							
			        	}		        	 
			 		});
	
	});
	</script>
<?php } ?>
<!-- fund broadcast section only in homepage-->
</body>
</html>