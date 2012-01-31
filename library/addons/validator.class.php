<?php

/**
 * This class is for validating supplied
 * variables using REGEX in a 'validations'
 * file.
 *
 * @package Validator
 * 
 **/

class Validator {
	
	/**
	 * These are the variables the class
	 * will need to use.
	 */

	protected $validationsPath;
	protected $errors = array();

	/**
	 * Called when a new instance of validator
	 * is called. However: An argument is only
	 * needed if you want to run the validations
	 * using a file that is not the default one.
	 * See config/validations.php for an example.
	 *
	 * @package __construct()
	 *
	 **/
	
	function __construct($path = NULL) {
		if($path !== NULL) {
			$this->validationsPath = $path;
		} else {
			$this->validationsPath = ROOT . DS . 'config' . DS . 'validations.php';
		}
		include($this->validationsPath);
	}
	
	/**
	 * This is the core function. Here is an
	 * explanation of the different arguments
	 * that are needed:
	 *		
	 *		$variable - The variable to validate.
	 *		$type     - The type of validation. See config/validations.php for a list.
	 *		$message  - The message to return if the validation is not met.
	 *
	 * To be used in conjuction with returnErrors()
	 *
	 * @package validate()
	 *
	 **/

	function validate($variable, $type, $message) {
		global $validations;
		
		if(!isset($validations[$type])) { return FALSE; }
		
		if(!preg_match($validations[$type], $variable)) {
			array_push($this->errors, $message);
		}
		return $this->errors;
	}

	/**
	 * Similar to the validate() function
	 * but this just makes sure $variable is
	 * the same length or more as specified by
	 * $length.
	 *
	 * @package validate_length()
	 *
	 **/

	function validate_length($variable, $length, $message) {
		global $validations;
		
		if(strlen($variable) < $length) {
			array_push($this->errors, $message);
		}

		return $this->errors;
	}
	
	/**
	 * If any validations where not met this
	 * function will return the errors (an array
	 * with the $message argument from each validation
	 * you specified.) If the validations where met
	 * and everything went well it will return FALSE.
	 *
	 * @package returnErrors()
	 *
	 **/

	function returnErrors() {
		if(count($this->errors) > 0) {
			$errors = $this->errors;
			$this->errors = array();
			return $errors;
		} else {
			return FALSE;
		}
	}
	
}
