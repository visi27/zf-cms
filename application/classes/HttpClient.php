<?php
class  HttpClient
{
	protected $client;

	protected $secretKey;

	protected $appName;

	public function __construct($appName,$secretKey)
	{
		$this->client = new Zend_Http_Client();
		
		$this->secretKey = $secretKey;
		
		$this->appName = $appName;
	}

	public function call($method, $args, $oldTop = false)
	{
		//Select Web Service URI based on Top version to use
		if($oldTop){
			$uri = Zend_Registry::get('config')->api->path_old_top. '/' . $method;
		}else{
			$uri = Zend_Registry::get('config')->api->path. '/' . $method;
		}
		
	
		$this->client->setUri($uri);

		// setup all the arguments
		$this->client->setParameterPost('appName', $this->appName);

		foreach($args as $k=>$v)
			$this->client->setParameterPost($k, $v);

		$this->client->setParameterPost('auth', $this->signArgs($args));

		$result = $this->client->request(Zend_Registry::get('config')->api->httpmethod );

		return $result->getBody();

	}
	
	private function signArgs($args){
		$args['appName'] = $this->appName;
		ksort($args);
		$a = '';
		foreach($args as $k => $v)
		{
			$a .= $k . $v;
		}
		return md5($this->secretKey.$a);
	}
}

?>