<?php
class Ajax_Response_Edit_RecipesFromWeb extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
	        
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		$elements =	array("zf" 	=> 	array("title","author", "servings", "total_time", "difficulty", "category", "cuisine", 
		                                  "meal", "receipt_type", "seasonality", "base_product", "festivity"),
						  "alias"=> array("ID", "Autori", "Porcionet", "Kohezgjatja", "Veshtiresia", "Kategoria", "Lloji i Kuzhines", "Vakti", 
						                  "Lloji i Recetes", "Sezonaliteti","Produkti Baze", "Festa"));
		
		$buttons = array("new"=>false, "edit"=>false, "delete"=>true);
		$myGrid = new Utility_Grid($module, $this->_action, $elements, $ajax=true, $buttons);
	
				
		// select method to generate the grid resultset
		$myGrid->setSelectMethod("selectRowsForGrid");
	
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
	
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		
		// hide the table header called Id
		$myGrid->hideColumn("ID","id_td");	
		
		$myGrid->setDialogSize("700px");
		
		if(isset($_SESSION['receipt_from_web']['selected'])){
		    $myGrid->setSelectedElement($_SESSION['receipt']['selected']);
		}
		if(isset($this->_params["itemFound"])){
		    $myGrid->setSelectedElement($this->_params["itemFound"]);
		}
		
		
		//*********    START TOOLBAR FILTER SECTION     ********//
		
// 		//Author Filter
// 		$authorAutocomplete = new Utility_Autocomplete("filterAuthor", "Ajax_Response_Utility", "getAuthors");
		
// 		$authorAutocomplete->setDefaultText("Autori");
// 		$authorAutocomplete->setMapedData(
// 		    $mapedData = array("label"	=> "id, firstname, lastname",
// 		        "value"	=>	"id, firstname, lastname",
// 		        "id" => 'id'));
		
// 		$myGrid->addFilter($authorAutocomplete, $dbColumn = "receipts.author", " = ");
// 		//END Author Filter
		
		//Category Filter
		
		
		$myGrid->setScript('
		    
// 		    // pas ngarkimit te grides, vendos statuset e emerimeve me icona																												
// 			$("#data-table > tbody  > tr").each(function(index) {
// 		      var status = $(this).find("td#receipt_status_td > label");

		    
// 	          if(status.html() == "0"){
// 	               status.attr("title", "Recete e Papublikuar");
// 			       status.html(\'<img style="width:18px;height:18px" border="0" src="images/status-unredacted.png">\');
// 	          }
		    
// 		      if(status.html() == "1"){
// 	               status.attr("title", "Recete e Publikuar");
// 			       status.html(\'<img style="width:18px;height:18px" border="0" src="images/status-published.png">\');
// 	          }
// 	        });
		    
			$("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']").change(function() {
			    $(this).attr(\'checked\',\'checked\');
			    var selectedRowId = $(this).val();
		        $.ajax({
			        type: "GET",
			        url: "index.php?c=ajax",
			        data: {
			            loadClass: "Ajax_Response_Utility",
			            method: "selectReceiptFromWeb",
			            parameter: selectedRowId
	                },
			        success: function(response){
			            //$("#info-panel").html(response); // old version
			            // highlight the selected table row
			            $( "#'.$myGrid->getViewPaneId().' table[id=\'data-table\'] tr").removeClass("selected");
			            $( "#'.$myGrid->getViewPaneId().' table[id=\'data-table\'] tr[id=\'"+selectedRowId+"\']").addClass("selected");
	                }
	            });
		
	        });');
		
		 
		$header = $this->renderBreadCrumb();
		
		$output = $header.$myGrid->render();
		
		// button UnPublish Article
		$preview_recipe = HtmlView::displayButton("previewRecipe".$myGrid->getModuleId(), "Shiko",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Shiko Receten',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
			        if (selectedRowId){
    		            var win = window.open("http://admin.shije.al/?c=recipe&r="+selectedRowId, "_blank");
    		    
                		if(win){
                		    //Browser has allowed it to be opened
                		    win.focus();
                		}else{
                		    //Broswer has blocked it
                		    alert("Please allow popups for this site");
                		}	
		            }else{
		              alert("Zgjidhni nje recete fillimisht");
		            }
	
		    
		') // array
		); // end toolbar button

		$output .= $preview_recipe;
		
		echo $output;
		
	}
	
	public function makeFeatured_Action(){
	    // load the neccesary model
	    $classObj = new Table_Receipts();
	    $featured = $classObj->makeFeatured($this->_params['itemFound']);
	    if($featured){
	        return true;
	    }
	    return false;
	
	}
	
	public function unPublishReceipt_Action(){
	    $receiptId = $this->_params['itemFound'];
	    $receiptObj = new Table_Receipts();
	    $status = $receiptObj->unPublishReceipt($receiptId);
	     
	    // render the grid
	    $this->layoutCenter_Action();
	    if ($status === True || intval($status)>0){
	        $this->getScript('noty({"text":"Receta u kthye per redaktim!","layout":"Center","type":"success","speed":10,"timeout":3000,"closeButton":true,"closeOnSelfClick":true});');
	    }else{
	        $this->getScript('noty({"text":"Receta nuk mund te kthehej per redaktim! Provoni Perseri!","layout":"Center","type":"success","speed":10,"timeout":10000,"closeButton":true,"closeOnSelfClick":true});');
	    }
	}
	
	private function getScript($script, $render = true){
	    $script = "\r\n<script>\r\n $(function() {\r\n" . $script . "\r\n}); \r\n</script>\r\n";
	    if($render == true)
	        echo $script;
	    else
	        return $script;
	}
}