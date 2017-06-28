<?php

class Utility_AutocompleteOld {

	protected $jscript; 
	
	protected $name;
	
	protected $txtValue;
	
	protected $controller;
	
	protected $dataType;
	
	protected $responseClass;
	
	protected $responseMethod;
	
	protected $mapedData = array("label"	=> "id, name",
								"value"	=>	"id, name",
								"id"	=>	"id",
								"module"	=>	"module",
								"moduleId"	=>	"moduleId");

	protected $parameters = array();
	
	protected $onChangeMethod;

	
	public function __construct($name, $class, $method){
		$this->name = $name;	
		$this->setDataType("json");
		$this->setController("index.php?c=ajax");
		$this->setResponseClass($class);
		$this->setResponseMethod($method);
		$this->addParameter("request.term");
		$this->setTxtValue($this->getName());
	}
	
	
	public function render($minLength =0){
		
		$jsScript = '
		$( "#'.$this->getName().'" ).button({}).click(function() {$(this).val("");})
        .autocomplete({
	        autoFocus: true,
	        delay: 300,
	        minLength: '.$minLength.',
			source: function( request, response ) {
				$.ajax({
					url: "'.$this->getController().'",
					dataType: "'.$this->getDataType().'",
					data: {
						loadClass: "'.$this->getResponseClass().'",
						method: "'.$this->getResponseMethod().'",
						maxRows: 12,
						parameter: "text:"+'.implode('+ "," +', $this->getParameters()).'
					},
					success: function( data ) {
						if(null!= data){ 
							response( $.map( data, function( item ) {
								return { ';
		
								foreach ($this->getMapedData() as $key => $value){
									$map .= "$key:";
									$valueArray = explode(",", $value);
									foreach($valueArray as $value){
										$map.="item.$value + ', ' +";
									}
									$map = substr($map, 0, -8).", \r\n";
								}
								
								$map = rtrim(trim($map),",");
								
		$jsScript.=$map.'			};
							}));
						} 	
					}
				});
			} ,
			change: function( event, ui ){
				if ( !ui.item ) {
					$(this).val("'.$this->getTxtValue().'"); '
					.$this->getOnChangeMethod().
					'
				}else{
					'.$this->getOnChangeMethod().' 
				}
			},
			select: function(event, ui) { '
				.$this->getOnChangeMethod().
			' }
		})
        .next()
		.button( {
				text: false,
				icons: {
					primary: "ui-icon-triangle-1-s"
				}
		})
		.click(function() {
				
			// give focus to the autocomplete field
			$("#'.$this->getName().'").focus();
				
			// set the autocomplete value to null
			$("#'.$this->getName().'").val("");
				
			// search with an empty parameter (load everything)
			$("#'.$this->getName().'").autocomplete( "search", "" );								
		})
			// link the autocomplete and the button in a buttonset	
		.parent().buttonset();';
		
		// call the setScript method
		$this->setJscript($jsScript);
		
		echo $this->getJscript().'<div class="toolbar-menu" style="float:left;padding-right: 5px;">
		<input type="text" class="textGrey" id="'.$this->getName().'" value="'.$this->getTxtValue().'"/>
		<button id="'.$this->getName().'Button">'.ucfirst($this->getName()).'</button>
		</div>';
		
	}
	
	public function renderSimple( $sourceArray=array()){
	
		$jsArray = join("\", \"", $sourceArray);
	
		$this->setJscript(
				'
				var rankGroups = ["'.$jsArray.'"];
				$( "#'.$this->getName().'" ).button({}).click(function() {$(this).val("");})
				.autocomplete({
				autoFocus: true,
				delay: 0,
				minLength: 0,
				source: rankGroups,
				change: function( event, ui ){
				if ( !ui.item ) {
				$(this).val(""); '
				.$this->getOnChangeMethod().
				'
	}else{
				'
				.$this->getOnChangeMethod().
				'
	}
	},
				select: function(event, ui) { '
				.$this->getOnChangeMethod().
				' }
	})
				.next()
				.button( {
				text: false,
				icons: {
				primary: "ui-icon-triangle-1-s"
	}
	})
				.click(function() {
				$("#'.$this->getName().'").autocomplete( "search", "" );
	})
				.parent().buttonset();
	
				');
	
		echo $this->getJscript().'<div class="toolbar-menu" style="float:left;padding-right: 5px;">
		<input type="text" id="'.$this->getName().'" value="'.$this->getTxtValue().'"/>
		<button id="'.$this->getName().'Button">'.ucfirst($this->getName()).'</button>
		</div>';
	
	
	}
	
	
	
	public function datePicker($script){
		$jsScript = '
		$( "#'.$this->getName().'" ).button({}).click(function() {})
		.datepicker('.
			$script
		.')
		.next()
		.button( {
			text: false,
			icons: {
				primary: "ui-icon-triangle-1-s"
			}
		})
		.click(function() {
			$("#'.$this->getName().'").datepicker("show");
		})
		.parent().buttonset();';
	
		$this->setJscript($jsScript);
	
		echo $this->getJscript().'<div class="toolbar-menu" style="float:left;padding-right: 5px;">
		<input type="text" id="'.$this->getName().'" value="'.$this->getTxtValue().'"/>
		<button id="'.$this->getName().'Button">'.ucfirst($this->getName()).'</button>
		</div>';
	
		//var_dump($map);
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
	public function setMapedData($mapedData) {
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
	
	/**
	 * @return the $onChangeMethod
	 */
	public function getOnChangeMethod() {
		return $this->onChangeMethod;
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
		$this->txtValue = ucfirst($txtValue);
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
	 * @param field_type $onChangeMethod
	 */
	public function setOnChangeMethod($onChangeMethod) {
		$this->onChangeMethod = $onChangeMethod;
	}
	
	public function setJscript($jScript){	
		$this->jscript.= $jScript;
	}
	
}

?>