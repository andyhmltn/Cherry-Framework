<?php

/**
 * Define your custom routing rules here using REGEX.
 * On the left is the URL you'd like to redirect
 * On the right is the URL you'd like to redirect to 
 *
 * @package Routing class
 * 
 **/

$routes = array(
	'/example\/route\/?(.*?)/' => 'goes/here/\1'
);