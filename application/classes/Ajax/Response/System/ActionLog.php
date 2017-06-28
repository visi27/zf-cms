<?php

class Ajax_Response_System_ActionLog extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		// Toolbar Initializations
	
		$userAutocomplete = new Utility_Autocomplete("filterUsers", "Ajax_Response_Utility", "getUser");
		
		
		$userAutocomplete->setDefaultText("Perdoruesi");
		$userAutocomplete->setMapedData(
				$mapedData = array("label"	=> "id, username",
						"value"	=>	"username",
						"id"	=>	"id"));
		
		
		$actionAutocomplete = new Utility_Autocomplete("filterTbl", "Ajax_Response_Utility", "getDbTables");
		
		
		$actionAutocomplete->setDefaultText("Tabelat");
		$actionAutocomplete->setMapedData(
				$mapedData = array("label"	=> "name",
						"value"	=>	"name"));
		
		// Toolbar - Date From
		$stDate = new Utility_Autocomplete("from", "Ajax_Response_Utility", "void");
		$stDate->setClearOnExit(false);
		$stDate->setDefaultText("Start Date");
		
		// Toolbar - Date To
		$endDate = new Utility_Autocomplete("to", "Ajax_Response_Utility", "void");
		$endDate->setClearOnExit(false);
		$endDate->setDefaultText("End Date");
		
	$elements =
		array(//"zf" 	=> 	array("id", "module", "field","description","fullname","reportDate", "problem_status"),
			  "alias"=> array("id","Db Tablename","Row Id", "Action Type","Username","TimeStamp","Stack"));
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>false, "edit"=>false, "delete"=>false), "system");
		
		
		$myGrid->addFilter($userAutocomplete, $dbColumn = "username", " = ");
		$myGrid->addFilter($actionAutocomplete, $dbColumn = "table_name", " = ");
		$myGrid->addFilter($stDate, $dbColumn = "timestamp", " > ");
		$myGrid->addFilter($endDate, $dbColumn = "sys_action_log.timestamp", " < ");
		
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		
		$myGrid->hideColumn("data_stack");
		$myGrid->hideColumn("id");
		//define columns for the tooltip
		$myGrid->setTooltipCols(array("data_stack"));
		$myGrid->setOrderByColumn("timestamp");
		$myGrid->setSortDir("desc");
		echo $myGrid->render();
		echo '<script>
				$(function() {
					var dates = $( "#from, #to" ).button({}).datepicker({
						defaultDate: "+1w",
						changeMonth: true,
						numberOfMonths: 2,
						dateFormat: "yy/mm/dd",
						minDate: -180,
						maxDate: "+3M +10D",
						onSelect: function( selectedDate ) {
							var option = this.id == "from" ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings
							);
							dates.not( this ).datepicker( "option", option, date );
						}
					});
				});
			</script>';
		
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