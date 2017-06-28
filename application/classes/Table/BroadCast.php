<?php

class Table_BroadCast extends Table_Abstract implements Interface_iForm{
	
	protected $_name 	= 'sys_broadcast';
	
	protected $_primary = 'id';
	
		
	//$broadcast_msg_time = Zend_Registry::get('config')->broadcast_msg_time;
	
	
	/**
	 * Kthen rekord sipas id
	 *
	 */
	public function getDataById($id, $current = true){
		
		if($current == true){
			return $this->find ( $id )->current ();
			
		}else{
			return $this->find ( $id );
		}
		
	}
	
	
	
	/**
	 * Kthen dataset sipas disa kritereve kerkimi
	 *
	 */
public function selectData($filters = array(), $sortField = null, $limit = null) {
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("id",  "title_al", "title_en","substring(body_al,1,50)","substring(body_en,1,50)",
						new Zend_Db_Expr(
									"CASE sys_broadcast.display 
											WHEN  1
											THEN 'True' 
											ELSE 'False' 
									 END AS 'Displayed'"), "date_created AS Creation date","sys_users.username AS Created by")
							  )
				->joinLeft('sys_users', 'sys_broadcast.user_id = sys_users.id', '');
						
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
	 * Shtoj rekord te ri
	 *
	 */
	public function createNew(Zend_Form $formObj){
		// create a new row in the table
		$row = $this->createRow ();
		
		// load the user model
		$user = new Table_Users();
		// load the user data
		$userData = $user->getDataById(Authenticate::getUserId());
		//get the user id
		$userID = $userData->id;
		
		$row->title_al = $formObj->getElement('title_al')->getValue();
		$row->title_en = $formObj->getElement('title_en')->getValue();
		$row->body_al = $formObj->getElement('body_al')->getValue();
		$row->body_en = $formObj->getElement('body_en')->getValue();
		$row->display = $formObj->getElement('display')->getValue();
		$row->user_id = $userID;
		
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
	
	
	
	
	/**
	 * Update rekord
	 *
	 */
	public function updateRow(Zend_Form $formObj) {
		// find the row that matches the id
		// row_id -  hidden element te forma
		$current_time = date("Y-m-d H:i:s");
		
		$row = $this->getDataById($formObj->getElement('row_id')->getValue());
		if ($row) {
			// set the row data			
		$row->title_al = $formObj->getElement('title_al')->getValue();
		$row->title_en = $formObj->getElement('title_en')->getValue();
		$row->body_al = $formObj->getElement('body_al')->getValue();
		$row->body_en = $formObj->getElement('body_en')->getValue();
		$row->display = $formObj->getElement('display')->getValue();
			$row->date_created = $current_time;
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
	
	
	
	
	
	/**
	 * Fshij rekord sipas id
	 *
	 */
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
	public function selectRowsForGrid($id = array(),$sortField = null, $sortDir = null){
		
		$select = $this->select ();
		
		$select	->setIntegrityCheck(false)
				->from($this, array("id",  "title_al", "title_en","body_al","body_en",
						new Zend_Db_Expr(
									"CASE sys_broadcast.display 
											WHEN  1
											THEN 'True' 
											ELSE 'False' 
									 END AS 'Displayed'"), "date_created AS Creation date","sys_users.username AS Created by")
							  )
				->joinLeft('sys_users', 'sys_broadcast.user_id = sys_users.id', '');
		
		// add the sort field if it is set
		if (null != $sortField) {
			//marilda: add sort direction if it is set
			if (null != $sortDir) {
				$sortField = $sortField." ".$sortDir;
			}
		
			$select->order ( $sortField );
		}
		else {
			$select->order('sys_broadcast.date_created desc');
		}
		
		return $this->fetchAll ( $select );
	}
	
	
	

	
	// Provides data to the zend form Sector
	// Must return the same field names as the zend form in 
	// Return Type: Json
	public function selectRowForGrid(array $args){
		// get the parameters
		$rowId = Utility_Functions::argsToArray($args);
		$select = $this->select ();		
		$select	->setIntegrityCheck(false)
				->from($this, array("id", "title_al", "title_en", "body_al", 
									"body_en","display", "date_created", "sys_users.username"))
				->joinLeft('sys_users', 'sys_broadcast.user_id = sys_users.id', '')
				->where('sys_broadcast.id = ?', $rowId['itemFound']);
		
		$item = $this->fetchRow ( $select ); 
		//echo $select->__toString ();

		//return the result in json format
		echo Utility_Functions::_toJson(!empty($item)?$item->toArray():""); 
	}
	
	
	
	/**
	 * Get last 2 active messages added at max $days days before
	 * 
	 */
	public function lastMessages ($days){
	
		$select = $this->select ();
	
		$select	->setIntegrityCheck(false)
		->from($this, array("id",  "title_al", "title_en","body_al","body_en","date_created","DATEDIFF(NOW(),sys_broadcast.date_created) AS date_dif" ))
		->where('sys_broadcast.display = ?', 1)
		->where('DATEDIFF(NOW(),sys_broadcast.date_created) <= ?', $days )
		->order('sys_broadcast.date_created DESC')
		->limit(2);
		return $this->fetchAll ( $select );
	}
}

?>