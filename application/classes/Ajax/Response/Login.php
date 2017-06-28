<?php
class Ajax_Response_Login {

	public function logOut()
	{
		// close the session
		Dispatcher::logOut($reload = false);
		
		// Encode it and return to the client:
		echo Zend_Json::encode(array("status"=>"true"));
	}
}
?>