<?php
session_start();
//**COMMAND LINE OPERATOR**/
include '../config/config.php';
include 'commands.php';
include '../library/log.class.php';
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

$log = new Log();

if(!DEVELOPMENT_ENVIRONMENT) { die('Invalid request'); } #If the framework is in production mode don't allow requests
if(!isset($_POST['key'])) { $_POST['key'] = 'NULL'; } #If there is no key set it to null to stop warnings

$_key = $_POST['key'];
if(isset($_POST['connection'])) {
	$log->logCMD('Connection established via command line', 'CONNECTION');
}

if(isset($_POST['command'])) {
	if(preg_match('/generate key ([^.\/]+)*/', $_POST['command'], $_args)) {
		echo gen_hash($_args);
	} else {
		if($_key == DEVELOPMENT_KEY) {
			$commands = array(
				'/help/' => 'help',
				'/create controller ([^.\/]+)*/' => 'createController',
				'/clear logs/' => 'clearLogs',
				'/mark logs ([^.\/]+)*/' => 'logMarker',
				'/db backup ([^.\/]+)*/' => 'dbBackup',
				'/db restore ([^.\/]+)*/' => 'dbRestore'
				);
			$i = 0;
			foreach($commands as $a => $b) {
				if(preg_match($a, $_POST['command'], $_args)) {
					unset($_args[0]);
					$log->logCMD("'" . $_POST['command'] . "' entered", 'COMMAND');
					echo call_user_func_array($commands[$a], $_args);
					break;
				} else {
					$i++;
				}
			}
			if($i == count($commands)) {
				echo colourText('That command was unrecognised. Type `help` for a list of available commands.', 'red');
				$log->logCMD('User entered invalid command: ' . $_POST['command'], 'ERROR');
			}
		} else {
			$log->logCMD('User not authorised. Attempt made with key: ' . $_POST['key'], 'AUTH');
			echo colourText('You\'re not authorised to do that', 'red');
		}
	}
}
?>