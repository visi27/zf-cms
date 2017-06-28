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
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="NO-CACHE" />
<meta http-equiv="PRAGMA" content="NO-CACHE" />
	
<title><?=$pageTitle?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>

<!-- BEGIN Application CSS -->
<link type="text/css" rel="stylesheet" href="css/themes/redmond/jquery-ui-1.9.2.custom.css" media="screen" />
<link type="text/css" rel="stylesheet" href="css/index.css" /> 
<link type="text/css" rel="stylesheet" href="css/plugins/noty/jquery.noty.css"/>
<link type="text/css" rel="stylesheet" href="css/plugins/layout/layout-default-latest.css" />
<link type="text/css" rel="stylesheet" href="css/noty_theme_default.css"/>
<link type="text/css" rel="stylesheet" href="css/pagination.css" />
<link type="text/css" rel="stylesheet" href="css/jquery.contextMenu.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.fancybox.css" />
<link rel="stylesheet" type="text/css" href="css/jquery.fancybox-buttons.css" />
<link rel="stylesheet" type="text/css" href="css/gallery.css" />


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
    z-index:25000 !important
}
</style>
	
<!-- END APPLICATION CSS -->

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="template/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="template/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link href="template/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="template/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="template/global/plugins/jquery-file-upload/blueimp-gallery/blueimp-gallery.min.css" rel="stylesheet"/>
<link href="template/global/plugins/jquery-file-upload/css/jquery.fileupload.css" rel="stylesheet"/>
<link href="template/global/plugins/jquery-file-upload/css/jquery.fileupload-ui.css" rel="stylesheet"/>
<!-- END PAGE LEVEL STYLES -->
 
<!-- BEGIN PAGE STYLES 
<link href="template/admin/pages/css/tasks.css" rel="stylesheet" type="text/css"/>
 END PAGE STYLES -->
 
<!-- BEGIN THEME STYLES -->
<!-- DOC: To use 'rounded corners' style just load 'components-rounded.css' stylesheet instead of 'components.css' in the below style tag -->
<link href="template/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="template/global/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="template/layout/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="template/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="template/layout/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->


<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->


<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="template/global/plugins/respond.min.js"></script>
<script src="template/global/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="template/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="template/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="template/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="template/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="template/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="template/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="template/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>

<script src="template/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!--BEGIN APP Javascript -->

	<script type="text/javascript" src="js/md5.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/noty/jquery.noty.js"></script>
	<script type="text/javascript" src="js/jquery/plugins/contextmenu/jquery.contextMenu.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.fancybox-buttons.js"></script>
	<script src="js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="js/jquery-ckeditor-fix.js"></script>

<!--END APP Javascript -->

<script src="template/global/scripts/metronic.js" type="text/javascript"></script>
<script src="template/layout/scripts/layout.js" type="text/javascript"></script>
<script src="template/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="template/layout/scripts/demo.js" type="text/javascript"></script>
<script src="template/layout/scripts/form-fileupload.js"></script>
<script>
jQuery(document).ready(function() {    
	Metronic.init(); // init metronic core components
	Layout.init(); // init current layout
	QuickSidebar.init(); // init quick sidebar
	Demo.init(); // init demo features

	<?php if (isset ( $_POST ['treeNodeId'] )) { ?>
	//$("li #<?=$treeNodeId?>").parent().closest('li').addClass('open');
	//$("li #<?=$treeNodeId?>").parent().closest('ul').css('display', 'block');
	//$("li #<?=$treeNodeId?>").addClass('active');
	
	var activeLink = jQuery("li#<?=$treeNodeId?>");
	if(activeLink.hasClass('level2')){
		//Add active class to parent menu
		activeLink.parent().parent().addClass('active');

		//Remove active class from parent menu siblings
		activeLink.siblings().removeClass('active');

		//Add active class to self
		activeLink.addClass('active');
	}

	if(activeLink.hasClass('level1')){

		//Show the submenu if we find one
		activeLink.find('.sub-menu').slideDown();
		//Hide submenu of siblings if we find one for each sibling
		activeLink.siblings().each(function(){
			$(this).find('.sub-menu').slideUp();
			$(this).find('.active').removeClass('active');
		});
		//Remove active class from siblings
		activeLink.siblings().removeClass('active');

		//Add active class to self
		activeLink.addClass('active');
	}
	<?php }?>
});


