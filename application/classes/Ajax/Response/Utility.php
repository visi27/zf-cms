<?php
/**
 *
 * @author Administrator
 * @since 08.11.2012 16:40
 */

class Ajax_Response_Utility {
	
	private function _toJson($data){
	  	return html_entity_decode(
	  			preg_replace('/\\\\u([0-9a-f]{4})/i', '&#x\1;', Zend_Json::encode($data)),
        		ENT_QUOTES, 'UTF-8'
	  	);
	}
	
	public function getController(){
		$this->getRequest()->getControllerName();
	}
	public function void(){
		echo $this->_toJson("");
	}
	private function getChildrens($parentId, $roleId){
		// load the data model class
		$module = new Table_Modules();
		$acl = new Table_Acl();
		$childrens = $module -> selectModules( $where = array(
													"parent_id" => array( "=" => $parentId ), 
													"display_order" => array (">" => 0)
												  	), $order = "display_order"
											);		
		$json = '['; $i=0; // json start
		foreach($childrens as $child){ // browse the childrens one by one
			if($acl->hasAccess($roleId, $child->id, "read")){ //$acl->hasAccess($roleId, $child->id, "read")
				$i++;
				// generating json
				//if this is not the first row, add a comma
				$json .= ( $i>1 )?",":"";	
				$json .= '{"title": "'.$child->{'name_'.$_SESSION['lang']['selected']}.'", "key": "'.$child->id.'" , "form": "'.$child->form_name.'"';
				//count the number of childrens for this child
				$hasChilds = $module->selectModules($where = array(
													"parent_id" => array("=" => $child->id)
													));		
				//if there are any childrens, make a recoursive call
				if( $hasChilds->count() ){
					$json .= ', "isFolder": true, ';
					$json .= '"children":'. $this->getChildrens($child->id, $roleId);
				}
				$json .= '}';
				
			}
		}
		$json .= ']'; // json end
		
		return $json;
	}
	
	public static function getSideBarMenu($parentId, $roleId, $firstLevel = false){
		
		// load the data model class
		$module = new Table_Modules();
		$acl = new Table_Acl();
		$childrens = $module -> selectModules( $where = array(
				"parent_id" => array( "=" => $parentId ),
				"display_order" => array (">" => 0)
		), $order = "display_order"
		);
		
		if($firstLevel){
			$html = '<ul class="page-sidebar-menu" data-keep-expanded="true" data-auto-scroll="true" data-slide-speed="200">';
			$html.='<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- END SIDEBAR TOGGLER BUTTON -->
				</li>';
		}else{
			$html = '<ul class="sub-menu">';
		}
		
		foreach($childrens as $child){ // browse the childrens one by one
			if($acl->hasAccess($roleId, $child->id, "read")){ //$acl->hasAccess($roleId, $child->id, "read")
				
				if($firstLevel){
					$html.='<li class="level1" id = "'.$child->id.'">
							<a id="'.$child->form_name.'" href="javascript:;">
							<i class="icon-pointer"></i>
							<span class="title">'.$child->name_al.'</span>
							<span class="arrow "></span>
							</a>';
				}else{
					$html.='<li class="level2" id = "'.$child->id.'">
							<a id="'.$child->form_name.'" href="javascript:;">'.$child->name_al.'</a>';
				}
				
				// generating json
				//if this is not the first row, add a comma
				$hasChilds = $module->selectModules($where = array(
						"parent_id" => array("=" => $child->id)
				));
				//if there are any childrens, make a recoursive call
				if( $hasChilds->count() ){
					$html.= Ajax_Response_Utility::getSideBarMenu($child->id, $roleId, false);
				}
				
				$html.='</li>';
			}
		}
		
		$html.='</ul>';
		
		return $html;
	}
	
