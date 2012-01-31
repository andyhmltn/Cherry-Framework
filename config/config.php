<?php

/**
 * Configuration file for the cherry framework. 
 * Used to define framework wide constants.
 * 
 * @package Config file
 **/
 

/**
 * Development settings
 * Used when the site isn't live.
 **/

#Set this to false when the site goes into production to stop errors displaying /CMD management.
define('DEVELOPMENT_ENVIRONMENT', TRUE);
#Authentication key for the command line. To set open Plum and type 'generate key password' 
define('DEVELOPMENT_KEY', '');

/**
 * URL Defaults.
 * For when a use navigations to http://root/
 **/

define('DEFAULT_CONTROLLER', 'index');
define('DEFAULT_ACTION', 'index');

/**
 * Application settings
 **/

#Can be used as a default title element.
define('APPLICATION_NAME', '');
#This is important. Set it **WITHOUT** a trailing slash.
define('BASE_PATH', '');

/**
 * MySQL Database Credentials
 * These are essential for database use
 **/

#The database to use
define('DB_NAME', '');
#Your MySQL username
define('DB_USER', '');
#Your MySQL password
define('DB_PASSWORD', '');
#Your current MySQL host
define('DB_HOST', '');
