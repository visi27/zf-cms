<?php

class Table_MyRowClass extends Zend_Db_Table_Row_Abstract {
	
	private $_userId;
	
	private function checkAccessControlOverData(){
		//---------------------- ACCESS CONTROL OVER THE DATA (ACD) CHECK ---------//
		
		//load the Modules table
		$modules = new Table_Modules();
		
		// the module name being used in this transaction
		$moduleName = substr($this->_tableClass, strrpos($this->_tableClass,"_")+1);
		
		// get the module details by the module name
		$currentModule = $modules->getModuleByFormName($moduleName);
			
		// STEP-1. identify updates that are performed over the person's data
		// this check can be done by looking at the module name being used
		
		// load this model in order to make use of isChildOf function
		$ajaxUtilities = new Ajax_Response_Utility();
		
		// check if the current module belongs (is child of) to the Personal - Tab
		$status = $ajaxUtilities->isChildOf(	Zend_Registry::get ( 'config' )->personel->tree->root,
				$currentModule->id	);
		
		if($status == true){ //YES it belongs to the Personal - Tab (it is a child node)
				
			// STEP-2. check if the current user has rights to perform an update over this person (force/unit)
			// check user access controll over the data
				
			// load the personal table model
			$personal = new Table_Personal();
				
			// get the current data (force_code, unit_code, rank) for the selected person
			$currentFlags = $personal->getCurrentForceUnitRank();
		
			$acd = new Table_Acd();
			// check this user's right over the force/unit defined by the person's data (force_code, unit_code)
			$hasWriteAccess = $acd->hasAcdRight($currentFlags->flag_force_code, $currentFlags->flag_unit_code);
			//per Formen Create	
			if((!$hasWriteAccess) && ($currentFlags!='XX')){
				
				throw new Zend_Exception (Zend_Registry::get('lang')->model->abstract->noWriteDataAccess.$currentFlags->flag_unit_code."<br>");
				//echo ($status.":Updating Personel Data - ". $moduleName );
			}
		}
		
		//----------------------------- END OF ACD CHECK ---------------------------//
	}
	
	public function init(){
		 $this->_userId = Authenticate::getUserId();
	}
	
	//pre update logic
	public function _insert(){
		//$this->checkAccessControlOverData();
	}
	
	//pre update logic
	public function _update(){
		//$this->checkAccessControlOverData();
			
	}
	
	//pre delete logic
	public function _delete(){
		//$this->checkAccessControlOverData();
	}
	
	//post insert
	public function _postInsert(){

		$tableInfo = $this->getTable()->info();
		
		$logger = new Table_ActionLog();
		
		$result = $logger->insertNew($tableInfo['name'], 
		$this->_data[$this->_primary[1]], 'C', 
		$this->_userId, print_r($this->_data, true));
	}
	
	//post update
	public function _postUpdate(){

		$tableInfo = $this->getTable()->info();
		
		$logger = new Table_ActionLog();
		
		$result = $logger->insertNew($tableInfo['name'], 
		$this->_data[$this->_primary[1]], 'U', 
		$this->_userId, print_r($this->_data, true));
	}
	
	//post delete
	public function _postDelete(){
		$tmp = $this;
		$tableInfo = $this->getTable()->info();
		
		$logger = new Table_ActionLog();
		
		$result = $logger->insertNew($tableInfo['name'], 
		$this->_data[$this->_primary[1]], 'D', 
		$this->_userId, print_r($this->_data, true));
	}
}

?>