	public function getAccordionHTML($parentId, $roleId){
		// load the data model class
		$module = new Table_Modules();
		$acl = new Table_Acl();
		$childrens = $module -> selectModules( $where = array(
				"parent_id" => array( "=" => $parentId ),
				"display_order" => array (">" => 0)
		), $order = "display_order"
		);
		
		$html = "<ul>";
		
		foreach($childrens as $child){ // browse the childrens one by one
			if($acl->hasAccess($roleId, $child->id, "read")){ //$acl->hasAccess($roleId, $child->id, "read")
				// generating json
				//if this is not the first row, add a comma
				$hasChilds = $module->selectModules($where = array(
						"parent_id" => array("=" => $child->id)
				));
				//if there are any childrens, make a recoursive call
				if( $hasChilds->count() ){
					$html.= "<li id = '".$child->id."'><h3>
							<span class='icon-dashboard'></span>";
							
					$html.="<a class='parent' id='".$child->form_name."' href='#'>".$child->name_al."</a>";
					
					$html.= '</h3>
							<ul>';
					$html.=Ajax_Response_Utility::getAccordionChildrenHTML($child->id, $roleId);
					$html.='</ul></li>';
				}
			}
		}
		$html.='</ul>';
	
		return $html;
	}
	
	public function getAccordionChildrenHTML($parentId, $roleId){
		$module = new Table_Modules();
		$acl = new Table_Acl();
		$childrens = $module -> selectModules( $where = array(
				"parent_id" => array( "=" => $parentId ),
				"display_order" => array (">" => 0)
		), $order = "display_order"
		);
		$html = "";
		foreach($childrens as $child){
			if($acl->hasAccess($roleId, $child->id, "read")){ //$acl->hasAccess($roleId, $child->id, "read")
				$html.="<li id = '".$child->id."'><a id='".$child->form_name."' href='#'>".$child->name_al."</a></li>";
			}
			
		}
		
		return $html;
		
	}
		
	public function addTabsToForm(Utility_Grid $myGrid, array $tabsToBeCreated) {
	
	
		$tabsHTML='<ul class = model_tabs id=tabs >';
	
		foreach ($tabsToBeCreated as $distinctTab){
	
			$tabsHTML.='<li><a href=\"#'.$distinctTab["reference"].'\"><span>'.$distinctTab["name"].'</span></a></li>';
		}
	
		$tabsHTML.='</ul>';
	
		$script='
		<script type="text/javascript">
			$(function() {
		
					if ($("#tabs_cont").length==0){
	
							$("#'.$myGrid->getZendFormId().'").prepend("<div id=tabs_cont>'.$tabsHTML.'</div><br/>");';
			
		foreach ($tabsToBeCreated as $distinctTab){
	
			//The JQuery UI tabs expects the content divs to be in the same container as the ul of links
			$script.= '$(\'#'.$distinctTab["reference"].'\').appendTo($(\'#tabs_cont\'));';
		}
			
		$script.='
				
								$("#tabs_cont").tabs({ fx: { opacity: "toggle", duration: 400 } });
	
								//IF THERE ARE ERRORS FIND THE DIV WHERE THE ERRORS ARE AND ACTIVATE THE TAB THAT IS RELATED TO THAT DIV
								if($(".errors").length!=0){
	
									var firstError = $(".errors").first();
					
									var errorParentDiv = firstError.parents("div.ui-tabs-panel").attr("id");
	
									var errorTabIndex = $("ul#tabs").siblings("div").index($("#"+errorParentDiv));
					
									$("#tabs_cont").tabs("select", errorTabIndex);
					
								}
					}
							
			});
		</script>';
	
		return $script;
	}
	
