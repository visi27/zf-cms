<?php

class Ajax_Response_System {

	public function ActionLog(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
		
	public function AccessLog(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function ReportProblem(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	public function BroadCast(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
}
?>