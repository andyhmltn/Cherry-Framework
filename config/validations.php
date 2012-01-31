<?php

/**
 * This file is to define validations. There are some
 * Here by default such as 'email' and 'URL' but this
 * allows you to add your own to use in conjunction
 * with the validations addon class
 *
 * @package Validations
 * 
 **/

global $validations;

$validations = array(
	'email' => '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'
);