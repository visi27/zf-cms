<?php

class Table_ReceiptsIngredients extends Table_Abstract implements Interface_iForm{

	protected $_name 		= 'receipts_ingredients';

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



	public function createNew(Zend_Form $formObj){

		// create a new row in the table
		$row = $this->createRow ();
			
		
		if(isset($_SESSION['receipt']['selected'])){
		    $row->receipt_id = trim($_SESSION['receipt']['selected']);
		}
			
		$ingredient = explode ( "-", $formObj->getElement('ingredient_id')->getValue());
		$row->ingredient_id = trim( $ingredient[0] );
		
		$row->unit = $formObj->getElement('unit')->getValue();
		$row->qty = $formObj->getElement('qty')->getValue();
		
		$row->instructions = $formObj->getElement('instructions')->getValue();
		
		$row->ingredient_for = $formObj->getElement('ingredient_for')->getValue();
		
		try{
			// save
			$row->save();
				
			// if no exception, return true
			return true;

		}catch(Exception $e){
			return $e->getMessage();
		}
	}

	public function updateRow(Zend_Form $formObj){


		// find the row that matches the id
		$row = $this->getDataById($formObj->getElement('row_id')->getValue());
		if ($row) {
			// set the row data
    		$ingredient = explode ( "-", $formObj->getElement('ingredient_id')->getValue());
    		$row->ingredient_id = trim( $ingredient[0] );
    		
    		$row->unit = $formObj->getElement('unit')->getValue();
    		$row->qty = $formObj->getElement('qty')->getValue();
    		
    		$row->instructions = $formObj->getElement('instructions')->getValue();
    		
    		$row->ingredient_for = $formObj->getElement('ingredient_for')->getValue();
			
			try{
				// update the row
				$row->save();

					

				// if no exception, return true
				return true;

			}catch(Exception $e){

				return $e->getMessage();
			}
				
		}
		else {
			throw new Zend_Exception ( "Update function failed; could not find row!" );
		}
	}

	public function deleteRow($id) {

		try{
			// find the row that matches the id
			$row = $this->getDataById( $id );

			// delete the row
			$row->delete ();
				


			// if no exception, return true
			return true;

		}catch(Exception $e){
				
			return $e->getMessage();
		}
	}

	// GRID SECTION METHODS
	// Information to be displayed in the grid
	// Return Type: rowSet
	// $id['id'] contains selected role id
	public function selectRowsForGrid($id = array(),$sortField = null, $sortDir = null){

		$select = $this->select ();

		$select	->setIntegrityCheck(false)
		->from($this, array("$this->_name.id","config_ingredients.name","$this->_name.qty","$this->_name.unit", "$this->_name.instructions"))
        ->join("config_ingredients", "$this->_name.ingredient_id = config_ingredients.id","")
		->where('receipt_id = ?', $_SESSION['receipt']['selected']);

	     // add the sort field if it is set
        if (null != $sortField) {
            // sort field and direction from grid
            foreach ($sortField as $sort) {
                if (null != $sortDir) {
                    $sort = $sort . " " . $sortDir;
                }
                
                $select->order($sort);
            }
        }


		$test = $select->__toString();
		return $this->fetchAll ( $select );
	}

	// Provides data to the zend form Awards
	// Must return the same field names as the zend form in
	// Return Type: Json
	public function selectRowForGrid(array $args){

	// get the parameters
		$rowId = Utility_Functions::argsToArray($args);

		$select = $this->select ();
		$select	->setIntegrityCheck(false)
			->from($this, array("$this->_name.id","CONCAT($this->_name.ingredient_id,' - ', config_ingredients.name) as ingredient_id",
			    "$this->_name.qty","$this->_name.unit", "$this->_name.instructions", "$this->_name.ingredient_for"))
			->join("config_ingredients", "$this->_name.ingredient_id = config_ingredients.id","")
			->where("$this->_name.id = ?", $rowId['itemFound']);

		$item = $this->fetchRow ( $select );
		//echo $select->__toString ();

		//return the result in json format
		echo Utility_Functions::_toJson(!empty($item)?$item->toArray():"");
	}
}		
?>

