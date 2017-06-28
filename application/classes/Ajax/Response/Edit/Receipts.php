<?php
class Ajax_Response_Edit_Receipts extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
	        
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		$elements =	array("zf" 	=> 	array("title", array("name"=>"description", "type"=>"textarea"), "instructions","author", "publish_date", "publish_time", "servings", "total_time", "difficulty", 
		                  "category", "cuisine", "meal", "receipt_type", "seasonality", "base_product", "festivity" ,"video"),
						//"db" 	=> 	array("id_award", "award_name", "order_nr", "order_date", "awards_type"), //always start with the primary key
						  "alias"=> array("ID", "Statusi", "Titulli i Recetes","Autori", "Porcionet", "Kohezgjatja", "Veshtiresia", "Kategoria", "Lloji i Kuzhines", "Vakti", 
						                  "Lloji i Recetes", "Sezonaliteti","Produkti Baze", "Festa","Data e Publikimit", "Ora e Publikimit"));
		
		$myGrid = new Utility_Grid($module, $this->_action, $elements, $ajax=true);
	
				
		// select method to generate the grid resultset
		$myGrid->setSelectMethod("selectRowsForGrid");
	
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
	
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		
		// hide the table header called Id
		$myGrid->hideColumn("ID","id_td");	
		
		$myGrid->setDialogSize("700px");
		
		if(isset($_SESSION['receipt']['selected'])){
		    $myGrid->setSelectedElement($_SESSION['receipt']['selected']);
		}
		if(isset($this->_params["itemFound"])){
		    $myGrid->setSelectedElement($this->_params["itemFound"]);
		}
		
		
		//*********    START TOOLBAR FILTER SECTION     ********//
		
		//Receipt Status Filter
		$statusAutocomplete = new Utility_Autocomplete("filterStatus", "Ajax_Response_Utility", "getReceiptStatus");
		
		$statusAutocomplete->setDefaultText("Statusi i Recetes");
		$statusAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		$myGrid->addFilter($statusAutocomplete, $dbColumn = "receipts.receipt_status", " = ");
		//END Receipt Status Filter
		
		//Daily Receipt Filter
		$dailyAutocomplete = new Utility_Autocomplete("filterDaily", "Ajax_Response_Utility", "getYesNo");
		
		$dailyAutocomplete->setDefaultText("Receta te Dites");
		$dailyAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($dailyAutocomplete, $dbColumn = "receipts.featured", " = ");
		//END Daily Receipt Filter
		
		//Author Filter
		$authorAutocomplete = new Utility_Autocomplete("filterAuthor", "Ajax_Response_Utility", "getAuthors");
		
		$authorAutocomplete->setDefaultText("Autori");
		$authorAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, firstname, lastname",
		        "value"	=>	"id, firstname, lastname",
		        "id" => 'id'));
		
		$myGrid->addFilter($authorAutocomplete, $dbColumn = "receipts.author", " = ");
		//END Author Filter
		
		//Category Filter
		$categoryAutocomplete = new Utility_Autocomplete("filterCategory", "Ajax_Response_Utility", "getReceiptCategories");
		
		$categoryAutocomplete->setDefaultText("Kategoria e Recetes");
		$categoryAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($categoryAutocomplete, $dbColumn = "receipts.category", " = ");
		//END Category Filter
		
		//Cuisine Filter
		$cuisineAutocomplete = new Utility_Autocomplete("filterCuisine", "Ajax_Response_Utility", "getReceiptCuisineTypes");
		
		$cuisineAutocomplete->setDefaultText("Lloji i Kuzhines");
		$cuisineAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($cuisineAutocomplete, $dbColumn = "receipts.cuisine", " = ");
		//END Cuisine Filter
		
		//Meal Filter
		$mealAutocomplete = new Utility_Autocomplete("filterMeal", "Ajax_Response_Utility", "getReceiptMeals");
		
		$mealAutocomplete->setDefaultText("Vakti");
		$mealAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($mealAutocomplete, $dbColumn = "receipts.meal", " = ");
		//END Meal Filter
		
		//Receipt Type Filter
		$typeAutocomplete = new Utility_Autocomplete("filterReceiptType", "Ajax_Response_Utility", "getReceiptTypes");
		
		$typeAutocomplete->setDefaultText("Lloji i Recetes");
		$typeAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($typeAutocomplete, $dbColumn = "receipts.receipt_type", " = ");
		//Receipt Type Filter
		
		//Seasonality Filter
		$seasonalityAutocomplete = new Utility_Autocomplete("filterSeasonality", "Ajax_Response_Utility", "getReceiptSeasonalities");
		
		$seasonalityAutocomplete->setDefaultText("Sezonaliteti");
		$seasonalityAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($seasonalityAutocomplete, $dbColumn = "receipts.seasonality", " = ");
		//END Seasonality Filter
		
		//Base Product Filter
		$baseProductAutocomplete = new Utility_Autocomplete("filterBaseProduct", "Ajax_Response_Utility", "getReceiptBaseProducts");
		
		$baseProductAutocomplete->setDefaultText("Produkti Baze");
		$baseProductAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($baseProductAutocomplete, $dbColumn = "receipts.base_product", " = ");
		//END Base Product Filter
		
		//Festivity Filter
		$festivityAutocomplete = new Utility_Autocomplete("filterFestivity", "Ajax_Response_Utility", "getReceiptFestivities");
		
		$festivityAutocomplete->setDefaultText("Festa");
		$festivityAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		
		$myGrid->addFilter($festivityAutocomplete, $dbColumn = "receipts.festivity", " = ");
		//END Festivity Filter
		//*********    END TOOLBAR FILTER SECTION     ********//
		
		
		$myGrid->addAutoComplete("author", "Ajax_Response_Utility", "getAuthors",
		    array(	"value" => 'id, firstname, lastname',
		        "id" => 'id',
		        "label" => 'firstname, lastname'),
		    0, $clearOnExit=true, $minLength=0);
			
		$myGrid->addAutoComplete("category", "Ajax_Response_Utility", "getReceiptCategories",
				array(	"value" => 'id, name',
						"id" => 'id',
						"label" => 'name'),
				0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("cuisine", "Ajax_Response_Utility", "getReceiptCuisineTypes",
		    array(	"value" => 'id, name',
		        "id" => 'id',
		        "label" => 'name'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("meal", "Ajax_Response_Utility", "getReceiptMeals",
		    array(	"value" => 'id, name',
		        "id" => 'id',
		        "label" => 'name'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("receipt_type", "Ajax_Response_Utility", "getReceiptTypes",
		    array(	"value" => 'id, name',
		        "id" => 'id',
		        "label" => 'name'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("seasonality", "Ajax_Response_Utility", "getReceiptSeasonalities",
		    array(	"value" => 'id, name',
		        "id" => 'id',
		        "label" => 'name'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("base_product", "Ajax_Response_Utility", "getReceiptBaseProducts",
		    array(	"value" => 'id, name',
		        "id" => 'id',
		        "label" => 'name'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("festivity", "Ajax_Response_Utility", "getReceiptFestivities",
		    array(	"value" => 'id, name',
		        "id" => 'id',
		        "label" => 'name'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("difficulty", "Ajax_Response_Utility", "getReceiptDifficulty",
		    array(	"value" => 'id, name',
		        "id" => 'id',
		        "label" => 'name'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->setScript('
		    
		    // pas ngarkimit te grides, vendos statuset e emerimeve me icona																												
			$("#data-table > tbody  > tr").each(function(index) {
		      var status = $(this).find("td#receipt_status_td > label");

		    
	          if(status.html() == "1"){
	               status.attr("title", "Recete e Paredaktuar");
			       status.html(\'<img style="width:18px;height:18px" border="0" src="images/status-unredacted.png">\');
	          }
		    
		      if(status.html() == "2"){
	               status.attr("title", "Recete ne Redaktim");
			       status.html(\'<img style="width:18px;height:18px" border="0" src="images/status-redacting.png">\');
	          }
		    
		      if(status.html() == "3"){
	               status.attr("title", "Recete e Publikuar");
			       status.html(\'<img style="width:18px;height:18px" border="0" src="images/status-published.png">\');
	          }
	        });
		    
			$("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']").change(function() {
			    $(this).attr(\'checked\',\'checked\');
			    var selectedRowId = $(this).val();
		        $.ajax({
			        type: "GET",
			        url: "index.php?c=ajax",
			        data: {
			            loadClass: "Ajax_Response_Utility",
			            method: "selectReceipt",
			            parameter: selectedRowId
	                },
			        success: function(response){
			            //$("#info-panel").html(response); // old version
			            // highlight the selected table row
			            $( "#'.$myGrid->getViewPaneId().' table[id=\'data-table\'] tr").removeClass("selected");
			            $( "#'.$myGrid->getViewPaneId().' table[id=\'data-table\'] tr[id=\'"+selectedRowId+"\']").addClass("selected");
	                }
	            });
		    
		          $.ajax({
						url:"index.php?c=ajax",
						type: "GET",
						data: {
							loadClass: "Ajax_Response_Gallery",
							method: "getGalleryHtml",
							parameter: "receiptId:"+selectedRowId
						},
						context: this,
						cache: false,
						dataType: "html",
						success: function(response){
							$("#dragdropwrapper").html(response);
						}
					});
		
	        });');
		
		 
		$header = $this->renderBreadCrumb();
		
		$output = $header.$myGrid->render();
		
		
		// button Publish Article
		$publish_button = HtmlView::displayButton("publishReceipt".$myGrid->getModuleId(), "Publiko",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Publiko Receten',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
		            var centerPannel = jQuery(".page-content");
					if(selectedRowId){
					// custom notify message
					noty({
					    text: "Jeni i sigurte se doni te publikoni receten e perzgjedhur!?\r\n",
						layout:"center",
						modal:true,
					    buttons: [
					      {type: "btn btn-mini btn-primary", text: "Po", click: function($noty) {
					          $noty.close();
							    Metronic.blockUI({
                                    boxed: true,
                                    target: "div.page-content"
                                })
						          $.ajax({
						            url:"index.php?c=ajax",
						            type: "POST",
						            data: {
						        		  loadClass: "Ajax_Response_Edit",
						        		  method: "Receipts",
							        	  parameter: "ajaxAction:publishReceipt" + ";" + "itemFound:"+selectedRowId + ";" + "moduleId:"+'.$module.'
						        	  },
						        	context: document.body,
			          	  			success: function(response){
			          	    			centerPannel.html(response);
						        	}
						 		});
							  // remove the loading... message.
		        	    	  Metronic.unblockUI("div.page-content");
					        }
					      },
					      {type: "btn btn-mini btn-danger", text: "Jo", click: function($noty) {
					          $noty.close();
					          noty({force: true, text: "Procesi i anullimit u nderpre.", type: "error",layout:"topCenter",closeButton:true,timeout:3000});
					        }
					      }
					    ],
					    closable: false,
					    timeout: false
					  });
	 				}// row selected
	 				else {
	 					alert("Ju lutem, zgjidhni nje element nga tabela .");
	 				}') // array
		); // end toolbar button
		
		// button UnPublish Article
		$un_publish_button = HtmlView::displayButton("unPublishReceipts".$myGrid->getModuleId(), "Ktheje Per Redaktim",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Ktheje Per Redaktim',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
					var centerPannel = jQuery(".page-content");
		            if(selectedRowId){
					// custom notify message
					noty({
					    text: "Jeni i sigurte se doni te ktheni per redaktim receten e perzgjedhur!?\r\n",
						layout:"center",
						modal:true,
					    buttons: [
					      {type: "btn btn-mini btn-primary", text: "Po", click: function($noty) {
					          $noty.close();
							  Metronic.blockUI({
                                    boxed: true,
                                    target: "div.page-content"
                                })
						          $.ajax({
						            url:"index.php?c=ajax",
						            type: "POST",
						            data: {
						        		  loadClass: "Ajax_Response_Edit",
						        		  method: "Receipts",
							        	  parameter: "ajaxAction:unPublishReceipt" + ";" + "itemFound:"+selectedRowId + ";" + "moduleId:"+'.$module.'
						        	  },
						        	context: document.body,
			          	  			success: function(response){
		
			          	    			centerPannel.html(response);
						        	}
						 		});
							  // remove the loading... message.
		        	    	  Metronic.unblockUI("div.page-content");
					        }
					      },
					      {type: "btn btn-mini btn-danger", text: "Jo", click: function($noty) {
					          $noty.close();
					          noty({force: true, text: "Procesi i anullimit u nderpre.", type: "error",layout:"topCenter",closeButton:true,timeout:3000});
					        }
					      }
					    ],
					    closable: false,
					    timeout: false
					  });
	 				}// row selected
	 				else {
	 					alert("Ju lutem, zgjidhni nje element nga tabela .");
	 				}') // array
		); // end toolbar button
		
		// button Preview Recipes
		$preview_button = HtmlView::displayButton("previewRecipe".$myGrid->getModuleId(), "Preview",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Preview',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
			        var win = window.open("http://www.shije.al/recipe/"+selectedRowId+"/", "_blank");
		
            		if(win){
            		    //Browser has allowed it to be opened
            		    win.focus();
            		}else{
            		    //Broswer has blocked it
            		    alert("Please allow popups for this site");
            		}
		') // array
		); // end toolbar button
		
		$userId = Authenticate::getUserId();
		$userObj = new Table_Users();
		$user = $userObj->getDataById($userId);
		
		if($user->role_id == Zend_Registry::get( 'config' )->user->editor
		    or $user->role_id == 1){
		    
		    $output .= $publish_button;
		    $output .= $un_publish_button;
		}
		
		$output .= $preview_button;

		$output .="<div class='clearfix'></div>";
		$output .= "<div id='dragdropwrapper' style='float: left; width: 99%; margin-top:15px; height: auto; border: 1px solid;'></div>";
		$output .="<div class='clearfix'></div>";
		
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
	
	public function unMakeFeatured_Action(){
	
	    // load the neccesary model
	    $classObj = new Table_Receipts();
	    $featured = $classObj->unMakeFeatured($this->_params['itemFound']);
	    if($featured){
	        return true;
	    }
	    return false;
	
	}
	
	public function removeFeatured_Action(){
	
	    // load the neccesary model
	    $classObj = new Table_Receipts();
	    $featured = $classObj->removeFeatured();
	    if($featured){
	        return $featured;
	    }
	    return false;
	
	}
	
	public function publishReceipt_Action(){
	    $receiptId = $this->_params['itemFound'];
	    $receiptObj = new Table_Receipts();
	    $status = $receiptObj->publishReceipt($receiptId);
	     
	    // render the grid
	    $this->layoutCenter_Action();
	    if ($status === True || intval($status)>0){
	        $this->getScript('noty({"text":"Receta u publikua!","layout":"topCenter","type":"success","speed":10,"timeout":3000,"closeButton":true,"closeOnSelfClick":true});');
	    }else{
	        $this->getScript('noty({"text":"Receta nuk mund te publikohej! Provoni Perseri!","layout":"topCenter","type":"success","speed":10,"timeout":10000,"closeButton":true,"closeOnSelfClick":true});');
	    }
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