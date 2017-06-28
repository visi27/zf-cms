<?php

class Table_Modules extends Table_Abstract {

	protected $_name 	= 'sys_modules';
	
	protected $_primary = 'id';

	public function getPrimaryKey(){
		return $this->_primary;
	}
	
	public function getModuleById($moduleId){
		return $this->find ( $moduleId )->current ();
	}
	
	public function getModuleByFormName($formName){
		
		$filter = array("form_name" => array("=" => $formName));
		
		$data = $this->selectModules($filter, null, 1);
		
		return $data->current();
		
	}
	
	public function getSubModules($moduleId){
		return $this->selectModules($where = array(
							"parent_id" => array("=" => $moduleId),
							"id" => array(">" => "1")
							));
	}
	
	public function findModulesByText($filters = array()){
		$select = $this->select ();
		if (count ( $filters ) > 0) {
			foreach ( $filters as $field => $filter ) {
				$select->where ( $field . ' like ?', "%".$filter."%" );
			}
		}
		return $this->fetchAll ( $select );
	}
	
	public function selectModules($filters = array(), $sortField = null, $limit = null) {
		$select = $this->select ();
		// add any filters which are set
		if (count ( $filters ) > 0) {
			foreach ( $filters as $field => $filter ) {
				if (count ( $filter ) > 0){
					foreach ($filter as $operator => $value)
					$select->where ( $field . $operator. ' ?', $value );
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
		//$test = $select->__toString();
	
		return $this->fetchAll ( $select );
	}
}

?>