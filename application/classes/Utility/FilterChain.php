<?php

/**
 * @author Administrator
 *
 */
class Utility_FilterChain extends Utility_Autocomplete {
	
	protected $chainName = "myChain";
	
	protected $chainIndex = 0;
		
	protected $filters = array();
	
	protected $responseController="edit";
	
	public function __construct($chainName, $responseController){
		$this->setChainName($chainName);
		$this->responseController = $responseController;
	}
	
	/**
	 * 
	 * @param Utility_Autocomplete $filterObject
	 * @param Integer $chainIndex
	 */
	public function addFilter($filterObject, $chainIndex = null, $required = false){
		
		// check the new chain index
		if(empty($chainIndex) //empty or null values
				|| intval($chainIndex)== 0 // non valid integer
					|| in_array($chainIndex, array_keys($this->filters))) // existing key index
		{
			// find the max element from the filters
			$maxElement =  array_pop( array_keys($this->getFilters()) );
			// increment it by one and set it
			$this->setChainIndex($maxElement+1);
		}
		
		// set the chain index
		$this->setChainIndex($chainIndex);
		
		// set the new filter element into the stack
		$this->filters[$this->getChainIndex()] = array("name" => $filterObject->getName(),
														"obj" => $filterObject,
														"req" => $required);
		// keep the stack sorted ascending
		ksort($this->filters);
	}
	
	/**
	 * @return the $filter
	 */
	public function getFilterByIndex($index) {
		return $this->filters[$index]['obj'];
	}
	
	/**
	 * @return the $filter
	 */
	public function getFilterByName($filterName) {
		$chainIndex = array_search($filterName, $this->getFilters());
		return $this->getFilterByIndex($filterName);
	}
	
	/**
	 * @return the $filters
	 */
	public function getFilters() {
		return $this->filters;
	}

	/**
	 * @return the $chainName
	 */
	public function getChainName() {
		return $this->chainName;
	}
	
	/**
	 * @param string $chainName
	 */
	private function setChainName($chainName) {
		$this->chainName = $chainName;
	}
	
	/**
	 * @return the $chainIndex
	 */
	public function getChainIndex() {
		return $this->chainIndex;
	}
	
	/**
	 * @param number $chainIndex
	 */
	private function setChainIndex($chainIndex) {
		$this->chainIndex = intval($chainIndex);
	}
	
	/**
	 * @return the jquery code for grabbing all filter's name:value
	 */
	public function getChainJqueryValues(){
		// the parameter delimiter
		$delimiter = Utility_Functions::parDelimiter();
		
		// the html container
		$htmlContainer = " \"{$delimiter}filterSubmit:true\"";
			
		// for every filter
		foreach($this->getFilters() as $chainIndex => $filter){
			// get the object
			$filter = $filter['obj'];
			// create the jquery script
			$htmlContainer .= " + \"$delimiter{$filter->getName()}:\" + $(\"#{$filter->getName()}\").val()";
			
		}
		
		return $htmlContainer ;
	}
	
	//As above but added parameter to show that we are requesting HTML output
	public function getChainJqueryValuesHTMLReport(){
		// the parameter delimiter
		$delimiter = Utility_Functions::parDelimiter();
	
		// the html container
		$htmlContainer = " \"{$delimiter}filterSubmit:true\"";
		$htmlContainer .=" + \"{$delimiter}filterSubmitHTMLReport:true\"";
		// for every filter
		foreach($this->getFilters() as $chainIndex => $filter){
			// get the object
			$filter = $filter['obj'];
			// create the jquery script
			$htmlContainer .= " + \"$delimiter{$filter->getName()}:\" + $(\"#{$filter->getName()}\").val()";
				
		}
	
		return $htmlContainer ;
	}
	
	/**
	 * @return the jquery code for emptying all filter's 
	 */
	public function resetChainJqueryValues(){
	
		// the html container
		$htmlContainer = "";
	
		// the parameter delimiter
		$delimiter = Utility_Functions::parDelimiter();
	
		// for every filter
		foreach($this->getFilters() as $chainIndex => $filter){
			
			// get the object
			$filter = $filter['obj'];
			
			// create the jquery script
			$htmlContainer .= "$(\"#{$filter->getName()}\").val('{$filter->getDefaultText()}');";
						
		}
	
		return $htmlContainer;
	}
	