	public function addTabsToFormNew($zendFormId, array $tabsToBeCreated) {
	
	
		$tabsHTML='<ul class = model_tabs id=tabs >';
	
		foreach ($tabsToBeCreated as $distinctTab){
	
			$tabsHTML.='<li><a href=\"#'.$distinctTab["reference"].'\"><span>'.$distinctTab["name"].'</span></a></li>';
		}
	
		$tabsHTML.='</ul>';
	
		$script='
		<script type="text/javascript">
			$(function() {
	
					if ($("#tabs_cont").length==0){
	
							$("#'.$zendFormId.'").prepend("<div id=tabs_cont>'.$tabsHTML.'</div><br/>");';
			
		foreach ($tabsToBeCreated as $distinctTab){
	
			//The JQuery UI tabs expects the content divs to be in the same container as the ul of links
			$script.= '$(\'#'.$distinctTab["reference"].'\').appendTo($(\'#tabs_cont\'));';
		}
			
		$script.='
	
								$("#tabs_cont").tabs({ fx: { opacity: "toggle", duration: 400 } });
	
								//IF THERE ARE ERRORS FIND THE DIV WHERE THE ERRORS ARE AND ACTIVATE THE TAB THAT IS RELATED TO THAT DIV
								if($(".errors").length!=0){
	
									var firstError = $(".errors").first();
			
									var errorParentDiv = firstError.parents("div.ui-tabs-panel").attr("id");
	
									var errorTabIndex = $("ul#tabs").siblings("div").index($("#"+errorParentDiv));
			
									$("#tabs_cont").tabs("select", errorTabIndex);
			
								}
					}
				
			});
		</script>';
	
		return $script;
	}
	public function isChildOf($parentId, $childId){
	
		// load the user model
		$user = new Table_Users();
		// load the user data
		$userData = $user->getDataById(Authenticate::getUserId());
		//get the role id
		$roleId = $userData->role_id;
	
		// load the data model class
		$module = new Table_Modules();
		$acl = new Table_Acl();
		$childrens = $module -> selectModules( $where = array(
				"parent_id" => array( "=" => $parentId ),
				"display_order" => array (">" => 0)
		), $order = "display_order"
		);
		$result =false;
		foreach($childrens as $child){ // browse the childrens one by one
				
			if($child->id == $childId){
				if($acl->hasAccess($roleId, $child->id, "read")){
					$result .= true;
					break;
				}
			}else{
				$result .= false;
				//count the number of childrens for this child
				$hasChilds = $module->selectModules($where = array(
						"parent_id" => array("=" => $child->id)
				));
				//if there are any childrens, make a recoursive call
				if( $hasChilds->count() ){
					$result .= $this->isChildOf($child->id, $childId);
				}
			}
				
		}
	
		return $result;
	
	}
	
	public function getModule($filter = array()){
	
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
	
		$model = new Table_Modules();
	
		$where = array( "name_al" => array( " like " => "{$args['text']}%" ) );
	
		$foundData = $model->selectData($where, $sortField = "sys_modules.name_al", 
				$sortDir=null,$limit = null, array("sys_modules.id as id","sys_modules.name_al as name"));
		
		$json = $foundData->toArray();
	
		echo $this->_toJson($json);
	}
	
	public function loadTree($par = 1){
		// load the user model
		$user = new Table_Users();
		// load the user data
		$userData = $user->getDataById($_SESSION['userId']);
		//get the role id
		$role_id = $userData->role_id;
		// load the tree
		echo $this->getChildrens($par, $role_id);
	}
	
	public function getRole($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
		
		$model = new Table_Role();
		
		$where = array( "role_name" => array( " like " => "{$args['text']}%" ) );
				
		$foundData = $model->selectData($where, $sortField = null, $limit = 30, array("sys_roles.id", "sys_roles.role_name as name"));
		
		$json = $foundData->toArray();	

		echo $this->_toJson($json);
	}
	
	
	/**
	 *  Populates FilterByTableName Toolbar
	 * 	Marilda
	 */
	public function getDbTables($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
	
		$model = new Table_ActionLog();
	
		$where = array( "table_name" => array( " like " => "{$args['text']}%" ) );
	
		$foundData = $model->selectActions($where, $sortField = null, $limit = 15, array("sys_action_log.table_name as name"),"table_name");
	
		$json = $foundData->toArray();
	
		echo $this->_toJson($json);
	}
		

	/**
	 *  Populates FilterByAction Toolbar
	 * 	Marilda
	 */
	public function getDbActions($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
	
		$model = new Table_AccessLog();
	
		$where = array( "description" => array( " like " => "{$args['text']}%" ) );
	
		$foundData = $model->selectActions($where, $sortField = null, $limit = 15, array("sys_logs.description as desc"),"description");
	
		$json = $foundData->toArray();
	
		echo $this->_toJson($json);
	}
		
	
	
	
	/**
	 *  Returns json of all possible access rights - to populate a toolbar
	 * 	Marilda
	 */
	public function getAccessRights($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
		
		$param = $args['text'];		
		$param =trim ($param);
		
		$access_rights = array();
		$access_rights["0"] = array ("action" => "R","label" => "Read") ;
		$access_rights["1"]= array ("action" => "W","label" => "Write") ;
		$access_rights["2"]= array ("action" => "P","label" => "Print") ;		
		
		//$sel_access_rights = array();
		
		//$i = 0;		
		//while (list($key, $arr) = each($access_rights)) {
			//$pos = strpos ($arr["label"],$param);
			//if (strpos ($arr["label"],$param) !== false ){
				//$i = $i + 1;
				//$sel_access_rights["$i"] = array ("action" =>$arr["action"],"label" =>$arr["label"]) ;				
			//}
		//}
	  
		echo $this->_toJson($access_rights);
	}
	
	
	/**
	 *  Returns json of all rule options - to populate a toolbar
	 * 	Marilda
	 */
	public function getRuleOptions($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
	
		$param = "{$args['text']}%";
		$param = trim ($param);
	
		$rule_opts = array();
		$rule_opts["0"] = array ("rule" => "A","label" => "Allow") ;
		$rule_opts["1"]= array ("rule" => "D","label" => "Deny") ;		
	
		$sel_rules = array();
		/**
			$i = 0;
			while (list($key, $arr) = each($access_rights)) {
			if (strcmp ($arr["action"],$param) !== 0 ){
			$i = $i + 1;
			$sel_access_rights["$i"] = array ("action" =>$arr["action"]) ;
			}
			}
			**/
		echo $this->_toJson($rule_opts);
	}
	
	
	
