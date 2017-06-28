<?php
/**
 *
 * @author Administrator
 * @since 15.Oct.2012 15:30 
 *
 */

class Form_Login extends Zend_Form {
	public $formDecorators = array (array ('FormElements' ), array ('Form' ) );
	public $elementDecorators = array(
		    'ViewHelper',
		    'Description',
		    'Errors',
		    array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class'=>'login-input')),
		    array(array('td' => 'HtmlTag'), array('tag' => 'td')),
		    array('Label', array('tag' => 'td')),
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

				 		  
		$username = new Zend_Form_Element_Text('username', array ('class' => 'form-control', 'placeholder' => 'Perdoruesi', 'autocomplete'=>'off' ));
		$username	->setLabel ( $tr->_('Perdoruesi:') )
				
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setDecorators($this->elementDecorators)
				->addDecorator('Label', array('class' => 'control-label visible-ie8 visible-ie9'))
				->addValidator('StringLength', false, array(3, 20))
				->addFilter('StringTrim');

		$password = new Zend_Form_Element_Password('password', array ('class' => 'form-control', 'placeholder' => 'Fjalekalimi' ));
		$password	->setLabel ( $tr->_('Fjalekalimi:') )
				->setOptions ( array ('size' => '30') )
				->setRequired ( true )
				->setAttrib('onclick', '$(this).val( "" );')
				->setDecorators($this->elementDecorators)
				->addDecorator('Label', array('class' => 'control-label visible-ie8 visible-ie9'))
				//->addValidator('StringLength', false, array(4, 12))
				->addFilter('StringTrim');
		

		$submit = new Zend_Form_Element_Submit ( 'submit_form', array ('class' => 'submit btn btn-success uppercase' ) );
		$submit	->setLabel ( $tr->_('Login') )
				->setAttrib('onclick', '$("#password").val( CryptoJS.MD5($("#password").val()).toString() );')
				->setDecorators ( $this->buttonDecorators );
		
		// attach elements to form
		$this	->addElement ( $username )
				->addElement ( $password )
				->addElement ( $submit )
				->addElement ( $mode )
				;
	}
}

?>
