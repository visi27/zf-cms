<?php

class Ajax_Response_Edit_RptTotalsUsersByCategory extends Ajax_Response_Abstract{

	private $filterChain;
	
	private $moduleName;
	
	private $birtQueryString = array();
	
	public function _init(){
		$moduleName = explode("_", __CLASS__);
		$this->moduleName = array_pop($moduleName);
	}
	
	public function layoutCenter_Action(){
		//display security report
		
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// the filter chain container object
		$this->setFilterChain(new Utility_FilterChain($this->moduleName, 'edit'));
		
		
		//Date From Filter
		$dateFromAutocomplete = new Utility_AutocompleteDate("filterDateFrom","yy/mm/dd");
		$dateFromAutocomplete->setDefaultText("Nga Data");
		$this->birtQueryString[$dateFromAutocomplete->getName()] = "dateFrom";
		//add the dateFrom filter into the chain
		$this->getFilterChain()->addFilter($dateFromAutocomplete, $index = 1, $required = false );
		//END Date From Filter
		
		//Date To Filter
		$dateToAutocomplete = new Utility_AutocompleteDate("filterDateTo","yy/mm/dd");
		$dateToAutocomplete->setDefaultText("Ne Daten");
		$this->birtQueryString[$dateToAutocomplete->getName()] = "dateTo";
		//add the dateFrom filter into the chain
		$this->getFilterChain()->addFilter($dateToAutocomplete, $index = 2, $required = false );
	    //END Date To Filter
				
		//Render The Filters
		echo "<div class='filterWrap'>".
				$this->getFilterChain()->renderFilters().
				"</div>";
		
		// strip the parameters from the text part and keep only the idPart
		$filterData = Utility_Functions::cleanArgsValue($this->_params);
		
		// validate filter's data
		foreach ($this->getFilterChain()->getFilters() as $filter){
		
			//a filter content is missing
			if( $this->_params[$filter["name"]] == "" OR
					$this->_params[$filter["name"]] == $filter["obj"]->getDefaultText() ){
		
				// the filter content is required for the report
				if( $filter['req'] == true ){
					$missingFilterData = true;
				}
		
			}else{ //a filter content is Ok
			    if($filter["name"]=="filterDateFrom" or $filter["name"]=="filterDateTo"){
			        //Convert date to yyyy-mm-dd
			        $filterData[$filter["name"]] = str_replace("/", "-", $filterData[$filter["name"]]);
			    }
				$queryString .= "&".$this->birtQueryString[$filter["name"]]."=".$filterData[$filter["name"]];
			}
		}
		
		// if all filters are valid
		if (  $this->_params['filterSubmit'] AND !$missingFilterData ){
		
			echo Ajax_Response_Report::loadReport("shije/rptTotalsUsersByCategory.rptdesign", $queryString);
		}
	}
	
	/**
	 * @return the $filterChain
	 */
	public function getFilterChain() {
		return $this->filterChain;
	}

	/**
	 * @param field_type $filterChain
	 */
	public function setFilterChain($filterChain) {
		$this->filterChain = $filterChain;
	}
}

?>