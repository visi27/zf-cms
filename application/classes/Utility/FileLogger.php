<?php
class Utility_FileLogger{

	protected $writer;
	protected $logger;
	
	public function __construct(){
			$this->writer = new Zend_Log_Writer_Stream('tmp/log.txt');
			$this->logger = new Zend_Log($this->writer);
 	}
	
	public function log($message){
		$this->logger->info("UserId:".Authenticate::getUserId().
							"; IP:". $_SERVER['REMOTE_ADDR'].
							"; SessionId:". session_id().
							";\r\n". $message."\r\n");
	}
	
}
?>