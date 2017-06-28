<?php

class Form_WebDailyRecipes extends Zend_Form {
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
		
	
		$recipe_id = new Zend_Form_Element_Text('recipe_id', array ('class' => 'form-control' ));
		$recipe_id	->setLabel ( $tr->_('Receta:') )
    		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
    		->setOptions ( array ('size' => '30') )
    		->setRequired ( true )
    		->setDecorators($this->elementDecorators);
		
		
		
		$publish_date = new Zend_Form_Element_Text('publish_date');
		$publish_date	->setLabel (  $tr->_('Data e Publikimit(dd/mm/yyyy):' ))
		->setAttrib( 'onfocus', '$(this).datepicker({ dateFormat: "dd/mm/yy" }); $(this).datepicker("show");')
		->setOptions ( array ('size' => '30') )
		->setRequired ( true )
		->addValidator('notEmpty', true, array('messages' => array(
		    'isEmpty' => Zend_Registry::get('lang')->form->notempty)))
		    ->addValidator('StringLength', false, array(10,'messages'=>array(
		        'stringLengthTooShort'=>Zend_Registry::get('lang')->form->datelength)))
		        ->addValidator(new Zend_Validate_Date(
		            array(
		                'format' => 'dd/mm/yyyy',
		            )))
		            ->setDecorators($this->elementDecorators);
		        
        $order = new Zend_Form_Element_Text('order_nr', array ('class' => 'form-control' ));
        $order	->setLabel ($tr->_('Renditja:'))
                ->setOptions ( array ('size' => '30') )
                ->setRequired ( true )
                ->addValidator(new Zend_Validate_Int())
                ->setDecorators($this->elementDecorators)
                ->addFilter('StringTrim');
       
     
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $recipe_id )
				->addElement ( $publish_date )
				->addElement ( $order)
				->addElement ( $submit );
		
	}
}

?>
