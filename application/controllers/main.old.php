<?php 
// the default controller name inside the Main Controller
$defaultController = "edit";

// the current controller name being requested
$controller = isset($_GET['m'])?htmlentities($_GET['m']) : $defaultController;

// check if there is no ajax controller class supporting this request
if (!class_exists("Ajax_Response_".ucfirst($controller))){ //Error
	
	// get the logger class
	$logger = Zend_Registry::get('applog');
	
	// store this error
	$logger->log( __FILE__.". Menu controller: '$controller' not found.");
	
	// reroute to the default controller
	$controller = $defaultController;

}

//get working module
if (isset ( $_POST ['treeNodeId'] )) { 
	$treeNodeId = $_POST ['treeNodeId'];
}
 
//Get Selected Language
if (isset($_GET['lang'])){
	$lang = $_GET['lang'];
}else{
	$lang = isset($_SESSION['lang']['selected']) ? $_SESSION['lang']['selected'] : 'al';
}

//Set Translator to selected language
$_SESSION['lang']['selected'] = $lang;

$tr = Zend_Registry::get('translator');
$tr->setLocale($lang); 


// create the proper ajax response controller
$ajaxContrClass = "Ajax_Response_".ucfirst($controller);

// the root node id of the modules to be displayed for this controller
$treeRootId = Zend_Registry::get ( 'config' ) ->{$controller}->tree->root;

// the default module element to be rendered
$defTreeNodeId = Zend_Registry::get ( 'config' ) ->{$controller}->tree->default;

