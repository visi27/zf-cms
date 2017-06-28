<?php

class Form_WebFeatured extends Zend_Form {
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
		
		$type = new Zend_Form_Element_Text('type', array ('class' => 'form-control' ));
		$type	->setLabel ( $tr->_('Tipi i Elementit:') )
		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( true )
		->setDecorators($this->elementDecorators);
	
		$element_id = new Zend_Form_Element_Text('element_id', array ('class' => 'form-control' ));
		$element_id	->setLabel ( $tr->_('Elementi:') )
    		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
    		->setOptions ( array ('size' => '30') )
    		->setRequired ( true )
    		->setDecorators($this->elementDecorators);
		
		
		
		$start_date = new Zend_Form_Element_Text('start_date');
		$start_date	->setLabel (  $tr->_('Data e Fillimit(dd/mm/yyyy):' ))
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
		        
        $end_date = new Zend_Form_Element_Text('end_date');
        $end_date	->setLabel (  $tr->_('Data e Perfundimit(dd/mm/yyyy):' ))
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
		$this	->addElement ( $type )
				->addElement ( $element_id )
				->addElement ( $start_date )
				->addElement ( $end_date )
				->addElement ( $order)
				->addElement ( $submit );
		
	}
}

?>
