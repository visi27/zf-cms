<?php

class Form_ConfigReceiptType extends Zend_Form {
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

				  
		$name = new Zend_Form_Element_Text('name', array ('class' => 'form-control' ));
		$name	->setLabel (  $tr->_('Lloji i Recetes:' ))
				->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
				->setRequired ( true )
					->addValidator('notEmpty', true, array('messages' => array(
        				'isEmpty' => Zend_Registry::get('lang')->form->notempty)))
				->setDecorators($this->elementDecorators)
				->addFilter('StringTrim');
					

		$description = new Zend_Form_Element_Textarea('description', array ('class' => 'form-control' ));
		$description	->setLabel ($tr->_('Pershkrimi:'))
				->setOptions ( array ('rows' => '5', 'cols'=>'28') )
				->setRequired ( false )
        		->addValidator('notEmpty', true, array('messages' => array(
        				'isEmpty' => Zend_Registry::get('lang')->form->notempty)))   
        		
				->setDecorators($this->elementDecorators);
			
				
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $name )
				->addElement ( $description)
				->addElement ( $submit );
	}
}

?>



