<?php


class Authenticate {
	
	private static $isAuthenticated;
	private static $description;
	
	CONST SESSION_EXPIRED 	= "Session Expired";
	CONST SESSION_ERROR		= "Session Error";
			
	private function __construct() {
		//$this->setUserDetails ( $userName, $password );
	}
	
	/**
	 * 
	 * We use this method to perform database user authentication.
	 * The username and password provided by the user via html forms
	 * are checked with the ones stored in the users table in database.
	 * @param string $username
	 * @param string $password
	 */
	public static function database($username = null, $password = null ) {
		// the default status
		self::$isAuthenticated = false;
		
		// get the session's data model class
		$sessionObj = new Table_Sessions();
		
		// get the user's data model class
		$userObj = new Table_Users();
		
		try{
			// check that the user is active and can perform actions		
			// Check if the user has already loged in, and created a session
			if($userObj->getDataById(self::getUserId())->id){
				
				// check if session is alive and renew it
				if($userObj->getDataById(self::getUserId())->isactive AND
						$sessionObj->keepSessionAlive(session_id())){ 
					
		          	// user is active and can go on    	
					self::$isAuthenticated = true;	
				
				// expired session
				}else{
					// close the session
					Dispatcher::logOut($reload = false);
					self::$description = self::SESSION_EXPIRED;
				}		
			}
			// The user has not yet loged in. 
			// This could be a request to authenticate.
			// Check if username/password have a value.
			elseif (!empty($username) && !empty($password)){
	
				// check username/password combination
				$userData = $userObj->selectData(array(
						"username" => array( "=" => $username ),
						"password" => array ("=" => $password)));
				$userData = $userData->current();
				
				// If authentication user/pass was successful.
				if($userData->id && $userData->isactive){
				
					// Create the session in database.
					// Check inside this function, to understand how existing user's
					// sessions, having same user_id are expired before starting a new one.
					if($sessionObj->createSessionDb(session_id(), $userData->id)){
						
						//Set the session variables.
						$_SESSION['userId'] = $userData->id;
						$_SESSION['username'] = $username;
						$_SESSION['password'] = $password;
						
						// Set authentication status to TRUE
						self::$isAuthenticated = true;
						
						// get the db logger object
						$dbLog = new Table_AccessLog();
							
						// Store this action into the Database Logs
						$dbLog->createLog("Login", "User Loged In", $userData->id, session_id());
						
					}else{
						// close the session
						Dispatcher::logOut($reload = false);
						// provide description
						self::$description = self::SESSION_ERROR;
					}							
				}
				else{ // Else if, authentication was not successful
					// the value stored in $description, will be used
					// for showing in user interface.
				
					if($userData->id && !$userData->isactive) // Set authentication description.
						self::$description = "This account has been disabled !";
					else{
						self::$description = (self::$description == "")?
						"Invalid username/password combination !":self::$description;
					}
				}					
			}// end elseif			
			
		}// end try
		
		// query not executed because of connection error
		catch (Exception $e){
			// get the file logger object
			$fileLog = Zend_Registry::get('applog');
			//store the message into the log file.
			$fileLog->log($e->getMessage());
			self::$description = "Connection Error. Contact the Administrator !";
		}
			
		return self::$isAuthenticated;
	}
	
	public static function getUserId(){
		return $_SESSION['userId'];
	}
	
	/**
	 * Used in Toe
	public static function getDbVersion(){
		return $_SESSION['version'];
	}
	
	public static function getDbName() {
		$version=self::getDbVersion();
		return Zend_Registry::get('config')->$version->database->dbname;
	}
	*/
	public static function getErrorMsg(){
		return self::$description;
	}

	function __destruct() {
	
	}
}
//============================================================+
// END OF FILE
//============================================================+
?>