<?php

class Ajax_Response_Security_AcdRole extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// Toolbar Initializations
	
		$structureAutocomplete = new Utility_Autocomplete("filterStruct", "Ajax_Response_Utility", "getStructures");
		// Toolbar - Structure Autocomplete Filter
		
		$structureAutocomplete->setDefaultText("Zgjidhni Strukturen");
		$structureAutocomplete->setMapedData(
				$mapedData = array("label"	=> "name",
						"value"	=>	"name"));
		
		
	$elements =
		array("zf" 	=> 	array("acd_role_id", "structure_name", "function_name", array("name"	=>	"rank_group", "type" =>	"select")),
			  "alias"=> array("acd_role_id", "Struktura", "Funksioni", "Grada"));
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>true, "edit"=>true, "delete"=>true), "security");
		
		
		$myGrid->addFilter($structureAutocomplete, $dbColumn = "sys_acd_roles.structure_name", " = ");
		
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
		$myGrid->hideColumn("acd_role_id");
				
		echo $myGrid->render();
		
	}
	protected function layoutEast_Action(){
	
		//clipboard for displaying actions
		$cp_html =
		'<div class=\"group\" id=\"action_clipboard\" style=\"display:none\">'.
		'<label><span id =\"lbl_action\" ></span> Actions</label>'.
		'<div id=\"action_clip_content\">'.
		'<div id=\"action_1\">'.
		'<span id =\"action_1_title\" style=\"font-weight:bold;\"></span><br/>'.
		'<span id =\"action_1_txt\" ></span>'.
		'</div>'.
		'</div>'.
		'</div>';
		//end of clipboard-->
	
		//customize default accordion
		echo '
		<script>
			$("'.$cp_html.'").insertAfter("#technical_support");
		</script>';
	
		$accordion = new Utility_Accordion();
		$default_accordion = $accordion->loadDefault();
		echo $default_accordion;
	}
	}
	
	?>