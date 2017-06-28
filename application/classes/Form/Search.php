<?php

class Form_Search extends Zend_Form {
	public $formDecorators = array (array ('FormElements' ), array ('Form' ) );
	public $elementDecorators = array(
		    'ViewHelper',
		    'Description',
		    'Errors',
		    array(array('elementDiv' => 'HtmlTag'), array('tag' => 'b')),
		    array(array('td' => 'HtmlTag'), array('tag' => 'td')),
		    array('Label', array('tag' => 'td', 'requiredPrefix' => '* ')),
		);
	public $buttonDecorators = array (array ('ViewHelper' ), array ('HtmlTag', array ('tag' => 'p' ) ) );
	public $outerDivStyle = 'overflow:auto;';
	
	public $innerDivStyle = 'float:left;padding: 5px;';
	public function __construct($options = null) {
		
		//Get Translator
		$tr = Zend_Registry::get('translator');
		
		// initialize form
		$treeModuleId = intval(substr($options["id"], strrpos($options["id"],"_")+1));
		$this	->setMethod ( 'POST' )
				->setDecorators ( $this->formDecorators )
			    ->setAttribs(array('autocomplete'=>'off', "id" => $options["id"]));
		
		$mode = new Zend_Form_Element_Hidden("form_mode");
		$mode -> setValue('')
				 -> removeDecorator('label');		
		/*
		$rowId = new Zend_Form_Element_Hidden("row_id");
		$rowId -> setValue('')
				 -> removeDecorator('label');
			*/	 
		$moduleId = new Zend_Form_Element_Hidden("treeNodeId");
		$moduleId -> setValue( '' )
					->setRequired ( true )
				  -> removeDecorator('label');
		
		$idcard = new Zend_Form_Element_Text('idcard');
		$idcard	->setLabel ( $tr->_('Numri i identitetit: [NID]' ))
				->setOptions ( array ('size' => '20') )
				->setDecorators($this->elementDecorators);
				//->addValidator('StringLength', false, array(4))
				//->addFilter('StringTrim'); 

	    $firstname = new Zend_Form_Element_Text('firstname');
		$firstname	->setLabel ($tr->_( 'Emri:' ))
				->setOptions ( array ('size' => '30') )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(3, 30,'messages'=>array(
						'stringLengthTooShort'=>Zend_Registry::get('lang')->form->search->stringlength)))
				->addValidator(new Zend_Validate_Alpha())
				->addFilter('StringTrim');


		$lastname = new Zend_Form_Element_Text('lastname');
		$lastname	->setLabel ( $tr->_('Mbiemri:' ))
				->setOptions ( array ('size' => '30') )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(1, 30,'messages'=>array(
						'stringLengthTooShort'=>Zend_Registry::get('lang')->form->search->stringlength)))
				->addValidator(new Zend_Validate_Alpha())
				->addFilter('StringTrim'); 		

		
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit' ) );
		$submit	->setLabel ( $tr->_('Kerko' ))
		        ->setAttrib('class', 'submit_form')
		        ->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $idcard )
				->addElement ( $firstname )
				->addElement ( $lastname )
				//->addElement ( $rowId )
				->addElement ( $moduleId)
				->addElement ( $mode);
		

		
		$this->addDisplayGroup( array($idcard,$firstname, $lastname)
				, 'force'
				, array('disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements',
								 'Fieldset',
								array(array('Dashed'=>'HtmlTag'), array('tag'=>'div','id'=>'personal_group', 'style'=>'width:auto; clear:both; padding: 25px 65px 0px 0px;')),
								array('HtmlTag',array('tag' => 'div', 'id'=>'wrapper_div','style'=> $this->outerDivStyle))
						),
					'legend' => ''
				)
		);
				$this->addElement($submit);
				
				
		
		
	}
}

?>
