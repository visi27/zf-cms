<?php

class Table_WebFeatured extends Table_Abstract implements Interface_iForm
{

    protected $_name = 'web_featured';

    protected $_primary = 'id';

    protected $_rowClass = 'Table_MyRowClass';
    
    // protected $_smartSearch = array(
    // 'Display' => array("name"),
    // 'Search' => array("name"),
    // 'Method' => 'selectRowsForSearch'
    // );
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
            $select->order("$this->_name.order_nr");
        }
        
        $test = $select->__toString();
        
        return $this->fetchAll($select);
    }

    public function createNew(Zend_Form $formObj)
    {
        
        // create a new row in the table
        $row = $this->createRow();
        
        $type = explode("-", $formObj->getElement('type')->getValue());
        $row->type = trim($type[0]);
        
        $element = explode("-", $formObj->getElement('element_id')->getValue());
        $row->element_id = trim($element[0]);
        
        if ($formObj->getElement('start_date')->getValue() != "") {
            $date = new Zend_Date($formObj->getElement('start_date')->getValue(), 'dd/MM/y');
            $start_date = $date->toString('y-MM-dd');
        } else {
            $date = new Zend_Date();
            $start_date = $date->toString('y-MM-dd');
        }
        $row->start_date = $start_date;
        
        if ($formObj->getElement('end_date')->getValue() != "") {
            $date = new Zend_Date($formObj->getElement('end_date')->getValue(), 'dd/MM/y');
            $end_date = $date->toString('y-MM-dd');
        } else {
            $date = new Zend_Date();
            $end_date = $date->toString('y-MM-dd');
        }
        $row->end_date = $end_date;
        
        $row->order_nr = $formObj->getElement('order_nr')->getValue();
        
        $this->moveOthers($row->order_nr);
        
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
            $type = explode("-", $formObj->getElement('type')->getValue());
            $row->type = trim($type[0]);
            
            $element = explode("-", $formObj->getElement('element_id')->getValue());
            $row->element_id = trim($element[0]);
            
            if ($formObj->getElement('start_date')->getValue() != "") {
                $date = new Zend_Date($formObj->getElement('start_date')->getValue(), 'dd/MM/y');
                $start_date = $date->toString('y-MM-dd');
            } else {
                $date = new Zend_Date();
                $start_date = $date->toString('y-MM-dd');
            }
            $row->start_date = $start_date;
            
            if ($formObj->getElement('end_date')->getValue() != "") {
                $date = new Zend_Date($formObj->getElement('end_date')->getValue(), 'dd/MM/y');
                $end_date = $date->toString('y-MM-dd');
            } else {
                $date = new Zend_Date();
                $end_date = $date->toString('y-MM-dd');
            }
            $row->end_date = $end_date;
            
            $row->order_nr = $formObj->getElement('order_nr')->getValue();
            
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
        $select_articles = $this->select();
        $select_articles->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "order_nr",
            new Zend_Db_Expr('"Artikull" as type'),
            "blog_articles.title",
            "start_date",
            "end_date"
        ))
            ->join("blog_articles", "$this->_name.element_id = blog_articles.id", "")
            ->where("$this->_name.type = ?", "1");
        
        $select_receipts = $this->select();
        $select_receipts->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "order_nr",
            new Zend_Db_Expr('"Recete" as type'),
            "receipts.title",
            "start_date",
            "end_date"
        ))
            ->join("receipts", "$this->_name.element_id = receipts.id", "")
            ->where("$this->_name.type = ?", "2");
        
        $select = $this->select();
        
        $select->union(array(
            '(' . $select_articles . ')',
            '(' . $select_receipts . ')'
        ));
        $select->order("order_nr");
        
        Zend_Registry::get("applog")->log($select->__toString());
        return $this->fetchAll($select);
    }
    
    // Provides data to the zend form Awards
    // Must return the same field names as the zend form in
    // Return Type: Json
    public function selectRowForGrid(array $args)
    {
        
        // get the parameters
        $rowId = Utility_Functions::argsToArray($args);
        
        $element = $this->getDataById($rowId['itemFound']);
        
        if ($element->type == 1) {
            $select = $this->select();
            $select->setIntegrityCheck(false)
                ->from($this, array(
                "$this->_name.id",
                new Zend_Db_Expr('"1 - Artikull" as type'),
                "CONCAT(blog_articles.id, ' - ', blog_articles.title) as element_id",
                "DATE_FORMAT($this->_name.start_date,'%d/%m/%Y') as start_date",
                "DATE_FORMAT($this->_name.end_date,'%d/%m/%Y') as end_date",
                "order_nr"
            ))
                ->join("blog_articles", "$this->_name.element_id = blog_articles.id", "")
                ->where("$this->_name.id = ?", $rowId['itemFound']);
        } else {
            $select = $this->select();
            $select->setIntegrityCheck(false)
                ->from($this, array(
                "$this->_name.id",
                new Zend_Db_Expr('"2 - Recete" as type'),
                "CONCAT(receipts.id, ' - ', receipts.title) as element_id",
                "DATE_FORMAT($this->_name.start_date,'%d/%m/%Y') as start_date",
                "DATE_FORMAT($this->_name.end_date,'%d/%m/%Y') as end_date",
                "order_nr"
            ))
                ->join("receipts", "$this->_name.element_id = receipts.id", "")
                ->where("$this->_name.id = ?", $rowId['itemFound']);
        }
        
        $item = $this->fetchRow($select);
        // echo $select->__toString ();
        
        // return the result in json format
        echo Utility_Functions::_toJson(! empty($item) ? $item->toArray() : "");
    }

    public function moveOthers($order_nr)
    {
        $data = array(
            'order_nr' => new Zend_Db_Expr('order_nr+1')
        );
        $where = $this->getAdapter()->quoteInto('order_nr >= ?', $order_nr);
        
        $update = $this->update($data, $where);
    }
}
?>

