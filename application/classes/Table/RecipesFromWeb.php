<?php

class Table_RecipesFromWeb extends Table_Abstract
{

    protected $_name = 'recipes_from_web';

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
    public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "$this->_name.title",
            "CONCAT(web_users.firstname, ' ', web_users.lastname) as author",
            "servings",
            "total_time",
            "config_receipt_difficulty.name as difficulty",
            "config_receipt_category.name as category",
            "config_receipt_cuisine_type.name as cuisine",
            "config_receipt_meal.name as meal",
            "config_receipt_type.name as receipt_type",
            "config_receipt_seasonality.name as seasonality",
            "config_receipt_base_product.name as base_product",
            "config_receipt_festivity.name as festivity"
        ))
            ->joinLeft("config_receipt_category", "$this->_name.category = config_receipt_category.id", "")
            ->joinLeft("config_receipt_cuisine_type", "$this->_name.cuisine = config_receipt_cuisine_type.id", "")
            ->joinLeft("config_receipt_meal", "$this->_name.meal = config_receipt_meal.id", "")
            ->joinLeft("config_receipt_type", "$this->_name.receipt_type = config_receipt_type.id", "")
            ->joinLeft("config_receipt_seasonality", "$this->_name.seasonality = config_receipt_seasonality.id", "")
            ->joinLeft("config_receipt_base_product", "$this->_name.base_product = config_receipt_base_product.id", "")
            ->joinleft("config_receipt_festivity", "$this->_name.festivity = config_receipt_festivity.id", "")
            ->joinLeft("config_receipt_difficulty", "$this->_name.difficulty = config_receipt_difficulty.id", "")
            ->join("web_users", "$this->_name.author = web_users.id", "");
        
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
            // add sort direction if it is set
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

    public function previewRecipeData($id)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "$this->_name.title",
            "$this->_name.desc",
            "$this->_name.steps",
            "$this->_name.ingredients",
            "CONCAT(COALESCE(web_users.firstname,''), ' ', COALESCE(web_users.lastname,'')) as author",
            "web_users.email",
            "web_users.hybridauth_provider_name as provider",
            "web_users.hybridauth_provider_uid as uid",
            "servings",
            "total_time",
            "config_receipt_difficulty.name as difficulty",
            "config_receipt_category.name as category",
            "config_receipt_cuisine_type.name as cuisine",
            "config_receipt_meal.name as meal",
            "config_receipt_type.name as receipt_type",
            "config_receipt_seasonality.name as seasonality",
            "config_receipt_base_product.name as base_product",
            "config_receipt_festivity.name as festivity"
        ))
            ->joinLeft("config_receipt_category", "$this->_name.category = config_receipt_category.id", "")
            ->joinLeft("config_receipt_cuisine_type", "$this->_name.cuisine = config_receipt_cuisine_type.id", "")
            ->joinLeft("config_receipt_meal", "$this->_name.meal = config_receipt_meal.id", "")
            ->joinLeft("config_receipt_type", "$this->_name.receipt_type = config_receipt_type.id", "")
            ->joinLeft("config_receipt_seasonality", "$this->_name.seasonality = config_receipt_seasonality.id", "")
            ->joinLeft("config_receipt_base_product", "$this->_name.base_product = config_receipt_base_product.id", "")
            ->joinleft("config_receipt_festivity", "$this->_name.festivity = config_receipt_festivity.id", "")
            ->joinLeft("config_receipt_difficulty", "$this->_name.difficulty = config_receipt_difficulty.id", "")
            ->join("web_users", "$this->_name.author = web_users.id", "")
            ->where("$this->_name.id = ?", $id);
        
        $test = $select->__toString();
        return $this->fetchRow($select)->toArray();
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
            "CONCAT($this->_name.festivity,' - ', config_receipt_festivity.name) as festivity"
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
}
?>
