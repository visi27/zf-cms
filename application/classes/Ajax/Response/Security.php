<?php

class Ajax_Response_Security {

	public function Role(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	
	public function Users(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function Acl(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function AcdRole(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function Acd(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function Acr(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
}
?>