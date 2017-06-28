<?php

class Form_Profile extends Zend_Form {
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
	
	public function __construct($options = null) {
		
		//Get Translator
		$tr = Zend_Registry::get('translator');
		
		// initialize form
		$treeModuleId = intval(substr($options["id"], strrpos($options["id"],"_")+1));
		$this	->setMethod ( 'post' )
				->setDecorators ( $this->formDecorators )
			    ->setAttribs(array('autocomplete'=>'off', "id" => $options["id"]));
				  
		$username = new Zend_Form_Element_Text('username');
		$username	->setLabel ( $tr->_('Username:' ))
				->setOptions ( array ('size' => '30', 'readonly' => 'readonly') )
				->setIgnore(true)
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(3, 20))
				->addValidator('regex', false ,array('/(^[A-Za-z0-9_.]*)$/'))
				->addFilter('StringTrim');

		$password = new Zend_Form_Element_Password('currentPassword');
		$password	->setLabel ( $tr->_('Password:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setAttrib('onclick', '$(this).val( "" );')
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', true, array(4, 12))
				->addFilter('StringTrim');
				
		$newPassword = new Zend_Form_Element_Password('newPassword');
		$newPassword	->setLabel ( $tr->_('Password-i i Ri:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setAttrib('onclick', '$(this).val( "" );')
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', true, array(4, 12))
				->addValidator(new ZExt_Validate_IdenticalField( 'repeatPassword', 'Confirm Password'))
				->addFilter('StringTrim');
				
		$repeatPassword = new Zend_Form_Element_Password('repeatPassword');
		$repeatPassword	->setLabel ( $tr->_('Konfirmo Password-in:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setAttrib('onclick', '$(this).val( "" );')
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', true, array(4, 12))
				->addFilter('StringTrim');

		$fullname = new Zend_Form_Element_Text('fullname');
		$fullname	->setLabel ( $tr->_('Emri i Plote:' ))
				->setOptions ( array ('size' => '30', 'readonly' => 'readonly') )
				->setIgnore(true)
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', true, array(5, 45))
				->addValidator(new Zend_Validate_Alpha(array('allowWhiteSpace' => true)))
				->addFilter('StringTrim');		
    	
		$description = new Zend_Form_Element_Text('description');
		$description	->setLabel ( $tr->_('Pershkrimi:' ))
				->setOptions ( array ('size' => '30', 'readonly' => 'readonly') )
				->setRequired ( false )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', array('min' => 2))
				->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => true)))
				->addFilter('StringTrim');
				
		$submit = new Zend_Form_Element_Button ( 'submit_form', array ('class' => 'submit' ) );
		$submit	->setLabel ( $tr->_('Ruaj' ))
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $fullname )
				->addElement ( $username )
				->addElement ( $password )
				->addElement ( $newPassword )
				->addElement ( $repeatPassword )
				->addElement ( $description );
				//->addElement ( $submit );
	}
}

?>