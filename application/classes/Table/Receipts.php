<?php

class Table_Receipts extends Table_Abstract implements Interface_iForm
{

    protected $_name = 'receipts';

    protected $_primary = 'id';

    protected $_rowClass = 'Table_MyRowClass';

    protected $_smartSearch = array(
        'Display' => array(
            "title"
        ),
        'Search' => array(
            "title"
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

    public function selectAutocomplete($filters = array(), $sortField = null, $limit = null)
    {
        $select = $this->select();
        $select->from($this, array(
            "id",
            "title"
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
        $select->order("title");
        return $this->fetchAll($select);
        // return $select->__toString();
    }

    public function selectRowsForSearch($filters = array(), $sortField = null, $sortDir = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)->from($this, array(
            "$this->_name.id",
            "$this->_name.title"
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
            $select->order("$this->_name.title");
        }
        
        $test = $select->__toString();
        
        return $this->fetchAll($select);
    }

    public function createNew(Zend_Form $formObj)
    {
        
        // create a new row in the table
        $row = $this->createRow();
        
        $row->title = $formObj->getElement('title')->getValue();
        
        $row->rewrite_title = Utility_Functions::generateRewriteTitle($row->title);
        
        $row->description = $formObj->getElement('description')->getValue();
        
        $row->instructions = $formObj->getElement('instructions')->getValue();
        
        $row->servings = $formObj->getElement('servings')->getValue();
        $row->total_time = $formObj->getElement('total_time')->getValue();
        
        if ($formObj->getElement('difficulty')->getValue() != "") {
            $difficulty = explode("-", $formObj->getElement('difficulty')->getValue());
            $row->difficulty = trim($difficulty[0]);
        }
        
        $author = explode("-", $formObj->getElement('author')->getValue());
        $row->author = trim($author[0]);
        
        $category = explode("-", $formObj->getElement('category')->getValue());
        $row->category = trim($category[0]);
        
        $cuisine = explode("-", $formObj->getElement('cuisine')->getValue());
        $row->cuisine = trim($cuisine[0]);
        
        if ($formObj->getElement('meal')->getValue() != "") {
            $meal = explode("-", $formObj->getElement('meal')->getValue());
            $row->meal = trim($meal[0]);
        }
        if ($formObj->getElement('receipt_type')->getValue() != "") {
            $receipt_type = explode("-", $formObj->getElement('receipt_type')->getValue());
            $row->receipt_type = trim($receipt_type[0]);
        }
        
        if ($formObj->getElement('seasonality')->getValue() != "") {
            $seasonality = explode("-", $formObj->getElement('seasonality')->getValue());
            $row->seasonality = trim($seasonality[0]);
        }
        
        $base_product = explode("-", $formObj->getElement('base_product')->getValue());
        $row->base_product = trim($base_product[0]);
        
        if ($formObj->getElement('festivity')->getValue() != "") {
            $festivity = explode("-", $formObj->getElement('festivity')->getValue());
            $row->festivity = trim($festivity[0]);
        }
        
        if ($formObj->getElement('publish_date')->getValue() != "") {
            $date = new Zend_Date($formObj->getElement('publish_date')->getValue(), 'dd/MM/y');
            $publish_date = $date->toString('y-MM-dd');
        } else {
            $date = new Zend_Date();
            $publish_date = $date->toString('y-MM-dd');
        }
        
        $row->publish_date = $publish_date;
        
        if ($formObj->getElement('publish_time')->getValue() != "") {
            $publish_time = date('H:i:s', strtotime($formObj->getElement('publish_time')->getValue()));
        } else {
            $publish_time = date('H:i:s', strtotime("09:00"));
        }
        
        $row->publish_time = $publish_time;
        
        $row->video = $formObj->getElement('video')->getValue();
        
        $row->receipt_status = 1;
        "Receipt has just been entered in the system and is not redacted or published";
        
        try {
            // save
            $id = $row->save();
            
            // if no exception, return true
            return $id;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateRow(Zend_Form $formObj)
    {
        $userId = Authenticate::getUserId();
        $userObj = new Table_Users();
        $user = $userObj->getDataById($userId);
        
        // find the row that matches the id
        $row = $this->getDataById($formObj->getElement('row_id')
            ->getValue());
        
        if ($row) {
            
            // If user is operator and the receipt is redacted or in redaction process do not allow editing
            if (($user->role_id == Zend_Registry::get('config')->user->operator) and ($row->receipt_status != 1)) {
                return "Receta eshte ne redaktim ose e redaktuar. Ju nuk keni te drejta ta modifikoni ate!";
            }
            
            // If user is journalist and the article is redacted and published do not allow editing
            if (($user->role_id == Zend_Registry::get('config')->user->journalist) and ($row->receipt_status == 3)) {
                return "Receta eshte e redaktuar dhe e publikuar. Ju nuk keni te drejta ta modifikoni ate!";
            }
            
            $row->title = $formObj->getElement('title')->getValue();
            
            $row->rewrite_title = Utility_Functions::generateRewriteTitle($row->title);
            
            $row->description = $formObj->getElement('description')->getValue();
            
            $row->instructions = $formObj->getElement('instructions')->getValue();
            
            $row->servings = $formObj->getElement('servings')->getValue();
            $row->total_time = $formObj->getElement('total_time')->getValue();
            
            if ($formObj->getElement('difficulty')->getValue() != "") {
                $difficulty = explode("-", $formObj->getElement('difficulty')->getValue());
                $row->difficulty = trim($difficulty[0]);
            } else {
                $row->difficulty = "";
            }
            
            $author = explode("-", $formObj->getElement('author')->getValue());
            $row->author = trim($author[0]);
            
            $category = explode("-", $formObj->getElement('category')->getValue());
            $row->category = trim($category[0]);
            
            $cuisine = explode("-", $formObj->getElement('cuisine')->getValue());
            $row->cuisine = trim($cuisine[0]);
            
            if ($formObj->getElement('meal')->getValue() != "") {
                $meal = explode("-", $formObj->getElement('meal')->getValue());
                $row->meal = trim($meal[0]);
            }
            if ($formObj->getElement('receipt_type')->getValue() != "") {
                $receipt_type = explode("-", $formObj->getElement('receipt_type')->getValue());
                $row->receipt_type = trim($receipt_type[0]);
            }
            
            if ($formObj->getElement('seasonality')->getValue() != "") {
                $seasonality = explode("-", $formObj->getElement('seasonality')->getValue());
                $row->seasonality = trim($seasonality[0]);
            }
            
            $base_product = explode("-", $formObj->getElement('base_product')->getValue());
            $row->base_product = trim($base_product[0]);
            
            if ($formObj->getElement('festivity')->getValue() != "") {
                $festivity = explode("-", $formObj->getElement('festivity')->getValue());
                $row->festivity = trim($festivity[0]);
            }
            
            if ($formObj->getElement('publish_date')->getValue() != "") {
                $date = new Zend_Date($formObj->getElement('publish_date')->getValue(), 'dd/MM/y');
                $publish_date = $date->toString('y-MM-dd');
            } else {
                $date = new Zend_Date();
                $publish_date = $date->toString('y-MM-dd');
            }
            
            $row->publish_date = $publish_date;
            
            if ($formObj->getElement('publish_time')->getValue() != "") {
                $publish_time = date('H:i:s', strtotime($formObj->getElement('publish_time')->getValue()));
            } else {
                $publish_time = date('H:i:s', strtotime("09:00"));
            }
            
            $row->publish_time = $publish_time;
            
            $row->video = $formObj->getElement('video')->getValue();
            
            if ($user->role_id == Zend_Registry::get('config')->user->operator) {
                $row->receipt_status = 1;
                "Receipts has just been entered in the system and is not redacted or published";
            }
            
            if (($user->role_id == Zend_Registry::get('config')->user->journalist) or ($user->role_id == Zend_Registry::get('config')->user->editor) or ($user->role_id == 1)) {
                $row->receipt_status = 2;
                "Receipt is being redacted";
            }
            
            try {
                // update the row
                $id = $row->save();
                
                // if no exception, return true
                return $id;
            } catch (Exception $e) {
                
                return $e->getMessage();
            }
        } else {
            throw new Zend_Exception("Update function failed; could not find row!");
        }
    }

    public function deleteRow($id)
    {
        $userId = Authenticate::getUserId();
        $userObj = new Table_Users();
        $user = $userObj->getDataById($userId);
        
        try {
            // find the row that matches the id
            $row = $this->getDataById($id);
            
            // If user is operator and the article is redacted or in redaction process do not allow delete
            if ((($user->role_id == Zend_Registry::get('config')->user->operator) or ($user->role_id == Zend_Registry::get('config')->user->journalist)) and ($row->receipt_status == 2 or $row->receipt_status == 3)) {
                return "Nuk keni te drejta te fshini artikullin!";
            }
            
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
    public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "$this->_name.receipt_status",
            "$this->_name.title",
            "CONCAT(authors.firstname, ' ', authors.lastname) as author",
            "servings",
            "total_time",
            "config_receipt_difficulty.name as difficulty",
            "config_receipt_category.name as category",
            "config_receipt_cuisine_type.name as cuisine",
            "config_receipt_meal.name as meal",
            "config_receipt_type.name as receipt_type",
            "config_receipt_seasonality.name as seasonality",
            "config_receipt_base_product.name as base_product",
            "config_receipt_festivity.name as festivity",
            "publish_date",
            "publish_time"
        ))
            ->joinLeft("config_receipt_category", "$this->_name.category = config_receipt_category.id", "")
            ->joinLeft("config_receipt_cuisine_type", "$this->_name.cuisine = config_receipt_cuisine_type.id", "")
            ->joinLeft("config_receipt_meal", "$this->_name.meal = config_receipt_meal.id", "")
            ->joinLeft("config_receipt_type", "$this->_name.receipt_type = config_receipt_type.id", "")
            ->joinLeft("config_receipt_seasonality", "$this->_name.seasonality = config_receipt_seasonality.id", "")
            ->joinLeft("config_receipt_base_product", "$this->_name.base_product = config_receipt_base_product.id", "")
            ->joinleft("config_receipt_festivity", "$this->_name.festivity = config_receipt_festivity.id", "")
            ->joinLeft("config_receipt_difficulty", "$this->_name.difficulty = config_receipt_difficulty.id", "")
            ->join("authors", "$this->_name.author = authors.id", "");
        
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
            $select->order("$this->_name.title");
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
            "$this->_name.title",
            "$this->_name.description",
            "$this->_name.instructions",
            "CONCAT($this->_name.author,' - ',authors.firstname,' ', authors.lastname) as author",
            "servings",
            "total_time",
            "CONCAT($this->_name.difficulty,' - ', config_receipt_difficulty.name) as difficulty",
            "CONCAT($this->_name.category,' - ', config_receipt_category.name) as category",
            "CONCAT($this->_name.cuisine,' - ', config_receipt_cuisine_type.name) as cuisine",
            "CONCAT($this->_name.meal,' - ', config_receipt_meal.name) as meal",
            "CONCAT($this->_name.receipt_type,' - ', config_receipt_type.name) as receipt_type",
            "CONCAT($this->_name.seasonality,' - ', config_receipt_seasonality.name) as seasonality",
            "CONCAT($this->_name.base_product,' - ', config_receipt_base_product.name) as base_product",
            "CONCAT($this->_name.festivity,' - ', config_receipt_festivity.name) as festivity",
            "DATE_FORMAT($this->_name.publish_date,'%d/%m/%Y') as publish_date",
            "DATE_FORMAT($this->_name.publish_time,'%H:%i') as publish_time",
            "$this->_name.video"
        ))
            ->joinleft("config_receipt_category", "$this->_name.category = config_receipt_category.id", "")
            ->joinleft("config_receipt_cuisine_type", "$this->_name.cuisine = config_receipt_cuisine_type.id", "")
            ->joinleft("config_receipt_meal", "$this->_name.meal = config_receipt_meal.id", "")
            ->joinleft("config_receipt_type", "$this->_name.receipt_type = config_receipt_type.id", "")
            ->joinleft("config_receipt_seasonality", "$this->_name.seasonality = config_receipt_seasonality.id", "")
            ->joinleft("config_receipt_base_product", "$this->_name.base_product = config_receipt_base_product.id", "")
            ->joinleft("config_receipt_festivity", "$this->_name.festivity = config_receipt_festivity.id", "")
            ->joinLeft("config_receipt_difficulty", "$this->_name.difficulty = config_receipt_difficulty.id", "")
            ->
        join("authors", "$this->_name.author = authors.id", "")
            ->where("$this->_name.id = ?", $rowId['itemFound']);
        
        $item = $this->fetchRow($select);
        // echo $select->__toString ();
        
        // return the result in json format
        echo Utility_Functions::_toJson(! empty($item) ? $item->toArray() : "");
    }

    public function publishReceipt($receiptId)
    {
        $row = $this->getDataById($receiptId);
        $row->receipt_status = 3;
        
        try {
            // update the row
            $row->save();
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            return false;
        }
    }

    public function unPublishReceipt($receiptId)
    {
        $row = $this->getDataById($receiptId);
        $row->receipt_status = 2;
        
        try {
            // update the row
            $row->save();
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            return false;
        }
    }

    public function assignProfileImage($receiptId, $fileName)
    {
        $row = $this->getDataById($receiptId);
        $row->profile_image = $fileName;
        
        try {
            // update the row
            $row->save();
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            return false;
        }
    }

    public function assignVerticalProfileImage($receiptId, $fileName)
    {
        $row = $this->getDataById($receiptId);
        $row->vertical_profile_image = $fileName;
        
        try {
            // update the row
            $row->save();
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            return false;
        }
    }

    public function makeFeatured($receipt_id)
    {
        $row = $this->getDataById($receipt_id);
        $row->featured = 1;
        
        try {
            // update the row
            $row->save();
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            return false;
        }
    }

    public function unMakeFeatured($receipt_id)
    {
        $row = $this->getDataById($receipt_id);
        $row->featured = 0;
        
        try {
            // update the row
            $row->save();
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            return false;
        }
    }

    public function removeFeatured()
    {
        $data = array(
            'featured' => '0'
        );
        $where = $this->getAdapter()->quoteInto('featured = ?', '1');
        
        try {
            // update the row
            $update = $this->update($data, $where);
            // if no exception, return true
            return $update;
        } catch (Exception $e) {
            $this->getAdapter()->rollBack();
            return false;
        }
    }
}
?>
