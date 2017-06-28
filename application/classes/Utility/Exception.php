<?php

class Utility_Exception extends Zend_Exception {
	
	public function __construct($msg = '', $code = 0, Exception $previous = null){
		
		// log the error to analyze it later
		Zend_Registry::get('applog')->log($msg);
		
		parent::__construct($msg, (int) $code, $previous);
	}
	
}

?>