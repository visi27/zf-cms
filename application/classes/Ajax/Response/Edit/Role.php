<?php

class Ajax_Response_Edit_Role extends Ajax_Response_Abstract{
	private $filterChain;
	
	private $moduleName;
	
	private $birtQueryString = array();
	
	public function _init(){
		$moduleName = explode("_", __CLASS__);
		$this->moduleName = array_pop($moduleName);
	}
	public function layoutCenter_Action(){
	 // the module id of this report
		$module = $this->_params["moduleId"];
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// the smartSearch result
		$smartSearch = $this->_params["itemFound"];
		$elements =
		array("zf" 	=> 	array("row_id", "role_name", "role_desc"),
				"db" 	=> 	array("id", "role_name", "description"), //always start with the primary key
				"alias"=> array("id", "Roli", "Pershkrimi"));
		
		$myGrid = new Utility_Grid($module, $this->_action, $elements, false,
				array("new"=>true, "edit"=>true, "delete"=>true), "security");
		
		// ------------------------- FILTER BY SMARTSEARCH ------------------------------
		// allow smartsearch rendering, call this method to smartsearch
		if ( !empty($smartSearch) ){
			// render the paginator's page where this element belongs to
			$myGrid->setSelectedElement($smartSearch);
		}
		// ------------------------- END FILTER BY SMARTSEARCH ---------------------------
		
		// select method to generate the grid resultset
		$myGrid->setSelectMethod($methodName = "selectRowsForGrid", $orderBy = "");
		
		//$myGrid->setSearchMethod($smartSearch, "selectRowsForGrid");
		
		$header = $this->renderBreadCrumb();
		
		$output = $header.$myGrid->render();
	}
	
}

?>