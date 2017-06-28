<?php
class Ajax_Response_Personel_ModuleSearch extends Ajax_Response_Abstract {

	public function layoutCenter_Action(){
		//Get Translator
		$tr = Zend_Registry::get('translator');
		
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		$dialogId = "div-Dialog-$module";
		$zendFormId = "zend-form_$module";
		$viewPaneId = "table-$module";

		// load the data model
		$personel = new Table_Personal();
		
		// load the form object
		$form = new Form_Search(array('id'=>$zendFormId));			
		$form -> setAction("index.php?c=main&m=personel");
		$form->getElement("treeNodeId") -> setValue($module);
		
		//get the formMode
		$formMode = ($this->_action !="default")?$this->_action:$this->_params['form_mode'];
		
		// switch different actions
		if ($formMode == "submit_form"){
			
			if($_GET['p'] || $form->isValid($_POST)){
				
				$form->getElement("form_mode") -> setValue('submit_form');
				$form->getElement("idcard") -> setValue(!empty($this->_params['idcard'])?$this->_params['idcard']:$_POST['idcard']);
				$form->getElement("firstname") -> setValue(!empty($this->_params['firstname'])?$this->_params['firstname']:$_POST['firstname']);
				$form->getElement("lastname") -> setValue(!empty($this->_params['lastname'])?$this->_params['lastname']:$_POST['lastname']);
				
				$toolBarElements['idcard'] = 'idcard';
				$toolBarElements['firstname'] = 'firstname';
				$toolBarElements['lastname'] = 'lastname';
				$toolBarElements['form_mode'] = 'form_mode';
				// select all data from the db table
				
				$searchResultColumns = array('emp_number', 'idcard', 'firstname',
						'fathername', 'lastname', 'birthday', 'gender', 'marital_status');
				
				if ($form->getElement("idcard")->getValue() != ""){			
					$dataSet = $personel->selectGrid( 
							array( "idcard" => array( " like " => $form->getElement("idcard")->getValue()."%" ) ), 
							"firstname", null, $searchResultColumns);
				}
				
				else if ($form->getElement("firstname")->getValue() != "" || $form->getElement("lastname")->getValue() !="") {
					$dataSet = $personel->selectGrid( 
							array( "firstname" => array( " like " => $form->getElement("firstname")->getValue()."%" ),
							"lastname" => array( " like " => $form->getElement("lastname")->getValue()."%" ) ) , 
							"firstname", null , $searchResultColumns);
				}
					
			}
			
		}
		
		$output.= $form->render(new Zend_View());
		
		//$filter=array($idcard,$firstname,$lastname);
		
		$columnAlias = array($tr->_('ID'), $tr->_('ID card'), $tr->_('Emri'),
				$tr->_('Atesia'), $tr->_('Mbiemri'), $tr->_('Datelindja'), $tr->_('Gjinia'), $tr->_('Gjendja civile'));
		// some jQuery scripting :)
		$output.= '
				<script>
					$(function() {
		
						// hide the empolyer id number
						$("#'.$viewPaneId.' table[id=\'data-table\'] th[id=\''.$columnAlias[0].'\']").hide();
						$("#'.$viewPaneId.' table[id=\'data-table\'] td[id=\''.$searchResultColumns[0].'_td\']").hide();
		
						//when a search result (person) is selected
						$("#'.$viewPaneId.' input[name=\'rowSelectionRadio\']").change(function() {
		
							var selectedRowId = $("#'.$viewPaneId.' input[name=\'rowSelectionRadio\']:checked").val();
		
							if(selectedRowId){
								//set the form values
								$.ajax({
									type: "GET",
									url: "index.php?c=ajax",
									data: {
										loadClass: "Ajax_Response_Utility",
										method: "selectPerson",
										parameter: selectedRowId
									},
									success: function(response){
										// do something if you need to
										$(".ui-layout-west .ui-layout-content").dynatree("getTree").activateKey(3);
									}
								});
				
							}else{
								alert("Zgjidhni nje element nga tabela fillimisht.");
							}
						}); // end function
					});// end jquery
				</script>';
		
		// display results, if any
		if(!empty($dataSet)){
				
			$headerText = "Rezultati e Kerkimit ";
			
			
			$output.=  '<div class="">';
			$output.=  HtmlView::displayTable(
					$dataSet, $viewPaneId, $columnAlias, $rowSelection = TRUE,
					$unique = "emp_number", $headerText, $paginator = true,
					$toolBarElements, $controller = "personel",
					$page=1, $allowSort = false
			);
			$output.='</div>';
		}else{
			//echo "Ju mund te kerkoni me nje nga kombinimet e meposhtme:";
		}
				echo $output;
	}
}