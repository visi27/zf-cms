<?php

class Table_ReceiptsSteps extends Table_Abstract implements Interface_iForm
{

    protected $_name = 'receipts_steps';

    protected $_primary = 'id';

    protected $_rowClass = 'Table_MyRowClass';

    public function selectData($filters = array(), $sortField = null, $limit = null)
    {
        $select = $this->select();
        
        // add any filters which are set
        if (count($filters) > 0) {
            foreach ($filters as $field => $filter) {
                if (count($filter) > 0) {
                    foreach ($filter as $operator => $value)
                        $select->where($field . $operator . '?', $value);
                }
            }
        }
        // add the sort field if it is set
        if (null != $sortField) {
            $select->order($sortField);
        }
        // add the limit field if it is set
        if (null != $limit) {
            $select->limit($limit);
        }
        return $this->fetchAll($select);
        // return $select->__toString();
    }

    public function createNew(Zend_Form $formObj)
    {
        
        // create a new row in the table
        $row = $this->createRow();
        
        if (isset($_SESSION['receipt']['selected'])) {
            $row->receipt_id = trim($_SESSION['receipt']['selected']);
        }
        
        $row->step_nr = $formObj->getElement('step_nr')->getValue();
        
        $row->step_instructions = $formObj->getElement('step_instructions')->getValue();
        
        try {
            // save
            $row->save();
            
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateRow(Zend_Form $formObj)
    {
        
        // find the row that matches the id
        $row = $this->getDataById($formObj->getElement('row_id')
            ->getValue());
        if ($row) {
            // set the row data
            $row->step_nr = $formObj->getElement('step_nr')->getValue();
            
            $row->step_instructions = $formObj->getElement('step_instructions')->getValue();
            
            try {
                // update the row
                $row->save();
                
                // if no exception, return true
                return true;
            } catch (Exception $e) {
                
                return $e->getMessage();
            }
        } else {
            throw new Zend_Exception("Update function failed; could not find row!");
        }
    }

    public function deleteRow($id)
    {
        try {
            // find the row that matches the id
            $row = $this->getDataById($id);
            
            // delete the row
            $row->delete();
            
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            
            return $e->getMessage();
        }
    }
    
    // GRID SECTION METHODS
    // Information to be displayed in the grid
    // Return Type: rowSet
    // $id['id'] contains selected role id
    public function selectRowsForGrid($id = array(), $sortField = null, $sortDir = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "$this->_name.step_nr",
            "$this->_name.step_instructions"
        ))
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
        } else {
            $select->order("$this->_name.step_nr");
        }
        
        $test = $select->__toString();
        return $this->fetchAll($select);
    }
    
    // Provides data to the zend form Awards
    // Must return the same field names as the zend form in
    // Return Type: Json
    public function selectRowForGrid(array $args)
    {
        
        // get the parameters
        $rowId = Utility_Functions::argsToArray($args);
        
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "$this->_name.step_nr",
            "$this->_name.step_instructions"
        ))
            ->where("$this->_name.id = ?", $rowId['itemFound']);
        
        $item = $this->fetchRow($select);
        // echo $select->__toString ();
        
        // return the result in json format
        echo Utility_Functions::_toJson(! empty($item) ? $item->toArray() : "");
    }
}
?>

