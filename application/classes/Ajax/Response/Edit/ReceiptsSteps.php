<?php
class Ajax_Response_Edit_ReceiptsSteps extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
	    if(isset($_SESSION['receipt']['selected'])){
	        
	        $receipt_obj = new Table_Receipts();
	        $receipt = $receipt_obj->getDataById($_SESSION['receipt']['selected']);
	        
    		// the module id of this report
    		$module = $this->_params["moduleId"];
    		
    		$elements =	array("zf" 	=> 	array("step_nr",  array("name"=>"step_instructions", "type"=>"textarea")),
    						//"db" 	=> 	array("id_award", "award_name", "order_nr", "order_date", "awards_type"), //always start with the primary key
    						  "alias"=> array("ID","Nr. Rendor","Instruksionet"));
    		
    		$myGrid = new Utility_Grid($module, $this->_action, $elements, $ajax=true);
    	
    				
    		// select method to generate the grid resultset
    		$myGrid->setSelectMethod("selectRowsForGrid");
    	
    		// method to be called when retrieving row information for edit
    		$myGrid->setSelectRowDetails("selectRowForGrid");
    		
    		$myGrid->setSelectedElement($this->_params["itemFound"]);
    	
    		// hide the table header called Id
    		$myGrid->hideColumn("ID","id_td");	
  		
    		
    		$header = $this->renderBreadCrumb($receipt->title);
    		
    		$output = $header.$myGrid->render();
    
    		echo $output;
	    }else{
	        echo "Zgjidhni nje recete paraprakisht";
	    }
				
	}
}