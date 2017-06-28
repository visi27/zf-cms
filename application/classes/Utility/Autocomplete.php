<?php

/**
 * @author Administrator
 *
 */
class Utility_Autocomplete {

	protected $jscript; 
	
	protected $name;
	
	protected $txtValue;
	
	protected $defaultText;
	
	protected $controller;
	
	protected $dataType;
	
	protected $responseClass;
	
	protected $responseMethod;
	
	protected $delimiter = "-";
	
	protected $mapedData = array("label"	=> "id, name",
								"value"	=>	"id, name",
								"id"	=>	"id",
								"module"	=>	"module",
								"moduleId"	=>	"moduleId");

	protected $parameters = array();
	
	protected $onChangeMethod;

	protected $clearOnExit = true;
	
	public $isDependent=true;
	
	
	public function __construct($name, $class, $method, $isDependent=true){
		$this->name = $name;	
		$this->setDataType("json");
		$this->setController("index.php?c=ajax");
		$this->setResponseClass($class);
		$this->setResponseMethod($method);
		$this->addParameter("request.term");
		$this->setResultLimit(false);
		$this->setTxtValue($this->getName());
		$this->isDependent=$isDependent;
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
		
		
		$jsScript = '
		
		$( "#'.$this->getName().'" ).autocomplete({
	        autoFocus: true,
	        delay: 300,
	        minLength: '.$minSearchChar.',
			source: function( request, response ) {
	        	var myCookie = CryptoJS.MD5( $.cookie("'.session_name().'") );
				$.ajax({
					url: "'.$this->getController().'",
					dataType: "'.$this->getDataType().'",
					data: {
						loadClass: "'.$this->getResponseClass().'",
						method: "'.$this->getResponseMethod().'",
						format: "'.$this->getDataType().'",
						parameter: "text:"+'.implode('+ ";" +', $this->getParameters()).'
					}		
				}).done( function( data ) {	
								
					// ---- check session availability ----- //
					if(data.id == myCookie){
						noty({
							"text":"Your session has expired due to inactivity! Redirecting to the login... "+
								   "<img width=\"16px\" height=\"16px\" src=\"images/ajax-loader.gif\"/>",
							"layout":"center",
							"type":"warning",
							"speed":10,
							"timeout":2900
						});	
						setTimeout(function() {
							window.location  = "index.php";
						}, 3000);
						return;
					}//else
					// ---------- end of session check ---------- //
																
					if(null!= data){ 
						response( $.map( data, function( item ) {							
							return { ';
							foreach ($this->getMapedData() as $key => $value){
								$map .= "$key:";
								$valueArray = explode(",", $value);
								foreach($valueArray as $value){
									$map.="item.$value + ' ".$this->getDelimiter()." ' +";
								}
								$map = substr($map, 0, -9).", \r\n";
							}	
							$map = rtrim(trim($map),",");			
			$jsScript.=$map.'};
						}));
					} 	
				});
			} ,
			change: function( event, ui ){
				if ( !ui.item ) {';
					$jsScript.= ($this->clearOnExit == true)?'$(this).val("'.$this->getTxtValue().'");':'';
					$jsScript.= $this->getOnChangeMethod().
					'
				}
			},
			select: function(event, ui) { '
				.$this->getOnChangeMethod().'
				
			}
		}); //end of autocomplete
						
		$( "#'.$this->getName().'" ).button({}).click(function() {$(this).val("");}); 
				
		$( "#'.$this->getName().'Button" ).click(function() {
						// give focus to the autocomplete field
						$("#'.$this->getName().'").focus();
							
						// set the autocomplete value to null
						$("#'.$this->getName().'").val("");
							
						// search with an empty parameter (load everything)
						$("#'.$this->getName().'").autocomplete( "search", "" );								
					});

		$( "#'.$this->getName().'" ).parent().buttonset();';
		
		
		// call the setScript method
		$this->setJscript($jsScript);
		
		return $this->getJscript().'<div style="float:left;padding-right: 5px; margin-bottom:5px;">
		
				<div class="input-group" style="max-width:250px;">
					<input type="text" class="form-control"id="'.$this->getName().'" placeholder="'.html_entity_decode($this->getTxtValue(), ENT_COMPAT).'" value="'.html_entity_decode($this->getTxtValue(), ENT_COMPAT).'"/>		
					<span class="input-group-btn">
						<button class="btn btn-success" id="'.$this->getName().'Button">
								<i class="fa fa-toggle-down"></i>
								
						</button>
					</span>
				</div>
			</div>';
		
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
		return "\r\n<script>\r\n $(function() {\r\n" . $this->jscript . "\r\n}); \r\n</script>\r\n";
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
	 * @param field_type $dataType
	 */
	public function setDataType($dataType) {
		$this->dataType = $dataType;
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