<?php

class Form_BroadCast extends Zend_Form {
	public $formDecorators = array (array ('FormElements' ), array ('Form' ) );
	public $elementDecorators = array(
		    'ViewHelper',
		    'Description',
		    'Errors',
		    array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div')),
		    array(array('td' => 'HtmlTag'), array('tag' => 'td')),
		    array('Label', array('tag' => 'td', 'requiredPrefix' => '* ')),
		);
	public $buttonDecorators = array (array ('ViewHelper' ), array ('HtmlTag', array ('tag' => 'p' ) ) );
	
	
	/**
	 * Ndertoj elementet e formes
	 * 
	 */
	public function __construct($options = null) {
		
		//Get Translator
		$tr = Zend_Registry::get('translator');
		
		// initialize form
		
		$treeModuleId = intval(substr($options["id"], strrpos($options["id"],"_")+1));
		
		$this	->setMethod ( 'post' )
				->setDecorators ( $this->formDecorators )
			    ->setAttribs(array('autocomplete'=>'off', "id" => $options["id"]));
		
			    
		$mode = new Zend_Form_Element_Hidden("form_mode");
		$mode -> setValue('')
				 -> removeDecorator('label');		
		
		
		$rowId = new Zend_Form_Element_Hidden("row_id");
		$rowId -> setValue('')
				 -> removeDecorator('label');

		
		$moduleId = new Zend_Form_Element_Hidden("treeNodeId");
		$moduleId -> setValue( $treeModuleId )
					->setRequired ( true )
				  -> removeDecorator('label');
			
			  
		$title_al = new Zend_Form_Element_Text('title_al');
		$title_al	->setLabel ( $tr->_('Titulli:' ))
					->setOptions ( array ('size' => '30') )
					->setRequired ( true )
					->setDecorators($this->elementDecorators)
					->addValidator('StringLength', false, array(3, 45))					
					->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => true)))
					->addFilter('StringTrim');


		$title_en = new Zend_Form_Element_Text('title_en');
		$title_en	->setLabel ( $tr->_('Title (en):' ))
					->setOptions ( array ('size' => '30') )
					->setRequired ( true )
					->setDecorators($this->elementDecorators)
					->addValidator('StringLength', true, array(3, 45))					
					->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => true)))
					->addFilter('StringTrim');		
		

		$body_al = new Zend_Form_Element_Text('body_al');
		$body_al	->setLabel ( $tr->_('Pjesa Kryesore:' ))
					->setOptions ( array ('size' => '30') )
					->setRequired ( true )
					->setDecorators($this->elementDecorators)
					//->addValidator('StringLength', true, array(3, 100))
					->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => true)))
					->addFilter('StringTrim');

		
		$body_en = new Zend_Form_Element_Text('body_en');
		$body_en	->setLabel ( $tr->_('Body (en):' ))
					->setOptions ( array ('size' => '30') )
					->setRequired ( true )
					->setDecorators($this->elementDecorators)
					//->addValidator('StringLength', true, array(3, 100))
					->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => true)))
					->addFilter('StringTrim');


		$display = new Zend_Form_Element_Select('display');
        $display->setLabel($tr->_('Aktive:'))
        		->setDecorators($this->elementDecorators)
              	->setMultiOptions(array('0'=>$tr->_('No'), '1'=>$tr->_('Yes')))
              	->setRequired(true)->addValidator('NotEmpty', true);	
        

        //$date_created = new Zend_Form_Element_Hidden("date_created");
              	
				
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit' ) );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $title_al )
				->addElement ( $title_en )
				->addElement ( $body_al )
				->addElement ( $body_en )
				->addElement ( $display )
			//	->addElement ( $date_created )
				->addElement ( $moduleId )
				->addElement ( $mode )
				->addElement ( $rowId )
				->addElement ( $submit );
	}
}

?>