// the main page title to be displayed for this controller
$pageTitle = Zend_Registry::get ( 'config' )->{$controller}->page->title;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Cache-Control" content="NO-CACHE" />
	<meta http-equiv="PRAGMA" content="NO-CACHE" />
	
	<title><?=$pageTitle?></title>
	
	<link type="text/css" rel="stylesheet" href="css/themes/redmond/jquery-ui-1.9.2.custom.css" media="screen" />
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
	
	<link rel="stylesheet" type="text/css" href="css/accordion.menu.css" />

	
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
	<script type="text/javascript" src="js/jquery/plugins/htmlbox/htmlbox.full.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/htmlbox/htmlbox.xhtml.js"></script>
	
	<script type="text/javascript">
	var pageLayout;

	$(document).ready(function(){
		// create page layout
		pageLayout = $('body').layout({
			scrollToBookmarkOnLoad:		false // handled by custom code so can 'unhide' section first
		,	defaults: {
			}
		,	north: {
				spacing_open:			0			// cosmetic spacing
			,	togglerLength_open:		0			// HIDE the toggler button
			,	togglerLength_closed:	-1			// "100%" OR -1 = full width of pane
			,	resizable: 				false
			,	slidable:				false
			//	override default effect
			,	fxName:					"none"
			}	
		,	
			south: {
				size:					25
			,	spacing_open:			0
			,	closable:				false
			,	resizable:				false
			}
		,
			west: {
				size:					250
			,	spacing_closed:			22
			,	togglerLength_closed:	140
			,	togglerAlign_closed:	"top"
			,	togglerContent_closed:	"M<BR>o<BR>d<BR>u<BR>l<BR>e<BR>t"
			,	togglerTip_closed:		"Open & Pin Contents"
			,	sliderTip:				"Slide Open Contents"
			,	slideTrigger_open:		"mouseover"
			}
		,
			east: {
				size:					200
			,	spacing_closed:			22
			,   initClosed:				true
			,	togglerLength_closed:	40
			,	togglerAlign_closed:	"top"
			,	togglerContent_closed:	"<img src='images/support.png' border='0'/>"
			,	togglerTip_closed:		"Open & Pin Contents"
			,	sliderTip:				"Slide Open Contents"
			,	slideTrigger_open:		"click"
			}
		});
		// BIND events to hard-coded buttons in the NORTH toolbar
		pageLayout.addToggleBtn( "#tbarToggleNorth", "north" );
	});
	
	// Master Javascript
	$(function(){
		jQuery.fn.extend({
			loadContent: function (node_id, node_form, container) {
		    	// the east container selector
			  	var myContainer = $(this);
				// block the center UI  
			  	myContainer.block({ 
					message: '<h1><img src="images/ajax-loader.gif" /> Loading content...</h1>'  
				});
				// perform the ajax request
		        $.ajax({
			    	type: "GET",
		        	url: "index.php?c=ajax",
		        	dataType: "html",
		        	context: document.body,
		        	cache: false,
		        	data: {
			        	loadClass: <?="'".$ajaxContrClass."'"?>,
			        	method: node_form,
			       		parameter:  "moduleId:" + node_id + ";ajaxAction:" + container
		        	}
		        }).done( function(response){
		            // write the response in the center layout
	        		myContainer.html(response);
			        // remove the loading... message.
	        		myContainer.unblock();
    	  		}); 
	  		}
	  	});     
	  	
		$(document).ajaxStart(
			$.blockUI({ 
				css: {  
		        	opacity: .7 
		    	},  
		    	message: '<h1><img src="images/ajax-loader.gif" /> Loading...</h1>'  
			})
		).ajaxStop($.unblockUI);
			
		// the selectors
	  	//var myTree = $(".ui-layout-west .ui-layout-content");
	  	var centerPannel = $(".ui-layout-center .ui-layout-content");
	  	var eastPannel = $(".ui-layout-east .ui-layout-content");
	  	
		$.unblockUI();
		$("#accordian h3").click(function(){
			//slide up all the link lists
			//$("#accordian ul ul").slideUp();
			//slide down the link list below the h3 clicked - only if its closed
			$(this).next().toggle(400);

		});
		
		<?php if (isset ( $_POST ['treeNodeId'] )) { ?>
			$("li #<?=$treeNodeId?>").parent().closest('ul').slideDown();
			$("li #<?=$treeNodeId?>").addClass('active');
		<?php }?>	

		$("#accordian li a").click(function(){
		  	// remove any existing jquery dialog objects
		  	// remove any hidden div, having the id like "div-Dialog-%"
		  	$('[id^=div-Dialog-]:hidden').remove();

		  	var node_form = $(this).attr('id');
		  	var node_id = $(this).parent().closest('li').attr('id')
		  	$('li.active').removeClass('active');
		  	$(this).parent().closest('li').addClass('active');

		  	// load the center pannel
		  	centerPannel.loadContent(node_id, node_form, "layoutCenter");
			// load the east pannel
		  	eastPannel.loadContent(node_id, node_form, "layoutEast");
		})

		 // logout onclick
		$( "a#userLogout" ).click(function() {
			$.blockUI({ message: '<h1> Goodbye...</h1>'  });
			$.ajax({
	            url:"index.php?c=ajax",
	            type: "POST",
	            dataType: "json",
	            context: this,
	            data: {
	        		  loadClass: "Ajax_Response_Login",
	        		  method: "logOut",
		        	  parameter: ""
	        	  }
	 		}).done( function(data){
        		if(data.status){
        			$.unblockUI();
	        		window.location.href = "index.php";
        		}
        	});
		});

		// user profile section
		// when clicking profile
		$( "div#userProfile" ).click(function() {
			if(!$("#dialog-userProfile").html()){	
				$('.ui-layout-north').after('<div id="dialog-userProfile"></div>');
			}
			$.ajax({
	            url:"index.php?c=ajax",
	            type: "POST",
	            dataType: "html",
	            context: this,
	            data: {	loadClass: "Ajax_Response_Utility",
	        		  	method: "profile",
		        	  	parameter: ""	}  
	 		}).done( function(response){           	 
        		$( "#dialog-userProfile" ).html(response);
	       	});
		});

		// reporting  problems section
		// when clicking 'Report a problem'
		$( "a#reportProblem" ).click(function() {
			if(!$("#dialog-reportProblem").html()){	
				$('.ui-layout-north').after('<div id="dialog-reportProblem"></div>');
			}
			$.ajax({
	            url:"index.php?c=ajax",
	            type: "POST",
	            dataType: "html",
	            context: this,
	            data: {	loadClass: "Ajax_Response_Utility",
	        			method: "reportProblem",
		        		parameter: ""	}	
	 		}).done( function(response){           	 
	 			$( "#dialog-reportProblem" ).html(response);
	       	});
		});
		
		// smart search section
		$( "input#searchInput" ).autocomplete({
			minLength: 2,
			delay: 300,
			source: function( request, response ) {
				
				var moduleId = 0;
				if($('li.active').length){
					moduleId = $('li.active').attr('id');
				}
				$.ajax({
					url: "index.php?c=ajax",
					dataType: "json",
					data: {
						loadClass: "Ajax_Response_Utility",
						method: "mySearch",
						maxRows: 12,
						parameter: "text:"+request.term + ";moduleId:" + moduleId
					}			
				}).done( function( data ) {				
					response( $.map( data, function( item ) {
						return {
							label: item.name,
							value: item.name,
							id: item.id,
							module: item.module,
							moduleId: item.moduleId
						};
					}));	
				});
			},
			// selecting an element from the search sugestions
			select: function( event, ui ) {
					$.ajax({
				          type: "GET",
			        	  url: "index.php?c=ajax",
			        	  dataType: "html",
			        	  context: document.body,
			        	  data: {
			        		  loadClass: <?='"Ajax_Response_'.ucfirst($controller).'"'?>,
			        		  method: ui.item.module,
			        		  parameter:  "moduleId:" + ui.item.moduleId + ";itemFound:" + ui.item.id
			        	  }
			        }).done( function(response){
			        	  // write the response in the center layout
		        	      $(".ui-layout-center .ui-layout-content").html(response);
		        	    
			        	  // triger an change event over the selected radio
						  $("#table-"+ui.item.moduleId+" table[id='data-table'] tr[id='"+ui.item.id+"'] input[name='rowSelectionRadio']").change();
		        	}); 
				
			}
		});
		
		//mark the selected main Menu item
		$("#navigation #<?=$controller?>").addClass("current");

		//display jQuery tooltips		
		$( document ).tooltip({
			track: true,
			show: {
                effect: "slideDown",
                delay: 250
        	}
		});

		// remove possibility to right click
		//$("body").mousedown(function(event) {
		//    if (event.which != 1) {
		 //      	alert("E do tiiii, e dooooo :)"); 	
		//    	document.oncontextmenu=new Function("return false");
		//    }
		//});
		 
	});
	</script>
	
