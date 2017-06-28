<?php
/*
if($_SERVER ['REMOTE_ADDR'] != '127.0.0.1')
	die("Not allowed to access the site.");
*/
//session_save_path ('c:\tmp');

// utf8 encoding
header('Content-Type: text/html; charset=utf-8');

// default timezone
date_default_timezone_set('Europe/Berlin');

// define the session cookie name
defined ('__SESSION__')
	or define('__SESSION__', 'shadmin'); //poDal

//define the root
defined ('__ROOT__')
	or define('__ROOT__', dirname(dirname(__FILE__))); 

// Set your web root's path(s) here
defined('WEBROOT_PATH')
	or define('WEBROOT_PATH', __ROOT__ . '/web_root'); 

// Set your library path(s) here - default is the master library/ directory 
defined ('LIBRARY_PATH')
	or define('LIBRARY_PATH', __ROOT__ . '/library');

// Set your application path(s) here - default is the master application/ directory 
defined ('APPLICATION_PATH')
	or define('APPLICATION_PATH', __ROOT__ . '/application');

// Set your Language path here - default is the master application/configs/lang directory
defined ('LANG_PATH')
	or define('LANG_PATH', APPLICATION_PATH . '/configs/lang');

// Include the code library
set_include_path ( get_include_path () . PATH_SEPARATOR . LIBRARY_PATH );	
	
// Include the application library
set_include_path ( get_include_path () . PATH_SEPARATOR . APPLICATION_PATH );

// Allow to display any error
ini_set ( 'display_errors', 'On' );

// Display all errors but not notices
ini_set ( 'error_reporting', E_ALL & ~ E_NOTICE & ~ E_STRICT );

// Set the session name
session_name(__SESSION__);

// Start the session
session_start ();

// create the logger object
$app_logger = new Utility_FileLogger();
// set the logger object into the registry
Zend_Registry::set('applog', $app_logger);

// Get the requested controller name, otherwise consider the default 
$controller = isset($_GET['c'])? htmlentities($_GET['c']):Zend_Registry::get('config')->main_controller;
// Try to load this controller
require_once Dispatcher::loadController($controller);

// autoload class libraries
function __autoload($loadClass) {
	
	require_once 'Zend/Loader/Autoloader.php';
	Zend_Loader_Autoloader::autoload('Zend_Registry');
	
	// load the configuration file
	$config = new Zend_Config_Ini( APPLICATION_PATH . '/configs/application.ini', 'DEVELOPMENT');
	// set the configuration file into the registry
	Zend_Registry::set('config', $config);
	
	//Set Up Zend Translate
	$tr = new Zend_Translate('gettext', LANG_PATH . '/sq.mo', 'al');
	$tr->addTranslation(LANG_PATH . '/en.mo', 'en');
	
	$lang = isset($_SESSION['lang']['selected']) ? $_SESSION['lang']['selected'] : 'al';
	$tr->setLocale($lang);
	Zend_Registry::set('translator', $tr);
	
	// load the language file
	$language = new Zend_Config_Ini( APPLICATION_PATH . '/configs/language.ini', 'albanian');
	// set the configuration file into the registry
	Zend_Registry::set('lang', $language);
	
	$className = str_replace("_", DIRECTORY_SEPARATOR , $loadClass).".php";

    $canNotIncludeFile = false;
	// check first the local application class path
	if (file_exists ( APPLICATION_PATH . DIRECTORY_SEPARATOR . 
			Zend_Registry::get('config')->class_path . DIRECTORY_SEPARATOR . $className  )){
		// load the needed file
		require_once  APPLICATION_PATH . DIRECTORY_SEPARATOR .
			Zend_Registry::get('config')->class_path . DIRECTORY_SEPARATOR . $className;
	
	//look into the include path array
	}else { 	
		// get the include path as an array
		$inclArray = explode( PATH_SEPARATOR, get_include_path() );	
			
		// dont consider the local application class path
		array_pop($inclArray); 

		// look inside every directory specified in the include_path
		for($i=sizeof($inclArray)-1; $i>=0; $i--){
			// the file path to be included
			$filePath =  $inclArray[$i] . DIRECTORY_SEPARATOR . $className;
			// include the file if it exists
			if(file_exists( $filePath )){		
				require_once $filePath;
				return; //exit
				
			}else {
				$canNotIncludeFile = true;
			}
		}
	}
	// error, file not found
	if($canNotIncludeFile){
		die("Autoloadeer Error: File [<b>".$loadClass."]</b> not found.");
	}
}
// set the session's expiry time in server equal to the database expiry time
//ini_set('session.gc_maxlifetime', Zend_Registry::get('config')->cookie_life);