<?php

class Table_Comments extends Table_Abstract
{

    protected $_name = 'web_comments';

    protected $_primary = 'id';

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

    public function createNew($post_id, $content, $user_id, $user_name, $reply_to = 0)
    {
        
        // create a new row in the table
        $row = $this->createRow();
        
        $row->post_id = $post_id;
        // $row->user_id = $user_id;
        $row->user_id = $user_id;
        $row->user_name = $user_name;
        
        $row->content = $content;
        
        $row->parent = $reply_to;
        $row->published = 1;
        
        try {
            // save
            $id = $row->save();
            
            // if no exception, return true
            return true;
        } catch (Exception $e) {
            Zend_Registry::get("applog")->log($e->getMessage());
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
        
        $select->from($this, array(
            "id",
            "post_id",
            "user_name",
            new Zend_Db_Expr("LEFT(content, 80) as content"),
            "content as content_tooltip",
            "content_uncensored",
            "modified_timestamp",
            "upvotes",
            "downvotes"
        ));
        
        // add any filters which are set
        if (count($filters) > 0) {
            foreach ($filters as $field => $filter) {
                if (count($filter) > 0) {
                    foreach ($filter as $operator => $value)
                        $select->where($field . $operator . '?', $value);
                }
            }
        } else {
            $select->where("redacted = 0");
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
                'web_comments.modified_timestamp',
                'web_comments.published'
            ));
        }
        
        // $test = $select->__toString();
        // print_r($test);
        return $this->fetchAll($select);
    }

    public function getComments($postId, $limit = 0, $parent = 0)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from($this, array(
            "id",
            "post_id",
            "user_name",
            "user_id",
            "content",
            "modified_timestamp",
            "upvotes",
            "downvotes"
        ))
            ->where("post_id = ?", $postId)
            ->where("parent = ?", $parent)
            ->where("published = ?", 1)
            ->where("deleted = ?", 0)
            ->order("id desc")
            ->limit($limit);
        
        $results = $this->fetchAll($select);
        Zend_Registry::get("applog")->log($select->__toString());
        return (! empty($results)) ? $results : null;
    }

    public function unPublishComment($commentId)
    {
        $row = $this->getDataById($commentId);
        $row->published = 0;
        
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

    public function publishComment($commentId)
    {
        $row = $this->getDataById($commentId);
        $row->published = 1;
        
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

    public function deleteComment($commentId)
    {
        $row = $this->getDataById($commentId);
        $row->deleted = 1;
        
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

    public function flagComment($commentId)
    {
        $row = $this->getDataById($commentId);
        $row->flagged = 1;
        
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

    public function unFlagComment($commentId)
    {
        $row = $this->getDataById($commentId);
        $row->flagged = 0;
        
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
