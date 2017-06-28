<?php
/**
 * Duty_start_date must be greater than latest appointment's end date.
 * @author Administrator
 *
 */
class ZExt_Validate_AppointmentDate extends Zend_Validate_Abstract {
	
	const EFFECTIVE_START_DATE = 'effectiveStartDate';
	const MIN_ALLOWED_DATE = '30/09/2013';
	
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
			self::EFFECTIVE_START_DATE => "Gabim: Data efektive e fillimit bie ndesh me emerimin paraardhes!" 
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
	 * Duty Appointments: duty_start_date Validator.
	 * @see Zend_Validate_Interface::isValid()
	 */
	public function isValid($value, $context = null) {
		
		$this->_setValue($value);
		$field = $this->getFieldName();
		$rowId = $context[$field];
		
		// default min allowed date
		$minAllowedStartDate = new Zend_Date( self::MIN_ALLOWED_DATE, 'dd/MM/y' );
		
		// default max allowed date
		$now = new Zend_Date();
		$maxAllowedStartDate = $now->addMonth(6);

		// load the duty appointments model
		$position = new Table_DutyAppointments();
		
		// get the latest appointment in time
		$latestAppointment = $position->getLatestAppointment(
				Utility_Functions::getSelectedPerson() // the current selected person
		);
	
		// get the new effective start date
		$new_duty_start_date = new Zend_Date($value, 'dd/MM/y');
		
		// the :LATEST appointment starting date
		$latest_duty_start_date = new Zend_Date( 
				is_null( $latestAppointment->duty_start_date) ? $minAllowedStartDate
															  : $latestAppointment->duty_start_date, 'y-MM-dd');			
		// the :LATEST appointment end date
		$latest_duty_end_date = new Zend_Date( 
				is_null( $latestAppointment->duty_end_date) ? $latest_duty_start_date
														    : $latestAppointment->duty_end_date, 'y-MM-dd');			
		//new date <= latest date, not Allowed!
		if($new_duty_start_date->compareDate($latest_duty_end_date) <= 0 and
				$latestAppointment->duty_id != $rowId){ // earlier or equal
			// Error: date mismatch
			$this->_error (self::EFFECTIVE_START_DATE);
			return false;
		}
			
		// max date controll
		//if($new_duty_start_date->compare($minAllowedStartDate)>=0){
		//	$this->_error (self::EFFECTIVE_DATE);
		//	return false;
		//}
		
		// all controlles are suceeded, true
		return true;
	}

}
?>