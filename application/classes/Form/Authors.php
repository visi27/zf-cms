<?php

class Form_Authors extends Zend_Form {
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

				  
		$firstname = new Zend_Form_Element_Text('firstname', array ('class' => 'form-control' ));
		$firstname	->setLabel (  $tr->_('Emri:' ))
				->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
				->setRequired ( true )
					->addValidator('notEmpty', true, array('messages' => array(
        				'isEmpty' => Zend_Registry::get('lang')->form->notempty)))
				->setDecorators($this->elementDecorators)
				->addFilter('StringTrim');
					

		$lastname = new Zend_Form_Element_Text('lastname', array ('class' => 'form-control' ));
		$lastname	->setLabel ($tr->_('Mbiemri:'))
				->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
				->setRequired ( false )
        		  
        		
				->setDecorators($this->elementDecorators);
			
				
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $firstname )
				->addElement ( $lastname)
				->addElement ( $submit );
	}
}

?>