	public function getAcdFullRoleName($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
	
		$model = new Table_AcdRole();
		if(intval($args['text']) > 0){
			$where = array( "acd_role_id" => array( " = " => "{$args['text']}%" ) );
		}else
		$where = array( "structure_name" => array( " like " => "{$args['text']}%" ) );
	
		$foundData = $model->selectData($where, $sortField = 'structure_name', $limit = 15,
				array("acd_role_id", "concat(structure_name,' -> ', function_name) as name"), true);
	
		$json = $foundData->toArray();
	
		echo $this->_toJson($json);
	
	}
	
	public function getAcdRoleName($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
		
		$model = new Table_AcdRole();
		
		$where = array( "structure_name" => array( " like " => "{$args['text']}%" ) );
		
		$foundData = $model->selectData($where, $sortField = null, $limit = 15, 
				array("sys_acd_roles.structure_name as name"), true);
		
		$json = $foundData->toArray();
		
		echo $this->_toJson($json);
		
	}
	
	public function getAcdRoleFunctionName($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
		
		// if there is a structure selected, get its dependent childrens (function names)
		if($args['structure'] != ""){
			$model = new Table_AcdRole();
			
			$where = array( "function_name" => array( " like " => "{$args['text']}%" ),
							 "structure_name" => array( " like " => "{$args['structure']}%" ));
			
			$foundData = $model->selectData($where, $sortField = null, $limit = 15, 
					array("sys_acd_roles.acd_role_id as id", "sys_acd_roles.function_name as name"), true);
			
			$json = $foundData->toArray();
		}
			
		echo $this->_toJson($json);
	}
	
	public function getModules($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
		
		$model = new Table_Modules();
		
		$foundData = $model->selectModules(
					 $where = array(
							"name_al" => array(" like " => "{$args['text']}%"),
							//"form_name" => array( "<>" => "Blank" ), 
							"display_order" => array (">" => 0)
					), $order = array("parent_id", "display_order"));
		
		//$json = $foundData->toArray();	

		echo $this->_toJson($foundData->toArray());
	}
	
	
	public function getFullModuleName($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		$args = Utility_Functions::cleanArgsValue($args);
	
		$model = new Table_Modules();
		if(intval($args['text']) > 0){
			$where = array( "id" => array( " = " => "{$args['text']}%" ) );
		}else
			$where = array( "name_al" => array( " like " => "{$args['text']}%" ) );
	
		$foundData = $model->selectData($where, $sortField = 'name_al', $limit = 15,
				array("id", "name_al"), true);
	
		$json = $foundData->toArray();
	
		echo $this->_toJson($json);
	
	}
	
	
	
