<?php

class Table_ConfigReceiptFestivity extends Table_Abstract implements Interface_iForm
{

    protected $_name = 'config_receipt_festivity';

    protected $_primary = 'id';

    protected $_rowClass = 'Table_MyRowClass';

    protected $_smartSearch = array(
        'Display' => array(
            "name"
        ),
        'Search' => array(
            "name"
        ),
        'Method' => 'selectRowsForSearch'
    );

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

    public function selectRowsForSearch($filters = array(), $sortField = null, $sortDir = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)->from($this, array(
            "$this->_name.id",
            "$this->_name.name"
        ));
        
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
            // marilda: add sort direction if it is set
            if (null != $sortDir) {
                $sortField = $sortField . " " . $sortDir;
            }
            
            $select->order($sortField);
        } else {
            $select->order("$this->_name.name");
        }
        
        $test = $select->__toString();
        
        return $this->fetchAll($select);
    }

    public function createNew(Zend_Form $formObj)
    {
        
        // create a new row in the table
        $row = $this->createRow();
        
        $row->name = $formObj->getElement('name')->getValue();
        
        $row->description = $formObj->getElement('description')->getValue();
        
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
            $row->name = $formObj->getElement('name')->getValue();
            
            $row->description = $formObj->getElement('description')->getValue();
            
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
        
        $select->setIntegrityCheck(false)->from($this, array(
            "$this->_name.id",
            "$this->_name.name",
            "$this->_name.description"
        ));
        
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
            $select->order("$this->_name.name");
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
            "$this->_name.name",
            "$this->_name.description"
        ))
            ->where('id = ?', $rowId['itemFound']);
        
        $item = $this->fetchRow($select);
        // echo $select->__toString ();
        
        // return the result in json format
        echo Utility_Functions::_toJson(! empty($item) ? $item->toArray() : "");
    }
}
?>

