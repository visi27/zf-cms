<?php

class Utility_Accordion {
	
	public function loadDefault(){
	
		echo '<div id="accordion">
			
				<div class="group">
					<label> Veprimet e fundit</label>
					<div>
						<p>
							Ketu duhet te shfaqen statistikat mbi perdorimin e 
							programit nga perdoruesi aktiv.
						</p>
					</div>
				</div>
				 
				<div class="group" id="technical_support">
					<label> Suport Teknik</label>
					<div>
						<p>
							<a class="link" id="reportProblem" href="#"> 
							<img src="images/support.png" border="0"/> Raporto </a>
						</p>
					</div>
				</div>
				
			</div>';
		

 echo  '<script>	
 		// reporting  problems section
		// when clicking Report a problem
 		$( "a#reportProblem" ).click(function() {
			if(!$("#dialog-reportProblem").html()){
				$(".ddcolortabsline").after("<div id=\'dialog-reportProblem\'></div>");
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
 				
		//accordion menu on the right side
		var icons = {
			      header: "ui-icon-circle-arrow-e",
			      activeHeader: "ui-icon-circle-arrow-s"
		};    
		$( ".ui-layout-east #accordion" ).accordion({
			 icons: icons,
		     heightStyle: "content",
		     collapsible: true,
		     header: "> div > label"
		})
		.sortable({
	         axis: "y",
	         handle: "label",
	         stop: function( event, ui ) {
		         // IE doesn\'t register the blur when sorting
		         // so trigger focusout handlers to remove .ui-state-focus
		         ui.item.children( "label" ).triggerHandler( "focusout" );
	         }
		 });
		</script>';
	}
}

?>