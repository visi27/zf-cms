<?php

class Table_Acd extends Table_Abstract implements Interface_iForm{
	
	protected $_name 		= 'sys_acd';
	
	protected $_primary 	= 'acd_id';
	
	protected $_rowClass 	= 'Table_MyRowClass';
	
	
	public function hasAcdRight($forceCode, $unitCode, $userId=null){
		
		//get the userId, if null -> get the selected userId
		$userId = ($userId==null)?Authenticate::getUserId():$userId;
		
		// load the users table model
		$users = new Table_Users();
		// get the user details, by searching by userId
		$userDetails = $users->getDataById($userId);
		
		// get the acdRoleId belonging to the selected user
		$acdRoleId = $userDetails->acd_role_id; 
		
		// query
		$select = $this->select ();
		$select	->setIntegrityCheck(false)
		->from($this, array("count(*) as rights"))
				->where("
						('$forceCode') in (select force_code from sys_acd where acd_role_id='$acdRoleId' and struc_code = '*' 
			                        and rule='A' and sys_acd.action='W')
						OR
						('$unitCode') in (select struc_code from sys_acd where acd_role_id='$acdRoleId' and struc_code <> '*'
			                        and rule='A'  and sys_acd.action='W')
				")				
				->where("
						('$forceCode') not in (select force_code from sys_acd where acd_role_id='$acdRoleId' and struc_code = '*' 
			                        and rule='D' and sys_acd.action='W')
				")		
				->where("
						('$unitCode') not in (select struc_code from sys_acd where acd_role_id='$acdRoleId' and struc_code <> '*'
			                        and rule='D'  and sys_acd.action='W')
				")
				->where("sys_acd.action='W'")
				->where("sys_acd.acd_role_id=?", $acdRoleId);
		$test = $select->__toString();
		
		$result = $this->fetchRow($select);
		
		return $result->rights;
		
	}
	
	public function getDataById($id, $current = true){	
		if($current == true){
			return $this->find ( $id )->current ();
			
		}else{
			return $this->find ( $id );
		}
	}
	
public function selectData($filters = array(), $sortField = null, $limit = null) {
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array(
						"acd_id", 
						"CONCAT(r.structure_name,' -> ', r.function_name) as acd_role", 
						"force_code", 
						new Zend_Db_Expr(
							"CASE struc_code 
								WHEN '*' THEN '[*ALL UNITS*]' 
								ELSE struc_code 
							END AS unit_code"),
						new Zend_Db_Expr(
							"CASE sys_acd.action 
        						WHEN 'R' then 'Read'
        						WHEN 'W' then 'Write'
        						WHEN 'P' then 'Print'
							 END as action_type"), 
						new Zend_Db_Expr(
							"CASE sys_acd.rule 
        						WHEN 'A' then 'Allow'
        						WHEN 'D' then 'Deny'
							END as rule_type")
						)
					  )
				->join('sys_acd_roles as r', 'sys_acd.acd_role_id = r.acd_role_id', '');
						
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
		
	public function selectActions($filters = array(), $sortField = null, $limit = null, $columns = null , $distinct=false){
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
		//$test = $select->__toString();
		return $this->fetchAll ( $select );
		//return
	}
		
	public function createNew(Zend_Form $formObj){
												
			// create a new row in the table
		$row = $this->createRow ();
		
		// set values
		$row->acd_role_id = $formObj->getElement('acd_role_id')->getValue();
		$forceCode = explode("-",$formObj->getElement('force_code')->getValue());
		$row->force_code = trim($forceCode[1]);
		$strucCode = explode("-",$formObj->getElement('struc_code')->getValue());
		$row->struc_code = ($formObj->getElement('struc_code')->getValue()=="")?"*": trim($strucCode[0]);
		$row->action =$formObj->getElement('action')->getValue();
		$row->rule = $formObj->getElement('rule')->getValue();
		
		// begin a transaction
		$this->getAdapter()->beginTransaction();
		try{
			// save
			$row->save();
			
			//commit the changes
			$this->getAdapter()->commit();
			
			// if no exception, return true
			return true;
			
		}catch(Exception $e){
		 	$this->getAdapter()->rollBack();
			return false;	
		}
	}
	
	public function updateRow(Zend_Form $formObj) {
		
		// find the row that matches the id
		$row = $this->getDataById($formObj->getElement('row_id')->getValue());
		
		if ($row) {
			// set values
		$row->acd_role_id = $formObj->getElement('acd_role_id')->getValue();
		$forceCode = explode("-",$formObj->getElement('force_code')->getValue());
		$row->force_code = trim($forceCode[1]);
		$strucCode = explode("-",$formObj->getElement('struc_code')->getValue());
		$row->struc_code = ($formObj->getElement('struc_code')->getValue()=="")?"*": trim($strucCode[0]);
		$row->action =$formObj->getElement('action')->getValue();
		$row->rule = $formObj->getElement('rule')->getValue();
		
			
			// begin a transaction
			$this->getAdapter()->beginTransaction();
			try{
				// update the row
				$row->save();
				
				//commit the changes
				$this->getAdapter()->commit();
				
				// if no exception, return true
				return true;
				
			}catch(Exception $e){
			 	$this->getAdapter()->rollBack();
				return false;	
			}
			
		} 
		else {
			throw new Zend_Exception ( "Update function failed; could not find row!" );
		}
	}
	
	public function deleteRow($id) {	
		// begin a transaction
		$this->getAdapter()->beginTransaction();
		try{
			// find the row that matches the id
			$row = $this->getDataById( $id );
	
			// delete the row
			$row->delete ();
			
			//commit the changes
			$this->getAdapter()->commit();
				
			// if no exception, return true
			return true;
				
		}catch(Exception $e){
			 $this->getAdapter()->rollBack();
			return false;	
		}
	}
	
	// GRID SECTION METHODS
	// Information to be displayed in the grid
	// Return Type: rowSet
	public function selectDataForGrid($filters = array(), $sortField = null, $sortDir = null){
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array(
						"acd_id", 
						"CONCAT(r.structure_name,' -> ', r.function_name) as acd_role", 
						"force_code", 
						new Zend_Db_Expr(
							"CASE struc_code 
								WHEN '*' THEN '[*ALL UNITS*]' 
								ELSE struc_code 
							END AS unit_code"),
						new Zend_Db_Expr(
							"CASE sys_acd.action 
        						WHEN 'R' then 'Read'
        						WHEN 'W' then 'Write'
        						WHEN 'P' then 'Print'
							 END as action_type"), 
						new Zend_Db_Expr(
							"CASE sys_acd.rule 
        						WHEN 'A' then 'Allow'
        						WHEN 'D' then 'Deny'
							END as rule_type")
						)
					  )
				->join('sys_acd_roles as r', 'sys_acd.acd_role_id = r.acd_role_id', '');
		
		// apply filtering by roleId - example: used at the toolbar
		if(isset($filters['acdRole'])){
			$select->where("sys_acd.acd_role_id = ?", $filters['acdRole']);
		}
		
		// apply filtering by structure name - used at the toolbar
		if(isset($filters['struct']) && strlen($filters['struct'])>0 && strcmp ($filters['struct'],"FilterByStructure")!==0){
			$select->where("r.structure_name LIKE ?", $filters['struct']);
		}
		
		// apply filtering by position - used at the toolbar
		if(isset($filters['funct']) && strlen($filters['funct'])>0 && strcmp ($filters['funct'],"FilterByFunction")!==0){
			$select->where("r.function_name LIKE ?", $filters['funct']);
		}
		
		// apply filtering by force number - used at the toolbar
		if(isset($filters['forceNo']) && strlen($filters['forceNo'])>0 && strcmp ($filters['forceNo'],"FilterByForce")!==0){
			$select->where("sys_acd.force_code LIKE ?", $filters['forceNo']);
		}
		
		// apply filtering by access right - used at the toolbar
		if(isset($filters['access']) && strlen($filters['access'])>0 && strcmp ($filters['access'],"FilterByAccessRight")!==0){
			$select->where("sys_acd.action LIKE ?", $filters['access']);
		}
		
		// apply filtering by rule - used at the toolbar
		if(isset($filters['rule']) && strlen($filters['rule'])>0 && strcmp ($filters['rule'],"FilterByRule")!==0){
			$select->where("sys_acd.rule LIKE ?", $filters['rule']);
		}
		
		// add the sort field if it is set
		if (null != $sortField) {
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
	public function selectRowForGrid($rowId){
		$select = $this->select ();
		$select	->setIntegrityCheck(false)
				->from($this, array("acd_id", 
									"acd_role_id", 
									"force_code",
									"struc_code", "action","rule"))
				->where('sys_acd.acd_id = ?', $rowId);
		
		$json['modules'] = $this->fetchRow ( $select )->toArray();	

		echo Zend_Json::encode($json);	 
	}
}

?>