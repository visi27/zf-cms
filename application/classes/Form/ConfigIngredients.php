<?php

class Form_ConfigIngredients extends Zend_Form {
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
		$name	->setLabel ($tr->_('Emri i Perberesit:'))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
        		->addValidator('StringLength', false, array(1, 250))  
				->setDecorators($this->elementDecorators)
				->addFilter('StringTrim');
		
		$description = new Zend_Form_Element_Textarea('description', array ('class' => 'form-control' ));
		$description	->setLabel ($tr->_('Pershkrimi i Perberesit:'))
				->setOptions ( array ('rows' => '3', 'cols'=>'28') )
				->setRequired ( false )
  
				->setDecorators($this->elementDecorators)
        		->addFilter('StringTrim');
        		
		$category = new Zend_Form_Element_Text('category', array ('class' => 'form-control' ));
		$category	->setLabel ( $tr->_('Kategoria') )
				->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->addValidator('StringLength', false,array(1,30))
				->setDecorators($this->elementDecorators);
		

		$default_unit = new Zend_Form_Element_Text('default_unit', array ('class' => 'form-control' ));
		$default_unit	->setLabel ( $tr->_('Njesia Baze:') )
						->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
						->setRequired ( true )
						->addValidator('notEmpty', true, array('messages' => array('isEmpty' => Zend_Registry::get('lang')->form->notempty)))
						->setDecorators($this->elementDecorators)
						->addFilter('StringTrim');
     
		$notes = new Zend_Form_Element_Textarea('notes', array ('class' => 'form-control' ));
		$notes	->setLabel ($tr->_('Shpjegim Extra:'))
		->setOptions ( array ('rows' => '3', 'cols'=>'28') )
		->setRequired ( false )
		
		->setDecorators($this->elementDecorators)
		->addFilter('StringTrim');
		
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		// attach elements to form
		$this	->addElement ( $name )
				->addElement ( $description )
				->addElement ( $category )
				->addElement ( $default_unit )
				->addElement ( $notes )
				->addElement ( $submit );
	}
}

?>