	public function profile($filter = array()){
		// parameters
		$filter = $filter[0];
		$mode = ($filter!="")?"submit":"default";
		
		// create a user object to retrieve user profile data
		$userObject = new Table_Users();
		
		
		// get the current user's data
		$user = $userObject->find(Authenticate::getUserId())-> current();
		
		switch ($mode){
			case "default":	
				// create the zend form object
				$form = new Form_Profile(array( 'id'=>'userProfile-Form' ));
		
				// set the form action, to no action, it will be ajax
				$form -> setAction(""); 
				// set the user data into the zend form
				$form -> getElement('username')-> setValue($user->username);
				$form -> getElement('fullname')-> setValue($user->fullname);
				$form -> getElement('description')-> setValue($user->description);
				
				// serialize the form object into the session
				$_SESSION['profileForm'] = serialize($form);
				
				// the main javascript 
				echo '<script>
				$(function() {
					// submiting the profile form
					$("#'.$form->getAttrib('id').'").wrap(\'<div id="div-'.$form->getAttrib('id').'"/>\');
					$("<div id=\'div-sumbit-bttn\'><br/><button id=\'submit_form\'>Save</button></div>").insertAfter( "#div-'.$form->getAttrib('id').'" );
					
					$("#div-sumbit-bttn button[id=\'submit_form\']").click(function(){
					     var sendData=$("#'.$form->getAttrib('id').'").serialize();
				         $.ajax({
				                url:"index.php?c=ajax",
				                dataType: "html",
				                type: "POST",
				                data: {
				                		loadClass: "Ajax_Response_Utility",
				                		method: "profile",
				        	        	parameter: sendData
				               	},
				                context: this,
				               	success: function(response){
				                	$( "#div-'.$form->getAttrib('id').'" ).html(response);
				                }   
				         });
					 });
					 
					$( "#dialog-userProfile" ).dialog({
	        			autoOpen: false,
	        			show: "scale",
	        			hide: "fade",
	        			modal: false,
	        			beforeClose: function(event, ui) { $("ul.ui-autocomplete").hide(); }
        			});
        			$( "#dialog-userProfile" ).dialog( { title: "Profili i Perdoruesit" } );
        			$( "#dialog-userProfile" ).dialog( "open" );
        			
        			// ie7 compatibility mode for dialog box items
        			setTimeout(function() {	
				       $("#'.$form->getAttrib('id').'").find("input[type=text], input[type=password], input[type=checkbox], select, textarea").each(function(){
                          $(this).attr("style", "DISPLAY: inline-block");
                       });	
				    }, 400); 
				});
				</script>';
				
				// render the form
				echo $form->render(new Zend_View());
				
				echo '<script>
				
				</script>';
			break;
			
			case "submit":
				// create the form object from the session
				$form = unserialize ( $_SESSION[ "profileForm"] );
				
				// translate the parameters array into a zend form friendly format
				$filter = str_replace("+", " ", explode("&", $filter));
				$data = array();
				foreach ($filter as $oneRow) {
					$t = explode("=", urldecode($oneRow));
					$data[$t[0]] = $t[1];
				}
				
				// check form validation
				if ($form->isValid ( $data )) {// form validated
					
					// old password 
					$password = md5(urldecode($form -> getElement('currentPassword')-> getValue()));
					
					// new password
					$newPassword = md5(urldecode($form -> getElement('newPassword')-> getValue()));
					
					// confirm password
					$confirmPassword = md5(urldecode($form -> getElement('repeatPassword')-> getValue()));
					
					// check if old password is correct
					if($user->password == $password){
						// the new password is compared to the confirm password
						// into the zend form validator, so we dont check here again
						$user->password = md5(urldecode($form -> getElement('newPassword')-> getValue()));
						
						// save the data into the database
						$user-> save();
						
						// update the session information
						$_SESSION['password']=$user->password;
						
						// display message to the user
						$message = "<br/> <b>Password-i u ndryshua me sukses!</b>";
					}else{
						// display message to the user
						$message = "<br/> <b>Password i gabuar!</b> <br/> Asgje nuk ndryshoi.";
					}
					// remove the submit button and close the dialog box
					echo '<script>
							$(function() {
								$("#div-'.$form->getAttrib('id').'").html("'.$message.'");
								$("#div-sumbit-bttn").remove();
								setTimeout(function() {  $( "#dialog-userProfile" ).dialog( "close" ); }, 1500);	
							});
						</script>';	
				}else{
					// any of the form elements is not validated
					// render the form again
					echo $form->render(new Zend_View());
				}	
			break;	
		}
	}
			
	/**
	 * Parameters: an array of data. The array should have as min two values
	 * First: the text to search for, Second: the module id where to search
	 * [0] -> text to be searched {format xx - abcd or xx or abcd}
	 * [1] -> module Id where to search
	 * Returns search results in Json format [ {id:value, name:value} ]
	 * last update: 28.08.2012
	 */ 
	public function smartSearch($filter = array(), $condition = array()){

		//get the parameters in a beautiful shape
		$args = Utility_Functions::argsToArray($filter);
		
		// the module id, where to search 
		$moduleId = $args['moduleId'];
		
		// the text typed by the user
		$textToSearch = explode("-", $args['text']); //
		
		// remove the white spaces from the text
		$textToSearch = trim(!empty($textToSearch[1])?$textToSearch[1]:$textToSearch[0]);
		
		// row limit of result to display
		$limit = "10"; 
		
		// default Smart Search ResultSet
		$returnArray = "";
		
		// load the module table 
		$model = new Table_Modules();
		
		// search for the current selected moduleId
		$moduleData = $model->getModuleById($moduleId);
		
		// if the current module has a form name
		if( $moduleData->form_name ){
			try{
				// the module name
				$moduleName = $moduleData->form_name;
				
				//the module id
				$moduleId = $moduleData->id;
				
				// the modEl class name
				$modelClassName = "Table_".$moduleName;
				
				// if the modEl class exists, we can try to search
				if(class_exists($modelClassName)){
					
					// create an instance of the model
					$model = new $modelClassName();
							
					// get information from the model
					$tblInfo = $model-> info();
						
					// get the primary key column
					$primaryKeyCol = $tblInfo['primary'][1];
					
					// set an empty column names array container
					$searchFileds = array();
					
					// check if the default method that returns the 
					// column names where to query, exists
					if (method_exists ( $model, "getSearchFields" )){
						
						// get the column names array where to query
						$searchFileds = $model->getSearchFields();
						
						// get the column names to show in the resut
						if (method_exists ( $model, "getFieldsToShow" )){
							// get the column names array to show
							$displayNameCol = $model->getFieldsToShow();
							
						}else{
							// column to be returned with the json
							$displayNameCol = array($searchFileds[0]);
						}	
					}
					// search on the default columns: PK column, name_al, name_en
					else{
						
						// collect the Primary Key column name
						array_push($searchFileds, $primaryKeyCol);
						
						// collect name_al
						array_push($searchFileds, "name_al");
						
						// collect name_en
						array_push($searchFileds, "name_en");
						
						// column to be displayed in the autocomplete result list
						$displayNameCol = array("name_al");
					}
			
					foreach ($searchFileds as $key => $columnName){
						$where = array();
						$where[$columnName] = array( " like " => $textToSearch."%" );
						$where[$condition["fieldName"]] = $condition["condition"];
						$rowSet = $model->selectData($where, $columnName, $limit);
						if(count($rowSet) > 0){ 
							// if the smart search has generated a resultSet,
							// extract only the relevant data to the client
							// start Json encoding
							// transform the rowset into an array
							$rowsetArray = $rowSet->toArray();
							$i=0;
							// keep only the parentId and name colons
							foreach ($rowsetArray as $row){
								$returnArray[$i]["id"]=$row[$primaryKeyCol];
								foreach($displayNameCol as $id=>$val){
									$returnArray[$i]["name"].='  '.$row[$val];
								}
								$returnArray[$i]["name"]=trim($returnArray[$i]["name"], "  ");
								$returnArray[$i]["module"]=$moduleName;
								$returnArray[$i]["moduleId"]=$moduleId;
								$i++;
							}
							// BREAK and EXIT
							break;
						}
					}
				}
			}
			catch (Exception $e){
				$log = $e->getMessage();
				return false;
			}
			
		}
		
		//$json['modules'] = $returnArray;
		echo $this->_toJson($returnArray);	
	}
	
	public function mySearch($filter = array(), $condition = array()){
	
		$searchLimit = 5;
		//get the parameters in a beautiful shape
		$args = Utility_Functions::argsToArray($filter);

		// clean the arguments by keeping only the values
		$args = Utility_Functions::cleanArgsValue($args);
	
		//print_r($args);
		// row limit of result to display
		$limit = isset($args['limit'])?intval($args['limit']):$searchLimit;
	
		// default Smart Search ResultSet
		$returnArray = "";
	
		// load the module table
		$model = new Table_Modules();
	
		// search for the current selected moduleId
		$moduleData = $model->getModuleById($args['moduleId']);
	
		// if the current module has a form name
		if( $moduleData->form_name ){
			try{
				// the module name
				$moduleName = $moduleData->form_name;
	
				//the module id
				$moduleId = $moduleData->id;
	
				// the modEl class name
				$modelClassName = "Table_".$moduleName;
	
				// if the model class exists and if the default method that returns the
				// column names where to query, exists, we can try to search
				if(class_exists($modelClassName)
						&& method_exists(new $modelClassName(), "getSmartSearch")){
	
							// create an instance of the model
							$model = new $modelClassName();
								
							// get information from the model
							$tblInfo = $model-> info();
	
							// get the primary key column
							$primaryKeyCol = $tblInfo['primary'][1];
	
							// get the column names array where to query
							$searchFileds = $model->getSmartSearch('Search');
	
							// get the column names array to show
							$displayFields = $model->getSmartSearch('Display');
	
							// get the search method name
							$searchMethod = method_exists($model, $model->getSmartSearch('Method'))?
							$model->getSmartSearch('Method') : 'selectData';
								
							$searchParams = $model->getSmartSearch('Params')?
							Utility_Functions::argsToArray($model->getSmartSearch('Params')):null;
								
							foreach ($searchFileds as $key => $columnName){
								$where = array();
								$where[$columnName] = array( " like " => "%".$args['text']."%" );
								$where[$condition["fieldName"]] = $condition["condition"]; //@todo make a loop
								Zend_Registry::get('applog')->log(print_r($where, true));
								$rowSet = $model->{$searchMethod}($where, $searchParams['orderBy'],
								$searchParams['orderDir'], $limit, $columnName);
									
								if(count($rowSet)){
									// if the smart search has generated a resultSet,
									// extract only the relevant data to the client
									// start Json encoding
									// transform the rowset into an array
									$rowsetArray = $rowSet->toArray();
									$i=0;
									// keep only the parentId and name colons
									foreach ($rowsetArray as $row){
										$returnArray[$i]["id"]=$row[$primaryKeyCol];
										foreach($displayFields as $id => $val){
											$returnArray[$i]["name"].=' - '.$row[$val];
										}
										$returnArray[$i]["name"]=trim($returnArray[$i]["name"], " - ");
										$returnArray[$i]["module"]=$moduleName;
										$returnArray[$i]["moduleId"]=$moduleId;
										$i++;
									}
									// BREAK and EXIT
									break;
								}
							}
						}
			}
			catch (Exception $e){
				$log = $e->getMessage();
				return false;
			}
	
		}
	
		//$json['modules'] = $returnArray;
		echo $this->_toJson($returnArray);
	}
	
	public function selectReceipt($receiptData = array()){
	   $_SESSION['receipt']['selected'] = $receiptData[0];
	   $_SESSION['gallery']['type'] = "receipt";
	}
	
	public function selectReceiptFromWeb($receiptData = array()){
	    $_SESSION['receipt_from_web']['selected'] = $receiptData[0];
	}
	
	public function selectArticle($articleData = array()){
	    $_SESSION['article']['selected'] = $articleData[0];
	    $_SESSION['gallery']['type'] = "article";
	}
	
			
	public function addParameter($parameter) {
		array_push($this->parameters, $parameter);
	}	
	
	
	//*********************** Autocomplete Section *******************************//
	public function getIngredientsCategories(array $args){
		$args = Utility_Functions::argsToArray($args);
		$args = Utility_Functions::cleanArgsValue($args);
		$searchFor = $args["text"];
	
		$model = new Table_ConfigIngredientsCategory();
	
		$foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
		echo $this->_toJson($foundData->toArray());
	}
	
	public function getIngredients(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigIngredients();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getReceiptCategories(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigReceiptCategory();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	
	
	
	public function getReceiptBaseProducts(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigReceiptBaseProduct();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getReceiptCuisineTypes(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigReceiptCuisineType();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getReceiptTypes(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigReceiptType();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getReceiptFestivities(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigReceiptFestivity();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getReceiptMeals(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigReceiptMeal();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getReceiptSeasonalities(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_ConfigReceiptSeasonality();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getReceiptDifficulty(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"]; 
	
	    $model = new Table_ConfigReceiptDifficulty();
	
	    $foundData = $model->selectData(array("name" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	
	public function getSections(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_BlogSections();
	
	    $foundData = $model->selectData(array("title" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	
	public function getCities(array $args){
		$args = Utility_Functions::argsToArray($args);
		$args = Utility_Functions::cleanArgsValue($args);
		$searchFor = $args["text"];
	
		$model = new Table_ConfigCities();
	
		$foundData = $model->selectData(array("city" => array(" like " => "%$searchFor%")), null);
	
		echo $this->_toJson($foundData->toArray());
	}
	
	
	public function getAuthors(array $args){
		$args = Utility_Functions::argsToArray($args);
		$args = Utility_Functions::cleanArgsValue($args);
		$searchFor = $args["text"];
	
		$model = new Table_Authors();
	
		$foundData = $model->selectData(array("firstname" => array(" like " => "%$searchFor%"), "lastname" => array(" like " => "%$searchFor%")), null);
	
		echo $this->_toJson($foundData->toArray());
	}
	
	public function getBlogCategories(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	    $section = $args["section"];
	
	    if (isset ($section) && strlen($section)>0)
	        $where= array( "section" => array( " = " => $section ),"title" => array( " like " => "%$searchFor%" ) );
	    else
	        $where = array( "title" => array( " like " => "%$searchFor%" ));
	     
	    
	    $model = new Table_BlogCategories();
	
	    $foundData = $model->selectData($where, null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getArticleStatus(array $args){
	
	    $source = array('0' => array('id'=>'1', 'name'=>'I Paredaktuar'),
	        '1' => array('id'=>'2', 'name'=>'Ne Redaktim'),
	        '2' => array('id'=>'3', 'name'=>'I Redaktuar')
	    );
	     
	    echo $this->_toJson($source);
	}
	
	public function getReceiptStatus(array $args){
	
	    $source = array('0' => array('id'=>'1', 'name'=>'E Paredaktuar'),
	        '1' => array('id'=>'2', 'name'=>'Ne Redaktim'),
	        '2' => array('id'=>'3', 'name'=>'E Redaktuar')
	    );
	
	    echo $this->_toJson($source);
	}
	
	public function getYesNo(array $args){

	    $source = array('0' => array('id'=>'0', 'name'=>'Jo'),
	                   '1' => array('id'=>'1', 'name'=>'Po'));
	    
	    echo $this->_toJson($source);
	}
	
	public function getFeaturedElementTypes(array $args){
	
	    $source = array('0' => array('id'=>'1', 'name'=>'Artikull'),
	        '1' => array('id'=>'2', 'name'=>'Recete'));
	     
	    echo $this->_toJson($source);
	}
	
	//Autocomplete to get either receipt or article title based on selected type
	public function getElementTitle(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    
	    $searchFor = $args["text"];
	    $typeArr = explode("-", $args["type"]);
	
	    $type = trim($typeArr[0]);
	    
	    if($type == 1){
	        $model = new Table_BlogArticles();
	    }else{
	        $model = new Table_Receipts();
	    }   
	
	    $foundData = $model->selectAutocomplete(array("title" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	
	public function getCommentStatus(array $args){
	
	    $source = array('0' => array('id'=>'0', 'name'=>'I Papublikuar'),
	        '1' => array('id'=>'1', 'name'=>'I Publikuar')
	    );
	
	    echo $this->_toJson($source);
	}
	
	public function getCommentDeletedStatus(array $args){
	
	    $source = array('0' => array('id'=>'0', 'name'=>'Jo i fshire'),
	        '1' => array('id'=>'1', 'name'=>'I fshire')
	    );
	
	    echo $this->_toJson($source);
	}
	
	public function getCommentFlagStatus(array $args){
	
	    $source = array('0' => array('id'=>'0', 'name'=>'I pamarkuar'),
	        '1' => array('id'=>'1', 'name'=>'I markuar')
	    );
	
	    echo $this->_toJson($source);
	}
	
	public function getCommentRedactedStatus(array $args){
	
	    $source = array('0' => array('id'=>'0', 'name'=>'I paredaktuar'),
	        '1' => array('id'=>'1', 'name'=>'I redaktuar')
	    );
	
	    echo $this->_toJson($source);
	}
	
	public function getUsers(array $args){
	    $args = Utility_Functions::argsToArray($args);
	    $args = Utility_Functions::cleanArgsValue($args);
	    $searchFor = $args["text"];
	
	    $model = new Table_Users();
	
	    $foundData = $model->selectData(array("username" => array(" like " => "%$searchFor%")), null);
	
	    echo $this->_toJson($foundData->toArray());
	}
	//*********************** END Autocomplete Section *******************************//
	
}	
?>