<?php

abstract class Ajax_Response_Abstract {
	
	// methods suffix
	protected $_suffix = "_Action";
	
	protected $_action = "default";
	
	protected $_params = array();
	
	protected function _init(){}
	
	public function __construct(array $parameters, $action = null){
		//print_r($parameters);
		

		// get the parameters in a beautiful shape
		$this->_params  = Utility_Functions::argsToArray($parameters);
		
		foreach($this->_params as $key => $value){
			$this->_params[$key] = html_entity_decode($value, ENT_COMPAT);
		}
		
		// get the current action (default/submit)
		$this->_action = empty($action)?$this->_action:$action;
		
		// initialise the child class
		$this->_init();
		
		// the method name being requested
		$methodToCall = $this->_params['ajaxAction'] . $this->_suffix;
		
		// check if the method being requested exists
		if(method_exists($this, $methodToCall)){
				
			// the method exists, call it
			$this->{ $methodToCall }();
				
		}else{
			// the method does not exist, call the default method
			$this->layoutCenter_Action();
		}

	}
	
	protected function layoutCenter_Action(){}
	
	protected function renderBreadCrumb($extraInfo = ""){
		$moduleId = $this->_params["moduleId"];
		$modules = new Table_Modules();
		$module = $modules->getDataById($moduleId);
		
		$breadCrumbList ='<ul class="page-breadcrumb">';
		$breadCrumbList .= $this->getBreadCrumbList($this->_params["moduleId"], true);
		
		if ($extraInfo==""){
		    $printExtra = "";
		}else{
		    $printExtra = " (".$extraInfo.")";
		}
		
		$html = '<h3 class="page-title">'.$module->name_al.$printExtra.'</h3>
				<div class="page-bar">';
		
		$html .= $breadCrumbList;
		
		$html .= '</div>';
		
		// the default controller name inside the Main Controller
		$defaultController = "edit";
		// the current controller name being requested
		$controller = isset($_GET['m'])?htmlentities($_GET['m']) : $defaultController;
		
		$html.= '<form method="post" name="frmPost" id="frmPost" action="index.php?m='.$controller.'">
					
					<input id="treeNodeId" type="hidden" name="treeNodeId" value="'.$moduleId.'"/>
					<input id="breadcrumb_submit" type="hidden" name="breadcrumb_submit" value="1"/>
					</form>
					
					<script>
						jQuery( document ).ready(function() {
    						jQuery(".breadcrumb_click").click(function(){
								jQuery("#frmPost>input#treeNodeId").val($(this).attr("id"));
								$("#frmPost").submit();
							});
						});					
				
				</script>';
		
		return $html;
	}

	/**
	 * Provides, the neccessary javascript
	 * in order to disable the smartsearch
	 * in the browser.
	 */
	
	protected function getBreadCrumbList($moduleId, $last = false){
		$moduleObj = new Table_Modules();
		$module = $moduleObj->getDataById($moduleId);
	
		$html = '';
		
		//Make it more readable by introducing isRoot
		if($module->parent_id < 2){
			$html .= ' <li>
						<i class="fa fa-home"></i>
						<a class="breadcrumb_home" href="index.php">Home</a>
						';
			if(!$last){
				$html.='<i class="fa fa-angle-right"></i>
					</li>';
			}else{
				$html.='</li>';
			}
		}else{
			$html.= $this->getBreadCrumbList($module->parent_id);
			
			$html .= '<li>
						<a id="'.$module->id.'" class="breadcrumb_click" href="#">'.$module->name_al.'</a>
						';
			if(!$last){
				$html.='<i class="fa fa-angle-right"></i>
					</li>';
			}else{
				$html.='</li>';
			}
		}
		
		return $html;
	}
	protected function disableSmartSearch(){
		//disable the smartsearch field
		return '<script type="text/javascript">
				// disable the smartsearch field
				$("div#smartSearch").find("input").attr("readonly", "readonly");
				$("div#smartSearch").find("input").attr("title", "Ne kete modul te sistemit nuk mundesohet kerkimi");
				$("div#smartSearch").find("input").attr("placeholder", "Nuk mundesohet kerkimi");
			  </script>';
	}
}
?>