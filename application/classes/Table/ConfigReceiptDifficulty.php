<?php

class Table_ConfigReceiptDifficulty extends Table_Abstract{

	protected $_name 		= 'config_receipt_difficulty';

	protected $_primary 	= 'id';

	protected $_rowClass 	= 'Table_MyRowClass';



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
}		
?>

