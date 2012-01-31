<?php

/**
 * A logging class called in 'global.php'
 * that writes to data/logs if an error occurs
 *
 * @package Log
 * 
 **/

class Log {
	
	protected $logDir;
	

	/**
	 * This sets an array with the default log
	 * directories within it.
	 *
	 * @package __construct()
	 * 
	 */

	function __construct() {
		$this->logDir = array('default' => ROOT . DS . 'data' . DS . 'logs' . DS . 'errors.log',
							  'cmd' =>  ROOT . DS . 'data' . DS . 'logs' . DS . 'cmd.log');
	}
	
	/**
	 * Logs a traditional error in the file
	 * 'data/logs/errors.log' with a timestamp
	 *
	 * @package LogError()
	 * 
	 **/
	
	function logError($message, $type) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$logFile = fopen($this->logDir['default'], 'a');
		if(isset($_GET['url'])) {
			$url = $_GET['url'];
		} else {
			$url = '/';
		}
		$errorString = date('Y-m-d H:i:s ') . "($ip) - [" . strtoupper($type) . "] => $message in '" . $url . "'\n";
		if(fwrite($logFile, $errorString)) {
			fclose($logFile);
			unset($logFile);
		}
	}
	
	/**
	 * Used by the command line
	 * to log activity within it.
	 *
	 * @package LogCMD()
	 * 
	 **/
	
	function logCMD($command, $type) {
		$ip = $_SERVER['REMOTE_ADDR'];
		$logFile = fopen($this->logDir['cmd'], 'a');
		$commandString = date('Y-m-d H:i:s ') . "($ip) - [" . strtoupper($type) . "] => $command\n";
		if(fwrite($logFile, $commandString)) {
			fclose($logFile);
			unset($logFile);
		}
	}
	
}

?>