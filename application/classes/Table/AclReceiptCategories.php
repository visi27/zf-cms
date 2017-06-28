<?php

class Table_AclReceiptCategories extends Table_Abstract implements Interface_iForm
{

    protected $_name = 'sys_acl_receipt_categories';

    protected $_primary = 'id';

    public function getDataById($id, $current = true)
    {
        if (empty($id))
            return false;
        
        if ($current == true) {
            return $this->find($id)->current();
        } else {
            return $this->find($id);
        }
    }

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
        
        $category = explode("-", $formObj->getElement('category')->getValue()); // formati: "xy - abcd"
        $user = explode("-", $formObj->getElement('user')->getValue()); // formati: "xy - abcd"
        
        $row->category = trim($category[0]);
        $row->user = trim($user[0]);
        $row->read = $formObj->getElement('read')->getValue();
        $row->write = $formObj->getElement('write')->getValue();
        try {
            // save the new row
            $id = $row->save();
            // now fetch the id of the row you just created and return it
            return $id;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateRow(Zend_Form $formObj)
    {
        // find the row that matches the id
        $row = $this->getDataById($formObj->getElement('row_id')
            ->getValue());
        if ($row) {
            // set the row data
            $category = explode("-", $formObj->getElement('category')->getValue()); // formati: "xy - abcd"
            $user = explode("-", $formObj->getElement('user')->getValue()); // formati: "xy - abcd"
            
            $row->category = trim($category[0]);
            $row->user = trim($user[0]);
            $row->read = $formObj->getElement('read')->getValue();
            $row->write = $formObj->getElement('write')->getValue();
            try {
                // save the new row
                $row->save();
                
                return true;
            } catch (Exception $e) {
                
                return false;
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
            $row->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // GRID SECTION METHODS
    // Information to be displayed in the grid
    // Return Type: rowSet
    // $id['id'] contains selected role id
    public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "sys_users.username",
            "config_receipt_category.name",
            new Zend_Db_Expr("CASE WHEN $this->_name.read = 1 THEN 'True' ELSE 'False' END AS 'read'"),
            new Zend_Db_Expr("CASE WHEN $this->_name.write = 1 THEN 'True' ELSE 'False' END AS 'write'")
        ))
            ->join("config_receipt_category", "$this->_name.category = config_receipt_category.id", "")
            ->join("sys_users", "$this->_name.user = sys_users.id", "");
        
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
            // sort field and direction from grid
            foreach ($sortField as $sort) {
                if (null != $sortDir) {
                    $sort = $sort . " " . $sortDir;
                }
                
                $select->order($sort);
            }
        } else {
            $select->order(array(
                'sys_users.username',
                'config_receipt_category.name'
            ));
        }
        
        // $test = $select->__toString();
        // print_r($test);
        return $this->fetchAll($select);
    }
    
    // Provides data to the zend form Acl
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
            "CONCAT(sys_users.id,' - ',sys_users.username) as user",
            "CONCAT(config_receipt_category.id,' - ',config_receipt_category.name) as category",
            "read",
            "write"
        ))
            ->join("config_receipt_category", "$this->_name.category = config_receipt_category.id", "")
            ->join("sys_users", "$this->_name.user = sys_users.id", "")
            ->where("$this->_name.id = ?", $rowId["itemFound"]);
        
        $item = $this->fetchRow($select);
        // echo $select->__toString ();
        
        // return the result in json format
        echo Utility_Functions::_toJson(! empty($item) ? $item->toArray() : "");
    }
}

?>