</head>

<body class="ui-layout-container" style="overflow: hidden; width: auto; height: auto; margin: 0px; 
		position: absolute; top: 0px; bottom: 0px; left: 0px; right: 0px;">
		
	<noscript>
			<h1 align="center"><br/><br/><b>Unavailable because JavaScript is disabled on your computer. <br/>
			Please enable JavaScript and refresh this page.</b></h1>
	</noscript>
	
	<div class="pane ui-layout-north"><!-- North --> <!-- HEADER -->
	
		<div id="logo"><?php echo $tr->_('RESTORANTE.AL');?>
			<div class="langselection" style="float:right">
				<span>
					<a href="index.php?lang=al"><img src="images/al.gif" alt="Shqip" title="Shqip"></a>
				</span>
				<span id="active_language">
					<a href="index.php?lang=en"><img src="images/en.gif" alt="English" title="English"></a>
				</span>
			</div>
		</div>
		<div id="navigation">
			<a id="tbarToggleNorth" class="button-toggle-north" title="Hide Menu">&nbsp;&nbsp;&nbsp;&nbsp;</a>
			<a id="edit" href="index.php?m=edit"><?php echo $tr->_('EDITIMI');?></a>
			<a id="report" href="index.php?m=report"><?php echo $tr->_('RAPORTE');?></a>
			<a id="security" href="index.php?m=security"><?php echo $tr->_('SIGURIA');?></a>
	
			
			<div id="smartSearch">		
				<input type="text" id="searchInput" onclick="$(this).select();"
					size="35" <?="placeholder"?>="Kerko mbi modulin e perzgjedhur" value="" 
					title="Type to search... " />
			</div>	
			
		</div>
			
	</div>

	
	<div class="pane ui-layout-south"><!-- South --> <!-- FOOTER -->
		<?php 
 			$userInstance = new Table_Users();
 			$user = $userInstance->find(Authenticate::getUserId())-> current();
			
 			//$acd = new Table_AcdRole();
			//$acdData = $acd->getDataById($user->acd_role_id);
			
 			$userDetail = $user->fullname." [ ". $acdData->structure_name."->".$acdData->function_name ." ]";
		?>	
		<div id="footer">
			<div id="userProfile" style="float:left; width:33%">
				<img id='userIcon' src='images/profile.png' border='0' title="Profili i Perdoruesit"/> 
				<a class="link" href="#"><?//=$userDetail?></a>
			</div>
			<div id="copyright" style="float:left; width:34%; text-align: center;"> &copy; CUBIC - HRM v1.0 
			</div>
			<div id="exit" style="float:left; width:33%; text-align: right;">
				<a class="link" id="userLogout" title="Mbyllni Programin" href="#"> 
				<img src="images/door_lock_16-1.png" border="0"/> Exit </a>
			</div>		
		</div>
	</div>
	
	
	<div class="pane ui-layout-west"><!-- West --> <!-- TREE -->
		
		<div class="ui-layout-content" style="padding: 0;">
			<div id="accordian">
			<?php echo Ajax_Response_Utility::getAccordionHTML($treeRootId, $user->role_id);?>
			</div>
		</div>
	</div>
	
	
	<div class="pane ui-layout-east"><!-- East --> <!-- SUPPORT -->
		<div class="header">Suport dhe Ndihme</div>
		<div class="ui-layout-content" style="padding: 0px;">
			<br/>
		
		</div>
	
	</div>
	
		
	<div class="ui-layout-center"><!-- Center -->
		<div class="ui-layout-content" style="padding:0;">			
			<?php
			if (isset ( $_POST ['submit_form'] )) {
	
				$module = new Table_Modules();
				$moduleName = $module->getModuleById($treeNodeId)->form_name;
				
				$display = new $ajaxContrClass();
				$display ->$moduleName(Utility_Functions::getPostedArgs(), "submit_form");
			}
			?>
		</div>
	</div>
	
</body>
</html>