<?php

class Table_AccessLog extends Table_Abstract implements Interface_iData{
	
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
	
public function selectData($filters = array(), $sortField = null, $limit = null) {
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("id", "action", "description", "sys_users.username", "datetime"))
				->join('sys_users', 'sys_users.id = user_id', '');
						
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
	
	public function createNew($inputArray = array()){
		
	}
	
	public function updateRow($inputArray = array()){
		
	}
	
	public function deleteRow($id){
		
	}
	
	
	/**
	 * Kthen nje dataset me veprimet e perdoruesve
	 * marilda
	 */
	public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null){
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("id", "action", "description", "sys_users.username", "datetime"))
				->join('sys_users', 'sys_users.id = user_id', '');
		
		// apply filtering by userID - used at the toolbar
		if(isset($filters['user'])){
		   $select->where("sys_logs.user_id = ?", $filters['user']);			
		}	
		
		// apply filtering by action type - used at the toolbar
		if(isset($filters['action']) && strlen($filters['action'])>0 && strcmp ($filters['tbl'],"FilterByAction")!==0){
			$select->where("sys_logs.description LIKE ?", $filters['action']);
		}

			// apply filtering by start date - used at the toolbar
		// show actions after a given fromDate
		if(isset($filters['from']) && strlen($filters['from'])>0 && strcmp (trim($filters['from']),"StartDate")!==0 ){
			$date_from = explode("/",$filters['from']);
			$date_from_str = $date_from["2"]."-".$date_from["1"]."-".$date_from["0"];	
			$select->where("sys_logs.datetime >= ?", $date_from_str);	
		}
		
		// apply filtering by end date - used at the toolbar
		// show actions before a given endDate
		if(isset($filters['to']) && strlen($filters['to'])>0 && strcmp (trim($filters['to']),"EndDate")!==0 ){
			$date_to = explode("/",$filters['to']);
			$date_to_str = $date_to["2"]."-".$date_to["1"]."-".$date_to["0"];
			$select->where("sys_logs.datetime <= ?", $date_to_str);
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
			$select->order ( "sys_logs.datetime desc" );
		}
		
		return $this->fetchAll ( $select );
	}
	
	
	
	// Provides data to the zend form Sector
	// Must return the same field names as the zend form in
	// Return Type: Json
	public function selectRowForGrid($rowId){
		$select = $this->select ();
		$select	->setIntegrityCheck(false)
				->from($this, array("id as row_id", "action", "description", "sys_users.username", "datetime"))
				->join('sys_users', 'sys_users.id = sys_logs.user_id', '')				
				->where('sys_logs.user_id = ?', $rowId);
	
		$json['modules'] = $this->fetchRow ( $select )->toArray();
	
		echo Zend_Json::encode($json);
	}
	
	
	
}
?>