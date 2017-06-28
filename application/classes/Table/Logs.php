<?php

class Table_Logs extends Table_Abstract {
	
	protected $_name 		= 'sys_logs';
	
	protected $_primary 	= 'id';
	
	public function getDataById($id, $current = true){
	
		if($current == true){
			return $this->find ( $id )->current ();
				
		}else{
			return $this->find ( $id );
		}
	
	}
	
	public function createLog($action, $description, $user_id) {
		// create a new row in the logs table
		$row = $this->createRow ();
		// set the row data
		$row->action = $action;
		$row->description = $description;
		$row->user_id = $user_id;
		$row->datetime = date("Y-m-d h:i:s");

		// save the new row
		$row->save ();
		// now fetch the id of the row you just created and return it
		$id = $this->_db->lastInsertId ();
		
		return $id;
	}
	
	public function selectLogs($filters = array(), $sortField = null, $limit = null) {
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
		// add the sort field is it is set
		if (null != $sortField) {
			$select->order ( $sortField );
		}
		// add the limit field is it is set
		if(null != $limit){
			$select->limit($limit);
		}
		return $this->fetchAll ( $select );
		//return $select->__toString();
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
			return $e->getMessage();
		}
	}
	
	// GRID SECTION METHODS
	// Information to be displayed in the grid
	// Return Type: rowSet
	// $id['id'] contains selected user id
	public function selectRowsForGrid($id = array()){
	
		$select = $this->select ();
	
		$select	->setIntegrityCheck(false)
		->from($this, array("sys_logs.id as row_id", "sys_logs.action","sys_logs.description","sys_logs.user_id","sys_logs.datetime",
				"DATE_FORMAT(sys_logs.datetime,'%d/%m/%Y %H:%i:%s') as datetime"));
			
		//$test = $select->__toString();
		//print_r($test);
		return $this->fetchAll ( $select );
	}
	
	// Provides data to the zend form Sector
	// Must return the same field names as the zend form in
	// Return Type: Json
	public function selectRowForGrid($rowId){
		$select = $this->select ();
		$select	->setIntegrityCheck(false)
		->from($this, array("id as row_id", "username", "CONCAT('') as password", "fullname", "ip",
				"CONCAT(sys_roles.id,' - ',sys_roles.role_name) as role_id",
				"isactive", "description"))
				->join('sys_roles', 'sys_users.role_id = sys_roles.id', '')
				->where('sys_users.id = ?', $rowId);
	
		$json['modules'] = $this->fetchRow ( $select )->toArray();
	
		echo Zend_Json::encode($json);
	}
	
}

?>