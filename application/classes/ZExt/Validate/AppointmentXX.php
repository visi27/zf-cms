<?php
/**
 * Dont allow two XX positions in a row.
 * @author Administrator
 *
 */
class ZExt_Validate_AppointmentXX extends Zend_Validate_Abstract {
	
	const MULTIPLE_XX = "multipleXX";
	
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
			self::MULTIPLE_XX => 'Gabim: Nuk mund te regjistrohet me shume 
													se nje emerim per ne dispozicion!'
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
		$webServiceRequest = new Ajax_Response_TopWebServices();
		$forceData = $webServiceRequest->getForceByNumber(array("text:".substr($value,0,2)), false);
		$forceData = Zend_Json::decode($forceData);
		//$forceData['force_code'] = (is_array($forceData))?$forceData[0]['force_code']:"XX";
		if(is_array($forceData)){
			
			$forceData['force_code'] = $forceData[0]['force_code'];
		}
		
		else{
			
			$forceDataOldTop = $webServiceRequest->getForceByNumberPreviousTop(array("text:".substr($value,0,2)), false);
			$forceDataOld = Zend_Json::decode($forceDataOldTop);
			
			   if(is_array($forceDataOld)){
			   	
			   	   $forceDataOld['force_code'] = $forceDataOld[0]['force_code'];
			   	
			   }
			   else{
			   	
			   	   $forceDataOld['force_code'] ="XX";
			   }
		}
		
		// load the duty appointments model
		$position = new Table_DutyAppointments();
		
		// get the future appointment in time
		$latestAppointment = $position->getLatestAppointment(
				Utility_Functions::getSelectedPerson() // the current selected person
		);
		
		if($latestAppointment->toe_force_code == 'XX' and $forceData['force_code'] == 'XX'  
				and $latestAppointment->duty_id != $rowId){
			// Error: existing XX position
			$this->_error (self::MULTIPLE_XX);
			return false;
		}
		
		// all controlls have suceeded, true
		return true;
	}
}
?>