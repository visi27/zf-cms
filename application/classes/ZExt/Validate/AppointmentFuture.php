<?php
/**
 * Allow only one appointment set for future for this person.
 * @author Administrator
 *
 */
class ZExt_Validate_AppointmentFuture extends Zend_Validate_Abstract {
	
	const MULTIPLE_FUTURES = "multipleFutures";
	
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
			'fieldTitle' => '_fieldTitle'
	);
	
	protected $_messageTemplates = array (
			// @todo Translate. Read this this error message from a file.
			self::MULTIPLE_FUTURES => 'Gabim: Nuk mund te regjistrohet me shume 
													se nje emerim per ne te ardhmen!'
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
	 * Duty Appointments: allow only one appointment set for future for this person
	 * @see Zend_Validate_Interface::isValid()
	 */
	public function isValid($value, $context = null) {
		
		//$this->_setValue($value);
		$field = $this->getFieldName();
		$rowId = $context[$field];
		
		// load the duty appointments model
		$position = new Table_DutyAppointments();
		
		// get the future appointment in time
		$futureAppointment = $position->getFutureAppointment( 
				Utility_Functions::getSelectedPerson() 
		);
		
		// allow only one appointment set for future for this person
		if( $futureAppointment->duty_id and 
				$futureAppointment->duty_id != $rowId ){ 
			// Error: existing draft position
			$this->_error (self::MULTIPLE_FUTURES);
			return false;
		}
		
		// all controlls have suceeded, true
		return true;
	}
}
?>