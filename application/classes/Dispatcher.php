<?php
class Dispatcher {
	
	private static $controllerName;

	// kerkon nje modifikim, per te redirektuar kerkesat me ajax
	// ne momentin kur useri nuk eshte i authentifikuar
	// example: clicking over an autocomplete list while session is expired
	public static function loadController($controller) {
		
		// check authentication before loading
		switch (Authenticate::database ()){
			
			case TRUE:
				$path = self::getControllerFilePath( $controller );
			break;
					
			case FALSE:	
				// ajax request	
				if( (Zend_Registry::get('config')->ajax_controller == $controller ) ){
						
					// redirect the ajax request 	
					$path = self::getControllerFilePath(Zend_Registry::get('config')->redirector);
					
				// normal request
				}else{
					// redirect to the login page
					$path = self::getControllerFilePath(Zend_Registry::get('config')->login_page);
				}				
			break;	
		}
		
		return $path;		
	}
	
	public static function getControllerName(){
		return self::$controllerName;
	}
	
	private static function getControllerFilePath($controllerName) {
		// the default controller name
		$defaultController = Zend_Registry::get('config')->main_controller;
		
		// the directory where the controllers are
		$dirPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 
						Zend_Registry::get('config')->code_path . DIRECTORY_SEPARATOR;
		
		// full path of the default controller
		$defaultPath = $dirPath . $defaultController. '.php';
		
		// the full path of this controller
		$controllerPath = $dirPath . pathinfo ( $controllerName , PATHINFO_BASENAME ). '.php';
		
		if (file_exists ( $controllerPath )) {
			// set the current controller name
			self::$controllerName = $controllerName;
			return $controllerPath;
			
		} elseif (file_exists ( $defaultPath )) { 
			// set defaults
			self::$controllerName = $defaultController;
			return $defaultPath;
			
		}else{
			// error
			self::$controllerName = null;
			die("Dispatcher Error: File [<b>".$controllerName."]</b> not found.");
		}	
	}
	
	public static function logOut($reload=true) {
		
		// session model
		$session = new Table_Sessions();
		
		// end the database session 
		if(Authenticate::getUserId() && $session->expireSessionById(session_id())){
			
			// load the logs model
			$logs = new Table_AccessLog();
			// store the log
			$logs->createLog("Logout", "User Loged Out", Authenticate::getUserId(), session_id());
		}
		
		// Expire the cookie (session) by seting its lifetime to the past.
		setcookie(session_name(), session_id(), mktime(0, 0, 0, 1, 1, 1980), "/"); 
		
		// unset the session data
		session_unset();
		
		if($reload == true){		
			//reload to the front controller
			header("Location: index.php");
		}
		
	}
	
}
?>