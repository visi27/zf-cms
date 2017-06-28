<?php

/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZExt_Validate_DateCompare extends Zend_Validate_Abstract
{
    /**
     * Error codes
     * @const string
     */
    const NOT_SAME      = 'notSame';
    const MISSING_TOKEN = 'missingToken';
    const NOT_LATER = 'notLater';
    const NOT_EARLIER = 'notEarlier';
    const NOT_BETWEEN = 'notBetween';

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_SAME      => "The date '%token%' does not match the given '%value%'",
        self::NOT_BETWEEN      => "Data '%token%' nuk eshte e sakte",
        self::NOT_LATER      => "Data '%token%' duhet te jete para dates '%value%'",
        self::NOT_EARLIER      => "Data '%value%' nuk duhet te jete para dates '%token%'",
        self::MISSING_TOKEN => "No date was provided to match against"
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
    protected $_compare;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     * @param  mixed $compare
     * @return void
     */
    public function __construct($token = null, $compare=true)
    {
        if (null !== $token) {
            $this->setToken($token);
            $this->setCompare($compare);
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
        $this->_setValue((string) $value);
        $token        = $this->getToken();
		
        if ($value!=''){ 
        $date1=new Zend_Date($value,'dd/MM/y');
        $date2=new Zend_Date($token,'dd/MM/y');
       
        
        if ($this->getCompare()===true){

            if ($date1->compare($date2)<0 || $date1->equals($date2)){

                $this->_error(self::NOT_LATER);
                return false;
            }
        }else if ($this->getCompare()===false){
            if ($date1->compare($date2)>0 || $date1->equals($date2)){
                $this->_error(self::NOT_EARLIER);
                return false;
            }
        }else if ($this->getCompare()===null){
            if (!$date1->equals($date2)){
                $this->_error(self::NOT_SAME);
                return false;
            }
        }else if ($this->getCompare()==="EqualOrSmaller"){
        	if ($date1->compare($date2)>0){
                $this->_error(self::NOT_EARLIER);
                return false;
            }
        }else{
            $date3=new Zend_Date($this->getCompare());

            if ($date1->compare($date2)<0 || $date1->compare($date3)>0){
                $this->_error(self::NOT_BETWEEN);
                return false;
            }
        }


        return true;
    }
   }
}
