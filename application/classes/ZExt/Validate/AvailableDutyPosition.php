<?php
/**
 *  Dont allow two draft appointments (unconfirmed) positionin in a row.
 * @author Administrator
 *
 */
class ZExt_Validate_AvailableDutyPosition extends Zend_Validate_Abstract {
	
	const DATE_CONFLICT 	= "dateBefore";
	const POS_IN_CONFLICT 	= "inConflict";
	const SAME_POSITION		= "samePosition"; // versioning applied
	const MULTI_FUTURE		= "multiFuture";
	const EXCEPTION			= "errorOcured";
	
	/**
	 * Name of the field as it appear in the $context array.
	 *
	 * @var string
	 */
	protected $_startDate;
	
	/**
	 * Name of the field as it appear in the $context array.
	 *
	 * @var string
	 */
	protected $_dutyId;
	
	/**
	 * message variables
	 * @var string
	 */
	protected $_msgDate;
	
	protected $_personNID;
	
	/**
	 * @var array
	 */
	protected $_messageVariables = array(
			'msgDate'		=> '_msgDate',
			'personNID'		=> '_personNID',
	);
	
	protected $_messageTemplates = array (
			// @todo Translate. Read this this error messages from a file.
		self::DATE_CONFLICT => "Data '%msgDate%' konflikton me emerimin ekzistues 
								te personit me numer identiteti: '%personNID%' !",
			
		self::POS_IN_CONFLICT=> "Pozicioni i kerkuar ndodhet aktualisht ne nje gjendje
								konflikti. Ai mbahet nga personi me numer identiteti: 
								'%personNID%'. Rregulloni me pare konfliktin ekzistent.",
			
		self::SAME_POSITION	=> "Ky person eshte aktualisht i emeruar ne kete pozicion.
								Emerimi nuk mund te kryhet mbi te njejtin pozicion.",

		self::MULTI_FUTURE  => "Emerimi nuk mund te kryhet mbi kete pozicion. Ne kete 
								pozicion eshte emeruar (me date ne te ardhmen) personi me 
								numer identiteti: '%personNID%' !",
			
		self::EXCEPTION		=> "Emerimi nuk mund te kryhet mbi kete pozicion. Ju lutemi,
								te kontaktoni administratorin per nje zgjidhje."							
	);

	
	public function __construct($startDate, $dutyId) {
		$this->setStartDate($startDate);
		$this->setDutyId($dutyId);
	}
	
	/**
	 * Returns the field name.
	 *
	 * @return string
	 */
	public function getStartDate() {
		return $this->_startDate;
	}
	
	/**
	 * Sets the field name.
	 *
	 * @param  string $fieldName
	 * @return Zend_Validate_IdenticalField Provides a fluent interface
	 */
	public function setStartDate($fieldName) {
		$this->_startDate = $fieldName;
		return $this;
	}
	
	
	/**
	 * Returns the dutyId.
	 *
	 * @return integer
	 */
	public function getDutyId() {
		return $this->_dutyId;
	}
	
	/**
	 * Sets the dutyId.
	 *
	 * @param  string $fieldTitle
	 * @return Zend_Validate_IdenticalField Provides a fluent interface
	 */
	public function setDutyId($fieldTitle) {
		$this->_dutyId = $fieldTitle;
		return $this;
	}
	
	/**
	 * Duty Appointments: dont allow if this position is occupied.
	 * @see Zend_Validate_Interface::isValid()
	 */
	public function isValid($value, $context = null) {
				
	try{
		// the position id
		$toePositionCode = $value;	
		

		
		// Parameter: the new effective start date
		$newDutyStartDate = new Zend_Date($context[ $this->getStartDate() ], 'dd/MM/y');		
		// Parameter: the duty_id value (in case of update)
		$dutyId = $context[ $this->getDutyId() ];	
			
		// Get the Current Top Version ----------------------------------------
		// webservice handler
		$webServiceRequest = new Ajax_Response_TopWebServices();
		// validate the unit details, by calling a service
		$versionData = $webServiceRequest->getVersionByStructure(
				array("text:".substr($toePositionCode, 0, 4), "date:".$newDutyStartDate), false);
		$versionData = Zend_Json::decode($versionData);
		$topVersion = (is_array($versionData))?$versionData[0]['top_version_code']:"0";
		
		$toePositionCode=$topVersion."-".$toePositionCode;
		
		// load the duty appointments model
		$position = new Table_DutyAppointments();		
		// get the latest appointed person over this position
		$latestPersonAppointed = $position->getLatestPerson($toePositionCode);	
		// load the personal module
		$person = new Table_Personal();
		// get the details of the person lately holding the position
		$personDetails = $person->getDataById($latestPersonAppointed->person_id);
		
		// CASE: OK
		// if no other person is appointed, or this is an update mode, return true
		if(!$latestPersonAppointed or $latestPersonAppointed->duty_id == $dutyId ){
			// position is not occupied
			return true;
		}
		
		// CONDITION 1 -------------------------------------------------
		// can not be earlier or within the start-enddate of an existing appointment, 
		$latestPersonStartDate = new Zend_Date($latestPersonAppointed->duty_start_date, 'y-MM-dd');
		// end date is considered equal to startdate if it has no value
		$latestPersonEndDate = $latestPersonAppointed->duty_end_date
							?new Zend_Date($latestPersonAppointed->duty_end_date, 'y-MM-dd')
							:$latestPersonStartDate;
	
		// duty_start_date <= latest existing end date, not Allowed!
		if( $newDutyStartDate->compareDate($latestPersonEndDate) <= 0	){ // earlier or equal
			// set message variables
			$this->_msgDate = $newDutyStartDate->toString('dd/MM/y');
			$this->_personNID = $personDetails->idcard;
			// Error: date conflict
			$this->_error (self::DATE_CONFLICT);
			return false;
		}
	
		// CONDITION 2 -------------------------------------------------
		// the Position trying to be occupied is in a collision Status (Problem)
		if($latestPersonAppointed->collision == true ){
			// set the message variable
			$this->_personNID = $personDetails->idcard;
			// Error: date conflict
			$this->_error (self::POS_IN_CONFLICT);
			return false;
		}

		
		// CONDITION 3-------------------------------------------------
		// Can not reappoint the same person to the same position again.
		$currentPersonAppointed = $position->getCurrentPerson($toePositionCode);
		
		if(	!is_null($currentPersonAppointed) and
			($currentPersonAppointed->person_id == Utility_Functions::getSelectedPerson()) and
			($currentPersonAppointed->toe_version_code == $topVersion )){
			// Error: date conflict
			$this->_error (self::SAME_POSITION);
			return false;
		}
		
		
		// CONDITION 4 -------------------------------------------------
		// only one person can be appointed in the future for this Position
		$futurePersonAppointed = $position->getFuturePerson($toePositionCode);
		if(	!is_null($futurePersonAppointed) ){			
			// set the message variable
			$this->_personNID = $personDetails->idcard;
			// Error: date conflict
			$this->_error (self::MULTI_FUTURE);
			return false;
		}
				
		//$this->_setValue($value);
		// all controlles are suceeded, true
		return true;
		
	}catch (Exception $e){
		$this->_error (self::EXCEPTION);
		return false;
	}}
}
?>