//Master Javascript
jQuery(function(){
	// my function, to load the data
	jQuery.fn.extend({
		loadContent: function (node_id, node_form, container) {
	    	// the east container selector
		  	var myContainer = jQuery(this);
		  	
			// block the center UI  
            Metronic.blockUI({
           	 	boxed: true,
                target: myContainer
            });
			// perform the ajax request
		  	jQuery.ajax({
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
        		Metronic.unblockUI(myContainer);
	  		}); 
  		}
  	});     
  	
	jQuery(document).ajaxStart(
            Metronic.blockUI({
                boxed: true,
                target: "div.page-content"
            })
	).ajaxStop(Metronic.unblockUI("div.page-content"));
		
	// the selectors
  	//var myTree = $(".ui-layout-west .ui-layout-content");
	var centerPannel = jQuery(".page-content");
  	//var eastPannel = jQuery(".ui-layout-east .ui-layout-content");
  	
	Metronic.unblockUI();

	

	jQuery(".page-sidebar-menu li a").click(function(){
	  	// remove any existing jquery dialog objects
	  	// remove any hidden div, having the id like "div-Dialog-%"
	  	$('[id^=div-Dialog-]:hidden').remove();



		var parent = $(this).parent();
		if(parent.hasClass('level2')){
			//Add active class to parent menu
			parent.parent().parent().addClass('active');
			//Add active class to self
			parent.addClass('active');
			//Remove active class from parent menu siblings
			parent.siblings().removeClass('active');
		}

		if(parent.hasClass('level1')){
			parent.find('.sub-menu').find('.active').removeClass('active');
			//Add active class to self
			parent.addClass('active');
			//Show the submenu if we find one
			parent.find('.sub-menu').slideDown();
			//Hide submenu of siblings if we find one for each sibling
			parent.siblings().each(function(){
				$(this).find('.sub-menu').slideUp();
				$(this).find('.active').removeClass('active');
			});
			//Remove active class from siblings
			parent.siblings().removeClass('active');
		}

	  	
	  	var node_form = $(this).attr('id');
	  	var node_id = $(this).parent().closest('li').attr('id')


	  	// load the center pannel
	  	if(node_form!='Blank'){
	  		centerPannel.loadContent(node_id, node_form, "layoutCenter");
	  	}
		// load the east pannel
	  	//eastPannel.loadContent(node_id, node_form, "layoutEast");
	});

	 // logout onclick
	jQuery( "a#user-logout" ).click(function() {
        Metronic.blockUI({
            boxed: true
        });
		jQuery.ajax({
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
    			Metronic.unblockUI();
        		window.location.href = "index.php";
    		}
    	});
	});

	// user profile section
	// when clicking profile
	jQuery( "a#user-profile" ).click(function() {
		if(!jQuery("#dialog-userProfile").html()){	
			jQuery('.ui-layout-north').after('<div id="dialog-userProfile"></div>');
		}
		jQuery.ajax({
            url:"index.php?c=ajax",
            type: "POST",
            dataType: "html",
            context: this,
            data: {	loadClass: "Ajax_Response_Utility",
        		  	method: "profile",
	        	  	parameter: ""	}  
 		}).done( function(response){           	 
 			jQuery( "#dialog-userProfile" ).html(response);
       	});
	});

	// reporting  problems section
	// when clicking 'Report a problem'
	jQuery( "a#reportProblem" ).click(function() {
		if(!jQuery("#dialog-reportProblem").html()){	
			jQuery('.ui-layout-north').after('<div id="dialog-reportProblem"></div>');
		}
		jQuery.ajax({
            url:"index.php?c=ajax",
            type: "POST",
            dataType: "html",
            context: this,
            data: {	loadClass: "Ajax_Response_Utility",
        			method: "reportProblem",
	        		parameter: ""	}	
 		}).done( function(response){           	 
 			jQuery( "#dialog-reportProblem" ).html(response);
       	});
	});
	
	// smart search section
	jQuery( "input#searchInput" ).autocomplete({
		minLength: 2,
		delay: 300,
		source: function( request, response ) {
			
			var moduleId = 0;
			if(jQuery('li.active.level2').length){
				moduleId = jQuery('li.active.level2').attr('id');
			}else{
				moduleId = jQuery('li.active').attr('id');
			}
			jQuery.ajax({
				url: "index.php?c=ajax",
				dataType: "json",
				data: {
					loadClass: "Ajax_Response_Utility",
					method: "mySearch",
					maxRows: 12,
					parameter: "text:"+request.term + ";moduleId:" + moduleId
				}			
			}).done( function( data ) {				
				response( jQuery.map( data, function( item ) {
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
				jQuery.ajax({
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
		        	jQuery(".page-content").html(response);
	        	    
		        	  // triger an change event over the selected radio
		        	jQuery("#table-"+ui.item.moduleId+" table[id='data-table'] tr[id='"+ui.item.id+"'] input[name='rowSelectionRadio']").change();
	        	}); 
			
		}
	});
	
	//mark the selected main Menu item
	//jQuery("#navigation #<?=$controller?>").addClass("current");
});

</script>
<!-- END JAVASCRIPTS -->

<link rel="shortcut icon" href="favicon.ico"/>
</head>


<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content">


<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="index.php">
			<img src="template/layout/img/logo.png" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!-- BEGIN NOTIFICATION DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-bell"></i>
					<span class="badge badge-default">
					7 </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3><span class="bold">12 pending</span> notifications</h3>
							<a href="javascript:;">view all</a>
						</li>
						<li>
							<ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
								<li>
									<a href="javascript:;">
									<span class="time">just now</span>
									<span class="details">
									<span class="label label-sm label-icon label-success">
									<i class="fa fa-plus"></i>
									</span>
									New user registered. </span>
									</a>
								</li>

							</ul>
						</li>
					</ul>
				</li>
				<!-- END NOTIFICATION DROPDOWN -->
				<!-- BEGIN INBOX DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<i class="icon-envelope-open"></i>
					<span class="badge badge-default">
					4 </span>
					</a>
					<ul class="dropdown-menu">
						<li class="external">
							<h3>You have <span class="bold">7 New</span> Messages</h3>
							<a href="javascript:;">view all</a>
						</li>
						<li>
							<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
								<li>
									<a href="javascript:;">
									<span class="photo">
									<img src="template/layout/img/avatar2.jpg" class="img-circle" alt="">
									</span>
									<span class="subject">
									<span class="from">
									Lisa Wong </span>
									<span class="time">Just Now </span>
									</span>
									<span class="message">
									Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
									</a>
								</li>
								<li>
									<a href="javascript:;">
									<span class="photo">
									<img src="template/layout/img/avatar3.jpg" class="img-circle" alt="">
									</span>
									<span class="subject">
									<span class="from">
									Richard Doe </span>
									<span class="time">16 mins </span>
									</span>
									<span class="message">
									Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
									</a>
								</li>

							</ul>
						</li>
					</ul>
				</li>
				<!-- END INBOX DROPDOWN -->
				
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<img alt="" class="img-circle" src="template/layout/img/avatar3_small.jpg"/>
					<span class="username username-hide-on-mobile">
					Nick </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a id = "user-profile" href="javascript:;">
							<i class="icon-user"></i> Profili Im </a>
						</li>

						<li>
							<a href="javascript:;">
							<i class="icon-envelope-open"></i> My Inbox <span class="badge badge-danger">
							3 </span>
							</a>
						</li>
						<li class="divider">
						</li>
						<li>
							<a id = "user-logout" href="javascript:;">
							<i class="icon-key"></i> Log Out </a>
						</li>
					</ul>
				</li>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- BEGIN QUICK SIDEBAR TOGGLER -->
				<!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
				<li class="dropdown dropdown-quick-sidebar-toggler">
					<a href="javascript:;" class="dropdown-toggle">
					<i class="icon-logout"></i>
					</a>
				</li>
				<!-- END QUICK SIDEBAR TOGGLER -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
		
		<!-- BEGIN Smart Search -->
		<div id="smartSearch">		
			<input class="form-control" type="text" id="searchInput" onclick="$(this).select();" size="35" placeholder="Kerko mbi modulin e perzgjedhur" value="" title="Type to search... " />
		</div>	
		<!-- END Smart Search -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			
			<?php
			$userInstance = new Table_Users();
			$user = $userInstance->find(Authenticate::getUserId())-> current();
			
			echo Ajax_Response_Utility::getSideBarMenu($treeRootId, $user->role_id, true);
			?>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->

	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">			
			<!-- BEGIN PAGE CONTENT-->
			<?php
			if (isset ( $_POST ['submit_form'] )) {
	
				$module = new Table_Modules();
				$moduleName = $module->getModuleById($treeNodeId)->form_name;
				
				$display = new $ajaxContrClass();
				$display ->$moduleName(Utility_Functions::getPostedArgs(), "submit_form");
			}else if(isset ( $_POST ['breadcrumb_submit'] )){
				$module = new Table_Modules();
				$moduleName = $module->getModuleById($treeNodeId)->form_name;

				$display = new $ajaxContrClass();
				$display ->$moduleName(Utility_Functions::getPostedArgs());
			}
			?>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
	
	<!-- BEGIN QUICK SIDEBAR -->
	<a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-close"></i></a>
	<div class="page-quick-sidebar-wrapper">
		<div class="page-quick-sidebar">
			<div class="nav-justified">
				
			</div>
		</div>
	</div>
	<!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->

<!-- BEGIN FOOTER -->
<div class="page-footer">
	<div class="page-footer-inner">
		 2015 &copy;
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!-- END FOOTER -->



</body>
<!-- END BODY -->
</html>
