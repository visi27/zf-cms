<?php

class Form_Users extends Zend_Form {
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
				  
		$username = new Zend_Form_Element_Text('username', array ('class' => 'form-control' ));
		$username	->setLabel ( $tr->_('Username:') )
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', false, array(3, 20))
				->addValidator('regex', false ,array('/(^[A-Za-z0-9_.]*)$/'))
				->addFilter('StringTrim');

		$password = new Zend_Form_Element_Password('password', array ('class' => 'form-control' ));
		$password	->setLabel ( $tr->_('Password:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setAttrib('onclick', '$(this).val( "" );')
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', true, array(8, 32))
				->addFilter('StringTrim');

		$fullname = new Zend_Form_Element_Text('fullname', array ('class' => 'form-control' ));
		$fullname	->setLabel ( $tr->_('Emri i Plote:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', true, array(5, 45))
				->addValidator(new Zend_Validate_Alpha(array('allowWhiteSpace' => true)))
				->addFilter('StringTrim');		
		
		$ip = new Zend_Form_Element_Text('ip', array ('class' => 'form-control' ));
		$ip	->setLabel ( $tr->_('Ip adresa:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setDecorators($this->elementDecorators)
				->addValidator(new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_IP))
				->addFilter('StringTrim');	
		
		$roleId = new Zend_Form_Element_Text('role_id', array ('class' => 'form-control' ));
		$roleId	->setLabel ( $tr->_('Roli i Aksesit te Modulit:' ))
				->setOptions ( array ('size' => '30', 'readonly' => true, 'icon'=>'ui-icon-circle-triangle-s' ) )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->setAttrib('onfocus', '$(this).val("");')
				->setAttrib('onclick', '$(this).autocomplete( "search", $(this).val() );')
				->addValidator('regex', false ,array('/(^[0-9]+(\s)*(\-)*(\s)*[A-Z\a-z\0-9\_.]*)$/'))
				->addValidator('StringLength', array('min' => 4))
				->addFilter('StringTrim');		
		


		$isActive = new Zend_Form_Element_Select('isactive', array ('class' => 'form-control' ));
        $isActive->setLabel($tr->_('Aktive:'))
        		->setDecorators($this->elementDecorators)
              	->setMultiOptions(array('0'=>$tr->_('No'), '1'=>$tr->_('Yes')))
              	->setRequired(true)->addValidator('NotEmpty', true);		
              	
		$description = new Zend_Form_Element_Text('description', array ('class' => 'form-control' ));
		$description	->setLabel ( $tr->_('Pershkrimi:' ))
				->setOptions ( array ('size' => '30') )
				->setRequired ( false )
				->setDecorators($this->elementDecorators)
				->addValidator('StringLength', array('min' => 2))
				->addValidator(new Zend_Validate_Alnum(array('allowWhiteSpace' => true)))
				->addFilter('StringTrim');
				
		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-primary' )  );
		$submit	->setLabel ( $tr->_('Ruaj') )
				->setDecorators ( $this->buttonDecorators );
		
		
		// attach elements to form
		$this	->addElement ( $username )
				->addElement ( $password )
				->addElement ( $fullname )
				->addElement ( $ip )
				->addElement ( $roleId )
				->addElement ( $isActive )
				->addElement ( $description )
				->addElement ( $moduleId )
				->addElement ( $mode )
				->addElement ( $rowId )
				->addElement ( $submit );
	}
}

?>