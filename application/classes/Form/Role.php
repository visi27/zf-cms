<?php

class Form_Role extends Zend_Form {
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
				  
		$roleName = new Zend_Form_Element_Text('role_name');
		$roleName	->setLabel (  $tr->_('Roli:') )
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(2, 45))
				
				->addFilter('StringTrim');

		$roleDesc = new Zend_Form_Element_Text('role_desc');
		$roleDesc	->setLabel (  $tr->_('Pershkrimi:') )
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(5, 200))
				
				->addFilter('StringTrim');

		
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit' ) );
		$submit	->setLabel (  $tr->_('Ruaj' ))
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $roleName )
				->addElement ( $roleDesc )
				->addElement ( $moduleId )
				->addElement ( $mode )
				->addElement ( $rowId )
				->addElement ( $submit );
	}
}
?>