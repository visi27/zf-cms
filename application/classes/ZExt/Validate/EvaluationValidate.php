<?php
/** @see Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * @uses       ZExt_Validate_EvaluationValidate
 * @package    ZExt_Validate
*/
 
class ZExt_Validate_EvaluationValidate extends Zend_Validate_Abstract {
	
   const MSG_INSUFFICIENTSOLDIER = 'insuficientSoldier';
   const MSG_INSUFFICIENT = 'insufficient';
   const MSG_NOTAPPROPRIATE = 'notappropriate';
   const MSG_NOTAPPROPRIATESOLDIER = 'notappropriateSoldier';
   const MSG_SATISFACTORY = 'satisfactory';
   const MSG_SATISFACTORYSOLDIER = 'satisfactorySoldier';
   const MSG_VERYGOOD = 'verygood';
   const MSG_VERYGOODSOLDIER = 'verygoodSoldier';
   const MSG_EXCELLENT = 'excellent';
   const MSG_EXCELLENTSOLDIER = 'excellentSoldier';
   
    public $minimum = 1;
    public $maximum = 3;
 
    protected $_messageTemplates = array(
        self::MSG_INSUFFICIENTSOLDIER => "'%value%' 	nuk eshte midis 22 dhe 33 qe e jane vlerat e lejuara per kete vleresim dhe kete grade",
    	self::MSG_INSUFFICIENT => "'%value%' 	nuk eshte midis 24 dhe 36 qe jane vlerat e lejuara per kete vleresim dhe kete grade",
        self::MSG_NOTAPPROPRIATE => "'%value%' nuk eshte midis 37 dhe 54 qe jane vlerat e lejuara per kete vleresim dhe kete grade",
        self::MSG_NOTAPPROPRIATESOLDIER => "'%value%' nuk eshte midis 34 dhe 49 qe jane vlerat e lejuara per kete vleresim dhe kete grade",
    	self::MSG_SATISFACTORY => "'%value%' nuk eshte midis 55 dhe 90 qe jane vlerat e lejuara per kete vleresim dhe kete grade",
    	self::MSG_SATISFACTORYSOLDIER => "'%value%' nuk eshte midis 50 dhe 83 qe jane vlerat e lejuara per kete vleresim dhe kete grade ",
    	self::MSG_VERYGOOD => "'%value%' nuk eshte midis 91 dhe 108 qe jane vlerat e lejuara per kete vleresim dhe kete grade ",
    	self::MSG_VERYGOODSOLDIER => "'%value%' nuk eshte midis 84 dhe 99 qe jane vlerat e lejuara per kete vleresim dhe kete grade ",
    	self::MSG_EXCELLENT => "'%value%' nuk eshte midis 109 dhe 120 qe jane vlerat e lejuara per kete vleresim dhe kete grade ",
    	self::MSG_EXCELLENTSOLDIER => "'%value%' nuk eshte midis 100 dhe 110 qe jane vlerat e lejuara per kete vleresim dhe kete grade "
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
    
    //get the id of the selected person
    $selectedPerson = Utility_Functions::getSelectedPerson();
    //get the current rank of the selected person
    $currentRank = Ajax_Response_Utility::getPersonalRank($selectedPerson);

     if (($context[$field]) == 'E PaMjaftueshme') {
     
	    if ($value >= 37 && ($currentRank!="OR1" && $currentRank!="OR2" && $currentRank!="OR3" && $currentRank!="OR4")) {
	        $this->_error(self::MSG_INSUFFICIENT);
	        return false;
	        }
	    elseif ($value >= 34 && ($currentRank=="OR1" || $currentRank=="OR2" || $currentRank=="OR3" || $currentRank=="OR4")) {
	        	$this->_error(self::MSG_INSUFFICIENTSOLDIER);
	        	return false;
	        }
        
     }
    elseif (($context[$field]) == 'Nen Mesatare'){
    	
        if (($value < 37 || $value >= 55) && ($currentRank!="OR1" && $currentRank!="OR2" && $currentRank!="OR3" && $currentRank!="OR4")) {
	        $this->_error(self::MSG_NOTAPPROPRIATE);
	        return false;
	        }
	    elseif (($value < 34 || $value >= 50) && ($currentRank=="OR1" || $currentRank=="OR2" || $currentRank=="OR3" || $currentRank=="OR4")) {
	        	$this->_error(self::MSG_NOTAPPROPRIATESOLDIER);
	        	return false;
	        }
    	}
    elseif (($context[$field]) == 'Mesatare'){
    		 
    		if (($value < 55 || $value >= 91) && ($currentRank!="OR1" && $currentRank!="OR2" && $currentRank!="OR3" && $currentRank!="OR4")) {
    			$this->_error(self::MSG_SATISFACTORY);
    			return false;
    		}
    		elseif (($value < 50 || $value >= 84) && ($currentRank=="OR1" || $currentRank=="OR2" || $currentRank=="OR3" || $currentRank=="OR4")) {
    			$this->_error(self::MSG_SATISFACTORYSOLDIER);
    			return false;
    		}
    	}
    	
    elseif (($context[$field]) == 'Mbi Mesatare'){
    		 
    		if (($value < 91 || $value >= 109) && ($currentRank!="OR1" && $currentRank!="OR2" && $currentRank!="OR3" && $currentRank!="OR4")) {
    			$this->_error(self::MSG_VERYGOOD);
    			return false;
    		}
    		elseif (($value < 84 || $value >= 100) && ($currentRank=="OR1" || $currentRank=="OR2" || $currentRank=="OR3" || $currentRank=="OR4")) {
    			$this->_error(self::MSG_VERYGOODSOLDIER);
    			return false;
    		}
    	}
    	
    elseif (($context[$field]) == 'Shkelqyeshem'){
    		 
    		if (($value < 109 || $value >= 121) && ($currentRank!="OR1" && $currentRank!="OR2" && $currentRank!="OR3" && $currentRank!="OR4")) {
    			$this->_error(self::MSG_EXCELLENT);
    			return false;
    		}
    		elseif (($value < 100 || $value >= 111) && ($currentRank=="OR1" || $currentRank=="OR2" || $currentRank=="OR3" || $currentRank=="OR4")) {
    			$this->_error(self::MSG_EXCELLENTSOLDIER);
    			return false;
    		}
    	}
    return true;
  }
}
?>