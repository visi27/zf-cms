<?php

/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZExt_Validate_NotEmptyPair extends Zend_Validate_Abstract
{
    /**
     * Error codes
     * @const string
     */
    const MISSING_VALUE = 'missingValue';


    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::MISSING_VALUE      => "This field must be paired with the filed '%token%'"
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'token' => '_tokenString'
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $_tokenString;
    protected $_token;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     * @param  mixed $compare
     * @return void
     */
    public function __construct($token = null)
    {
        if (null !== $token) {
            $this->setToken($token);
        }
    }

    /**
     * Set token against which to compare
     *
     * @param  mixed $token
     * @return Zend_Validate_Identical
     */
    public function setToken($token)
    {
        $this->_tokenString = (string) $token;
        $this->_token       = $token;
        return $this;
    }

    /**
     * Retrieve token
     *
     * @return string
     */
    public function getToken()
    {
        return $_REQUEST[$this->_token];
    }

    /**
     * Set compare against which to compare
     *
     * @param  mixed $compare
     * @return Zend_Validate_Identical
     */
    public function setCompare($compare)
    {
        $this->_compareString = (string) $compare;
        $this->_compare       = $compare;
        return $this;
    }

    /**
     * Retrieve compare
     *
     * @return string
     */
    public function getCompare()
    {
        return $this->_compare;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        $token        = $this->getToken();
		
        if($value !== '' and $token !== ''){
        	return true;
        }else if($value == '' and $token == ''){
        	return true;
        }else{
        	$this->_error(self::MISSING_VALUE);
        	return false;
        }
       
    }
}