	/**
	 * Create the Html content for every filter object.
	 * @return string
	 */
	public function renderFilters(){
		$htmlContainer = "";
		
		$greaterThan = array_keys($this->getFilters());
		
		$smallerThan = array();
		
		foreach($this->getFilters() as $chainIndex => $filter){
			
			// get the object 
			$filter = $filter['obj'];
			
			// shift the first element out of the array
			$element = array_shift($greaterThan);		
			
			// higher hirearchy filters (include as parameters)
			foreach($smallerThan as $key => $chain){
				$filterObj = $this->getFilterByIndex($chain);
				//add parameters
				$filter->addParameter("\"{$filterObj->getName()}:\" + $(\"#{$filterObj->getName()}\").val()");
			}
			
			// depending filters (empty on change)
			foreach($greaterThan as $key => $chain){
				$filterObj = $this->getFilterByIndex($chain);
				//same chain, depended autocomplete object	
				if ($filterObj->isDependent)	
					$filter->setOnChangeMethod("$(\"#{$filterObj->getName()}\").val('{$filterObj->getDefaultText()}');");
			}
			
			// push element into the $smallerThan stack
			array_push($smallerThan, $element);	
			
			$htmlContainer .= $filter->render();
			/*
			
			$object->setOnChangeMethod("$(\"#reportTypeContainer\").hide();");
			$object->setOnChangeMethod("$(\"#reportContent\").hide();");
			*/
		}
		
		return $htmlContainer.$this->theFilterButton().$this->theFilterHTMLReportButton().$this->theResetButton("");
	}
	
	public function theFilterButton(){
		$buttonHtml = HtmlView::displayButton("toolbarBttn-".rand(), "Printo",
			array(	"primIcon"	=>	"ui-icon-print",
					"showtext" => "false",
					"click" => '
					
						$(document).ajaxStart(
							Metronic.blockUI({
           	 					boxed: true
            				})
						).ajaxStop(Metronic.unblockUI());
		
						$.ajax({
							type: "GET",
							url: "index.php?c=ajax",
							data: {
								loadClass: "Ajax_Response_'.ucfirst($this->responseController).'",
								method: "'.$this->getChainName().'",
								parameter: '.$this->getChainJqueryValues().'
							},
							success: function(response){
								 if("'.$this->getChainName().'"=="ForecastReport"){
						          $( "#dialog-viewReport" ).html(response);
						        }
						      else {
								$(".page-content-wrapper .page-content").html(response);
						           }
							}
						});'
			)
		);
		return $buttonHtml;
	}
	
	public function theFilterHTMLReportButton(){
		$buttonHtml = HtmlView::displayButton("toolbarBttn-".rand(), "Shfaq",
				array(	"primIcon"	=>	"ui-icon-search",
						"showtext" => "false",
						"click" => '
			
						$(document).ajaxStart(
							Metronic.blockUI({
           	 					boxed: true
            				})
						).ajaxStop(Metronic.unblockUI());
	
						$.ajax({
							type: "GET",
							url: "index.php?c=ajax",
							data: {
								loadClass: "Ajax_Response_'.ucfirst($this->responseController).'",
								method: "'.$this->getChainName().'",
								parameter: '.$this->getChainJqueryValuesHTMLReport().'
							},
							success: function(response){
						        if("'.$this->getChainName().'"=="ForecastReport"){
						          $( "#dialog-viewReport" ).html(response);
						        }
						      else {
								$(".page-content-wrapper .page-content").html(response);
						           }
							}
						});'
				)
		);
		return $buttonHtml;
	}
	
	public function theResetButton($script=""){
		$buttonHtml = HtmlView::displayButton("refreshBttn-".rand(), "Rifresko",
			array(	"icon"	=>	"ui-icon-refresh",
					"showtext" => "false",
					"click" => $this->resetChainJqueryValues().$script				
				)
		);
		return $buttonHtml;	
	}
}
?>