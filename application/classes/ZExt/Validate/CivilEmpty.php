<?php
/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @uses       ZExt_Validate_LanguageValidate
 * @package    ZExt_Validate
 * @author     Sean P. O. MacCath-Moran
 * @email      zendcode@emanaton.com
 * @website    http://www.emanaton.com
 * @copyright  This work is licenced under a Attribution Non-commercial Share Alike Creative Commons licence
 * @license    http://creativecommons.org/licenses/by-nc-sa/3.0/us/
*/
 
class ZExt_Validate_CivilEmpty extends Zend_Validate_Abstract {
	
   const MSG_NUMERIC = 'msgNumeric';
   const MSG_MINIMUM = 'msgMinimum';
   const MSG_MAXIMUM = 'msgMaximum';
   const MSG_LENGTH = 'length';
   const MSG_MATCH = 'MsgMatch';
   const MSG_100 = 'Msg100';
 
    public $minimum = 1;
    public $maximum = 3;
 
    protected $_messageTemplates = array(
        self::MSG_NUMERIC => "'%value%' is not numeric",
    	self::MSG_LENGTH => "'%value%' must be 4 digits in length",
        self::MSG_MINIMUM => "'%value%' must be at least '%min%'",
        self::MSG_MAXIMUM => "'%value%' must be no more than '%max%' digits in length",
    	self::MSG_MATCH => "'%value%' must match 1-3 model ",
    	self::MSG_100 => "'%value%' can't be larger then 100 "
    );
  /**
   * @var array
  */
  protected $_messageVariables = array(
    'fieldName' => '_fieldName',
    'fieldTitle' => '_fieldTitle',
  	'min' => 'minimum',
  	'max' => 'maximum'
  );
 
  /**
   * Name of the field as it appear in the $context array.
   *
   * @var string
   */
  protected $_fieldName;
 
  /**
   * Title of the field to display in an error message.
   *
   * If evaluates to false then will be set to $this->_fieldName.
   *
   * @var string
  */
  protected $_fieldTitle;
 
  /**
   * Sets validator options
   *
   * @param  string $fieldName
   * @param  string $fieldTitle
   * @return void
  */
  public function __construct($fieldName, $fieldTitle = null) {
    $this->setFieldName($fieldName);
    $this->setFieldTitle($fieldTitle);
  }
 
  /**
   * Returns the field name.
   *
   * @return string
  */
  public function getFieldName() {
    return $this->_fieldName;
  }
 
  /**
   * Sets the field name.
   *
   * @param  string $fieldName
   * @return ZExt_Validate_LanguageValidate Provides a fluent interface
  */
  public function setFieldName($fieldName) {
    $this->_fieldName = $fieldName;
    return $this;
  }
 
  /**
   * Returns the field title.
   *
   * @return integer
  */
  public function getFieldTitle() {
    return $this->_fieldTitle;
  }
 
  /**
   * Sets the field title.
   *
   * @param  string:null $fieldTitle
   * @return Zend_Validate_IdenticalField Provides a fluent interface
  */
  public function setFieldTitle($fieldTitle = null) {
    $this->_fieldTitle = $fieldTitle ? $fieldTitle : $this->_fieldName;
    return $this;
  }
 
  /**
   * Defined by Zend_Validate_Interface
   *
   *
   * @param  string $value
   *
   * @return boolean
  */
  public function isValid($value, $context = null) {
    $this->_setValue($value);
    $field = $this->getFieldName();
 
  
     if ($value !== 'Civil') {
    
	   // if (empty($value)) {
	        $this->_error(self::MSG_LENGTH);
	        return false;
	  }
	        else {
	        	return true;
	       }
       // }
  
  	}
  }

?>