<?php

class Ajax_Response_Security_Users extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// Toolbar - Force Selection Autocomplete Filter
		$roleModuleData = $moduleTable->getModuleByFormName("Role");
		
		// Toolbar Initializations
		$roleAutocomplete = new Utility_Autocomplete("filterRole", "Ajax_Response_Utility", "getRole");
		$structureAutocomplete = new Utility_Autocomplete("filterStruct", "Ajax_Response_Utility", "getStructures");
		$functionAutocomplete = new Utility_Autocomplete("filterFunct", "Ajax_Response_Utility", "getPositionNames");
		
		// Toolbar - Role Autocomplete Filter
		$roleAutocomplete->setDefaultText("Zgjidhni Rolin");
		$roleAutocomplete->setMapedData( 
				$mapedData = array("label"	=> "id,name",
				"value"	=>	"id,name",
				"id"	=>	"id"));
		$roleAutocomplete->addParameter("\"moduleId\" + \":{$roleModuleData->id}\"");
		
		// Toolbar - Structure Autocomplete Filter
		$structureAutocomplete->setOnChangeMethod("$('#filterFunct').val('{$functionAutocomplete->getDefaultText()}');");
		$structureAutocomplete->setDefaultText("Zgjidhni Strukturen");
		$structureAutocomplete->setMapedData(
				$mapedData = array("label"	=> "name",
						"value"	=>	"name"));
		
		// Toolbar - Function Autocomplete Filter
		$functionAutocomplete->addParameter("\"{$structureAutocomplete->getName()}:\" + $(\"#{$structureAutocomplete->getName()}\").val()");
		$functionAutocomplete->setDefaultText("Zgjidhni Funksionin");
		$functionAutocomplete->setMapedData(
				$mapedData = array("label"	=> "name",
						"value"	=>	"name"));
		
	$elements = array(	"zf" 	=> 	array(
												"id", "username", "password", 
												"fullname", "ip", "role_id", "acd_role_id",
												array(	"name"	=>	"isactive", 
														"type"	=>	"select"), 
												"description"
											), 
							"alias"	=> 	array(	
												"id", "Username", "Emri i Plote", 
												"Ip", "Aksesi i Modulit", "Aksesi i te Dhenave", "Aktive"
											)
						 );
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>true, "edit"=>true, "delete"=>true), "security");
		
		$myGrid->addFilter($roleAutocomplete, $dbColumn = "sys_users.role_id", " = ");
		$myGrid->addFilter($structureAutocomplete, $dbColumn = "sys_acd_roles.structure_name", " = ");
		$myGrid->addFilter($functionAutocomplete, $dbColumn = "sys_acd_roles.function_name", " = ");
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		//$myGrid->setSelectMethod($methodName = "selectRowsForGrid", $orderBy = "role_id");

		$myGrid->addAutoComplete("role_id", "Ajax_Response_Utility", "getRole",
				array("value" => 'id+" - "+item.name',
						"id" => 'id',
						"label" => 'id+" - "+item.name'));

		$myGrid->addAutoComplete("acd_role_id", "Ajax_Response_Utility", "getAcdFullRoleName",
				array("value" => 'acd_role_id+" - "+item.name',
						"id" => 'acd_role_id',
						"label" => 'acd_role_id+" - "+item.name'));
		
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
				
		echo $myGrid->render();
		
	}
	
	}
	
	?>