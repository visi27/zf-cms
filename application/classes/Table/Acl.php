<?php

class Table_Acl extends Table_Abstract implements Interface_iForm{
	
	protected $_name 	= 'sys_acl';
	
	protected $_primary = 'acl_id';
	
	protected $_rowClass 	= 'Table_MyRowClass';
	
	
public function getDataById($id, $current = true){
		
		if(empty($id))
			return false;
		
		if($current == true){
			return $this->find ( $id )->current ();
			
		}else{
			return $this->find ( $id );
		}
		
	}
	
	public function hasAccess($roleId, $moduleId, $action){
		$where = array(
							"role_id" => array("=" => $roleId),
							"module_id" => array("=" => $moduleId),
							$this->_name.".$action" => array("=" => "1")
					  );
		$result = $this->selectData($where);
		
		if( $result->count() == 1){
			$data = $result->getRow(0, FALSE);
			return $data->{$action};
			
		}else{
			return false;
		}
		
		//return true;
	}
	
public function selectData($filters = array(), $sortField = null, $limit = null) {
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("acl_id", "sys_roles.role_name", 
									"sys_modules.name_al", 
									new Zend_Db_Expr(
									"CASE WHEN sys_acl.read = 1
									THEN 'True' ELSE 'False' END AS 'read'"), 
									new Zend_Db_Expr(
									"CASE WHEN sys_acl.write = 1
									THEN 'True' ELSE 'False' END AS 'write'")
									)
					  )
				->join('sys_roles', 'sys_acl.role_id = sys_roles.id', '')
				->join('sys_modules', 'sys_acl.module_id = sys_modules.id', '');
						
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
		
	public function createNew(Zend_Form $formObj){
		// create a new row in the table
		$row = $this->createRow ();
		
		$role_id = explode("-", $formObj->getElement('role_id')->getValue()); // formati: "xy - abcd"
		$module_id = explode("-", $formObj->getElement('module_id')->getValue()); // formati: "xy - abcd"
		
		$row->role_id = trim($role_id[0]);
		$row->module_id = trim($module_id[0]);
		$row->read = $formObj->getElement('read')->getValue();
		$row->write = $formObj->getElement('write')->getValue();
		try{
			// save the new row
			$id = $row->save ();
			// now fetch the id of the row you just created and return it
			return $id;
		}catch(Exception $e){
			return false;
		}
	}
	
	public function updateRow(Zend_Form $formObj) {
		// find the row that matches the id
		$row = $this->getDataById($formObj->getElement('row_id')->getValue());
		if ($row) {
			// set the row data
		$role_id = explode("-", $formObj->getElement('role_id')->getValue()); // formati: "xy - abcd"
		$module_id = explode("-", $formObj->getElement('module_id')->getValue()); // formati: "xy - abcd"
		
		$row->role_id = trim($role_id[0]);
		$row->module_id = trim($module_id[0]);
		$row->read = $formObj->getElement('read')->getValue();
		$row->write = $formObj->getElement('write')->getValue();
			try{
				// save the new row
				$row->save ();
				
				return true;
				
			}catch(Exception $e){
				
				return false;
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
	// $id['id'] contains selected role id
	public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null){
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("acl_id as row_id", "sys_roles.role_name", 
									"sys_modules.name_al", 
									new Zend_Db_Expr(
									"CASE WHEN sys_acl.read = 1
									THEN 'True' ELSE 'False' END AS 'read'"), 
									new Zend_Db_Expr(
									"CASE WHEN sys_acl.write = 1
									THEN 'True' ELSE 'False' END AS 'write'")
									)
					  )
				->join('sys_roles', 'sys_acl.role_id = sys_roles.id', '')
				->join('sys_modules', 'sys_acl.module_id = sys_modules.id', '');
				
		// apply filtering by roleId - example: used at the toolbar 
		if(isset($filters['aclRole'])){
			$select->where("sys_roles.id = ?", $filters['aclRole']);
		}
		
		// apply filtering by roleId - example: used at the toolbar
		
		if(isset($filters['moduli'])){
			$select->where("sys_acl.module_id = ?", $filters['moduli']);
		}
		
		
		// add the sort field if it is set
		if (null != $sortField) {
			//echo $sortField;
			//marilda: add sort direction if it is set
			if (null != $sortDir) {
				$sortField = $sortField." ".$sortDir;
			}
			$select->order ( $sortField );
				
		}
		
		//$test = $select->__toString();
		//print_r($test);
		return $this->fetchAll ( $select );
	}
	
	// Provides data to the zend form Acl
	// Must return the same field names as the zend form in 
	// Return Type: Json
	public function selectRowForGrid(array $args){
		// get the parameters
		$rowId = Utility_Functions::argsToArray($args);
		
		$select = $this->select ();
		$select	->setIntegrityCheck(false)
				->from($this, array("acl_id", 
									"CONCAT(sys_roles.id,' - ',sys_roles.role_name) as role_id", 
									"CONCAT(sys_modules.id,' - ',sys_modules.name_al) as module_id",
									"read", "write"))
				->join('sys_roles', 'sys_acl.role_id = sys_roles.id', '')
				->join('sys_modules', 'sys_acl.module_id = sys_modules.id', '')
				->where('sys_acl.acl_id = ?', $rowId['itemFound']);
				
		$item = $this->fetchRow ( $select ); 
		//echo $select->__toString ();

		//return the result in json format
		echo Utility_Functions::_toJson(!empty($item)?$item->toArray():""); 
	}
}

?>