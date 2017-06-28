<?php

class Table_Role extends Table_Abstract implements Interface_iForm {

	protected $_name 		= 'sys_roles';
	
	protected $_primary 	= 'id';
	
	protected $_rowClass 	= 'Table_MyRowClass';
	
	protected $_smartSearch = array(
			'Display' => array("role_name", "description"),
			'Search'  => array("role_name", "description"),
			'Method'  => 'selectRowsForGrid'
	);
	
	public function getDataById($id, $current = true){		
		if($current == true){
			return $this->find ( $id )->current ();
			
		}else{
			return $this->find ( $id );
		}
		
	}
	
	public function getSearchFields(){
		// the first field in the array is the one used as header in the smartSearch
		return array("role_name", "description");
	}
	
	public function selectData($filters = array(), $sortField = null, $limit = null, $columns = null) {
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
		$row -> role_name = $formObj->getElement('role_name')->getValue();
		$row -> description = $formObj->getElement('role_desc')->getValue();
		try{
			// save the new row
			$row->save ();
			// now fetch the id of the row you just created and return it
			return true;
		}catch(Exception $e){
			if( $e-> getMessage() == "Cannot refresh row as parent is missing")
				return true;
			else 
				return $e->getMessage();
		}
	}
	
	public function updateRow(Zend_Form $formObj) {
		// find the row that matches the id
		$row = $this->getDataById($formObj->getElement('row_id')->getValue());
		if ($row) {
			// set the row data
			$row -> role_name = $formObj->getElement('role_name')->getValue();
		    $row -> description = $formObj->getElement('role_desc')->getValue();
			try{
				// save the new row
				$row->save ();
				// now fetch the id of the row you just created and return it
				return true;
			}catch(Exception $e){
				if( $e-> getMessage() == "Cannot refresh row as parent is missing")
					return true;
				else 
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
			return $e->getMessage();
		}
	}
	
	// GRID SECTION METHODS
	// Information to be displayed in the grid
	// Return Type: rowSet
	// marilda
	public function selectRowsForGrid($id = array(),$sortField = null, $sortDir = null){
		
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("id",  "role_name", "description" ));
		
		// add the sort field if it is set
		if (null != $sortField) {
			//marilda: add sort direction if it is set
			if (null != $sortDir) {
				$sortField = $sortField." ".$sortDir;
			}
			$select->order ( $sortField );
		}
		else {
			$select->order('role_name');
		}
		
		return $this->fetchAll ( $select );
	}
}
?>