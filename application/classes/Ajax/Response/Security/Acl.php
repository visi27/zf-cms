<?php

class Ajax_Response_Security_Acl extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// Toolbar - Force Selection Autocomplete Filter
		$roleModuleData = $moduleTable->getModuleByFormName("Role");
		
		// Toolbar Initializations
		$roleAutocomplete = new Utility_Autocomplete("filterRole", "Ajax_Response_Utility", "getRole");
		$moduleAutocomplete = new Utility_Autocomplete("filterModule", "Ajax_Response_Utility", "getModules");
		
		// Toolbar - Role Autocomplete Filter
		$roleAutocomplete->setDefaultText("Zgjidhni Rolin");
		$roleAutocomplete->setMapedData( 
				$mapedData = array("label"	=> "id,name",
				"value"	=>	"id,name",
				"id"	=>	"id"));
		$roleAutocomplete->addParameter("\"moduleId\" + \":{$roleModuleData->id}\"");
		
		// Toolbar - module Autocomplete Filter
		$moduleAutocomplete->setDefaultText("Modulet");
		$moduleAutocomplete->setMapedData(
				$mapedData = array("label"	=> "id, name_al",
						"value"	=>	"id, name_al",
						"id" => 'id'));
		
	
		
	   $elements = array(	"zf" 	=> 	array(
				"acl_id", "role_id", "module_id",
				array(	"name"	=>	"read",
						"type"	=>	"select"),
				array(	"name"	=>	"write",
						"type"	=>	"select")
		),
				"alias"	=> 	array(
						
						"acl_id","Role", "Module","Read", "Write"
				)
		);
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>true, "edit"=>true, "delete"=>true), "security");
		
		$myGrid->addFilter($roleAutocomplete, $dbColumn = "sys_acl.role_id", " = ");
		$myGrid->addFilter($moduleAutocomplete, $dbColumn = "sys_acl.module_id", " = ");
		
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		//$myGrid->setSelectMethod($methodName = "selectRowsForGrid", $orderBy = "role_id");

		$myGrid->addAutoComplete("role_id", "Ajax_Response_Utility", "getRole",
				array("value" => 'id+" - "+item.name',
						"id" => 'id',
						"label" => 'id+" - "+item.name'));

		$myGrid->addAutoComplete("module_id", "Ajax_Response_Utility", "getModules",
				array("value" => 'id+" - "+item.name_al',
						"id" => 'id',
						"label" => 'id+" - "+item.name_al'));
		
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
		$myGrid->hideColumn("acl_id");
				
		echo $myGrid->render();
		
	}
	
	}
	
	?>