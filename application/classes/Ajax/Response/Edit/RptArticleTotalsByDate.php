<?php

class Ajax_Response_Edit_RptArticleTotalsByDate extends Ajax_Response_Abstract{

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
		
		// construct the user filter object
		$userAutocomplete = new Utility_Autocomplete("filterUser", "Ajax_Response_Utility", "getUsers");
		$userAutocomplete->setDefaultText('Zgjidh Gazetarin');
		$userAutocomplete->setTxtValue( $this->_params[$userAutocomplete->getName()] != null ? $this->_params[$userAutocomplete->getName()]:$userAutocomplete->getDefaultText() );
		$this->birtQueryString[$userAutocomplete->getName()] = "userId";
		$userAutocomplete->setMapedData(array("label"	=> "id,username", "value"	=>	"id,username"));
		//add the force filter into the chain
		$this->getFilterChain()->addFilter($userAutocomplete, $index = 1, $required = false );
		
		//Date From Filter
		$dateFromAutocomplete = new Utility_AutocompleteDate("filterDtFrom","yy/mm/dd");
		$dateFromAutocomplete->setDefaultText("Nga Data");
		$dateFromAutocomplete->setTxtValue( $this->_params[$dateFromAutocomplete->getName()] != null ? $this->_params[$dateFromAutocomplete->getName()]:$dateFromAutocomplete->getDefaultText() );
		$this->birtQueryString[$dateFromAutocomplete->getName()] = "dtFrom";
		//add the dateFrom filter into the chain
		$this->getFilterChain()->addFilter($dateFromAutocomplete, $index = 2, $required = false );
		//END Date From Filter
		
		//Date To Filter
		$dateToAutocomplete = new Utility_AutocompleteDate("filterDtTo","yy/mm/dd");
		$dateToAutocomplete->setDefaultText("Ne Daten");
		$dateToAutocomplete->setTxtValue( $this->_params[$dateToAutocomplete->getName()] != null ? $this->_params[$dateToAutocomplete->getName()]:$dateToAutocomplete->getDefaultText() );
		$this->birtQueryString[$dateToAutocomplete->getName()] = "dtTo";
		//add the dateFrom filter into the chain
		$this->getFilterChain()->addFilter($dateToAutocomplete, $index = 3, $required = false );
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
			    if($filter["name"]=="filterDtFrom" or $filter["name"]=="filterDtTo"){
			        //Convert date to yyyy-mm-dd
			        $filterData[$filter["name"]] = str_replace("/", "-", $filterData[$filter["name"]]);
			    }
			    
			    if($filter["name"]=="filterUser"){
			        //Get username and pass it as a parameter to the report
			        $userObj = new Table_Users();
			        $user = $userObj->getDataById($filterData[$filter["name"]]);
			        
			        //add username to query string
			        $queryString .= "&userName=".$user->username;
			        
			    }
				$queryString .= "&".$this->birtQueryString[$filter["name"]]."=".$filterData[$filter["name"]];
			}
		}
		
		// if all filters are valid
		if (  $this->_params['filterSubmit'] AND !$missingFilterData ){
		
			echo Ajax_Response_Report::loadReport("shije/rptArticleTotalsByDate.rptdesign", $queryString);
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