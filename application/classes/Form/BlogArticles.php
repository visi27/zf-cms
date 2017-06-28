<?php

class Form_BlogArticles extends Zend_Form {
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
	
		$blog_category_id = new Zend_Form_Element_Text('blog_category_id', array ('class' => 'form-control' ));
		$blog_category_id	->setLabel ( $tr->_('Kategoria') )
    		->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
    		->setOptions ( array ('size' => '30') )
    		->setRequired ( true )
    		->addValidator('StringLength', false,array(1,30))
    		->setDecorators($this->elementDecorators);
		
		$title = new Zend_Form_Element_Text('title', array ('class' => 'form-control' ));
		$title	->setLabel ($tr->_('Titulli:'))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
        		->addValidator('StringLength', false, array(1, 250))  
				->setDecorators($this->elementDecorators)
				->addFilter('StringTrim');
		
		$subtitle = new Zend_Form_Element_Text('subtitle', array ('class' => 'form-control' ));
		$subtitle	->setLabel ($tr->_('Nentitulli:'))
		->setOptions ( array ('size' => '30') )
		->setRequired ( false )
		->addValidator('StringLength', false, array(1, 250))
		->setDecorators($this->elementDecorators)
		->addFilter('StringTrim');
		
		$author = new Zend_Form_Element_Text('author', array ('class' => 'form-control' ));
		$author	->setLabel ( $tr->_('Autori') )
		->setAttrib('onclick', '$(this).val("");$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( true )
		->addValidator('StringLength', false,array(1,30))
		->setDecorators($this->elementDecorators);
		
		$prepared_by = new Zend_Form_Element_Text('prepared_by', array ('class' => 'form-control' ));
		$prepared_by	->setLabel ( $tr->_('Pergatiti') )
		->setAttrib('onclick', '$(this).val("");$(this).autocomplete( "search", $(this).val() );')
		->setOptions ( array ('size' => '30') )
		->setRequired ( true )
		->addValidator('StringLength', false,array(1,30))
		->setDecorators($this->elementDecorators);
		
		$intro_text = new Zend_Form_Element_Textarea('intro_text', array ('class' => 'form-control' ));
		$intro_text	->setLabel ($tr->_('Intro:'))
		->setOptions ( array ('rows' => '4', 'cols'=>'28') )
		->setRequired ( true )
		->addValidator('notEmpty', true, array('messages' => array(
		    'isEmpty' => Zend_Registry::get('lang')->form->notempty)))
		    ->setDecorators($this->elementDecorators)
		    ->addFilter('StringTrim');
		
		$full_text = new ZExt_Form_Element_CKEditor('full_text');
		$full_text	->setLabel ($tr->_('Artikulli:'))
		->setOptions ( array ('rows' => '7', 'cols'=>'28') )
		->setRequired ( true )
		->addValidator('notEmpty', true, array('messages' => array(
		    'isEmpty' => Zend_Registry::get('lang')->form->notempty)))
		    ->setDecorators($this->elementDecorators)
		    ->addFilter('StringTrim');
		
// 		$published = new Zend_Form_Element_Select('published', array ('class' => 'form-control' ));
// 		$published	->setLabel ( 'I Publikuar:' )
// 		->setOptions ( array ('size' => '1',  'icon'=>'ui-icon-circle-triangle-s' ) )
// 		->setRequired ( true )
// 		->addValidator('notEmpty', true, array('messages' => array(
// 		    'isEmpty' => Zend_Registry::get('lang')->form->notempty)))
// 		    ->setDecorators($this->elementDecorators)
// 		    ->addMultiOption('','Zgjidh.. ')
// 		    ->addMultiOptions(array(
// 		        '1' => 'Po',
// 		        '0' => 'Jo'
// 		    ))
// 		    ->addFilter('StringTrim');
		
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
		        
        $publish_time = new Zend_Form_Element_Text('publish_time', array ('class' => 'form-control' ));
        $publish_time	->setLabel ( 'Ora e Publikimit(HH:MM):' )
        ->setOptions ( array ('size' => '25' ) )
        ->setRequired ( true )
        ->addValidator(new Zend_Validate_Date(array('format' => 'H:i',)))
        ->setDecorators($this->elementDecorators);
		        
        $archived = new Zend_Form_Element_Select('archived', array ('class' => 'form-control' ));
        $archived	->setLabel ( 'I Arkivuar:' )
        ->setOptions ( array ('size' => '1',  'icon'=>'ui-icon-circle-triangle-s' ) )
        ->setRequired ( false )
        ->addValidator('notEmpty', true, array('messages' => array(
            'isEmpty' => Zend_Registry::get('lang')->form->notempty)))
            ->setDecorators($this->elementDecorators)
            ->addMultiOption('','Zgjidh.. ')
            ->addMultiOptions(array(
                '1' => 'Po',
                '0' => 'Jo'
            ))
            ->addFilter('StringTrim');
        
        $archive_date = new Zend_Form_Element_Text('archive_date');
        $archive_date	->setLabel (  $tr->_('Data e Arkivimit(dd/mm/yyyy):' ))
            ->setAttrib( 'onfocus', '$(this).datepicker({ dateFormat: "dd/mm/yy" }); $(this).datepicker("show");')
            ->setOptions ( array ('size' => '30') )
            ->setRequired ( false )
                    ->addValidator(new Zend_Validate_Date(
                        array(
                            'format' => 'dd/mm/yyyy',
                        )))
                        ->setDecorators($this->elementDecorators);

        $magazine_nr = new Zend_Form_Element_Text('magazine_nr', array ('class' => 'form-control' ));
        $magazine_nr	->setLabel ($tr->_('Nr. i Revistes:'))
            ->setOptions ( array ('size' => '30') )
            ->setRequired ( false )
            ->addValidator(new Zend_Validate_Int())
            ->setDecorators($this->elementDecorators)
            ->addFilter('StringTrim');

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
		$this->addElement ( $blog_category_id )
			->addElement ( $title )
			->addElement ( $subtitle )
			->addElement ( $author )
			->addElement ( $prepared_by )
			->addElement ( $intro_text)
			->addElement ( $full_text )
// 			->addElement ( $published )
			->addElement ( $publish_date )
			->addElement ( $publish_time )
			->addElement ( $archived)
			->addElement ( $archive_date)
			->addElement ( $magazine_nr )
            ->addElement ( $video )
			->addElement ( $submit );
	}
}

?>
