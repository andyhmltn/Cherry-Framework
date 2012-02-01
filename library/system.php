<?php
/**
 *
 * The Cherry framework
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Framework
 * @package    Cherry Framework
 * @author     Andy Hamilton <andyhmltn@gmail.com>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    0.3a
 * @see        NetOther, Net_Sample::Net_Sample()
 * @since      File available since Release 1.2.0
 * @deprecated File deprecated in Release 2.0.0
 */

session_start();

/**
 * Unset globals - Unsets all the global variables to reduce
 * a security risk.
 *
 * @package UnsetGlobals()
 * @author Unknown - http://pastebin.com/gaUujxAQ
 **/

function unsetGlobals(){
	if(ini_get('register_globals')) {
		$allvars = array_merge($_COOKIE, $_POST, $_GET, $_SERVER);
	    if(isset($_SESSION)){
	   		$allvars = array_merge($allvars, $_SESSION);
	   	}
	    
		foreach($_FILES as $key => $val) {
	    	$allvars[] = $k;
	    	$allvars[] = $k.'_name';
	    	$allvars[] = $k.'_type';
	    	$allvars[] = $k.'_error';
	    	$allvars[] = $k.'_size';
	    }

	    // exceptions if someone where to set e.g. index.php?_GET=foo
	    $except = array('_POST', '_GET', '_COOKIE', '_FILES', '_ENV', '_SERVER', 'GLOBALS', '_SESSION', 'ikugb', 'except',
						'allvars', 'key', 'val');
	    
		foreach($allvars as $key => $val) {
			if(!in_array($key, $except)) {
				if(isset($GLOBALS[$key])) {
					unset($GLOBALS[$key]);
 				}
			}
		}
		
		unset($allvars, $except, $key, $val);
	}
}

/**
 * Converts an array (incl. multidimensionals) to
 * an object for use with the SQLFrame Class.
 * 
 * @package arrayToObject()
 *
 **/

function arrayToObject($d) {
                if (is_array($d)) {
                        /*
                        * Return array converted to object
                        * Using __FUNCTION__ (Magic constant)
                        * for recursive call
                        */
                        return (object) array_map(__FUNCTION__, $d);
                }
                else {
                        // Return object
                        return $d;
                }
}

/**
 * Escapes a string. If $x is an array the function
 * uses 'array_map' to cycle through and apply the function to
 * all of the array values.
 *
 * @package escapeString()
 * 
 **/

function escapeString($x) {
	if(is_array($x)) {
		$x = array_map('escapeString', $x);
	} else {
		//$x = stripslashes($x);
		$x = htmlentities($x);
	}
	return $x;
}

/**
 * Applies the 'escapestring' function to _GET, _POST and Cookies.
 *
 * @package escapeGlobals()
 * 
 **/

function escapeGlobals() {
	if (get_magic_quotes_gpc()) {
		$_GET = escapeString($_GET);
		$_POST = escapeString($_POST);
		$_COOKIE = escapeString($_COOKIE);
	}
}

/**
 * If a method exists then runs it. Written to save
 * repeating code constantly.
 *
 * @package ifExistsRun()
 * 
 **/

function ifExistsRun($controller, $action, $arguments = NULL) {
	if ((int)method_exists($controller, $action)) {
		call_user_func_array(array($controller,$action),$arguments);
	}
}

/**
 * Opens config/routing.php and preg_replaces $url with the contents
 *
 * @package route
 * 
 **/

function route($url) {
	global $routes;
	foreach ($routes as $pattern => $result ) {
            if ( preg_match( $pattern, $url ) ) {
				return preg_replace( $pattern, $result, $url );
			}
	}

	return ($url);
}


/**
 * Escapes, separates and then directs the URL.
 *
 * @package readURL()
 * 
 **/

