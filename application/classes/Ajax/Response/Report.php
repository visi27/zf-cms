<?php

class Ajax_Response_Report {
	
	
	public static function loadReport($reportName,$parameters, $html=false, $write=true)
	{
		$dbHost=Zend_Registry::get('config')->database->host;
		$rptIP = Zend_Registry::get ( 'config' )->reports->server->addr;
		$rptPort = Zend_Registry::get ( 'config' )->reports->server->port;
		$dbName=Zend_Registry::get('config')->database->dbname;
		$userId=Authenticate::getUserId();
		$sessionId=session_id();
		$privateKey="b845Hash";
	
		if(!$html)
			$url="http://$rptIP:$rptPort/BIRT/frameset?__report=".urlencode($reportName);
		else
			$url="http://$rptIP:$rptPort/BIRT/run?__format=html&__report=".urlencode($reportName);
		
		if (is_array($parameters))
		{
			$paramString="";
				
			foreach ($parameters as $key=>$value)
				$paramString.="&".$key."=".$value;
		}
		else
		{
			$paramString=$parameters;
		}
	
		//if ($dbHost=="localhost" || $dbHost=="127.0.0.1" || $rptIP=="localhost" || $rptIP=="127.0.0.1")
		//	$dt=date("Ymdhis");
		//else
		//	$dt = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchOne("select date_format(sysdate(),'%Y%m%d%H%i%s') from dual");
	
		$dtValue = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchOne("select date_format(sysdate(),'%Y%m%d%H%i%s') from dual");
		//$dtValue="20130611170000";
	
		$paramString.="&value_a2=".$sessionId;
		$paramString.="&value_a3=".$dtValue;
		$paramString.="&value_a4=".$dbName;
		$paramString.="&value_a5=".$userId;
	
		$paramArr=Utility_Functions::argsToArray( explode("&",trim($paramString,"&")) , "=" );
		ksort($paramArr);
	
		$md5Value=$privateKey;
		foreach ($paramArr as $key=>$value)
		{
			$md5Value.=$value;
		}
	
		$md5Calculated=md5($md5Value);
	
		$url.= $paramString . "&value_a1=".$md5Calculated;
	
		$html='<div id=subright><iframe src="'.$url.'" frameborder="0" width="100%" height="800" id="reportResult"></iframe></div>';
	
		return $html;
	}
	
	//these 2 functions are user by subclasses of the Report Class
	protected function argsToArray (array $args){
		$params = array();
		foreach ($args as $key => $value){
			$tmp = explode(":", $value);
			$params[$tmp[0]] = $tmp[1];
		}
		return $params;
	}
	
	public function decUrl($url){
		$decodedUrl = rawurldecode($url);
		$src= htmlspecialchars($decodedUrl);
		return $src;
	}
	
	//Blank Function
	public function Blank(Array $parameters, $action="default"){
		$className = __CLASS__.'_Module'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	//PhYsical Test Function
	public function DisplayTest(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	//Functional Courses Function
	public function DisplayFuncCourse(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	//Institutional Courses Function
	public function DisplayInstCourse(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	//security Function
	public function DisplaySecurity(array $parameters, $action="default"){
		
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	//expire certificate Function
	public function DisplayExpireCertificate(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	//service Function
	public function DisplayService(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	//rank Function
	public function DisplayRank(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	//language Function
	public function DisplayLanguage(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	//position time Function
	public function DisplayPositionTime(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	//not assign Function
	public function DisplayNotassign(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	//status unassign Function
	public function StatusUnassign(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	//user Function
	public function DisplayUser(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	//no rank Function
	public function DisplayPeopleWithoutRank(Array $parameters, $action="default"){
	$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	
	//IndividualWithScore Function
	public function IndividualWithScore($parameters){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters);
	}
	
	//IndividualWithoutScore Function
	public function IndividualWithoutScore($parameters){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters);
	}
	
	
	
	//Education Function
	public function IndividualforEducation(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	//manning Function
	public function manningReport(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters);
	}
	
	//manning Old TOP
	public function manningReportPreviousTop(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters);
	}
	
	public function Rollup(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
		
	public function IndividualRollupNoPoint(Array $parameters, $action="default"){
	$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	public function adHoc(Array $parameters, $action="default"){
		$className = __CLASS__.'_Module'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function languageAdHoc(Array $parameters, $action="default"){
		$className = __CLASS__.'_Module'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function Test(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	public function potentialFemale(Array $parameters, $action="default"){
		$className = __CLASS__.'_Module'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function statTwo(Array $parameters, $action="default"){
		$className = __CLASS__.'_Module'.__FUNCTION__;
		$moduleController = new  $className ($parameters, $action);
	}
	
	public function DisplayArchiveStats(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	public function SuperiorRating(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function SuperiorRollup(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function StatSuperior(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function Disciplinary(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
   public function Penal(array $parameters, $action="default"){
		
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function Remark(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function Supervizor(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function Civil(Array $parameters, $action="default"){
		
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function Punishment(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function PunishmentUnit(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function Offence(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	public function OffenceUnit(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
		
		$classObj = new  $className ($parameters, $action);
	}
	
	public function ServiceProblem(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function CivRemark(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function ServiceExpire(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function ContractProblem(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function TopHrRanks(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function ForecastReport(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function OrderError(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function RequiredSExpire(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function NoRequiredS(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function FullRemark(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	public function OctoberRollup(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
	
	public function ManningRec(Array $parameters, $action="default"){
		$className = __CLASS__.'_'.__FUNCTION__;
	
		$classObj = new  $className ($parameters, $action);
	}
}

?>