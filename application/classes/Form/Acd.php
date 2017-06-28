<?php

class Form_Acd extends Zend_Form {
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

		$roleId = new Zend_Form_Element_Hidden('acd_role_id');
		$roleId	->setRequired ( true )
				->addFilter('StringTrim');		
		
		$structure = new Zend_Form_Element_Text('structure');
		$structure	->setLabel ( $tr->_('Struktura:' ))
					->setOptions ( array ('size' => '30', 'icon'=>'ui-icon-circle-triangle-s') )
					->setRequired ( true )
					->setDecorators($this->elementDecorators)
					->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
					->addValidator('StringLength', array('min' => 2))
					->addFilter('StringTrim');

		$function = new Zend_Form_Element_Text('function');
		$function	->setLabel ( $tr->_('Funksioni:' ))
					->setOptions ( array ('size' => '30') )
					->setRequired ( true )
					->setDecorators($this->elementDecorators)
					->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
					->addFilter('StringTrim');
		
		$forceCode = new Zend_Form_Element_Text('force_code');
		$forceCode	->setLabel ( $tr->_('Kod Force:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
				->addFilter('StringTrim');	
				
				
		$strucCode = new Zend_Form_Element_Text('struc_code');
		$strucCode	->setLabel ($tr->_( 'Kod Njesie:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setDecorators($this->elementDecorators)
				->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
				->addFilter('StringTrim');			

              	
        $action = new Zend_Form_Element_Select('action');
        $action->setLabel($tr->_('Veprimi:'))
        		->setDecorators($this->elementDecorators)
              	->setMultiOptions(array('R'=>'Lexo', 'W'=>'Shkruaj', 'P'=>'Printo'))
              	->setRequired(true)->addValidator('NotEmpty', true);

        $rule = new Zend_Form_Element_Select('rule');
        $rule->setLabel($tr->_('Rregulli:'))
        		->setDecorators($this->elementDecorators)
        		->setMultiOptions(array('A'=>'Lejo', 'D'=>'Moho'))
        		->setRequired(true)->addValidator('NotEmpty', true);
              	
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this   ->addElement ( $roleId )
				->addElement ( $forceCode )
				->addElement ( $strucCode )
				->addElement( $structure )
				->addElement( $function )
				->addElement ( $action )
				->addElement ( $rule )
				->addElement ( $moduleId )
				->addElement ( $mode )
				->addElement ( $rowId )
				->addElement ( $submit );
				
		$this->addDisplayGroups(array(
		    'left' => array(
		        'elements' => array('rowId', 'structure', 'function', 'acd_role_id' ),
		    ),
		    'right' => array(
		        'elements' => array('force_code', 'struc_code', 'action', 'rule'),
		    ),
		    'bottom' => array(
		        'elements' => array('submit_form'),
		    )
		));
		 
		$this->setDisplayGroupDecorators(array('Description', 'FormElements', 'Fieldset'));
	}
}

?>