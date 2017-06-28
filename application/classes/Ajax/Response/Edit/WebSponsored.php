<?php
class Ajax_Response_Edit_WebSponsored extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
	        
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		$elements =	array("zf" 	=> 	array("type", "element_id", "section_id", "start_date", "end_date"),
						//"db" 	=> 	array("id_award", "award_name", "order_nr", "order_date", "awards_type"), //always start with the primary key
						  "alias"=> array("ID","Tipi i Elementit", "Titulli", "Data e Fillimit", "Data e Mbarimit", "Seksioni"));
		
		$myGrid = new Utility_Grid($module, $this->_action, $elements, $ajax=true);
	
				
		// select method to generate the grid resultset
		$myGrid->setSelectMethod("selectRowsForGrid");
	
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
		
		$myGrid->setSelectedElement($this->_params["itemFound"]);
	
		// hide the table header called Id
		$myGrid->hideColumn("ID","id_td");	
		
		//Section Filter
		$categoryAutocomplete = new Utility_Autocomplete("filterCategory", "Ajax_Response_Utility", "getSections");
		
		$categoryAutocomplete->setDefaultText("Seksioni i Artikullit");
		$categoryAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, title",
		        "value"	=>	"id, title",
		        "id" => 'id'));
		$myGrid->addFilter($categoryAutocomplete, $dbColumn = "web_sponsored.section_id", " = ");
		//END Section Filter
			
		$myGrid->addAutoComplete("type", "Ajax_Response_Utility", "getFeaturedElementTypes",
				array(	"value" => 'id, name',
						"id" => 'id',
						"label" => 'name'),
				0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("element_id", "Ajax_Response_Utility", "getElementTitle",
		    array(	"value" => 'id, title',
		        "id" => 'id',
		        "label" => 'title'),
		    'type:" + $( "#'.$myGrid->getZendFormId().' input[id=\'type\']" ).val() +"',
		    $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("section_id", "Ajax_Response_Utility", "getSections",
		    array(	"value" => 'id, title',
		        "id" => 'id',
		        "label" => 'title'),
		    0, $clearOnExit=true, $minLength=0);
		
		
		$header = $this->renderBreadCrumb();
		
		$output = $header.$myGrid->render();

		echo $output;
		  

		
		
				
	}
}