<?php
/**
 * Utility Grid Class.
 * @name Grid Class
 * @uses Zend Framework
 **/

class Utility_GridJquery {
	
	/**
	 * The main module id.
	 * 
	 * @var Integer
	 */
	protected $moduleId;
	
	/**
	 * Name of the master controller where all the Forms are submited.
	 * 
	 * @var String
	 */
	protected $controller;
	
	/**
	 * Variable specifying a form_submit or just default rendering.
	 * 
	 * @var String
	 */
	protected $action;
	
	/**
	 * Html element. Jquery dialog id.
	 *
	 * @var String
	 */
	protected $dialogId;
	
	/**
	 * Html element. Zend form id.
	 * 
	 * @var String
	 */
	protected $zendFormId;
	
	/**
	 * Html element. Data table id.
	 * 
	 * @var String
	 */
	protected $viewPaneId;
	
	/**
	 * Translator Object..
	 */
	protected $tr;
	
	protected $elementsArray;
	protected $bttnNameNew = "Shto";
	protected $bttnNameEdit = "Ndrysho";
	protected $bttnNameDel = "Fshi";
	protected $buttonsArray;
	protected $buttonEditMethod;
	protected $moduleFormName;
	protected $selectedElement;
	protected $zendForm;     // the zend form class name 
	protected $zendModel;    // the zend model class name 
	protected $zendFormObj; // the zend form object
	protected $zendModelObj;// the zend model object
	protected $dataSet;
	protected $createMethod = "createNew"; // the create method name inside the zend model
	protected $selectMethod = "selectData"; // the select method name inside the zend model
	protected $updateMethod = "updateRow"; // the update method name inside the zend model
	protected $deleteMethod = "deleteRow"; // the delete method name inside the zend model
	protected $autoCompleteMap = array(	
									"label"	=> "label",
									"value"	=>	"value",
									"id"	=>	"id"
								);
	protected $orderSelectedRowsBy;
	protected $selectArgs;
	protected $selectRowForEditMethod; // this method is used to return a row from DB
	protected $jscript;
	protected $ajaxEditmode;
	protected $output;
	protected $pdf;
	public    $htmlViewHeader = "Te Dhenat Aktuale";
	protected $page = 1;
	protected $sortDirect = "asc";
	protected $toolbarFilter = array();
	protected $tooltipCols = array();
	
