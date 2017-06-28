<?php

class Table_AcdRole extends Table_Abstract implements Interface_iForm {

	protected $_name 		= 'sys_acd_roles';
	
	protected $_primary 	= 'acd_role_id';
	protected $_smartSearch = array(
			'Display' => array("structure_name", "function_name"),
			'Search'  => array("structure_name", "function_name"),
			'Method'  => 'selectRowsForGrid'
	);
	public function getDataById($id, $current = true){
		
		if($current == true){
			return $this->find ( $id )->current ();
			
		}else{
			return $this->find ( $id );
		}
		
	}

	// the fields in database where the smartSearch can query
	public function getSearchFields(){
		// the first field in the array is the one used as header in the smartSearch
		return array("structure_name", "function_name");
	}
	
	// the fields to be shown for the smart search
	public function getFieldsToShow(){
		// the first field in the array is the one used as header in the smartSearch
		return array("structure_name", "function_name");
	}
	
	public function selectData($filters = array(), $sortField = null, $limit = null, 
			$columns = null , $distinct=false) {
		
		$select = $this->select ();
		
		if (isset($columns) && is_array($columns)){
			$select	->setIntegrityCheck(false)
					->from($this, $columns);
		}
		// add any filters which are set
		if (count ( $filters ) > 0) {
			foreach ( $filters as $field => $filter ) {
				if (count ( $filter ) > 0){
					foreach ($filter as $operator => $value)
					$select->where ( $field . $operator. '?', $value );
				}
			}
		}
		
		if ($distinct){
			$select->distinct();
		}
		
		// add the sort field if it is set
		if (null != $sortField) {
			$select->order ( $sortField );
		}
		// add the limit field if it is set
		if(null != $limit){
			$select->limit($limit);
		}
		$test = $select->__toString();
		//print_r($test);
		return $this->fetchAll ( $select );
		//return $select->__toString();
	}
	
	
		public function createNew(Zend_Form $formObj){
														
				// create a new row in the table
				$row = $this->createRow ();
				$row -> structure_name = $formObj->getElement('structure_name')->getValue();
				$row -> function_name =  $formObj->getElement('function_name')->getValue();
				$row -> rank_group =  $formObj->getElement('rank_group')->getValue();
			try{
					// save the new row
					return $row->save ();
		
				}catch(Exception $e){
				
					return false;
				}
			}
	public function updateRow(Zend_Form $formObj) {
												
	// find the row that matches the id
		$row = $this->getDataById($formObj->getElement('row_id')->getValue());
		
		if ($row) {
			// set the row data
				$row -> structure_name = $formObj->getElement('structure_name')->getValue();
				$row -> function_name =  $formObj->getElement('function_name')->getValue();
				$row -> rank_group =  $formObj->getElement('rank_group')->getValue();
		try{
				// save the new row, return the last inserted id
				return $row->save ();
												
			}catch(Exception $e){	
				return $e->getMessage();
			}
			
		} else {
			throw new Zend_Exception ( "Update function failed; could not find row!" );
		}
	}
	
	public function deleteRow($id) {
		
		try {
			// find the row that matches the id
			$row = $this->getDataById( $id );
			$row->delete ();
			return true;
		}catch(Exception $e){
			return false;
		}
		
	}
	
	
	// GRID SECTION METHODS
	// Information to be displayed in the grid
	// Return Type: rowSet	
	public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null){
		$select = $this->select ();
	
		$select	->setIntegrityCheck(false)
				->from($this, array(
						"acd_role_id", "structure_name", "function_name",  "rank_group"	));
	
		// apply filtering by structure name - example: used at the toolbar
		if(isset($filters['struct']) && strlen($filters['struct'])>0 && strcmp ($filters['struct'],"FilterByStructure")!==0){
			$select->where("structure_name LIKE ?", "{$filters['struct']}");
		}	
	
		// add the sort field if it is set
		if (null != $sortField) {
			//marilda: add sort direction if it is set
			if (null != $sortDir) {
				$sortField = $sortField." ".$sortDir;
			}
				
			$select->order ( $sortField );
		}
		else {
			$select->order('structure_name ASC');
			$select	->order('function_name ASC');
		}
		
	
		//$test = $select->__toString();
		return $this->fetchAll ( $select );
	}
	public function selectRowForGrid(array $args){
	// get the parameters
		$pars = Utility_Functions::argsToArray($args);
	
		$select = $this->select ();
		$select	->setIntegrityCheck(false)
		->from($this, array("acd_role_id", "structure_name", "function_name",  "rank_group"	))
		        ->where('sys_acd_roles.acd_role_id = ?', $pars['itemFound']);
	//echo $select;
		$item = $this->fetchRow ( $select );
	
		//return the result in json format
		echo Utility_Functions::_toJson(!empty($item)?$item->toArray():"");
	}

}
?>