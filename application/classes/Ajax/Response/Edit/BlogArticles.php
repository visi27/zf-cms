<?php
class Ajax_Response_Edit_BlogArticles extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
		
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		$elements =	array("zf" 	=> 	array("blog_category_id","title", "subtitle", "author", "prepared_by",
		                                  array("name" => "intro_text", "type" =>	"textarea"),
		                                  array("name" => "full_text", "type" =>	"textarea"), "publish_date", "publish_time",
		                                  array("name"=>"archived", "type"=>"select"), "archive_date","magazine_nr", "video"),
		                          
						//"db" 	=> 	array("id_award", "award_name", "order_nr", "order_date", "awards_type"), //always start with the primary key
						  "alias"=> array("ID","Statusi","Titulli","Nentitulli","Kategoria","Autori", "Data e Publikimit", "Ora e Publikimit", "Nr. i Revistes"));
		
		$myGrid = new Utility_GridJquery($module, $this->_action, $elements, $ajax=true);
	
		// select method to generate the grid resultset
		$myGrid->setSelectMethod("selectRowsForGrid");
	
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
	
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		
		// hide the table header called Id
		$myGrid->hideColumn("ID","id_td");	
		
		$myGrid->setDialogSize("1000", "750");
		
		if(isset($_SESSION['article']['selected'])){
		    $myGrid->setSelectedElement($_SESSION['article']['selected']);
		}
		if(isset($this->_params["itemFound"])){
		    $myGrid->setSelectedElement($this->_params["itemFound"]);
		}
		
		//*********    START FILTER SECTION     ********//
		
		//Article Status Filter
		$statusAutocomplete = new Utility_Autocomplete("filterStatus", "Ajax_Response_Utility", "getArticleStatus");
		
		$statusAutocomplete->setDefaultText("Statusi i Artikullit");
		$statusAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, name",
		        "value"	=>	"id, name",
		        "id" => 'id'));
		$myGrid->addFilter($statusAutocomplete, $dbColumn = "blog_articles.article_status", " = ");
		//END Article Status Filter
		
		//Author Filter
		$authorAutocomplete = new Utility_Autocomplete("filterAuthor", "Ajax_Response_Utility", "getAuthors");
		
		$authorAutocomplete->setDefaultText("Autori");
		$authorAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, firstname, lastname",
		        "value"	=>	"id, firstname, lastname",
		        "id" => 'id'));
		
		$myGrid->addFilter($authorAutocomplete, $dbColumn = "blog_articles.author", " = ");
		//END Author Filter
		
		//Category Filter
		$categoryAutocomplete = new Utility_Autocomplete("filterCategory", "Ajax_Response_Utility", "getBlogCategories");
		
		$categoryAutocomplete->setDefaultText("Kategoria e Artikullit");
		$categoryAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, title",
		        "value"	=>	"id, title",
		        "id" => 'id'));
		$myGrid->addFilter($categoryAutocomplete, $dbColumn = "blog_articles.blog_category_id", " = ");
		//END Category Filter
		
		//Mireqenie Filter
		$mireqenieAutocomplete = new Utility_Autocomplete("filterMireqenieCategory", "Ajax_Response_Utility", "getBlogCategories");
		
		$mireqenieAutocomplete->setDefaultText("Mireqenie");
		$mireqenieAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, title",
		        "value"	=>	"id, title",
		        "id" => 'id'));
		$mireqenieAutocomplete->addParameter("\"section:2\"");
		$myGrid->addFilter($mireqenieAutocomplete, $dbColumn = "blog_articles.blog_category_id", " = ");
		//END Mireqenie Filter
		
		//Personazh Filter
		$personazhAutocomplete = new Utility_Autocomplete("filterPersonazhCategory", "Ajax_Response_Utility", "getBlogCategories");
		
		$personazhAutocomplete->setDefaultText("Personazh");
		$personazhAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, title",
		        "value"	=>	"id, title",
		        "id" => 'id'));
		$personazhAutocomplete->addParameter("\"section:3\"");
		$myGrid->addFilter($personazhAutocomplete, $dbColumn = "blog_articles.blog_category_id", " = ");
		//END Personazh Filter
		
		//Shkolle Kuzhine Filter
		$schoolAutocomplete = new Utility_Autocomplete("filterSchoolCategory", "Ajax_Response_Utility", "getBlogCategories");
		
		$schoolAutocomplete->setDefaultText("Shkolle Kuzhine");
		$schoolAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, title",
		        "value"	=>	"id, title",
		        "id" => 'id'));
		$schoolAutocomplete->addParameter("\"section:4\"");
		$myGrid->addFilter($schoolAutocomplete, $dbColumn = "blog_articles.blog_category_id", " = ");
		//END Shkolle Kuzhine Filter
		
		//Date Filter
		$dateAutocomplete = new Utility_AutocompleteDate("filterDateFrom");
		
		$dateAutocomplete->setDefaultText("Nga Data");
		
		$myGrid->addFilterDate($dateAutocomplete, $dbColumn = "blog_articles.publish_date", " >= ");
		//END Date Filter
		
		//Date Filter
		$dateToAutocomplete = new Utility_AutocompleteDate("filterDateTo");
		
		$dateToAutocomplete->setDefaultText("Ne Daten");
		
		$myGrid->addFilterDate($dateToAutocomplete, $dbColumn = "blog_articles.publish_date", " <= ");
		//END Date Filter
		
		//****** END FILTER SECTION ******//
		
		$myGrid->addAutoComplete("author", "Ajax_Response_Utility", "getAuthors",
		    array(	"value" => 'id, firstname, lastname',
		        "id" => 'id',
		        "label" => 'firstname, lastname'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("prepared_by", "Ajax_Response_Utility", "getUsers",
		    array(	"value" => 'id, username',
		        "id" => 'id',
		        "label" => 'username'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("blog_category_id", "Ajax_Response_Utility", "getBlogCategories",
		    array(	"value" => 'id, title',
		        "id" => 'id',
		        "label" => 'title'),
		    0, $clearOnExit=true, $minLength=0);
		
		
		
		
			
			
		$myGrid->setScript('
		                    var full_editor = null;
		    
                		    jQuery("#'.$myGrid->getZendFormId().'").submit(function(e) {
                                 var self = this;
                                 e.preventDefault();
                                  if (full_editor) { 
		                              console.log("FULL DESTROYED")
		                              CKEDITOR.instances.full_text.destroy(false);
		                              full_editor = null;
	                               }
                                    var form = $(this);
                                    $("input[type=submit], input[type=button], button", form).eq(0).each(function(){
                                        var self= $(this),
                                            tempElement = $("<input type=\'hidden\'/>");
                                
                                        // clone the important parts of the button used to submit the form.
                                        tempElement
                                            .attr("name", this.name)
                                            .val(self.val())
                                            .appendTo(form);
                                    });
                                		    
		                         self.submit();
              

                                 return false; //is superfluous, but I put it here as a fallback
                            });
		    
                            $("#'.$myGrid->getDialogId().'").on("dialogopen", function() 
                            {		   
		                      $(this).parent().promise().done(function ()
                                {
                    		    for(name in CKEDITOR.instances)
                                {
                                    CKEDITOR.instances[name].destroy(true);
                                }
    		    
    		                      if(!full_editor){
        	                        full_editor = CKEDITOR.replace("full_text", {
                                            	filebrowserBrowseUrl : "filemanager/dialog.php?type=2&editor=ckeditor&fldr=",
                                            	filebrowserUploadUrl : "filemanager/dialog.php?type=2&editor=ckeditor&fldr=",
                                            	filebrowserImageBrowseUrl : "filemanager/dialog.php?type=1&editor=ckeditor&fldr="
                                            });
    		                  }
		                      });
                            }).on("dialogbeforeclose", function(){
                                  if (full_editor) { 
		                              CKEDITOR.instances.full_text.destroy(true);
		                              full_editor = null;
	                               }
	                        });
		    ');
		
		$myGrid->setScript('
		    
		    var d = new Date();

            var month = d.getMonth()+1;
            var day = d.getDate();
            
            var today = d.getFullYear() + "/" +
                ((""+month).length<2 ? "0" : "") + month + "/" +
                ((""+day).length<2 ? "0" : "") + day;
		    
		    // pas ngarkimit te grides, vendos statuset e emerimeve me icona																												
			$("#data-table > tbody  > tr").each(function(index) {
		      var status = $(this).find("td#article_status_td > label");
		      var publish_date = $(this).find("td#publish_date_td > label");

		      if(publish_date.html() > today){
		          publish_date.addClass("label-warning");
		          publish_date.css("padding-left", "3px");
		          publish_date.css("padding-right", "3px");
		      }
		    
	          if(status.html() == "1"){
	               status.attr("title", "Artikull i Paredaktuar");
			       status.html(\'<img style="width:18px;height:18px" border="0" src="images/status-unredacted.png">\');
	          }
		    
		      if(status.html() == "2"){
	               status.attr("title", "Artikull ne Redaktim");
			       status.html(\'<img style="width:18px;height:18px" border="0" src="images/status-redacting.png">\');
	          }
		    
		      if(status.html() == "3"){
	               status.attr("title", "Artikull i Publikuar");
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
			            method: "selectArticle",
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
						loadClass: "Ajax_Response_ArticleGallery",
						method: "getGalleryHtml",
						parameter: "articleId:"+selectedRowId
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
		$publish_button = HtmlView::displayButton("publishArticle".$myGrid->getModuleId(), "Publiko",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Publiko Artikullin',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
		            var centerPannel = jQuery(".page-content");
					if(selectedRowId){
					// custom notify message
					noty({
					    text: "Jeni i sigurte se doni te publikoni artikullin e perzgjedhur!?\r\n",
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
						        		  method: "BlogArticles",
							        	  parameter: "ajaxAction:publishArticle" + ";" + "itemFound:"+selectedRowId + ";" + "moduleId:"+'.$module.'
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
		$un_publish_button = HtmlView::displayButton("unPublishArticle".$myGrid->getModuleId(), "Ktheje Per Redaktim",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Ktheje Per Redaktim',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
					var centerPannel = jQuery(".page-content");
		            if(selectedRowId){
					// custom notify message
					noty({
					    text: "Jeni i sigurte se doni te ktheni per redaktim artikullin e perzgjedhur!?\r\n",
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
						        		  method: "BlogArticles",
							        	  parameter: "ajaxAction:unPublishArticle" + ";" + "itemFound:"+selectedRowId + ";" + "moduleId:"+'.$module.'
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
		$preview_button = HtmlView::displayButton("previewArticle".$myGrid->getModuleId(), "Preview",array(
		    "icon"		=>	'ui-icon-alert',
		    "showtext" 	=> 	'true',
		    "label" 	=>	'Preview',
		    "click" 	=> 	'
					var selectedRowId = $("#'.$myGrid->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
			        var win = window.open("http://www.shije.al/article/"+selectedRowId+"/", "_blank");
		    
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
	
	public function publishArticle_Action(){
	    $articleId = $this->_params['itemFound'];
	    $articleObj = new Table_BlogArticles();
	    $status = $articleObj->publishArticle($articleId);
	     
	    // render the grid
	    $this->layoutCenter_Action();
	    if ($status === True || intval($status)>0){
	        $this->getScript('noty({"text":"Artikulli u publikua!","layout":"topCenter","type":"success","speed":10,"timeout":3000,"closeButton":true,"closeOnSelfClick":true});');
	    }else{
	        $this->getScript('noty({"text":"Artikulli nuk mund te publikohej! Provoni Perseri!","layout":"topCenter","type":"success","speed":10,"timeout":10000,"closeButton":true,"closeOnSelfClick":true});');
	    }
	}
	
	public function unPublishArticle_Action(){
	    $articleId = $this->_params['itemFound'];
	    $articleObj = new Table_BlogArticles();
	    $status = $articleObj->unPublishArticle($articleId);
	     
	    // render the grid
	    $this->layoutCenter_Action();
	    if ($status === True || intval($status)>0){
	        $this->getScript('noty({"text":"Artikulli u kthye per redaktim!","layout":"topCenter","type":"success","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');
	    }else{
	        $this->getScript('noty({"text":"Artikulli nuk mund te kthehej per redaktim! Provoni Perseri!","layout":"topCenter","type":"success","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');
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