function readURL() {
	global $url;

	if($url !== '') {
		$url = route($url);
		$urlArray = explode("/",$url);
	}


	if(isset($urlArray)) {
		$controller = escapeString($urlArray[0]);
		array_shift($urlArray);
		if(isset($urlArray[0])) { $action = escapeString($urlArray[0]); } else { $action = 'index'; }
		if($action == '') {
			$action = 'index';
		} elseif($action == '_before' || $action == '_after') {
			$action = substr($action, 1);
		}
		array_shift($urlArray);
		$arguments = escapeString($urlArray);
	} else {
		$controller = DEFAULT_CONTROLLER;
		$action = DEFAULT_ACTION;
		$arguments = '';
	}
	

	$controllerName = $controller;
	$controller = ucwords($controller);
	if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . rtrim($controller, 's') . '.php')) {
		$model = rtrim($controller, 's');
	} else {
		$model = 'none';
	}
	
	$controller .= 'Controller';
	if(class_exists($controller)) {
		$dispatch = new $controller($model,$controllerName,$action);
	} else {
		header("location: " . BASE_PATH . "/errors/404");
		exit();
	}

	if ((int)method_exists($controller, $action)) {
		if(!isset($dispatch->except)) { $dispatch->except = array('none'); }
		
		if(!in_array($action, $dispatch->except)) {
			ifExistsRun($controller, '_before', $arguments);
		}
		call_user_func_array(array($dispatch,$action),$arguments);
		
		if(!in_array($action, $dispatch->except)) {
			ifExistsRun($controller, '_after', $arguments);
		}
	}
}

/**
 * Redirects to a certain controller/action
 *
 * @package redirectTo()
 * 
 **/

function redirectTo($controller, $action = '', $queryString = NULL) {
	$url = BASE_PATH . DS . $controller;
	if($action !== '') {
		$url .= DS . $action;
	}
	if($queryString !== NULL) {
		$url .= DS . $queryString;
	}
	header("location: $url");
}

/**
 * A useful little function to check if a word
 * has an 's' and if it doesn't append it
 *
 * @package pluralise()
 * 
 **/

function pluralise($string) {
	if(substr($string, -1) !== 's') {
		$stringNP = $string; 
		$string .= 's'; 
	} else {
		$stringNP = substr($string, 0, -1);
	}	
	return array('np' => $stringNP, 'p' => $string);
}

/**
 * Sets the logging paths and the error reporting
 * dependent on config/config.php
 *
 * @package setApacheSettings()
 * 
 **/

function setApacheSettings() {
	ini_set('log_errors', 'On');
	ini_set('error_log', ROOT . DS . 'data' . DS . 'logs' . DS . 'php_errors.log');
	session_save_path(ROOT . DS . 'data' . DS . 'sessions');
	if (DEVELOPMENT_ENVIRONMENT == true) {
		error_reporting(E_ALL);
		ini_set('display_errors','On');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors','Off');
	}	
}

/**
 * When a class is called this checks all of the
 * possible locations
 *
 * @package __AutoLoad()
 * 
 **/

function __autoload($class) {

	global $log;

	if (file_exists(ROOT . DS . 'library' . DS . strtolower($class) . '.class.php')) {
		require_once(ROOT . DS . 'library' . DS . strtolower($class) . '.class.php');
	} else if (file_exists(ROOT . DS . 'library' . DS . 'addons' . DS . strtolower($class) . '.class.php')) {
		require_once(ROOT . DS . 'library' . DS . 'addons' . DS . strtolower($class) . '.class.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($class) . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($class) . '.php');
	} else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($class) . '.php')) {
		require_once(ROOT . DS . 'application' . DS . 'models' . DS . strtolower($class) . '.php');
	} else {
		$log = New Log();
		$log->logError('Class not found ' . $class, 'CLASS');
	}

}

/**
 * Creates global instances of the logging 
 * and validator classes.
 * 
 * @package N/A
 * 
 **/

$log =& new Log();
$validate =& new Validator();

/**
 * Runs some of the above functions required to
 * operate the framework.
 *
 * @package N/A
 * 
 **/

setApacheSettings();
escapeGlobals();
unsetGlobals();
readURL();