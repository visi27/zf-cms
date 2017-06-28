<?php

class Table_Users extends Table_Abstract implements Interface_iForm
{

    protected $_name = 'sys_users';

    protected $_primary = 'id';

    protected $_rowClass = 'Table_MyRowClass';

    protected $_smartSearch = array(
        'Display' => array(
            "fullname",
            "username"
        ),
        'Search' => array(
            "fullname",
            "username"
        ),
        'Method' => 'selectData'
    );

    public function getUserByCredential($username, $password)
    {
        $where = $this->select()
            ->where('username = ?', $username)
            ->where('password = ?', $password);
        
        $user = $this->fetchRow($where);
        
        if (! is_null($user->id)) {
            if ($user->isactive == 1) {
                $user->isAuthenticated = true;
                $user->authenticationDesc = "Authenticated";
                $user->loadDataById($user->id);
            } elseif ($user->isActive == 0) {
                $user->isAuthenticated = false;
                $user->authenticationDesc = "Llogaria eshte bere inaktive !";
            } else {
                $user->isAuthenticated = false;
                $user->authenticationDesc = "Nuk mund te logoheni.Kontaktoni administratorin!";
            }
        } else {
            $user->isAuthenticated = false;
            $user->authenticationDesc = "Kombinim username/password i gabuar !";
        }
    }

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
    
    // the fields in database where the smartSearch can query
    public function getSearchFields()
    {
        // the first field used as label in the smartSearch
        return array(
            "fullname",
            "username"
        );
    }
    
    // the fields to be shown for the smart search
    public function getFieldsToShow()
    {
        // the first field in the array is the one used as header in the smartSearch
        return array(
            "username",
            "fullname"
        );
    }

    public function selectData($filters = array(), $sortField = null, $limit = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "id",
            "username",
            "fullname",
            "ip",
            "sys_roles.role_name",
            
            "isactive"
        ))
            ->join('sys_roles', 'sys_users.role_id = sys_roles.id', '');
        
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
        
        // $args = Utility_Functions::cleanArgsValue($inputArray);
        
        // create a new row in the table
        $row = $this->createRow();
        $row->username = strtolower($formObj->getElement('username')->getValue());
        $row->password = md5($formObj->getElement('password')->getValue());
        $row->fullname = $formObj->getElement('fullname')->getValue();
        $row->ip = $formObj->getElement('ip')->getValue();
        $role_id = explode("-", $formObj->getElement('role_id')->getValue()); // formati: "xy - abcd"
        $row->role_id = trim($role_id[0]);
        $row->isactive = $formObj->getElement('isactive')->getValue();
        $row->description = $formObj->getElement('description')->getValue();
        try {
            // save the new row
            $row->save();
            // now fetch the id of the row you just created and return it
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
            $row->username = $formObj->getElement('username')->getValue();
            $password = $formObj->getElement('password')->getValue();
            if ($password != "") {
                $row->password = md5($password);
            }
            
            $row->fullname = $formObj->getElement('fullname')->getValue();
            $row->ip = $formObj->getElement('ip')->getValue();
            $role_id = explode("-", $formObj->getElement('role_id')->getValue()); // formati: "xy - abcd"
            $row->role_id = trim($role_id[0]);
            $row->isactive = $formObj->getElement('isactive')->getValue();
            $row->description = $formObj->getElement('description')->getValue();
            try {
                // save the new row
                $row->save();
                // now fetch the id of the row you just created and return it
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
            $row->delete();
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    // GRID SECTION METHODS
    // Information to be displayed in the grid
    // Return Type: rowSet
    // $id['id'] contains selected user id
    public function selectRowsForGrid($filters = array(), $sortField = null, $sortDir = null)
    {
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "id",
            "username",
            "fullname",
            "ip",
            "sys_roles.role_name",
            new Zend_Db_Expr("CASE sys_users.isactive 
											WHEN  1
											THEN 'True' 
											ELSE 'False' 
									END AS 'isactive'")
        ))
            ->join('sys_roles', 'sys_users.role_id = sys_roles.id', '')
            ->joinLeft('sys_acd_roles as r', 'sys_users.acd_role_id = r.acd_role_id', '');
        
        // apply filtering by roleId - example: used at the toolbar
        if (isset($filters['aclRole'])) {
            $select->where("sys_users.role_id = ?", $filters['aclRole']);
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
        }
        
        // $test = $select->__toString();
        return $this->fetchAll($select);
    }
    
    // Provides data to the zend form Sector
    // Must return the same field names as the zend form in
    // Return Type: Json
    public function selectRowForGrid(array $args)
    {
        // get the parameters
        $rowId = Utility_Functions::argsToArray($args);
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "id",
            "username",
            "CONCAT('') as password",
            "fullname",
            "ip",
            "CONCAT(sys_roles.id,' - ',sys_roles.role_name) as role_id",
            "isactive",
            "description"
        ))
            ->join('sys_roles', 'sys_users.role_id = sys_roles.id', '')
            ->where('sys_users.id = ?', $rowId['itemFound']);
        
        $item = $this->fetchRow($select);
        // echo $select->__toString ();
        
        // return the result in json format
        echo Utility_Functions::_toJson(! empty($item) ? $item->toArray() : "");
    }
}

?>