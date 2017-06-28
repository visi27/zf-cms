<?php

class Table_BlogArticles extends Table_Abstract implements Interface_iForm
{

    protected $_name = 'blog_articles';

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
        $userId = Authenticate::getUserId();
        
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "blog_articles.id",
            "blog_articles.blog_category_id",
            "blog_articles.title",
            "blog_articles.rewrite_title",
            "blog_articles.subtitle",
            "blog_articles.author",
            "blog_articles.intro_text",
            "blog_articles.full_text",
            "blog_articles.published",
            "blog_articles.publish_date",
            "blog_articles.archive_date",
            "blog_articles.archived",
            "blog_articles.profile_image",
            "blog_articles.vertical_profile_image",
            "blog_articles.web_hits",
            "blog_articles.magazine_nr",
            "blog_articles.article_status"
        ))
            ->join("sys_acl_article_categories", "blog_articles.blog_category_id = sys_acl_article_categories.category", "")
            ->where("sys_acl_article_categories.user = ?", $userId)
            ->where("sys_acl_article_categories.read = ?", "1");
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
        $userId = Authenticate::getUserId();
        
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "$this->_name.title"
        ))
            ->join("sys_acl_article_categories", "blog_articles.blog_category_id = sys_acl_article_categories.category", "")
            ->where("sys_acl_article_categories.user = ?", $userId)
            ->where("sys_acl_article_categories.read = ?", "1");
        
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
        Zend_Registry::get("applog")->log($test);
        return $this->fetchAll($select);
    }

    public function createNew(Zend_Form $formObj)
    {
        
        // create a new row in the table
        $row = $this->createRow();
        
        $category = explode("-", $formObj->getElement('blog_category_id')->getValue());
        $row->blog_category_id = trim($category[0]);
        
        $row->title = $formObj->getElement('title')->getValue();
        
        $row->rewrite_title = Utility_Functions::generateRewriteTitle($row->title);
        
        substr(str_replace(' ', '_', $formObj->getElement('title')->getValue()), 0, 100);
        
        $row->subtitle = $formObj->getElement('subtitle')->getValue();
        
        $author = explode("-", $formObj->getElement('author')->getValue());
        $row->author = trim($author[0]);
        
        $prepared_by = explode("-", $formObj->getElement('prepared_by')->getValue());
        $row->prepared_by = trim($prepared_by[0]);
        
        $row->intro_text = $formObj->getElement('intro_text')->getValue();
        $row->full_text = $formObj->getElement('full_text')->getValue();
        
        // Initially article is always unpublished
        $row->published = 0;
        
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
        
        if (is_int($formObj->getElement('archived')->getValue())) {
            $row->archived = $formObj->getElement('archived')->getValue();
        }
        
        if ($formObj->getElement('archive_date')->getValue() != "") {
            $date = new Zend_Date($formObj->getElement('archive_date')->getValue(), 'dd/MM/y');
            $archive_date = $date->toString('y-MM-dd');
        } else {
            $date = new Zend_Date();
            $archive_date = $date->toString('y-MM-dd');
        }
        $row->archive_date = $archive_date;
        
        $row->magazine_nr = $formObj->getElement('magazine_nr')->getValue();

        $row->video = $formObj->getElement('video')->getValue();

        $row->article_status = 1;
        "Article has just been entered in the system and is not redacted or published";
        
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
            
            // If user is operator and the article is redacted or in redaction process do not allow editing
            if (($user->role_id == Zend_Registry::get('config')->user->operator) and ($row->article_status != 1)) {
                return "Artikulli eshte ne redaktim ose i redaktuar. Ju nuk keni te drejta ta modifikoni ate!";
            }
            
            // If user is journalist and the article is redacted and published do not allow editing
            if (($user->role_id == Zend_Registry::get('config')->user->journalist) and ($row->article_status == 3)) {
                return "Artikulli eshte i redaktuar dhe i publikuar. Ju nuk keni te drejta ta modifikoni ate!";
            }
            
            // set the row data
            $category = explode("-", $formObj->getElement('blog_category_id')->getValue());
            $row->blog_category_id = trim($category[0]);
            
            $row->title = $formObj->getElement('title')->getValue();
            
            $row->rewrite_title = Utility_Functions::generateRewriteTitle($row->title);
            
            $row->subtitle = $formObj->getElement('subtitle')->getValue();
            
            $author = explode("-", $formObj->getElement('author')->getValue());
            $row->author = trim($author[0]);
            
            $prepared_by = explode("-", $formObj->getElement('prepared_by')->getValue());
            $row->prepared_by = trim($prepared_by[0]);
            
            $row->intro_text = $formObj->getElement('intro_text')->getValue();
            $row->full_text = $formObj->getElement('full_text')->getValue();
            
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
            
            if (is_int($formObj->getElement('archived')->getValue())) {
                $row->archived = $formObj->getElement('archived')->getValue();
            }
            
            if ($formObj->getElement('archive_date')->getValue() != "") {
                $date = new Zend_Date($formObj->getElement('archive_date')->getValue(), 'dd/MM/y');
                $archive_date = $date->toString('y-MM-dd');
            } else {
                $date = new Zend_Date();
                $archive_date = $date->toString('y-MM-dd');
            }
            $row->archive_date = $archive_date;
            
            $row->magazine_nr = $formObj->getElement('magazine_nr')->getValue();

            $row->video = $formObj->getElement('video')->getValue();
            
            if ($user->role_id == Zend_Registry::get('config')->user->operator) {
                $row->article_status = 1;
                "Article has just been entered in the system and is not redacted or published";
            }
            
            if (($user->role_id == Zend_Registry::get('config')->user->journalist) or ($user->role_id == Zend_Registry::get('config')->user->editor) or ($user->role_id == 1)) {
                $row->article_status = 2;
                "Article is being redacted";
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
            if ((($user->role_id == Zend_Registry::get('config')->user->operator) or ($user->role_id == Zend_Registry::get('config')->user->journalist)) and ($row->article_status == 2 or $row->article_status == 3)) {
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
        $userId = Authenticate::getUserId();
        
        $select = $this->select();
        
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "$this->_name.id",
            "$this->_name.article_status",
            "$this->_name.title",
            "$this->_name.subtitle",
            "blog_categories.title as blog_category_id",
            "CONCAT(authors.firstname, ' ', authors.lastname) as author",
            "DATE_FORMAT($this->_name.publish_date,'%Y/%m/%d') as publish_date",
            "DATE_FORMAT($this->_name.publish_time,'%H:%i') as publish_time"
        ), "")
            ->join("blog_categories", "blog_articles.blog_category_id = blog_categories.id", "")
            ->joinLeft("authors", "$this->_name.author = authors.id", "$this->_name.magazine_nr")
            ->join("sys_acl_article_categories", "blog_articles.blog_category_id = sys_acl_article_categories.category", "")
            ->where("sys_acl_article_categories.user = ?", $userId)
            ->where("sys_acl_article_categories.read = ?", "1");
        
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
            "CONCAT(blog_categories.id,' - ',blog_categories.title) as blog_category_id",
            "$this->_name.title",
            "$this->_name.subtitle",
            "CONCAT($this->_name.author,' - ',authors.firstname,' ', authors.lastname) as author",
            "CONCAT($this->_name.prepared_by,' - ',sys_users.username) as prepared_by",
            "$this->_name.intro_text",
            "$this->_name.full_text",
            "$this->_name.published",
            "DATE_FORMAT($this->_name.publish_date,'%d/%m/%Y') as publish_date",
            "DATE_FORMAT($this->_name.publish_time,'%H:%i') as publish_time",
            "$this->_name.archived",
            "DATE_FORMAT($this->_name.archive_date,'%d/%m/%Y') as archive_date",
            "$this->_name.magazine_nr"
        ))
            ->join("blog_categories", "blog_articles.blog_category_id = blog_categories.id", "")
            ->joinLeft("authors", "$this->_name.author = authors.id", "")
            ->joinLeft("sys_users", "$this->_name.prepared_by = sys_users.id", "")
            ->where('blog_articles.id = ?', $rowId['itemFound']);
        
        $item = $this->fetchRow($select);
        // echo $select->__toString ();
        
        // return the result in json format
        echo Utility_Functions::_toJson(! empty($item) ? $item->toArray() : "");
    }

    public function publishArticle($articleId)
    {
        $row = $this->getDataById($articleId);
        $row->published = 1;
        $row->article_status = 3;
        
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

    public function unPublishArticle($articleId)
    {
        $row = $this->getDataById($articleId);
        $row->published = 0;
        $row->article_status = 2;
        
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

    public function assignProfileImage($articleId, $fileName)
    {
        $row = $this->getDataById($articleId);
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
}
?>

