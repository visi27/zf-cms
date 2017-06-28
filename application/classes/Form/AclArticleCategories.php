<?php

class Form_AclArticleCategories extends Zend_Form {
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

        
        $user = new Zend_Form_Element_Text('user', array ('class' => 'form-control' ));
        $user	->setLabel ( $tr->_('Perdoruesi:') )
        		->setAttrib('onclick', '$(this).val("");$(this).autocomplete( "search", $(this).val() );')
        		->setOptions ( array ('size' => '30') )
        		->setRequired ( true )
        		->addValidator('StringLength', false,array(1,30))
        		->setDecorators($this->elementDecorators);
        
        $category = new Zend_Form_Element_Text('category', array ('class' => 'form-control' ));
        $category	->setLabel ( $tr->_('Kategoria e Artikujve') )
        ->setAttrib('onclick', '$(this).val("");$(this).autocomplete( "search", $(this).val() );')
        ->setOptions ( array ('size' => '30') )
        ->setRequired ( true )
        ->addValidator('StringLength', false,array(1,30))
        ->setDecorators($this->elementDecorators);
        
        $read = new Zend_Form_Element_Select('read', array ('class' => 'form-control' ));
        $read  -> setLabel($tr->_('Lexo:'))
               ->setDecorators($this->elementDecorators)
               ->setMultiOptions(array('0'=>$tr->_('Jo'), '1'=>$tr->_('Po')))
               ->setRequired(true)->addValidator('NotEmpty', true);
        
         
        $write = new Zend_Form_Element_Select('write', array ('class' => 'form-control' ));
        $write  -> setLabel($tr->_('Shkruaj:'))
                ->setDecorators($this->elementDecorators)
                ->setMultiOptions(array('0'=>$tr->_('Jo'), '1'=>$tr->_('Po')))
                ->setRequired(true)->addValidator('NotEmpty', true);
			
				
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $user )
				->addElement ( $category)
				->addElement ( $read)
				->addElement ( $write)
				->addElement ( $submit );
		
		$this->addDisplayGroups(array(
		    'left' => array(
		        'elements' => array('rowId', 'user', 'category'),
		    ),
		    'right' => array(
		        'elements' => array('read', 'write'),
		    ),
		    'bottom' => array(
		        'elements' => array('submit_form'),
		    )
		));
			
		$this->setDisplayGroupDecorators(array('Description', 'FormElements', 'Fieldset'));
	}
}

?>



