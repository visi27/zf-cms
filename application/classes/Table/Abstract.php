<?php
abstract class Table_Abstract extends Zend_Db_Table_Abstract {

	protected $_smartSearch = array(
			'Display' => array(),
			'Search'  => array(),
			'Method'  => 'selectData',
			'Params' => array());
	
	protected function _setupDatabaseAdapter(){
		
		// registry instance
		$registry = Zend_Registry::get('config');
		
		// get the database adapter
		$dbAdapter = $registry->database->adapter;
		
		// get the deafult database charset
		$dbCharset = $registry->database->charset;
		
		// set the schema name
		$this->_schema = $registry->database->dbname;		
	
		// create the database parameters
		$params = array(
	    		'host' => $registry->database->host,
	    		'username' => $registry->database->username,
	    		'password' => $registry->database->password,
	    		'dbname' => $registry->database->dbname,
	   			'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$dbCharset.';')
		);
	
		// set the database connection and parameters
		$this->_db = Zend_Db::factory ($dbAdapter, $params );
		
		// set default adapter
		Zend_Db_Table_Abstract::setDefaultAdapter ( $this->_db );
		
		// call to parent method
		parent::_setupDatabaseAdapter();
	}
	
	/**
	 * SmartSearch
	 */
	public function getSmartSearch($index){
		if(!empty($index))
			return $this->_smartSearch[$index];
		else
			return $this->_smartSearch;
	}
	
	public function getDataById($id, $current = true){
	
		if($current == true){
			return $this->find ( $id )->current ();
				
		}else{
			return $this->find ( $id );
		}
	}
	
	public function selectData($filters = array(), $sortField = null, $sortDir = null, 
			$limit = null, $columns = null, Zend_Db_Table_Select $mySelect = null) {
		
		// set custom select
		$select = is_null($mySelect)?$this->select ():$mySelect;
			
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
		
		$select->where('id_nr = ?', $_SESSION['person']['selected']);
	
		// default sorting direction
		if (null == $sortDir) {
			$sortDir = 'ASC';
		}
		
		//add the sort field if it is set
		if (!empty($sortField)) {
			 
			if(is_array($sortField)  && count($sortField) > 0){
				
				foreach($sortField as $key => $value){
					if(!empty($value))
					$select->order($value." ".$sortDir); 
				}
			}else{
				$select->order($sortField." ".$sortDir);
			}		
		}
	
		// add the limit field if it is set
		if(null != $limit){
			$select->limit($limit);
		}
	
		$query = $select->__toString();
// 		echo $query;
		
		return $this->fetchAll ( $select );
	}
	
	//check user write access rights over this module
	protected  function _hasWriteModuleAccess($moduleId){
		// load the user model
		$user = new Table_Users();
	
		// load the access control list instance
		$acl = new Table_Acl();
	
		// load the user data
		$userData = $user->getDataById(Authenticate::getUserId());
	
		// check write access for this role over this module
		if($acl->hasAccess($userData->role_id, $moduleId, "write")){
			return true;
		}
	
		//default
		return false;
	}
	
}
?>