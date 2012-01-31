<?php

/**
 * This is the only file that is called
 * when a user navigates to any URL
 * within your application. It includes
 * the bootstrap file and sets two constants
 * that are needed throughout the script 
 *
 * @package - Index.php
 *
 */

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

if(isset($_GET['url'])) {
	$url = $_GET['url'];
} else {
	$url = '';
}

require_once(ROOT . DS . 'library' . DS . 'bootstrap.php');