<?php

class Table_ActionLog extends Table_Abstract implements Interface_iData{

	protected $_name 	= 'sys_action_log';
	
	protected $_primary = 'id';
	
	public function getDataById($id, $current = true){	
		if($current == true){
			return $this->find ( $id )->current ();
			
		}else{
			return $this->find ( $id );
		}
	}
	
	public function insertNew($tableName, $rowId, $actionType, $userId, $stack = null){
		// create a new row in the logs table
		$row = $this->createRow ();
		// set the row data
		$row->table_name = $tableName;
		$row->table_row_id = $rowId;
		$row->action_type = $actionType;
		$row->data_stack = $stack;
		$row->user_id = $userId;

		// save the new row
		$row->save ();
		// now fetch the id of the row you just created and return it
		return $this->_db->lastInsertId ();
	}
	
public function selectData($filters = array(), $sortField = null, $limit = null) {
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("id", "table_name as Db Tabela", 
						"table_row_id as Row Id", "action_type as Tipi Veprimit", 
						"sys_users.username as Username", "sys_action_log.timestamp as TimeStamp",
						"data_stack as Stack"))
				->join('sys_users', 'sys_users.id = sys_action_log.user_id', '');
						
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
	
	
	/**
	 *  Si metoda siper, shtoj param $columns 
	 * 	Marilda
	 */
	public function selectActions($filters = array(), $sortField = null, $limit = null, $columns = null,$distinct=false) {
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
		
		// add the distinct field 
		if ($distinct) {
			$select->distinct ( $distinct );
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
	
    // GRID SECTION METHODS
	// Information to be displayed in the grid
	// Return Type: rowSet	
	public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null){
		
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("id", "table_name as Db Tabela", 
						"table_row_id as Row Id", "action_type as Tipi Veprimit", 
						"sys_users.username as Username", "sys_action_log.timestamp as TimeStamp",
						"data_stack as Stack"))
				->join('sys_users', 'sys_users.id = sys_action_log.user_id', '');
						
		// apply filtering by user - example: used at the toolbar 
		if(isset($filters['user'])){
			$select->where("sys_action_log.user_id = ?", $filters['user']);
		}
		
		// apply filtering by table name - used at the toolbar
		if(isset($filters['tbl']) && strlen($filters['tbl'])>0 && strcmp (trim($filters['tbl']),"FilterByTableName")!==0){
			$select->where("sys_action_log.table_name = ?", $filters['tbl']);
		}	

		// apply filtering by start date - used at the toolbar
		// show actions after a given fromDate
		if(isset($filters['from']) && strlen($filters['from'])>0 && strcmp (trim($filters['from']),"StartDate")!==0 ){
			$date_from = explode("/",$filters['from']);
			$date_from_str = $date_from["2"]."-".$date_from["1"]."-".$date_from["0"];	
			$select->where("sys_action_log.timestamp >= ?", $date_from_str);	
		}
		
		// apply filtering by end date - used at the toolbar
		// show actions before a given endDate
		if(isset($filters['to']) && strlen($filters['to'])>0 && strcmp (trim($filters['to']),"EndDate")!==0 ){
			$date_to = explode("/",$filters['to']);
			$date_to_str = $date_to["2"]."-".$date_to["1"]."-".$date_to["0"];
			$select->where("sys_action_log.timestamp <= ?", $date_to_str);
		}
		
		// add the sort field if it is set
		if (null != $sortField) {
			//marilda: add sort direction if it is set
			if (null != $sortDir) {
				$sortField = $sortField." ".$sortDir;
			}
			$select->order ( $sortField );
		}
		else {//by default
			$select->order ( "sys_action_log.timestamp desc" );
		}
		
		return $this->fetchAll ( $select );
	}
	
	
	
	
	
}
?>