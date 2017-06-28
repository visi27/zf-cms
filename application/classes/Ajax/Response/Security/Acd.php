<?php

/**
 *
 * @author Administrator
 *        
 */
class Ajax_Response_Security_Acd extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
				
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// Toolbar - Structure selection
		$forceModuleData = $moduleTable->selectModules(array( "form_name" => array("=" => "Force")), null, $limit = 1);
		
		// Toolbar - Structure Autocomplete Filter
		$structureAutocomplete = new Utility_Autocomplete("filterStruct", "Ajax_Response_Utility", "getStructures");
		
		// Toolbar - Function Autocomplete Filter
		$functionAutocomplete = new Utility_Autocomplete("filterFunction", "Ajax_Response_Utility", "getPositionNames");
		$functionAutocomplete->addParameter("\"{$structureAutocomplete->getName()}:\" + $(\"#{$structureAutocomplete->getName()}\").val()");
		
		$structureAutocomplete->setOnChangeMethod("$('#filterFunct').val('{$functionAutocomplete->getDefaultText()}');");
		$structureAutocomplete->setDefaultText("Zgjidhni Strukturen");
		$structureAutocomplete->setMapedData(
				$mapedData = array("label"	=> "name",
						"value"	=>	"name"));
		
		$functionAutocomplete->setDefaultText("Zgjidhni Funksionin");
		$functionAutocomplete->setMapedData(
				$mapedData = array("label"	=> "name",
						"value"	=>	"name"));
		
		// Toolbar - Force Autocomplete Filter
		$forceAutocomplete = new Utility_Autocomplete("filterForce", "Ajax_Response_Utility", "getAllForces");
		$forceAutocomplete->addParameter("\"moduleId:\" + \"{$forceModuleData->current()->id}\"");
		$forceAutocomplete->setDefaultText("Zgjidhni Forcen");
		$forceAutocomplete->setMapedData(
				$mapedData = array("label"	=> "force_code",
						"value"	=>	"force_code"));
		
		// Toolbar - Access Right Autocomplete Filter
		$accessAutocomplete = new Utility_Autocomplete("filterAccess", "Ajax_Response_Utility", "getAccessRights");
		$accessAutocomplete->setDefaultText("Zgjidhni te drejtat");
		$accessAutocomplete->setMapedData(
				$mapedData = array("label"	=> "label",
						"value"	=>	"action"));
		
		// Toolbar - Access Right Autocomplete Filter
		$ruleAutocomplete = new Utility_Autocomplete("filterRule", "Ajax_Response_Utility", "getRuleOptions");
		$ruleAutocomplete->setDefaultText("Zgjidhni rregullin");
		$ruleAutocomplete->setMapedData( $mapedData = array("label"	=> "label",
											"value"	=>	"rule"));
		
		$elements = array(	"zf" 	=> 	array(
							"acd_id", "acd_role_id", "structure", "force_code", "struc_code",
							array(	"name"	=>	"action",
									"type"	=>	"select"),
							array(	"name"	=>	"rule",
									"type"	=>	"select")
							),
							"alias"	=> 	array(
										"acd_id", "Pozicioni", "Forca", "Njesia",
										"Veprimi", "Rregulli"
							)
		);
		
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>true, "edit"=>false, "delete"=>true), "security");
		
		$myGrid->addFilter($structureAutocomplete, $dbColumn = "r.structure_name", " = ");
		$myGrid->addFilter($functionAutocomplete, $dbColumn = "r.function_name", " = ");
		$myGrid->addFilter($forceAutocomplete, $dbColumn = "sys_acd.force_code", " = ");
		$myGrid->addFilter($accessAutocomplete, $dbColumn = "sys_acd.action", " = ");
		$myGrid->addFilter($ruleAutocomplete, $dbColumn = "sys_acd.rule", " = ");
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
						
		$myGrid->addAutoComplete("structure", "Ajax_Response_Utility", "getAcdRoleName",
				array("value" => 'name',
						"id" => 'name',
						"label" => 'name'), 0,  $clearOnExit=true, $minLength=2);

		$myGrid->addAutoComplete("function", "Ajax_Response_Utility", "getAcdRoleFunctionName",
				array("value" => 'name',
						"id" => 'id',
						"label" => 'name'),
				'structure:" + $( "#'.$myGrid->getZendFormId().' input[id=\'structure\']" ).val()+"',
				$clearOnExit=true, $minLength=0, ',select: function( event, ui ) {
					//set the location code
					$( "#'.$myGrid->getZendFormId().' input[id=\'acd_role_id\']" ).val(ui.item.id);
		}');
		
		// add an autocomplete field to the zend form
		$myGrid->addAutoComplete("force_code", "Ajax_Response_TopWebServices", "getForceByNumberExt",
				array("value" => 'force_number+" - "+item.force_code',
						"id" => 'force_number',
						"label" => 'force_number+" - "+item.force_code'),
				0, $clearOnExit=true, $minLength=0);
		
		// Toolbar - Structure selection
		
		$myGrid->addAutoComplete("struc_code", "Ajax_Response_TopWebServices", "getStructByForce",
				array("value" => 'full_struc_code,name_al',
						"id" => 'full_struc_code',
						"label" => 'full_struc_code,name_al'),
				'force:" + $( "#'.$myGrid->getZendFormId().' input[id=\'force_code\']" ).val()+"', 
				$clearOnExit=false, $minLength=0);
		
		$myGrid->setScript('$( "#'.$myGrid->getZendFormId().' input[id=\'structure\']" ).autocomplete({
				open: function(event, ui) {
				$( "#'.$myGrid->getZendFormId().' input[id=\'function\']" ).val("");
				$( "#'.$myGrid->getZendFormId().' input[id=\'acd_role_id\']" ).val("");
		}
		});');
		
		$myGrid->hideColumn("acd_id");	
		
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