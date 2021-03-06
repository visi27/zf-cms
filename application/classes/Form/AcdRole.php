<?php

class Form_AcdRole extends Zend_Form {
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
				  
		$roleName = new Zend_Form_Element_Text('structure_name');
		$roleName	->setLabel ( $tr->_('Struktura:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(2, 85))
				->addFilter('StringTrim');

		$roleDesc = new Zend_Form_Element_Text('function_name');
		$roleDesc	->setLabel ($tr->_( 'Funksioni:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(5, 85))
				->addFilter('StringTrim');

		$rankGroup = new Zend_Form_Element_Select('rank_group');
		$rankGroup->setLabel($tr->_('Grupi Gradave:'))
				->setDecorators($this->elementDecorators)
				->setMultiOptions(array('OFF'=>$tr->_('Oficer'), 'NCO'=>$tr->_('N/Oficer'), 'CIV'=>$tr->_('Civil')))
				->setRequired(true)->addValidator('NotEmpty', true);
		
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $roleName )
				->addElement ( $roleDesc )
				->addElement ( $moduleId )
				->addElement ( $rankGroup)
				->addElement ( $mode )
				->addElement ( $rowId )
				->addElement ( $submit );
	}
}
?>