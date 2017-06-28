<?php

class Table_ConfigCities extends Table_Abstract{
	protected $_name 	= 'config_cities';
	
	protected $_primary = 'id';

	
public function getDataById($id, $current = true){
		
		if($current == true){
			return $this->find ( $id )->current ();
			
		}else{
			return $this->find ( $id );
		}
	}
	
	public function selectData($filters = array(), $sortField = array(), $limit = null) {
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
		if (count ( $sortField ) > 0) {
			$select->order ( $sortField );
		}
		// add the limit field if it is set
		if(null != $limit){
			$select->limit($limit);
		}
		//$test = $select->__toString();
		return $this->fetchAll ( $select );
		//return $select->__toString();
	}
	
}