	/**
	 * 
	 * @param Integer $moduleId
	 * @param String $action
	 * @param Array $elements
	 * @param Boolean $ajax
	 * @param Array $buttons
	 * @param String $controller
	 * @param Array $dialog
	 */
	public function __construct($moduleId, $action="default", $elements=array(), $ajax = false,
								$buttons = array("new"=>true, "edit"=>true, "delete"=>true), $controller="edit"){
	    //Set Module Id in session
	    $_SESSION['system']['moduleId'] = $moduleId;
		// create unique id variables
		$this->action = $action;
		$this->controller = $controller;
		$this->moduleId = $moduleId;	
		$this->dialogId = "div-Dialog-$this->moduleId";	
		$this->zendFormId = "zend-form_$this->moduleId";
		$this->viewPaneId = "table-$this->moduleId";		
		$this->elementsArray = $elements;
		$this->buttonsArray = $buttons;
		$this->ajaxEditmode = $ajax;
		$this->buttonEditMethod = ($this->ajaxEditmode==true)?"createButtonEditAjax":"createButtonEditBasic";	
		
		//Initialise Translator
		$this->tr = Zend_Registry::get('translator');
		//Initalise Translated Button Names
		$this->setBttnNameNew($this->tr->_('Shto'));
		$this->setBttnNameEdit($this->tr->_('Ndrysho'));
		$this->setBttnNameDel($this->tr->_('Fshi'));
		//Initialise Translated View Header
		$this->sethtmlViewHeader($this->tr->_("Te Dhenat Aktuale"));
		
		$modules = new Table_Modules();
		$values = $modules->getModuleById($this->moduleId);
		$this->moduleFormName = $values->form_name;
		
		// name of the zend form class
		$this->zendFormName = "Form_".$this->getModuleFormName();
		
		// create the zend form object
		$this->zendFormObj = new $this->zendFormName (array( 'id'=>$this->getZendFormId() ));
		$this->zendFormObj->setAttrib('style' , 'display:none;');
		
		// add toolbar params to hidden form element
		$this->zendFormObj->addElement('hidden', 'treeNodeId', array( 'value' => $this->moduleId));
		$this->zendFormObj->treeNodeId->removeDecorator('label');
		$this->zendFormObj->treeNodeId->removeDecorator('htmlTag');
		
		$this->zendFormObj->addElement('hidden', 'row_id', array( 'value' => $_POST ['row_id']));
		$this->zendFormObj->row_id->removeDecorator('label');
		$this->zendFormObj->row_id->removeDecorator('htmlTag');
		
		$this->zendFormObj->addElement('hidden', 'form_mode', array( 'value' => $_POST ['form_mode']));
		$this->zendFormObj->form_mode->removeDecorator('label');
		$this->zendFormObj->form_mode->removeDecorator('htmlTag');
		
		$this->zendFormObj->addElement('hidden', 'toolbarFilter', array( 'value' => $_POST ['toolbarFilter']));
		$this->zendFormObj->toolbarFilter->removeDecorator('label');
		$this->zendFormObj->toolbarFilter->removeDecorator('htmlTag');
		
		$this->zendFormObj->addElement('hidden', 'smartSearchFilter', array( 'value' => $_POST ['smartSearchFilter']));
		$this->zendFormObj->smartSearchFilter->removeDecorator('label');
		$this->zendFormObj->smartSearchFilter->removeDecorator('htmlTag');
		
		// set the form action, to the current controller
		$this->zendFormObj -> setAction("index.php?m=".$this->controller); //ndrysho !
		
		// name of the zend model class
		$this->zendModel = "Table_".$this->getModuleFormName();
			
		$this->setScript('			
			// remove any existing dialog with the same id
			$("#'. $this->dialogId . '").remove();
				
			// wrap the form element inside a div element
			$("#'. $this->zendFormId . '").wrap(\'<div id="' . $this->dialogId . '"/>\');
			
			//set the jQuery dialog properties
			$( "#' . $this->dialogId . '" ).dialog({
				dialogClass: "myDialogUi",
				autoOpen: false,
				show: "blind",
				hide: "slide",
				modal: true,
				beforeClose: function(event, ui) { $("ul.ui-autocomplete").hide(); }
			});
				
			//make the form visible
			$("#'. $this->zendFormId . '").css("display", "block");
			
			// dont allow to wrap names	
			$("table#data-table td").attr("nowrap","nowrap");
				
			$(".errors li").css("marginLeft","0px");
			$(".elementWrap").css("marginBottom","8px");
				
		');
		
		// set the default dialog size
		$this->setDialogSize(); //@todo Fredi:auto
		
		// allow to mark the row as selected by clicking over it
		$this->markRowOnClick();
		
		// hide columns
		$this->hideColumn("id");
		$this->hideColumn("row_id");
		
		// remove the grid buttons, if no access rights
		if(!$this->hasWriteModuleAccess($this->moduleId))
		{
			$this->buttonsArray = array("new"=>false, "edit"=>false, "delete"=>false);
		}
	}
	
	/**
	 * 
	 * @return string $this->output
	 */
	public function render(){
		
		// if there is a toolbar registered with the grid object
		if($this->toolbarFilter){
			
			// get the zend form, toolbar element field
			$zendFilterElement = $this->zendFormObj->getElement('toolbarFilter');
			
			// get the filter values comming from the filtering (if done)
			$filteredPars = Utility_Functions::argsToArray(explode(";", $_GET ['parameter']));
			
			// get the filter values comming from the zend form element
			if($zendFilterElement->getValue()){
				// posted parameters from the form
				$filteredPars = Utility_Functions::argsToArray(explode(";", $_POST ['toolbarFilter']));
			}
			
			$this->output .= "<div class='navbar navbar-default filter-panel'>";
			// for every registered toolbar in the grid, 
			// assign the alias and value from the posted parameters
			foreach ($this->toolbarFilter as $filter => $data){
				
				// the delimiter char that separates the id from the text
				$filterDelimiter = $this->toolbarFilter[$filter]['object']->getDelimiter();
				
				// extract the value part, from the filter alias
				$filterAlias = Utility_Functions::cleanArgsValue(array($filteredPars[$filter]), $filterDelimiter);
				
				// the unique id of the filtered element
				$filterIdValue = array_pop($filterAlias); //@todo removed intval

				if($filterIdValue){				
					// store the toolbar value into the hidden zend form element
					$zendFilterElement->setValue(
							$zendFilterElement->getValue(). ";" .$filter.":".$filteredPars[$filter]);
					
					// set the toolbar displaying alias text
					$this->toolbarFilter[$filter]['alias'] = $filteredPars[$filter];
					
					if(strcmp($this->toolbarFilter[$filter]['object']->getDefaultText(), $filterIdValue) !=0)
					// set the value if it is not equal to the default autocomplete object text
					$this->toolbarFilter[$filter]['value'] = $filterIdValue;
					
					// display the alias text in the browser
					$this->toolbarFilter[$filter]['object']->setTxtValue(
							!empty($this->toolbarFilter[$filter]['alias']) ?
									$this->toolbarFilter[$filter]['alias'] : $filter);
				}
				
				//create the javascript command for filters parameters
				$jsPars.='+ ";'.$this->toolbarFilter[$filter]['object']->getName().':"+ jQuery("#'.$this->toolbarFilter[$filter]['object']->getName().'").val()';
				
				// render the filter
				$this->output.= $this->toolbarFilter[$filter]['object']->render();
			}// end foreach
				//print_r($this->toolbarFilter);		
			// Toolbar - Button - Click to display results based on filters
			
			//Create Bootstrap Button Group
			$this->output.='<div class="btn-group" role="group" aria-label="Default button group">';
			
			$this->output.= HtmlView::displayButton("toolbarBttn-$this->moduleId", "Shfaq",
				array(	"primIcon"	=>	"ui-icon-search",
						"showtext" => "false",
						"click" => '
				            console.log(\''.$jsPars.'\');
							var param = '.ltrim($jsPars, "+").';

								Metronic.blockUI({
	           	 					boxed: true,
									target: ".page-content-wrapper .page-content"
	            				});
												
							jQuery.ajax({
			  					type: "GET",
								url: "index.php?c=ajax",
								data: {
									loadClass: "Ajax_Response_'.ucfirst($this->controller).'",
									method: "'.$this->moduleFormName.'",
									parameter: "moduleId:'.$this->moduleId.';"+ param
								},
								success: function(response){
									jQuery(".page-content-wrapper .page-content").html(response);
				                    Metronic.unblockUI(".page-content-wrapper .page-content")
			        	    	}
							});'
				) // array
			); // end toolbar button
			
			
			// Toolbar - Button - Click to refresh
			$this->output.= HtmlView::displayButton("refreshBttn-$this->moduleId", "Rifresko",
					array(	"icon"	=>	"ui-icon-refresh",
							"showtext" => "false",
							"click" => '
								jQuery(document).ajaxStart(
									Metronic.blockUI({
		           	 					boxed: true,
										target: ".page-content-wrapper .page-content"
		            				})
								).ajaxStop(Metronic.unblockUI(".page-content-wrapper .page-content"));
								
								jQuery.ajax({
									type: "GET",
									url: "index.php?c=ajax",
									data: {
										loadClass: "Ajax_Response_'.ucfirst($this->controller).'",
										method: "'.$this->moduleFormName.'",
										parameter: "moduleId:'.$this->moduleId.'"
									},
									success: function(response){
										jQuery(".page-content-wrapper .page-content").html(response);
									}
								});'
					) // array
			); // end toolbar button
			
			$this->output .= "</div>";
			$this->output .= "</div>";
		}
		
		// load the data model
		$this->zendModelObj = new $this->zendModel ();
	
		//set the order by column
		$this->setOrderByColumn($_GET['s']!='' ? explode(";",htmlentities($_GET['s'])) : $this->getOrderByColumn());
	
		//set the sorting direction
		$this->setSortDir(isset($_GET['d']) ? htmlentities($_GET['d']) : $this->getSortDir());
	
				
		// deal with a form submition
		if ($this->getAction() == "submit_form"){
			
			// script for the unvalidated form, render the existing form, and display errors
			// run script to automatically show the jQuery dialog
			$displayFormDialog = '
				jQuery(function() {
		        	 //Set Bootstrap Modal Title
					jQuery( "#'.$this->getDialogId().'" ).dialog( { title: "'.
								$this->zendFormObj->getElement("form_mode")->getValue().'" } );
		
					jQuery( "#'.$this->getDialogId().'" ).dialog( "open" );	
		
					jQuery( "#'.$this->getViewPaneId().' input[name=\'rowSelectionRadio\'][value='.
								$this->zendFormObj->getElement("row_id")->getValue().']").attr("checked", "checked");
				});';
			//@todo shiko selectin e elementit me siper
			
			// check if all required values and validations are ok
			
				$status = false;
				switch ($this->zendFormObj->getElement("form_mode")->getValue()){
					
					case "_new":
						
						// if the form validation is successfull
						if ($this->zendFormObj->isValid ( $_POST ))
							// call the model and perform the insert
							$status = $this->zendModelObj->{$this->createMethod}( $this->zendFormObj );
						
						// check the action result
						if ($status === True || $this->zendModelObj->getDataById($status)){ //successfull action
							$this->setSelectedElement($status);
							$this->setScript('noty({"text":"Informacioni u shtua me sukses.","layout":"topCenter","type":"success","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');
						
						}else{//failure action
							
							if($status === False){
								$this->setScript('noty({"text":"Gabim gjate shtimit te te dhenave.","layout":"topCenter","type":"error","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');					
							}else{
								$this->setScript('noty({"text":"Gabim: '.$status.'","layout":"topCenter","type":"error","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');
							}
							// display the form again
							$this->setScript($displayFormDialog);
						}
					break;
							
					case "_edit":
						
						// if the form validation is successfull
						if ($this->zendFormObj->isValid ( $_POST ))
							// call the model and perform the update
							$status = $this->zendModelObj->{$this->updateMethod}( $this->zendFormObj );

						// check the action result
						if ($status === True || $this->zendModelObj->getDataById($status)){ //successfull action
							$this->setSelectedElement($status);
							$this->setScript('noty({"text":"Informacioni u ndryshua me sukses.","layout":"topCenter","type":"success","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');
						
						}else{ //failure action
							
							if($status === False){
								$this->setScript('noty({"text":"Gabim gjate ndryshimit te te dhenave.","layout":"topCenter","type":"error","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');							
							}else{
								$this->setScript('noty({"text":"Gabim: '.$status.'","layout":"topCenter","type":"error","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');
							}
							// display the form again
							$this->setScript($displayFormDialog);
						}
					break;
					
					case "_delete":
						
						//call the model and perform the delete
						$status = $this->zendModelObj->{$this->deleteMethod}($this->zendFormObj->getElement("row_id")->getValue());
						
						// check the action result
						if ($status === True || intval($status)>0){//successfull action
							$this->setScript('noty({force: true, text: "Informacioni i perzgjedhur u fshi me sukses!", type: "success",layout:"topCenter",closeButton:true, timeout:5000});');
						
						}else{ //failure action
							
							if($status === False){
								$this->setScript('noty({force: true, text: "Gabim gjate fshirjes te te dhenave.", type: "error",layout:"topCenter",closeButton:true,timeout:5000});');							
							}else{
								$this->setScript('noty({"text":"Gabim: '.$status.'","layout":"topCenter","type":"error","speed":10,"timeout":5000,"closeButton":true,"closeOnSelfClick":true});');
							}	
						}
					break;
					
					default:
						// @TODO
						// implement other actions into the Grid						
				}// end switch
		}
	
		// render the zend form into the variable
		$this->output .=  $this->zendFormObj->render(new Zend_View()) . 
							$this->getGridContent() . 
								$this->getScript($this->jscript);

		
		return $this->output;
	}
	
	/**
	 * Provide the html content of the data table and new, edit, delete buttons.
	 * @return string $this->output
	 */
	private function getGridContent(){
		
		// select all data from the db table
		$this->dataSet = $this->zendModelObj->{$this->getSelectMethod()} (	
			$this->exportFilters(),
			$this->getOrderByColumn(),
			$this->getSortDir() 
		);
		
		// get information from the model
		$tblInfo = $this->zendModelObj-> info();
		
		// get the primary key column
		$primaryKeyCol = $tblInfo['primary'][1];
		
		// this is a unique key in the database table
		// search for this element
		if( $this->getSelectedElement() && $this->dataSet->count() ){
		
			// return dataset to an array
			$dataSetArray = $this->dataSet->toArray();
		
			// check if the primary key is in the dataset
			if (array_key_exists($primaryKeyCol, $dataSetArray[0])) {
				$searchInColumn = $primaryKeyCol;
			}elseif(array_key_exists("id", $dataSetArray[0])){
				$searchInColumn = "id";
			}elseif(array_key_exists("row_id", $dataSetArray[0])){
				$searchInColumn = "row_id";
			}
				
			// find the page number where this element resides
			for($i=0;$i<count($dataSetArray);$i++){
				if($this->getSelectedElement() == $dataSetArray[$i][$searchInColumn]){
					// if the key is found, compute the page number of the paginator
					$key = $i+1;
					$this->page = $key / HtmlView::getRowsPerPage();
					$this->page = ($this->page > intval($this->page))?intval($this->page)+1:intval($this->page);
					// mark the selected row as selected
					$this->markThisRow($this->getSelectedElement());
					continue;
				}
			}
		
		}else{
			// render the first page by default, or the current working page if any
			$this->page = isset($_GET['p']) ? (int) htmlentities($_GET['p']) : $this->page;
		}
				
		$output= HtmlView::rowsetToHtml( $this->dataSet, $this->getViewPaneId(),
				$this->elementsArray["alias"], TRUE, $primaryKeyCol,
				$this->htmlViewHeader, true, array(),
				$this->controller, $this->page, true, null, $this->getOrderByColumn(),
				$this->getSortDir(),  $this->getTooltipCols() );
			
		// render the create new button
		$output.= ($this->buttonsArray["new"])?$this->createButtonNew():"";
		// render the update button
		$output.= ($this->buttonsArray["edit"])?$this->{$this->buttonEditMethod}():"";
		// render the delete button
		$output.= ($this->buttonsArray["delete"])?$this->createButtonDelete():"";
		
		return $output;
	}
	
	/**
	 * Sets the "toolbarFilter" class variable.
	 * Registers a filter object with the Grid. Stores the tableColumn 
	 * and operator to filter the current database table. 
	 * 
	 * @param Utility_Autocomplete $filter
	 * @param String $tableColumn
	 *   
	 */
	public function addFilter(Utility_Autocomplete $filter, $tableColumn, $operator = " = "){
		$this->toolbarFilter[$filter->getName()] = array(
														'object' =>  $filter,
														'tableColumn' =>  $tableColumn,
														'operator' =>  $operator,
														'alias' => null,
														'value' => null
														);		
	}
	
	/**
	 * Sets the "toolbarFilter" class variable.
	 * Registers a DATE filter object with the Grid. Stores the tableColumn
	 * and operator to filter the current database table.
	 *
	 * @param Utility_AutocompleteDate $filter
	 * @param String $tableColumn
	 *
	 */
	
	public function addFilterDate(Utility_AutocompleteDate $filter, $tableColumn, $operator = " = "){
	    $this->toolbarFilter[$filter->getName()] = array(
	        'object' =>  $filter,
	        'tableColumn' =>  $tableColumn,
	        'operator' =>  $operator,
	        'alias' => null,
	        'value' => null
	    );
	}
	
	/**
	 * Transform the filters information, into an sql select array.
	 * @return array:selectFilter
	 */
	private function exportFilters($for="query"){		
		$filtersData = array();	
		
		if($for == "query"){	
			// for every registered toolbar in the grid,
			// assign the alias and value from the posted parameters
			foreach ($this->toolbarFilter as $filter => $data){
				if(!empty($data['tableColumn']) && !empty($data['operator']) && !empty($data['value'])){
					if(stripos($data['operator'],'like') !== false)
						$data['value'] .= '%';
					$filtersData[$data['tableColumn']] = array($data['operator'] => $data['value']);
				}
			}
		}
		return $filtersData;	
	}
	
	/**
	 * @return the $bttnNameNew
	 */
	public function setBttnLabel($bttnName, $label) {
	
		// the button selector {button:id}
		$selector = bttnName . $this->getDialogId();
	
		// example how to change the button label (another version)
		//$this->setScript('jQuery( "#'.$selector.'" ).button({ label: "'.$label.'" });');
	
		// get the setter function name
		$setterFunctName = "setBttnName". ucfirst($bttnName) ;
	
		// set the label name by calling the setter function
		$this->{$setterFunctName} ($label);
	}
	
	/**
	 * @return the $bttnNameNew
	 */
	public function getBttnNameNew() {
		return $this->bttnNameNew;
	}
	
	/**
	 * @return the $bttnNameEdit
	 */
	public function getBttnNameEdit() {
		return $this->bttnNameEdit;
	}
	
	/**
	 * @return the $bttnNameDel
	 */
	public function getBttnNameDel() {
		return $this->bttnNameDel;
	}
	
	/**
	 * @param string $bttnNameNew
	 */
	private function setBttnNameNew($bttnNameNew) {
		$this->bttnNameNew = $bttnNameNew;
	}
	
	/**
	 * @param string $bttnNameEdit
	 */
	private function setBttnNameEdit($bttnNameEdit) {
		$this->bttnNameEdit = $bttnNameEdit;
	}
	
	/**
	 * @param string $bttnNameDel
	 */
	private function setBttnNameDel($bttnNameDel) {
		$this->bttnNameDel = $bttnNameDel;
	}
	
	/**
	 * 
	 * @param integer $moduleId
	 * @return boolean
	 * 
	 * @desc: Look if the user has write access over the given module.
	 */
	private function hasWriteModuleAccess($moduleId){
		
		// load the user model
		$user = new Table_Users();
		
		// load the user data
		$userData = $user->getDataById(Authenticate::getUserId());
		
		//get the role id
		$roleId = $userData->role_id;
		
		// load the access control list instance
		$acl = new Table_Acl();
		
		return $acl->hasAccess($roleId, $moduleId, "write");
	}
	
	/**
	 * Used in TOE
	 * @param string $controller
	 * @return boolean
	 * 
	 * @desc: Look whether the current db in use is Actual or Draft.
	 * 		  If it is Actual and current controller is TOE return False. 
	 * 		  This will not allow editing in Actual Db.
	
	private function canWriteInThisDb($controller){
		$version = Authenticate::getDbVersion();
		if( $version == Zend_Registry::get('config')->top->version->actual
				&& $controller == "toe"){
			// can not edit the Top in Actual Version 
			return false;
		}else{
			// can edit the draft version
			return true;
		}
	}
	 */
	
	public function getZendModel(){
		return $this->zendModel;
	}
	
	public function getSelectParameter(){
		return $this->selectParameter1;
	}
	
	public function getElementsArray(){
		return $this->elementsArray;
	}
	
	// paint an entire row with color, on radio click
	private function markRowOnClick(){
		$this->setScript('
			jQuery("#'.$this->getViewPaneId().' input[name=\'rowSelectionRadio\']").change(function() {
				
				jQuery(this).attr(\'checked\',\'checked\');
				
				var selectedRowId = jQuery(this).val();
				
				//console.log();
				jQuery( "#'.$this->getViewPaneId().' table[id=\'data-table\'] tr").contents("td").removeClass("my-hoveredTd");
				jQuery( "#'.$this->getViewPaneId().' table[id=\'data-table\'] tr[id=\'"+selectedRowId+"\']").contents("td").addClass("my-hoveredTd");
						
			});');
	}	
	
	// mark a row as selected, upon prividing an id
	// normaly it is getting invoked after form submition
	private function markThisRow($rowId){
		// Mark as checked, the radio button that matches the selected sector (if there is any)
		$this->setScript('
				jQuery("#'.$this->getViewPaneId().' tr[id=\''.$rowId.'\']'.
				' input[name=\'rowSelectionRadio\']").attr(\'checked\', \'checked\');
					
				// Highlight the selected table row
				jQuery( "#'.$this->getViewPaneId().' table[id=\'data-table\'] tr[id=\''.
				$rowId.'\']").contents("td").addClass("my-hoveredTd");
				
		');
	}
	
	// set the form's modal dialog width and height
	public function setDialogSize($width="600", $height="auto", $dialogId = null ) {
		
		$dialogId = is_null($dialogId)?$this->getDialogId():$dialogId;

		$this->setScript ( 'jQuery( "#' . $dialogId . '" ).dialog( "option", "height", "' . $height . '" );' );
		$this->setScript ( 'jQuery( "#' . $dialogId . '" ).dialog( "option", "width", "' . $width . '" );' );
		
	}
		
	/*
	 * @method	setSelectedElement
	 * Forces the grid, to be rendered on the exact page where
	 * this element resides.
	 */
	public function setSelectedElement($elementId){
		if(!empty($elementId))
		$this->selectedElement = $elementId;
	}
	
	/*
	 * @method getSelectedElement
	 * Get the selected grid element id.
	 */
	public function getSelectedElement(){
		return $this->selectedElement;
	}
	
	/*
	 * @method setSelectMethod
	* This method is used to set the model->method to be called
	* when the grid is populated with rows.
	*/
	public function setSelectMethod($methodName, $orderBy = null, $orderDir = null){
		$this->selectMethod = $methodName;
		$this->setOrderByColumn($orderBy);
		$this->setSortDir($orderDir);
	}
	
	/*
	 * @method getSelectMethod
	 * get the current method name that is called to populate the grid
	 */
	public function getSelectMethod(){
		return $this->selectMethod;
	}
	
	/*
	 * @method setSelectRowDetails
	 * This method is invoked when the grid is operating in ajax mode.
	 * It calls the model-method as specified by methodName parameter.
	 * This call gets a resultset that is used to fill the zend form 
	 * with data.
	 * 
	 */
	public function setSelectRowDetails($methodName){
		$this->selectRowForEditMethod = $methodName;
	}
	
	public function getSelectRowDetails(){
		return $this->selectRowForEditMethod;
	}

	/*
	 * @method setOrderByColumn
	 * This method is invoked only within @method setSelectMethod.
	 * It forces the model, to order the query results by this column name.
	 */
	public function setOrderByColumn($orderBy){
		$this->orderSelectedRowsBy = $orderBy;
	}
	
	/*
	 * @method getOrderByColumn
	* Get the orderby column name.
	*/
	public function getOrderByColumn(){
		return $this->orderSelectedRowsBy;
	}
		
	/**
	 * @return the $mapedData
	 */
	private function getAutoCompleteMap() {
		return $this->autoCompleteMap;
	}
	
	/**
	 * @param multitype: $mapedData
	 */
	private function setAutoCompleteMap($mapedData) {
		$this->autoCompleteMap = $mapedData;
	}
	
		
	public function setScript($script){	
		$this->jscript.= $script."\r\n";
	}
	
	public function getDialogId(){
		return $this->dialogId;
	}
	
	public function getZendFormId(){
		return $this->zendFormId;
	}
	
	public function getModuleId(){
		return $this->moduleId;
	}
	
	public function getModuleFormName(){
		return $this->moduleFormName;
	}
	
	public function getViewPaneId(){
		return $this->viewPaneId;
	}
	
	public function getAction(){
		return $this->action;
	}
	
	public function addAutoComplete($zfFieldName, $responseClass, $responseMethod, 
			$mappingArray, $parameters = 0, $clearOnExit = true, $minLength=0, $script=""){
		
		// set the mapping array for this autocomplete field
		$this->setAutoCompleteMap($mappingArray);
		
		$autocomplete = '// autocomplete field
			jQuery( "#'.$this->getZendFormId().' input[id=\''.$zfFieldName.'\']" ).autocomplete({
				minLength: '.$minLength.',
				source: function( request, response ) {
					jQuery.ajax({
						autoFocus: true,
	        			delay: 500,
						url: "index.php?c=ajax",
						dataType: "json",
						data: {
							loadClass: "'.$responseClass.'", 
							method: "'.$responseMethod.'",
							parameter: "text:"+request.term + ";" + "'.$parameters.'"
						},
						success: function( data ) {
							if(null!= data){ 
								response( jQuery.map( data, function( item ){ 
									return { ';
										foreach ($this->getAutoCompleteMap() as $key => $value){
											$map .= "$key:";
											$valueArray = explode(",", $value);
											foreach($valueArray as $value){
												$map.="item.".trim($value)." + ' - ' +";
											}
											//remove the string " + ' - ' +" from the end of the string
											$map = substr($map, 0, -9).", \r\n";
										}
										//remove the last space and comma
										$map = rtrim(trim($map),",");
		$autocomplete.=$map.'		};
								}));
							}			
						},
					   complete: function(){
					     jQuery( "#'.$this->getZendFormId().' input[id=\''.$zfFieldName.'\']" ).removeClass( "ui-autocomplete-loading" );
					   }
					});
				},
				change: function( event, ui ){
					if ( !ui.item ) {';
					$autocomplete.= ($clearOnExit == true)?'jQuery(this).val("");':'';
					$autocomplete.='  
					}
				}'.$script.'
				/*
				,select: function( event, ui ) {
					console.log( ui.item ?
						"Selected: " + ui.item.value + " aka " + ui.item.id :
						"Nothing selected, input was " + this.value );
					//alert(ui.item.value);
				}
				*/
				
			});';
		$this->setScript($autocomplete);
	}
	
	public function hideColumn($columnHeaderId, $columnDataId = null){
		// the default name
		$columnDataId = $columnDataId==null?$columnHeaderId."_td":$columnDataId;
		
		// hide the column header
		$this->setScript('jQuery("#'.$this->getViewPaneId().' table[id=\'data-table\'] th[id=\''.$columnHeaderId.'\']").hide();');
		
		// hide the column data
		$this->setScript('jQuery("#'.$this->getViewPaneId().' table[id=\'data-table\'] td[id=\''.$columnDataId.'\']").hide();');	
		
		//the values of hidden columns (except id and row_id) will be shown in a tooltip
		//if (($columnDataId != "id_td") && ($columnDataId != "row_id_td")) 
		//	array_push($this->tooltipCols, substr($columnDataId, 0, strlen($columnDataId)-3));
	}
	
	public function displayTooltip($columnHeaderId, $cssClass){	
		// hide the column that is being displayed as a tooltip
		$this->hideColumn($columnHeaderId);
		
		// set the column's css class
		$this->setScript('jQuery("#'.$this->getViewPaneId().' table[id=\'data-table\'] td[id=\''.$columnHeaderId.'_td\']").addClass(\''.$cssClass.'\');');
		
		// apply tooltip on for each table row
		$this->setScript('jQuery.each( jQuery( "#'.$this->getViewPaneId().' table[id=\'data-table\'] tr"), function(index, value) {'.
			'if(jQuery(this).attr("id"))'.
			'jQuery(this).tooltip(({'.
				'tip: \'#'. $this->getViewPaneId() .' table[id="data-table"] tr[id="\'+jQuery(this).attr(\'id\')+\'"] td[id="'.$columnHeaderId.'_td"] \','.
			    'position: \'bottom center\','.
				'offset: [15, 10],'.
				'tipClass: \''.$cssClass.'\','.
				'delay: 0'.
			'}));}'.
			');'
		);
	}
	
	public function exportPageToPdf(array $parameters, $showFilters = true, $showPage = true){
		
		if($showFilters == true){
			foreach ($this->toolbarFilter as $filter){
				if( !empty($filter["object"]) )
					$filterText .= 'jQuery("#' .$filter["object"]->getName(). '").val() + " | " + ';
			}
		}
		$filterText.='" "';
		
		if($showPage == true){
			
		}
		
		$pfdExport .= ' // create the export to pdf button
		jQuery("#'.$this->getViewPaneId().' .headerWrap").append("<div id=\"pdfExport\"><img src=\"images/pdf.png\"> </div>");';
				
		$pfdExport.=' // onClick function	
		jQuery("#pdfExport").click(function(){
				
			var toolbarFilters = '.$filterText.';
			var pageHeaderTxt = "'.$parameters['pageHeader'].', faqe:" + jQuery("ul#pagination > li.active").html();
							
			// custom function, used to remove the first column (radio selection)
			jQuery.fn.removeCol = function(col){
				// Make sure col has value
				if(!col){ col = 1; }
				jQuery("tr td:nth-child("+col+"), tr th:nth-child("+col+")", this).remove();
				return this;
			};
			
			// create a temporarly form, in order to POST the data
			jQuery("<form id=\"pdf-form\" method=\"post\" action=\"index.php?c=gridToPdf\"><input type=\"hidden\" name=\"gridObjId\" value=\"'.$this->viewPaneId.'\"></form>").appendTo(".page-content-wrapper .page-content");
			jQuery("<input type=\"hidden\" name=\"pageHeader\" value=\"" + pageHeaderTxt + "\">").appendTo("#pdf-form");
			jQuery("<input type=\"hidden\" name=\"pageTitle\" value=\"" + toolbarFilters + "\">").appendTo("#pdf-form");
			jQuery("<input type=\"hidden\" name=\"content\" value=\"\">").appendTo("#pdf-form");
					
			// store temporarly the original table content
			var tableContent = jQuery("#data-table").html();
			
			// load table data into the hidden field, by removing the first column
			jQuery("#pdf-form input[name=\"content\"]").val("'.preg_replace("/[\n\r]/","","").'<table>"+jQuery("#data-table").removeCol().html()+"</table>");
			
			// reset the original table columns, including the first one
			jQuery("#data-table").html(tableContent);
			
			// submit the form and remove it
			jQuery("#pdf-form").submit().remove();
			
	    });';
	
		$this->setScript($pfdExport);
	}
	
	
	
	// Forma e shtimit
	private function createButtonNew(){	

		$zendFormElements = $this->elementsArray["zf"];
		$buttonNewScript = '
        	jQuery( "#'."new".$this->getDialogId().'" ).click(function() {
        			
        //reset the form values to null';
		foreach ($zendFormElements as $id=>$element){
			
			if(is_array($element)){
				$buttonNewScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
				' '.$element["type"].'[id=\''.$element["name"].'\']" ).val("");';
			}else{
				$buttonNewScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
				' input[id=\''.$element.'\']" ).val("");';
			}
			
		}
				
		$buttonNewScript.='	
			jQuery( "#'.$this->getZendFormId().' input[id=\'form_mode\']" ).val("_new");	
			jQuery( "#'.$this->getZendFormId().' input[id=\'treeNodeId\']").val("'.$this->getModuleId().'");

			//reset the forms error messages to none
			jQuery("#'.$this->getDialogId().' ul.errors").each(function(){jQuery(this).hide();})

			//$(".page-content").html($( "#'.$this->getDialogId().'" )[0].outerHTML);
			//show the ZendForm in a jQuery dialog	
        	$( "#'.$this->getDialogId().'" ).dialog( { title: "Shto Rekord Te Ri" } );
			$( "#'.$this->getDialogId().'" ).dialog( "open" );
			    
			return false;
			}); ';
		
		$this->setScript($buttonNewScript);
		$jqueryBtn["icon"]="ui-icon-document";
		$jqueryBtn["showtext"] = "true";
		return HtmlView::displayButton("new".$this->getDialogId(), $this->getBttnNameNew(), $jqueryBtn);
	}
		
	private function createButtonEditBasic(){
				
		// get information from the model
		$tblInfo = $this->zendModelObj-> info();
		
		// get the primary key column
		$primaryKeyCol = $tblInfo['primary'][1];
		
		// the elements array 
		$elements = $this->getElementsArray();
		
		$buttonEditScript = '
		
			jQuery( "#'."edit".$this->getDialogId().'" ).click(function() {
			// get the selected rowId from the radio button
			var selectedRowId = jQuery("#'.$this->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
			if(selectedRowId){
			// if there is a selected component, set the form values
			';
		
		foreach ($elements["zf"] as $id=>$element){
			
			if(is_array($element)){
				$elementName = $element["name"];
				$elementType = $element["type"];
				$buttonEditScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
				' '.$elementType.'[id=\''.$elementName.'\']" ).val(jQuery( "#'.$this->getViewPaneId().
				' table tr[id="+selectedRowId+"] td[id=\''.$elements["db"][$id].'_td\'] label[class=\'dataContent\']").text());';
			}else{
				$buttonEditScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
				' input[id=\''.$element.'\']" ).val(jQuery( "#'.$this->getViewPaneId().
				' table tr[id="+selectedRowId+"] td[id=\''.$elements["db"][$id].'_td\'] label[class=\'dataContent\']").text());';
			}
		}
		
		// set the primary key value
		// @todo not working for agregate primary keys
		$buttonEditScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
		' input[id=\'row_id\']" ).val(jQuery( "#'.$this->getViewPaneId().
		' table tr[id="+selectedRowId+"] td[id=\''.$primaryKeyCol.'_td\'] label[class=\'dataContent\']").text());';

		
		$buttonEditScript.='
			jQuery( "#'.$this->getZendFormId().' input[id=\'form_mode\']" ).val("_edit");
			jQuery( "#'.$this->getZendFormId().' input[id=\'treeNodeId\']").val("'.$this->getModuleId().'");		

			//reset the forms error messages to none
			jQuery("#'.$this->getDialogId().' ul.errors").each(function(){jQuery(this).hide();});
				
			//show the ZendForm in a jQuery dialog
			jQuery( "#'.$this->getDialogId().'" ).dialog( { title: "'.$this->getBttnNameEdit().'" } );
			jQuery( "#'.$this->getDialogId().'" ).dialog( "open" );
					
			return false;
			}else{
				alert("Zgjidhni nje element nga tabela fillimisht.");
			}
		})';
		
		$this->setScript($buttonEditScript);
		$jqueryBtn["icon"]="ui-icon-pencil";
		$jqueryBtn["showtext"] = "true";
		return HtmlView::displayButton("edit".$this->getDialogId(), $this->getBttnNameEdit(), $jqueryBtn);
	}
	
	private function createButtonEditAjax(){
	
		// get information from the model
		$tblInfo = $this->zendModelObj-> info();
		
		// get the primary key column
		$primaryKeyCol = $tblInfo['primary'][1];
		
		// the elements array 
		$elements = $this->getElementsArray();
		
		$buttonEditScript = '
		
		jQuery( "#'."edit".$this->getDialogId().'" ).click(function() {

			// get the selected rowId from the radio button
			var selectedRowId = jQuery("#'.$this->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
			
			if(selectedRowId){
			    //Block User Interfae During Data Load
    			Metronic.blockUI({
    				boxed: true,
    				target: ".page-content-wrapper .page-content"
    			});
			    
    			jQuery( "#'.$this->getZendFormId().' input[id=\'form_mode\']" ).val("_edit");
    			jQuery( "#'.$this->getZendFormId().' input[id=\'treeNodeId\']").val("'.$this->getModuleId().'");		
    			
    			//reset the forms error messages to none
    			jQuery("#'.$this->getDialogId().' ul.errors").each(function(){jQuery(this).hide();});
    				
    			//set the form values
    			jQuery.ajax({
    					url: "index.php?c=ajax",
    					dataType: "json",
    					data: {
    						loadClass: "'.$this->zendModel.'", 
    						method: "'.$this->getSelectRowDetails().'",
    						parameter: "itemFound:" + selectedRowId
    					},
    					success: function( data ) {	';
    		
    					// set the values of other form elements
    					foreach ($elements["zf"] as $id=>$element){
    						if(is_array($element)){
    							$buttonEditScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
    							' '.$element["type"].'[id=\''.$element["name"].'\']" ).val(data.'.$element["name"].');';
    						}else{
    							$buttonEditScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
    							' input[id=\''.$element.'\']" ).val(data.'.$element.');';
    						}
    					}
    
    					// set the primary key value
    					// @todo not working for agregate primary keys
    					$buttonEditScript.= "\r\n".'jQuery( "#'.$this->getZendFormId().
    					' input[id=\'row_id\']" ).val(data.'.$primaryKeyCol.');';
    	
    					$buttonEditScript.= "\r\n".'
    					
    					//show the ZendForm in a jQuery dialog
					    $( "#'.$this->getDialogId().'" ).dialog( { title: "Modifiko Rekordin" } );
					    $( "#'.$this->getDialogId().'" ).dialog( "open" );
					    
    					 
    					 //Unblock User Interface After Data is Loaded
    					 Metronic.unblockUI(".page-content-wrapper .page-content");
    					}//end success
    				});
    					
    			return false;
    			}else{
    				alert("Zgjidhni nje element nga tabela fillimisht.");
    			}
		  })';
		
							
		$this->setScript($buttonEditScript);
		$jqueryBtn["icon"]="ui-icon-pencil";
		$jqueryBtn["showtext"] = "true";
		return HtmlView::displayButton("edit".$this->getDialogId(), $this->getBttnNameEdit(), $jqueryBtn);
	}
	
	private function createButtonDelete(){
		
		$buttonDeleteScript = '
		
		jQuery( "#'."delete".$this->getDialogId().'" ).click(function() {
			var selectedRowId = jQuery("#'.$this->getViewPaneId().' input[name=\'rowSelectionRadio\']:checked").val();
			if(selectedRowId){
				
				// reset the text elements
				//jQuery("form#'.$this->getZendFormId().' :input").not(":button, :submit, input[type=hidden]").val("");
				jQuery("form#'.$this->getZendFormId().' input[type=text]").val("");
						
				//set the form parameter values
				var formMode = jQuery( "#'.$this->getZendFormId().' input[id=\'form_mode\']" );
				//set the form mode to delete
				formMode.val("_delete");
				//set the selected rowId
				jQuery( "#'.$this->getZendFormId().' input[id=\'row_id\']").val(selectedRowId);
				
				//reset the forms error messages to none
				noty({
				    text: "Jeni i sigurte qe doni ta fshini elementin e perzgjedhur ?", 
					layout:"center",
					modal:true,
				    buttons: [
				      {type: "btn btn-mini btn-primary", text: "Ok", click: function($noty) {
				          $noty.close();
				          // create the new element via clone, naming it as the submit form button
				          var newElem = formMode.clone().attr("id", "submit_form").attr("name", "submit_form");
				 		  formMode.after(newElem);
				 		  //submit the zend form
						  jQuery("#'.$this->getZendFormId().'").submit();
				        }
				      },
				      {type: "btn btn-mini btn-danger", text: "Anullo", click: function($noty) {
				          $noty.close();
				          noty({force: true, text: "Elementi nuk u fshi", type: "error",layout:"topCenter",closeButton:true,timeout:2000});
				        }
				      }
				      ],
				    closable: false,
				    timeout: false
				  });
				return false;
			}else{
				alert("Zgjidhni nje element nga tabela fillimisht.");
			}
		});';
		$this->setScript($buttonDeleteScript);
		$jqueryBtn["icon"]="ui-icon-alert";
		$jqueryBtn["showtext"] = "true";
		return HtmlView::displayButton("delete".$this->getDialogId(), $this->getBttnNameDel(), $jqueryBtn);
	}
	
	public function getScript($script){
		return "\r\n<script>\r\n jQuery(function() {\r\n" . $script . "\r\n}); \r\n</script>\r\n";
	}
	
	/**
	 * Set html view header
	 * marilda
	 */
	public function sethtmlViewHeader($header=""){
		$this->htmlViewHeader = $header;
	}
	
	/**
	  * Get html view header
	  * marilda
	  */
	public function gethtmlViewHeader(){
		return $this->htmlViewHeader;
	}
	

	/**
	 * Define Grid Ascending or descending order
	 * marilda
	 */
	public function setSortDir($sort){
		if(isset($sort) && ( ($sort=="asc") || ($sort=="desc") ) )		
			$this->sortDirect = $sort;
		else 
		    $this->sortDirect = "asc";
	}
	
	/**
	 * Get Grid Sorting order
	 * marilda
	 */
	public function getSortDir(){
		return $this->sortDirect;
	}
	
	/**
	 * Set the columns to be shown in a tooltip
	 * @return the $tooltipCols
	 */
	public function getTooltipCols() {
		return $this->tooltipCols;
	}

	/**
	 * Return an array of the columns used in the tooltip
	 * @param array $tooltipCols
	 */
	public function setTooltipCols($tooltip_cols) {
		$this->tooltipCols = $tooltip_cols;
	}
	

	/**
	 * Attach a context menu to the grid
	 * 
	 */
	public function addContextMenu($action = "copy", $actionParams=array() ){
		// dont show the context menu, if no access rights
		if(!$this->hasWriteModuleAccess($this->moduleId)){
			return;
		}
		else {
			
			//possible actions array
			$possible_actions = array ("copy", "edit", "delete");
			if (in_array($action, $possible_actions)){
				
				$contextMenuScript = '
			
				jQuery("#'.$this->getViewPaneId().' table[id=\'data-table\']").contextMenu({
						selector: "tbody tr",
						className: "css-title",
						build: function($trigger, e) {
						
							// this callback is executed every time the menu is to be shown
							// its results are destroyed every time the menu is hidden
							// e is the original contextmenu event, containing e.pageX and e.pageY (amongst other data)
							return {
								callback: function(key, options) {
									
									//mark selected row on click
									jQuery( "#'.$this->getViewPaneId().' table[id=\'data-table\'] tr").contents("td").removeClass("my-hoveredTd");
									jQuery( "#'.$this->getViewPaneId().' table[id=\'data-table\'] tr").removeClass("selected_record");
									jQuery( "#'.$this->getViewPaneId().' table[id=\'data-table\'] tr[id=\'"+jQuery(this).attr("id")+"\']").attr("class", "selected_record");
																										
									//open east pane
									var myLayout = jQuery("body").layout();
									myLayout.open("east");
			
									//show & activate the Actions Section in the accordion
									jQuery("#action_clipboard").show();
									jQuery( "#accordion" ).accordion( "option", "active", 2 );				
										
									//show selected action in the header of the accordion
									jQuery("#lbl_action").text(key);
									jQuery("#action_1_title").text(key.toUpperCase());
									
									var recordId = jQuery( "#'.$this->getViewPaneId().' table tr[id="+jQuery(this).attr("id")+"] td[id=\'sector_code_td\'] label[class=\'dataContent\']").text();
									var recordAlias = jQuery( "#'.$this->getViewPaneId().' table tr[id="+jQuery(this).attr("id")+"] td[id=\'name_al_td\'] label[class=\'dataContent\']").text();
									jQuery("#action_1_txt").text(recordId + " - " + recordAlias);
							
									//store record data and action in a session
									jQuery.ajax({
										type: "GET",
										url: "index.php?c=ajax",
										data: {
											loadClass: "Ajax_Response_Utility",
											method: "setGridRecordAction",
											parameter: "moduleId:'.$this->moduleId.'" + ";recordId:"+ recordId + ";recordAction:"+ key+ ";recordAlias:"+ recordAlias
										}
									});
									
									//notify the user where the data is being copied from
									noty({"text":"Copying data from [" + recordAlias + "]","layout":"topCenter","type":"warning","speed":500,"timeout":2000,"closeButton":true,"closeOnSelfClick":true});
								},
								items: {
									"copy": { name: "Copy", icon: "copy"},
									"paste": { name: "Paste", icon: "paste",
											disabled: function(key, opt) {
												// this references the trigger element
												return !this.data("cutDisabled");
											}}
								}
							};
						}
					});';
				
			}
			else if ($action == "paste"){ //a specific action
				//allow paste action only if an item to copy is selected
				if (isset($actionParams["copy_from"]["itemId"])) {
					
					if (isset($actionParams["copy_to"]["itemFound"])  &&  ($actionParams["copy_from"]["itemId"] !=  $actionParams["copy_to"]["itemFound"]) ){
					
					$contextMenuScript = '
					
					jQuery("#'.$this->getViewPaneId().'").contextMenu({
						selector: ".headerWrap",
						className: "css-title",
						build: function($trigger, e) {
							return {
								callback: function(key, options) {
									//open east pane
									var myLayout = jQuery("body").layout();
									myLayout.open("east");
	
									//show & activate the Actions Section in the accordion
									jQuery( "#action_clipboard" ).show();
									jQuery( "#accordion" ).accordion( "option", "active", 2 );
										
									//show selected actions in the Actions Section of the accordion
									jQuery("#action_1_title").text("COPY");
									jQuery("#action_1_txt").text("'.$actionParams["copy_from"]["itemId"].' - '.$actionParams["copy_from"]["itemAlias"].'");
									
									if(jQuery("#action_2_title").text()) {
										jQuery("#action_2").html("");
									}
									if(jQuery("#action_status").text()) {
										jQuery("#action_status").html("");
									}
									jQuery("<div id=\'action_2\'><span id =\'action_2_title\' style=\'font-weight:bold;\'>"+ key.toUpperCase() +"</span><br/><span id =\'action_2_txt\'>'.$actionParams["copy_to"]["itemAlias"].'</span></div>").insertAfter( "#action_1" );
									
									//notify the user where the data is being copied to
									noty({"text":"Copying data to ['.$actionParams["copy_to"]["itemAlias"].']","layout":"topCenter","type":"warning","speed":500,"timeout":2000,"closeButton":true,"closeOnSelfClick":true});
									
									//show popup dialog to select the items to which the action will be applied
									if(!jQuery("#dialog-copyItems-'.$this->moduleId.'").html()){
										jQuery(".ui-layout-north").after(\'<div id="dialog-copyItems-'.$this->moduleId.'"></div>\');
									}
									jQuery.ajax({
										url:"index.php?c=ajax",
										type: "POST",
										data: {
											loadClass: "Ajax_Response_Utility",
											method: "copyItems",
											parameter: "cp_what:'.$this->moduleId.';mode:default"
										},
										context: this,
										success: function(response){
											jQuery( "#dialog-copyItems-'.$this->moduleId.'" ).html(response);
										}
									});
							},
							items: {
								"copy": { name: "Copy", icon: "copy",
									disabled: function(key, opt) {
										// this references the trigger element
										return !this.data("cutDisabled");
									}
								},
								"paste": {name: "Paste", icon: "paste"}
							}
						};
					}
					});';
					}
					else {
						//dont allow to copy items to the same element
						$this->setScript('noty({"text":"Nuk mund te kopjoni ne te njejtin element!","layout":"center","type":"error","speed":100,"timeout":3000,"closeButton":true,"closeOnSelfClick":true});');
					}
				}
			} //end of paste action
			$this->setScript($contextMenuScript);
		}
	}
	
}
?>