<?php

class Form_Logs extends Zend_Form {
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
				  
		$action = new Zend_Form_Element_Text('action');
		$action	->setLabel ( $tr->_('Veprimi:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(4, 45))
				->addValidator(new Zend_Validate_Alpha(array('allowWhiteSpace' => true)))
				->addFilter('StringTrim');

		$description= new Zend_Form_Element_Text('description');
		$description	->setLabel ( $tr->_('Pershkrim:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(5, 200))
				->addValidator(new Zend_Validate_Alpha(array('allowWhiteSpace' => true)))
				->addFilter('StringTrim');
		
		$user_id = new Zend_Form_Element_Hidden("user_id");
		$user_id -> setValue('')
		-> removeDecorator('label');
		
		$logdate = new Zend_Form_Element_Hidden("logdate");
		$logdate -> setValue('')
		-> removeDecorator('label');

		
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit' ) );
		$submit	->setLabel ( $tr->_('Ruaj' ))
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $action)
				->addElement ( $description)
				->addElement ( $user_id)
				->addElement ( $logdate)
				->addElement ( $moduleId)
				->addElement ( $mode)
				->addElement ( $rowId)
				->addElement ( $submit);
	}
}
?>
