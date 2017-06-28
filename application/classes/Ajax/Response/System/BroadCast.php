<?php

class Ajax_Response_System_BroadCast extends Ajax_Response_Abstract {
	
	protected function layoutCenter_Action() {
		
		//load the modules table object
		$moduleTable = new Table_Modules();
		
		
	$elements =
		array("zf" 	=> 	array("id", "title_al","title_en", "body_al", "body_en", array( "name"	=>	"display", "type"	=>	"select" )),
			  "alias"=> array("id","Titulli", "Title en","Pjesa Kryesore","Body en",  "Shfaqja","Data" ,"Krijuar Nga"));
		
		$myGrid = new Utility_Grid($this->_params["moduleId"], $this->_action, $elements, $ajax = true,
				array("new"=>true, "edit"=>true, "delete"=>true), "system");
		
		
		// allow smartsearch rendering, call this method to smartsearch
		$myGrid->setSelectedElement($this->_params["itemFound"]);
		// method to be called when retrieving row information for edit
		$myGrid->setSelectRowDetails("selectRowForGrid");
		$myGrid->hideColumn("id");
				
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