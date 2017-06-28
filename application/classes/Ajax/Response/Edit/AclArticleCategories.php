<?php
class Ajax_Response_Edit_AclArticleCategories extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
		
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		$elements =	array("zf" 	=> 	array("username", "category", array("name" => "read", "type" => "select"), array("name" => "write", "type" => "select")),
						//"db" 	=> 	array("id_award", "award_name", "order_nr", "order_date", "awards_type"), //always start with the primary key
						  "alias"=> array("ID","Perdoruesi","Kategoria e Artikujve","Lexim","Shkrim"));
		
		$myGrid = new Utility_Grid($module, $this->_action, $elements, $ajax=true);
	
		// select method to generate the grid resultset
		$myGrid->setSelectMethod("selectRowsForGrid");
	
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
	
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		
		//User Filter
		$usersAutocomplete = new Utility_Autocomplete("filterUser", "Ajax_Response_Utility", "getUsers");
		
		$usersAutocomplete->setDefaultText("Përdoruesi");
		$usersAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, username",
		        "value"	=>	"id, username",
		        "id" => 'id'));
		$myGrid->addFilter($usersAutocomplete, $dbColumn = "sys_acl_article_categories.user", " = ");
		//END User Filter
		
		//Category Filter
		$categoryAutocomplete = new Utility_Autocomplete("filterCategory", "Ajax_Response_Utility", "getBlogCategories");
		
		$categoryAutocomplete->setDefaultText("Kategoria");
		$categoryAutocomplete->setMapedData(
		    $mapedData = array("label"	=> "id, title",
		        "value"	=>	"id, title",
		        "id" => 'id'));
		$myGrid->addFilter($categoryAutocomplete, $dbColumn = "sys_acl_article_categories.category", " = ");
		//END Category Filter
		
		$myGrid->addAutoComplete("user", "Ajax_Response_Utility", "getUsers",
		    array(	"value" => 'id, username',
		        "id" => 'id',
		        "label" => 'username'),
		    0, $clearOnExit=true, $minLength=0);
		
		$myGrid->addAutoComplete("category", "Ajax_Response_Utility", "getBlogCategories",
		    array(	"value" => 'id, title',
		        "id" => 'id',
		        "label" => 'title'),
		    0, $clearOnExit=true, $minLength=0);
		
		// hide the table header called Id
		$myGrid->hideColumn("ID","id_td");	
		
		$header = $this->renderBreadCrumb();
		
		$output = $header.$myGrid->render();
				
		echo $output;
				
	}
}