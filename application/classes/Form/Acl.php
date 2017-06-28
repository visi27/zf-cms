<?php

class Form_Acl extends Zend_Form {
	public $formDecorators = array (array ('FormElements' ), array ('Form' ) );
	public $elementDecorators = array(
		    'ViewHelper',
		    'Description',
		    'Errors',
		    array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div')),
		    array(array('td' => 'HtmlTag'), array('tag' => 'td')),
		    array('Label', array('tag' => 'td', 'requiredPrefix' => '* ')),
		);
	public $buttonDecorators = array (array ('ViewHelper' ), array ('HtmlTag', array ('tag' => 'p' ) ) );
	
	public function __construct($options = null) {
		
		//Get Translator
		$tr = Zend_Registry::get('translator');
		
		// initialize form
		$treeModuleId = intval(substr($options["id"], strrpos($options["id"],"_")+1));
		$this	->setMethod ( 'post' )
				->setDecorators ( $this->formDecorators )
			    ->setAttribs(array('autocomplete'=>'off', "id" => $options["id"]));
			    
		$mode = new Zend_Form_Element_Hidden("form_mode");
		$mode -> setValue('')
				 -> removeDecorator('label');		
		
		$rowId = new Zend_Form_Element_Hidden("row_id");
		$rowId -> setValue('')
				 -> removeDecorator('label');
				 
		$moduleId = new Zend_Form_Element_Hidden("treeNodeId");
		$moduleId -> setValue( $treeModuleId )
					->setRequired ( true )
				  -> removeDecorator('label');

		$roleId = new Zend_Form_Element_Text('role_id', array ('class' => 'form-control' ));
		$roleId	->setLabel ( $tr->_('Roli:' ))
				->setOptions ( array ('size' => '30', 'readonly' => true, 'icon'=>'ui-icon-circle-triangle-s' ) )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->setAttrib('onclick', '$(this).val(""); $(this).autocomplete( "search", $(this).val() );')
				->addValidator('regex', false ,array('/(^[0-9]+(\s)*(\-)*(\s)*[A-Z\a-z\0-9\_.]*)$/'))
				->addValidator('StringLength', array('min' => 4))
				->addFilter('StringTrim');		
		
				
		$sysModuleId = new Zend_Form_Element_Text('module_id', array ('class' => 'form-control' ));
		$sysModuleId	->setLabel ( $tr->_('Moduli:' ))
				->setOptions ( array ('size' => '30', 'icon'=>'ui-icon-circle-triangle-s' ) )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->setAttrib('onfocus', '$(this).val("");')
				->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
				->addValidator('regex', false ,array('/(^[0-9]+(\s)*(\-)*(\s)*[A-Z\a-z\0-9\_.]*)$/'))
				->addValidator('StringLength', array('min' => 4))
				->addFilter('StringTrim');		
				
				
		$read = new Zend_Form_Element_Select('read', array ('class' => 'form-control' ));
        $read->setLabel($tr->_('Lexo:'))
        		->setDecorators($this->elementDecorators)
              	->setMultiOptions(array('0'=>$tr->_('Jo'), '1'=>$tr->_('Po')))
              	->setRequired(true)->addValidator('NotEmpty', true);		

              	
        $write = new Zend_Form_Element_Select('write', array ('class' => 'form-control' ));
        $write->setLabel($tr->_('Shkruaj:'))
        		->setDecorators($this->elementDecorators)
              	->setMultiOptions(array('0'=>$tr->_('Jo'), '1'=>$tr->_('Po')))
              	->setRequired(true)->addValidator('NotEmpty', true);

              	
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this   ->addElement ( $roleId )
				->addElement ( $sysModuleId )
				->addElement ( $read )
				->addElement ( $write )
				->addElement ( $moduleId )
				->addElement ( $mode )
				->addElement ( $rowId )
				->addElement ( $submit );
				
		$this->addDisplayGroups(array(
		    'left' => array(
		        'elements' => array('rowId', 'role_id', 'module_id'),
		    ),
		    'right' => array(
		        'elements' => array('read', 'write'),
		    ),
		    'bottom' => array(
		        'elements' => array('submit_form'),
		    )
		));
		 
		$this->setDisplayGroupDecorators(array('Description', 'FormElements', 'Fieldset'));
	}
}

?>