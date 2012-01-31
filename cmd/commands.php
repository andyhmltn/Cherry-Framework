<?php
/* Commands for the command line */

function colourText($text, $color = "red") {
	if(isset($_POST['os'])) {
		if($_POST['os'] == 'Windows') {
			return $text;
		}
	}
	$colors = array(
		"red" => "31",
		"green" => "32",
		"yellow" => "33",
		"blue" => "34",
		"pink" => "35",
		"cyan" => "36"
	);
	return "\033[" . $colors[$color] . "m" . $text . "\033[0m";
}
function gen_hash($arg1 = NULL) {
	return $arg1[1] . ' => ' . hash('sha256', '4224' . $arg1[1] . '4224');
} 

function greetings() {
	return 'Hello there! Welcome to the command line for the UserClass Framework';
}

function help() {
	return 'Here are a number of commands and their functions:
	
	----Basic command line functions-------
	`exit` - Quits the command line
	`generate key arg1` - Hashes arg1 for use with config/config.php and accessing the command line
	`key` - Displays the current key in use
	`set url` - Changes the URL to interact with (Important: This function has no arguments)
	
	----Database management---------------
	`db backup arg1` - Backs up the contents (not the structure) of table `arg1.` 
	`db restore arg1` - Restores the contents (not the structure) of a table from the file `data/db/arg1`
	
	----Controller/Model management-------
	`create controller arg1 arg2 arg3` etc - Creates a controller. Arg1 being the name, arg2,3,4 etc being actions within that 
	controller. To disable pluralisation of the controller name add a "%" on the end of arg1. Eg: arg1 produces arg1sController,
	arg1% produces arg1Controller. 
	
	----log management-------
	`clear logs` - Clears all logs in data/logs
	`mark logs arg1`` - For marking certain positions in logs eg: ******marker: arg1******
	';

}

function clearLogs() {
	$log[0] = 'errors.log';
	$log[1] = 'php_errors.log';
	$log[2] = 'cmd.log';
	$logDir = ROOT . DS . 'data' . DS . 'logs' . DS;
	foreach($log as $lf) {
		$logFile = fopen($logDir . $lf, 'w');
		if(fwrite($logFile, "***Logs cleared " . date('Y-m-d H:i:s') . " via command line ***\n")) {
			fclose($logFile);
			unset($logFile);
		}
	}
	echo colourText('All logs have been cleared', 'green');
}

function logMarker($marker) {
	$log[0] = 'errors.log';
	$log[1] = 'php_errors.log';
	$log[2] = 'cmd.log';
	$logDir = ROOT . DS . 'data' . DS . 'logs' . DS;
	foreach($log as $lf) {
		$logFile = fopen($logDir . $lf, 'a');
		if(fwrite($logFile, "***Marker: " . $marker . "***\n")) {
			fclose($logFile);
			unset($logFile);
		}
	}
	echo colourText('Marker added to logs: ' . $marker, 'green');
}
function dbBackup($table = '*') {  
	$connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Mysql Error');
	mysql_select_db(DB_NAME, $connection);
	if($table == '*') {
		$fileName = 'globalBackup';
	} else {
		$fileName = $table;
	}
	$file = ROOT . '/data/db/' . $fileName . '_' . date('YmdHis') . '.sql'; 
	$query = "SELECT * INTO OUTFILE '$file' FROM $table";  
	if($result = mysql_query($query)) {
		echo colourText("Table '$table' backed up to " . ROOT . '/data/db/' . $fileName . date('YmdHis') . '.sql', 'green');
	} else {
		echo colourText('There was an error with your request. Please check the table exists', 'red');
		$log = new Log();
		$log->logCMD($query, 'MYSQL');
	}
}

function dbRestore($fileName = '*') {
	$fileName = str_replace(".sql", "", $fileName);
	$table_name = substr($fileName, 0, -15);
	$file = ROOT . '/data/db/' . $fileName . '.sql'; 
	$connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ('Mysql Error');
	mysql_select_db(DB_NAME, $connection);
	$query = "TRUNCATE $table_name;";
	mysql_query($query);
	$query = "LOAD DATA INFILE '$file' INTO TABLE $table_name";  
	if($result = mysql_query($query)) {
		echo colourText("Table '$table_name' restored from " . $file, 'green');
	} else {
		echo colourText('There was an error with your request. Please check the table exists', 'red');
		$log = new Log();
		$log->logCMD($query, 'MYSQL');
	}
}


function createController($commands) {
	$args = explode(" ", $commands);
	$controllerNameNP = array_shift($args);
	
	if(substr($controllerNameNP, -1) == '%') {
		$controllerNameNP = rtrim($controllerNameNP, '%');
		$controllerNameP = $controllerNameNP;
	} elseif(substr($controllerNameNP, -1) !== 's') {
		$controllerNameP = $controllerNameNP . 's';
	} else {
		$controllerNameP = $controllerNameNP;
		$controllerNameNP = rtrim($controllerNameNP, 's');
	}

	
	$file = fopen("../application/controllers/" . $controllerNameP . 'controller.php', "w");
	$contents = "<?php

class " . ucFirst(strtolower($controllerNameP)) . "Controller extends Controller {";
		
	if(count($args) == 0) {
		$contents .= "
	function index() {
		#index
	}\n";
	}
	
	foreach($args as $a) {
		$contents .= "
	function " . $a . "() {
		#". $a . "
	}\n";
	}
	$contents .= 	"\n}\n";
	if(fwrite($file, $contents)) {
		echo colourText('Controller: `' . $controllerNameNP . '` successfully created.', 'green');
	} else {
		echo colourText('Something went wrong. Please try again', 'red');
	}
}
?>