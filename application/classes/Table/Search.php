<?php

class Table_Search extends Table_Abstract implements Interface_iData {
	
	protected $_name 		= 'hr_personal';
	
	protected $_primary 	= 'emp_number';
	
	public function getDataById($forceNumber){
		return $this->find ( $forceNumber )->current ();
	}
	
	public function selectData($filters = array(), $sortField = null, $limit = null) {
		$select = $this->select ();
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
		return $this->fetchAll ( $select );
		//return $select->__toString();
	}
	
	public function createNew($inputArray = array('')){
		// create a new row in the table
		$row = $this->createRow ();
		
		try{
			// save the new row
			$row->save ();
			return true;
		}catch(Exception $e){
			 return $e->getMessage();
		}
	}
	
	public function updateRow($inputArray = array('')) {
		// find the row that matches the id
		$row = $this->getDataById($inputArray['row_id']);
		if ($row) {
			// set the row data
			
			try{
				// save the new row
				$row->save ();
				return true;
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
			 return $e->getMessage();
		}
	}
}

?>