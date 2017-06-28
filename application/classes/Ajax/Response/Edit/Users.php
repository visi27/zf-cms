<?php

class Ajax_Response_Edit_Users extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// Toolbar - Force Selection Autocomplete Filter
		$roleModuleData = $moduleTable->getModuleByFormName("Role");
		
		// Toolbar Initializations
		$roleAutocomplete = new Utility_Autocomplete("filterRole", "Ajax_Response_Utility", "getRole");

		
		// Toolbar - Role Autocomplete Filter
		$roleAutocomplete->setDefaultText("Zgjidhni Rolin");
		$roleAutocomplete->setMapedData( 
				$mapedData = array("label"	=> "id,name",
				"value"	=>	"id,name",
				"id"	=>	"id"));
		$roleAutocomplete->addParameter("\"moduleId\" + \":{$roleModuleData->id}\"");
		
		
	   $elements = array(	"zf" 	=> 	array(
												"id", "username", "password", 
												"fullname", "ip", "role_id",
												array(	"name"	=>	"isactive", 
														"type"	=>	"select"), 
												"description"
											), 
							"alias"	=> 	array(	
												"id", "Username", "Emri i Plote", 
												"Ip", "Aksesi i Modulit", "Aktive"
											)
						 );
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>true, "edit"=>true, "delete"=>true), "edit");
		
		$myGrid->addFilter($roleAutocomplete, $dbColumn = "sys_users.role_id", " = ");
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		//$myGrid->setSelectMethod($methodName = "selectRowsForGrid", $orderBy = "role_id");

		$myGrid->addAutoComplete("role_id", "Ajax_Response_Utility", "getRole",
				array("value" => 'id+" - "+item.name',
						"id" => 'id',
						"label" => 'id+" - "+item.name'));
		
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
				
		$header = $this->renderBreadCrumb();
		
		$output = $header.$myGrid->render();
		
		echo $output;
		
	}
	
	}
	
	?>