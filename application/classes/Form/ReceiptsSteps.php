<?php

class Form_ReceiptsSteps extends Zend_Form {
	public $formDecorators = array (array ('FormElements' ), array ('Form' ) );
	public $elementDecorators = array(
		    'ViewHelper',
		    'Description',
		    'Errors',
		    array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div','class' => 'elementWrap')),
		    array(array('td' => 'HtmlTag'), array('tag' => 'td')),
		    array('Label', array('tag' => 'td', 'requiredPrefix' => '* ')),
		);
	public $buttonDecorators = array (array ('ViewHelper' ), array ('HtmlTag', array ('tag' => 'p' ) ) );
	
	public function __construct($options = null) {
		
		//Get Translator
		$tr = Zend_Registry::get('translator');
		
		// initialize form
		$this	->setMethod ( 'post' )
				->setDecorators ( $this->formDecorators )
			    ->setAttribs(array('autocomplete'=>'off', "id" => $options["id"]));
	
		
		$step_nr = new Zend_Form_Element_Text('step_nr', array ('class' => 'form-control' ));
		$step_nr	->setLabel ( $tr->_('Nr. Rendor:') )
		->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
		->setRequired ( true )
		->addValidator(new Zend_Validate_Int())
		->setDecorators($this->elementDecorators)
		->addFilter('StringTrim');
		
		$step_instructions = new Zend_Form_Element_Textarea('step_instructions', array ('class' => 'form-control' ));
		$step_instructions	->setLabel ($tr->_('Instruksionet:'))
		->setOptions ( array ('rows' => '3', 'cols'=>'28') )
		->setRequired ( true )
		    ->setDecorators($this->elementDecorators)
		    ->addFilter('StringTrim');
		 
     
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $step_nr )
				->addElement ( $step_instructions )
				->addElement ( $submit );
	}
}

?>
