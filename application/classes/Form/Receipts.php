<?php

class Form_Receipts extends Zend_Form {
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
	
		$title = new Zend_Form_Element_Text('title', array ('class' => 'form-control' ));
		$title	->setLabel ($tr->_('Titulli i Recetes:'))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
        		->addValidator('StringLength', false, array(1, 250))  
				->setDecorators($this->elementDecorators)
				->addFilter('StringTrim');
		
		$description = new Zend_Form_Element_Textarea('description', array ('class' => 'form-control' ));
		$description	->setLabel ($tr->_('Pershkrimi i Recetes:'))
				->setOptions ( array ('rows' => '3', 'cols'=>'28') )
				->setRequired ( true )
        		->addValidator('notEmpty', true, array('messages' => array(
        				'isEmpty' => Zend_Registry::get('lang')->form->notempty)))   
				->setDecorators($this->elementDecorators)
        		->addFilter('StringTrim');

		$instructions = new Zend_Form_Element_Text('instructions', array ('class' => 'form-control' ));
		$instructions	->setLabel ($tr->_('Instruksione:'))
		->setOptions ( array ('size' => '30') )
		->setRequired ( false )
		->setDecorators($this->elementDecorators)
		->addFilter('StringTrim');
        		
        		
		$author = new Zend_Form_Element_Text('author', array ('class' => 'form-control' ));
		$author	->setLabel ( $tr->_('Autori') )
        		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
        		->setOptions ( array ('size' => '30') )
        		->setRequired ( true )
        		->addValidator('StringLength', false,array(1,30))
        		->setDecorators($this->elementDecorators);
        		
		
		$publish_date = new Zend_Form_Element_Text('publish_date', array ('class' => 'form-control' ));
		$publish_date	->setLabel ( 'Data e Publikimit(dd/mm/yyyy):' )
		->setOptions ( array ('size' => '25' ) )
		->setRequired ( true )
		->setAttrib( 'onfocus', '$(this).datepicker({ dateFormat: "dd/mm/yy" }); $(this).datepicker("show");')
		->addValidator('StringLength', false, array(10,'messages'=>array(
		    'stringLengthTooShort'=>Zend_Registry::get('lang')->form->datelength)))
		    ->addValidator(new Zend_Validate_Date(
		        array(
		            'format' => 'dd/mm/yyyy',
		        )))
		        ->setDecorators($this->elementDecorators);
		    
	    $publish_time = new Zend_Form_Element_Text('publish_time', array ('class' => 'form-control' ));
	    $publish_time	->setLabel ( 'Ora e Publikimit(HH:MM):' )
	    ->setOptions ( array ('size' => '25' ) )
	    ->setRequired ( true )
        ->addValidator(new Zend_Validate_Date(array('format' => 'H:i',)))
	            ->setDecorators($this->elementDecorators);
     
	    $servings = new Zend_Form_Element_Text('servings', array ('class' => 'form-control' ));
	    $servings	->setLabel ($tr->_('Nr. i Porcioneve:'))
    	    ->setOptions ( array ('size' => '30') )
    	    ->setRequired ( false )
    	    ->addValidator(new Zend_Validate_Int())
    	    ->setDecorators($this->elementDecorators)
    	    ->addFilter('StringTrim');
	    
	    $total_time = new Zend_Form_Element_Text('total_time', array ('class' => 'form-control' ));
	    $total_time	->setLabel ($tr->_('Kohezgjatja (Ne Minuta):'))
    	    ->setOptions ( array ('size' => '30') )
    	    ->setRequired ( false )
    	    ->addValidator(new Zend_Validate_Int())
    	    ->setDecorators($this->elementDecorators)
    	    ->addFilter('StringTrim');
	    
	    $difficulty = new Zend_Form_Element_Text('difficulty', array ('class' => 'form-control' ));
	    $difficulty	->setLabel ( $tr->_('Veshtiresia') )
	    ->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
	    ->setOptions ( array ('size' => '30') )
	    ->setRequired ( false )
	    ->setDecorators($this->elementDecorators);
		        
		$category = new Zend_Form_Element_Text('category', array ('class' => 'form-control' ));
		$category	->setLabel ( $tr->_('Kategoria') )
				->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->addValidator('StringLength', false,array(1,30))
				->setDecorators($this->elementDecorators);

		
		$cuisine = new Zend_Form_Element_Text('cuisine', array ('class' => 'form-control' ));
		$cuisine	->setLabel ( $tr->_('Lloji i Kuzhines') )
		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( true )
		->addValidator('StringLength', false,array(1,30))
		->setDecorators($this->elementDecorators);
		
		$meal = new Zend_Form_Element_Text('meal', array ('class' => 'form-control' ));
		$meal	->setLabel ( $tr->_('Vakti') )
		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( false )
		->setDecorators($this->elementDecorators);
		
		$receipt_type = new Zend_Form_Element_Text('receipt_type', array ('class' => 'form-control' ));
		$receipt_type	->setLabel ( $tr->_('Lloji i Recetes') )
		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( false )
		->setDecorators($this->elementDecorators);
		
		$seasonality = new Zend_Form_Element_Text('seasonality', array ('class' => 'form-control' ));
		$seasonality	->setLabel ( $tr->_('Sezonaliteti') )
		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( false )
		->setDecorators($this->elementDecorators);
		
		$base_product = new Zend_Form_Element_Text('base_product', array ('class' => 'form-control' ));
		$base_product	->setLabel ( $tr->_('Produkti Baze') )
		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( true )
		->addValidator('StringLength', false,array(1,30))
		->setDecorators($this->elementDecorators);
		
		$festivity = new Zend_Form_Element_Text('festivity', array ('class' => 'form-control' ));
		$festivity	->setLabel ( $tr->_('Festa') )
		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( false )
		->setDecorators($this->elementDecorators);
		
		$video = new Zend_Form_Element_Text('video', array ('class' => 'form-control' ));
		$video	->setLabel ($tr->_('Youtube Video:'))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
        		->addValidator('StringLength', false, array(1, 250))  
				->setDecorators($this->elementDecorators)
				->addFilter('StringTrim');
		
		    
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $title )
				->addElement ( $description )
				->addElement ( $instructions )
				->addElement ( $author )
				->addElement ( $publish_date )
				->addElement ( $publish_time )
				->addElement ( $servings )
				->addElement ( $total_time )
				->addElement ( $difficulty )
				->addElement ( $category )
				->addElement ( $cuisine )
				->addElement ( $meal )
				->addElement ( $receipt_type )
				->addElement ( $seasonality )
				->addElement ( $base_product )
				->addElement ( $festivity )
				->addElement ( $video )
				->addElement ( $submit );
		
		$this->addDisplayGroups(array(
		    'left' => array(
		        'elements' => array("title", "description", "instructions", "author", "publish_date", "publish_time", "servings", "total_time", "difficulty"),
		    ),
		    'right' => array(
		        'elements' => array("category", "cuisine", "meal", "receipt_type", "seasonality", "base_product", "festivity", "video"),
		    ),
		    'bottom' => array(
		        'elements' => array('submit_form'),
		    )
		));
			
		$this->setDisplayGroupDecorators(array('Description', 'FormElements', 'Fieldset'));
	}
}

?>
