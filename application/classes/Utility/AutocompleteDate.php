<?php

/**
 * @author Administrator
 *
 */
class Utility_AutocompleteDate {

	protected $jscript; 
	
	protected $name;
	
	protected $txtValue;
	
	protected $defaultText;

	protected $parameters = array();
	
	protected $onChangeMethod;

	protected $clearOnExit = true;
	
	protected $delimiter = "-";
	
	public $isDependent=true;
	
	protected $dateFormat = "yy/mm/dd";
	
	
	public function __construct($name, $dateFormat = "yy/mm/dd", $isDependent=true){
		$this->name = $name;	
		$this->setResultLimit(false);
		$this->setTxtValue($this->getName());
		$this->isDependent=$isDependent;
		$this->setDateFormat($dateFormat);
	}
	
	public function setClearOnExit($value){
		$this->clearOnExit = $value;
	}	
	
	public function isSetDefaultVal() {
		if ($this->getTxtValue()==$this->getDefaultText())
			return true;
		else
			return false;
	}
	
	/**
	 * Set Date Format for Date Autocompletes
	 * @param string $dateformat
	 */
	public function setDateFormat($dateFormat){
	    $this->dateFormat = $dateFormat;
	}
	
	/**
	 * Get Date Format for Date Autocompletes
	 */
	public function getDateFormat(){
	    return $this->dateFormat;
	}
	
	/**
	 * Set the result limit
	 * @param int $limit
	 */
	public function setResultLimit($limit){
		$limit = (intval($limit)>0)?intval($limit):"0";
		$this->addParameter("\"limit\" + \":{$limit}\"");
	}
	
	/**
	 * @param $minLength
	 * @return String The html content.
	 */
	public function render($minSearchChar = 0){

			$jsScript='$("#'.$this->getName().'").val("'.html_entity_decode($this->getTxtValue(), ENT_COMPAT).'")';
		
		
		$jsScript .= '
			$(function() {
			
							var dates = $( "#'.$this->getName().'" ).button({}).datepicker({
							defaultDate: "+0w",
							  changeMonth: true,
						      changeYear: true,
						      gotoCurrent:true,
						      yearRange: "-120:+10",
						      
						      dateFormat: "'.$this->getDateFormat().'",
						      constrainInput: true,
						      minDate: "-120Y",
						      maxDate: "+10Y", 
						      shortYearCutoff: "+10",
							onSelect: function( selectedDate ) {
								var option = this.id == "from" ? "minDate" : "maxDate",
									instance = $( this ).data( "datepicker" ),
									date = $.datepicker.parseDate(
														instance.settings.dateFormat ||
														$.datepicker._defaults.dateFormat,
														selectedDate, instance.settings
											);
								dates.not( this ).datepicker( "option", option, date );
							}
						});
					});';
		
		
		// call the setScript method
		$this->setJscript($jsScript);
		
		return '<div style="float:left;padding-right: 5px; margin-bottom:5px;">
		
				<div class="input-group" style="max-width:250px;">
					<input type="text" class="form-control"id="'.$this->getName().'" placeholder="'.html_entity_decode($this->getTxtValue(), ENT_COMPAT).'" value="'.html_entity_decode($this->getTxtValue(), ENT_COMPAT).'"/>		
				</div>
			</div>'.$this->getJscript();
		
	}
	
	
	public function getDelimiter(){
		return $this->delimiter;
	}
	
	/**
	 * @return the $mapedData
	 */
	public function getMapedData() {
		return $this->mapedData;
	}

	/**
	 * @param multitype: $mapedData
	 */
	public function setMapedData(Array $mapedData) {
		$this->mapedData = $mapedData;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @return the $controller
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * @return the $dataType
	 */
	public function getDataType() {
		return $this->dataType;
	}

	/**
	 * @return the $responseClass
	 */
	public function getResponseClass() {
		return $this->responseClass;
	}

	/**
	 * @return the $responseMethod
	 */
	public function getResponseMethod() {
		return $this->responseMethod;
	}

	/**
	 * @return the $parameters
	 */
	public function getParameters() {
		return $this->parameters;
	}
	
	
	public function getJscript(){
		return "\r\n<script>\r\n \r\n" . $this->jscript . "\r\n \r\n</script>\r\n";
	}
	
	/**
	 * @return the $txtValue
	 */
	public function getTxtValue() {
		return $this->txtValue;
	}

	/**
	 * @param field_type $txtValue
	 */
	public function setTxtValue($txtValue) {
		$text = ($txtValue != "")?$txtValue:$this->getDefaultText();
		$this->txtValue = ucfirst($text);
	}
	
	public function getDefaultText(){
		return $this->defaultText;
	}
	
	public function setDefaultText($text){
		$this->defaultText = $text;
		$this->setTxtValue($text);
	}
	/**
	 * @param field_type $controller
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}


	/**
	 * @param field_type $responseClass
	 */
	public function setResponseClass($responseClass) {
		$this->responseClass = $responseClass;
	}

	/**
	 * @param field_type $responseMethod
	 */
	public function setResponseMethod($responseMethod) {
		$this->responseMethod = $responseMethod;
	}

	/**
	 * @param field_type $parameters
	 */
	public function addParameter($parameter) {
		array_push($this->parameters, $parameter);
	}
		
	/**
	 * @return the $onChangeMethod
	 */
	public function getOnChangeMethod() {
		return $this->onChangeMethod;
	}
	
	/**
	 * @param field_type $onChangeMethod
	 */
	public function setOnChangeMethod($onChangeMethod) {
		$this->onChangeMethod .= $onChangeMethod;
	}
	
	public function setJscript($jScript){	
		$this->jscript.= $jScript;
	}
	
}

?>