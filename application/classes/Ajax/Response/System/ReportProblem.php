<?php

class Ajax_Response_System_ReportProblem extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// Toolbar Initializations
	
		$structureAutocomplete = new Utility_Autocomplete("filterStatus", "Ajax_Response_Utility", "getProblemStatuses");
		// Toolbar - Structure Autocomplete Filter
		
		$structureAutocomplete->setDefaultText("Statusi");
		$structureAutocomplete->setMapedData(
				$mapedData = array("label"	=> "status",
						"value"	=>	"status"));
		
		
	$elements =
		array("zf" 	=> 	array("problem_id", "module", "field","description","fullname","reportDate", "problem_status"),
			  "alias"=> array("problem_id","Moduli","Fusha", "Pershkrimi","Emri i perdoruesit","Data e raportimit", "Status"));
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>false, "edit"=>false, "delete"=>false), "system");
		
		
		$myGrid->addFilter($structureAutocomplete, $dbColumn = "sys_report_problem.problem_status", " = ");
		
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
		$myGrid->hideColumn("problem_id");
				
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