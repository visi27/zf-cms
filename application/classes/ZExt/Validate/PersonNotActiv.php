<?php
/**
 *  Dont allow user to add or edit a appointment when a person is not activ.
 * @author Administrator
 *
 */
class ZExt_Validate_PersonNotActiv extends Zend_Validate_Abstract {
	
	const NOT_ACTIV = "notActiv";
	
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
	 * @var array
	 */
	protected $_messageVariables = array(
			'fieldName' => '_fieldName',
			'fieldTitle' => '_fieldTitle',
			'msgComponent'		=> '_msgComponent'
	);
	
	protected $_messageTemplates = array (
			// @todo Translate. Read this this error message from a file.
			self::NOT_ACTIV => "Gabim: Personi i perzgjedhur eshte ne '%msgComponent%'!"
	);
	
	
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
	 * @return Zend_Validate_IdenticalField Provides a fluent interface
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
	 * Duty Appointments: dont allow two draft appointments (unconfirmed) positionin in a row.
	 * @see Zend_Validate_Interface::isValid()
	 */
	public function isValid($value, $context = null) {
		
		//$this->_setValue($value);
		$field = $this->getFieldName();
		$rowId = $context[$field];
		
		// load the service model
		$service=new Table_Services();
		
		// get the latest appointment in time
		$component=$service->getComponentReserveOrLirim(
				Utility_Functions::getSelectedPerson() // the current selected person
		);
		
		// dont allow user to create or edit an appointment that is not activ
		if( $component->component == "Rezerve" || $component->component == "Lirim" || $component->component == "LirimCivil"){ 
			$this->_msgComponent = $component->component;
			$this->_error (self::NOT_ACTIV);
			return false;
		}
		
		// all controlles are suceeded, true
		return true;
	}
}
?>