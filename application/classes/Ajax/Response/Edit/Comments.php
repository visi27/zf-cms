<?php
class Ajax_Response_Edit_Comments extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
	        
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		$elements =	array("zf" 	=> 	array("title", array("name"=>"description", "type"=>"textarea"), "instructions","author", "publish_date", "publish_time", "servings", "total_time", "difficulty", 
		                  "category", "cuisine", "meal", "receipt_type", "seasonality", "base_product", "festivity" ,"magazine_nr"),
						//"db" 	=> 	array("id_award", "award_name", "order_nr", "order_date", "awards_type"), //always start with the primary key
						  "alias"=> array("ID", "Statusi", "Titulli i Recetes","Autori", "Porcionet", "Kohezgjatja", "Veshtiresia", "Kategoria", "Lloji i Kuzhines", "Vakti", 
						                  "Lloji i Recetes", "Sezonaliteti","Produkti Baze", "Festa","Data e Publikimit", "Ora e Publikimit"));
		$buttons = array("new"=>false, "edit"=>false, "delete"=>false);
		$myGrid = new Utility_Grid($module, $this->_action, $elements, $ajax=true, $buttons);
	
				
		// select method to generate the grid resultset
		$myGrid->setSelectMethod("selectRowsForGrid");
	
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
	
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		
		// hide the table header called Id
		$myGrid->hideColumn("ID","id_td");	
		$myGrid->hideColumn("post_id","post_id_td");
		$myGrid->setDialogSize("700px");
		
		if(isset($_SESSION['receipt']['selected'])){
		    $myGrid->setSelectedElement($_SESSION['receipt']['selected']);
		}
		if(isset($this->_params["itemFound"])){
		    $myGrid->setSelectedElement($this->_params["itemFound"]);
		}
		
		
		//*********    START TOOLBAR FILTER SECTION     ********//
		
		//Comment Status Filter
		$statusAutocomplete = new Utility_Autocomplete("filterStatus", "Ajax_Response_Utility", "getCommentStatus");
		
		$statusAutocomplete->setDefaultText("Statusi i Publikimit");
		$statusAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		$myGrid->addFilter($statusAutocomplete, $dbColumn = "web_comments.published", " = ");
		//END Receipt Status Filter
		
		//Redacted Status Filter
		$redactedAutocomplete = new Utility_Autocomplete("filterRedacted", "Ajax_Response_Utility", "getCommentRedactedStatus");
		
		$redactedAutocomplete->setDefaultText("Statusi i Redaktimit");
		$redactedAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		$myGrid->addFilter($redactedAutocomplete, $dbColumn = "web_comments.redacted", " = ");
		//END Receipt Status Filter
		
		//*********    END TOOLBAR FILTER SECTION     ********//
		
		
		$myGrid->hideColumn("content_tooltip");
		$myGrid->setTooltipCols(array("content_tooltip"));
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
		    
			');
		
		 
		$header = $this->renderBreadCrumb();
		
		$output = $header.$myGrid->render();
		
		// button unpublish Article
		$un_publish_button = HtmlView::displayButton("unPublishComment".$myGrid->getModuleId(), "Hiqe Nga Publikimi",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Hiqe Nga Publikimi',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
		            var centerPannel = jQuery(".page-content");
					if(selectedRowId){
					// custom notify message
					noty({
					    text: "Jeni i sigurte se doni te ktheni per redaktim komentin e perzgjedhur!?\r\n",
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
						        		  method: "Comments",
							        	  parameter: "ajaxAction:unPublishComment" + ";" + "itemFound:"+selectedRowId + ";" + "moduleId:"+'.$module.'
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
		
		// button publish Comment
		$publish_button = HtmlView::displayButton("publishComment".$myGrid->getModuleId(), "Publikoje",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Publikoje',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
		            var centerPannel = jQuery(".page-content");
					if(selectedRowId){
					// custom notify message
					noty({
					    text: "Jeni i sigurte se doni te ktheni per redaktim komentin e perzgjedhur!?\r\n",
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
						        		  method: "Comments",
							        	  parameter: "ajaxAction:publishComment" + ";" + "itemFound:"+selectedRowId + ";" + "moduleId:"+'.$module.'
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
		$preview_button = HtmlView::displayButton("previewArticle".$myGrid->getModuleId(), "Shiko Artikullin/Receten",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Shiko Artikullin/Receten',
		    "click" 	=> 	'
					var post_id = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").parent().parent().find("#post_id_td>.dataContent").html();
			        		    
		            var post_array = post_id.split("-")
		            //console.log(post_array[0]);
		            var win = window.open("http://www.shije.al/"+post_array[0]+"/"+post_array[1]+"/", "_blank");
		
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
		
		echo $output;
	}
	
	public function unPublishComment_Action(){
	    $commentId = $this->_params['itemFound'];
	    $commentObj = new Table_Comments();
	    $status = $commentObj->unPublishComment($commentId);
	     
	    // render the grid
	    $this->layoutCenter_Action();
	    if ($status === True || intval($status)>0){
	        $this->getScript('noty({"text":"Komenti u hoq nga publikimi!","layout":"topCenter","type":"success","speed":10,"timeout":3000,"closeButton":true,"closeOnSelfClick":true});');
	    }else{
	        $this->getScript('noty({"text":"Receta nuk mund te hiqej nga publikimi! Provoni Perseri!","layout":"topCenter","type":"success","speed":10,"timeout":10000,"closeButton":true,"closeOnSelfClick":true});');
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