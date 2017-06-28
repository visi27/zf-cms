<?php

class Form_ReceiptsIngredients extends Zend_Form {
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
	
		$ingredient_id = new Zend_Form_Element_Text('ingredient_id', array ('class' => 'form-control' ));
		$ingredient_id	->setLabel ( $tr->_('Perberesi') )
				->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->addValidator('StringLength', false,array(1,30))
				->setDecorators($this->elementDecorators);
		
   		$unit = new Zend_Form_Element_Text('unit', array ('class' => 'form-control' ));
		$unit	->setLabel ( $tr->_('Njesia:') )
						->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
						->setRequired ( false )
						->addValidator(new Zend_Validate_Alpha(array('allowWhiteSpace' => true)))
						->setDecorators($this->elementDecorators)
						->addFilter('StringTrim');
		
		$qty = new Zend_Form_Element_Text('qty', array ('class' => 'form-control' ));
		$qty	->setLabel ( $tr->_('Sasia:') )
		->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
		->setRequired ( false )
		->setDecorators($this->elementDecorators)
		->addFilter('StringTrim');
		
		$instructions = new Zend_Form_Element_Textarea('instructions', array ('class' => 'form-control' ));
		$instructions	->setLabel ($tr->_('Instruksione:'))
		->setOptions ( array ('rows' => '3', 'cols'=>'28') )
		->setRequired ( false )
		    ->setDecorators($this->elementDecorators)
		    ->addFilter('StringTrim');
		 
		$ingredient_for = new Zend_Form_Element_Text('ingredient_for', array ('class' => 'form-control' ));
		$ingredient_for	->setLabel ( $tr->_('Perberes per:') )
		->setOptions ( array ('size' => '30',  'icon'=>'ui-icon-circle-triangle-s' ) )
		->setRequired ( false )
		->addValidator(new Zend_Validate_Alpha(array('allowWhiteSpace' => true)))
		->setDecorators($this->elementDecorators)
		->addFilter('StringTrim');
		
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $ingredient_id )
				->addElement ( $unit )
				->addElement ( $qty )
				->addElement ( $instructions )
				->addElement ( $ingredient_for )
				->addElement ( $submit );
	}
}

?>
