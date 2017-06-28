<!-- marilda: js & css for alerts -->
<script type="text/javascript" src="js/jquery/jquery.noty.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.noty.css"/>
<link rel="stylesheet" type="text/css" href="css/noty_theme_default.css"/>
<!-- end js & css for alerts -->
<link href="css/pagination.css" rel="stylesheet" type="text/css" />

<!--header empty place-->
<div id="headWrapper" style="padding: 3px 0 0 0">
<div id="welcome" class="relPosition text">
		<?php 
		$userInstance = new Table_Users();
		$user = $userInstance->find(Authenticate::getUserId())-> current();
		
		$acd = new Table_AcdRole();
		$acdData = $acd->getDataById($user->acd_role_id); 

		echo "<img id='userIcon' src='images/user.png'>:". 
		$user->fullname." | ". $acdData->structure_name."->".$acdData->function_name;
		?>	
	<div id="logOut">
		<a class="link" id="reportProblem" href="#"> <img src="images/edit.png"
			border="0"> Raporto nje problem </a>
	    <b>&nbsp;|&nbsp;</b> 
	    <a class="link" id="userProfile" href="#"> <img src="images/profile.png"
			border="0"> Profili <b>&nbsp;|&nbsp;</b> 
		</a> 
		<a class="link" id="userLogout" href="#"> <img src="images/lock.png"
		border="0">Dil </a>
	</div>
</div>

<div id="fastSearchWrapper">
	<div class="text">Kerkim i shpejte....</div>
	
	<div style="position: relative">
	<div><input type="text" id="fastSearchInput" onClick="$(this).val('');"
		size="35" value="" title="Please type in and search " /></div>
	<div id="fastSearch"><img id="goQuickSearch" style="cursor: pointer"
		onclick='$("#fastSearchInput").autocomplete( "search", $("#fastSearchInput").val() );'
		src="images/ok_24x24.png"></div>
	</div>
</div>

</div>

<div id="colortab" class="ddcolortabs">
<ul>
	<li><a href="index.php?c=personel"
		title="Te Dhenat Personale" id="tab1"> <span> <img
		src="images/searchBook_24.png" border="0"> PERSONI</span></a></li>
	<li><a href="index.php?c=report" title="Raportet" id="tab2"><span><img
		src="images/viewBook_24.png" border="0"> RAPORTE</span></a></li>
	<li><a href="index.php?c=Queries" title="Kerkimet" id="tab3"> <span> <img
		src="images/report_24.png" border="0"> KERKIM</span></a></li>
	<li><a href="index.php?c=statistics" title="Statistikat" id="tab4"> <span> <img
		src="images/reportChart_24.png" border="0"> STATISTIKA</span></a></li>
	<li><a href="index.php?c=security" title="Siguria" id="tab5"><span><img
		src="images/searchDb_24.png" border="0"> SIGURIA</span></a></li>
	<li><a href="index.php?c=system" title="Sistemi" id="tab6"><span><img
		src="images/db-web.png" border="0"> SISTEMI</span></a></li>
</ul>
</div>
<div class="ddcolortabsline">&nbsp;</div>


<script type="text/javascript">
 $(function() {
	// smart search section
	$( "input#fastSearchInput" ).autocomplete({
		source: function( request, response ) {
			var moduleId = 0;
			if($("#tree").dynatree("getActiveNode")){
				moduleId = $("#tree").dynatree("getActiveNode").data.key;
			}
			$.ajax({
				minLength: 2,
				delay: 0,
				url: "index.php?c=ajax",
				dataType: "json",
				data: {
					loadClass: "Ajax_Response_Utility",
					method: "mySearch",
					maxRows: 12,
					parameter: "text:"+request.term + ";" + "moduleId:"+moduleId
				},
				success: function( data ) {
					response( $.map( data, function( item ) {
						return {
							label: item.id + "; " + item.name,
							value: item.id + "; " + item.name,
							id: item.id,
							module: item.module,
							moduleId: item.moduleId
						};
					}));	
				}
			});
		} ,
		// selecting an element from the search sugestions
		select: function( event, ui ) {
			if(!$("#tree").dynatree("getActiveNode") || ($("#tree").dynatree("getActiveNode").data.key <= 2)){
				$("#tree").dynatree("getTree").activateKey(ui.item.id);
			}else{
				$.ajax({
			          type: "GET",
		        	  url: "index.php?c=ajax",
		        	  data: {
		        		  loadClass: <?='"Ajax_Response_'.ucfirst(Dispatcher::getControllerName()).'"'?>,
		        		  method: ui.item.module,
			        	  parameter:  "moduleId:" + ui.item.moduleId + ";itemFound:" + ui.item.id
		        	  },
		        	  context: document.body,
		        	  success: function(response){
		        	    $("#rightcolumn").html(response);
			        	 // triger an click event over the selected radio
						$("#table-"+ui.item.moduleId+" table[id='data-table'] tr[id='"+ui.item.id+"'] input[name='rowSelectionRadio']").click();
		        	  }
		        	}); 
			}
		}
	});

	// reporting  problems section
	// when clicking 'Report a problem'
	$( "a#reportProblem" ).click(function() {
		if(!$("#dialog-reportProblem").html()){	
			$('.ddcolortabsline').after('<div id="dialog-reportProblem"></div>');
		}
		$.ajax({
            url:"index.php?c=ajax",
            type: "POST",
            data: {
        		  loadClass: "Ajax_Response_Utility",
        		  method: "reportProblem",
	        	  parameter: ""
        	  },
        	 context: this,
        	 success: function(response){
        		$( "#dialog-reportProblem" ).html(response);
        	 }
 		});
	});

	
	// user profile section
	// when clicking profile
	$( "a#userProfile" ).click(function() {
		if(!$("#dialog-userProfile").html()){	
			$('.ddcolortabsline').after('<div id="dialog-userProfile"></div>');
		}
		$.ajax({
            url:"index.php?c=ajax",
            type: "POST",
            data: {
        		  loadClass: "Ajax_Response_Utility",
        		  method: "profile",
	        	  parameter: ""
        	  },
        	 context: this,
        	 success: function(response){
        		$( "#dialog-userProfile" ).html(response);
        	 }
 		});
	});

	// logout onclick
	$( "a#userLogout" ).click(function() {
		$.blockUI({ message: '<h1> Goodbye...</h1>'  });
		$.ajax({
            url:"index.php?c=ajax",
            type: "POST",
            data: {
        		  loadClass: "Ajax_Response_Login",
        		  method: "logOut",
	        	  parameter: ""
        	  },
        	 context: this,
        	 success: function(response){
        		 $.unblockUI();
        		 window.location = 'index.php';
        	 }
 		});
	});
	
 });	
</script>

<!-- broadcast section only in homepage-->
<?php 
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

