<?php

/**
 * This is the templating class used
 * to call all the files for the 'view'
 * aspect of the MVC pattern.
 * 
 * @package Template
 *
 **/

class Template {

	protected $_vars = array();
	protected $_errorsSet = FALSE;
	protected $_errors = array();
	protected $_controller;
	protected $_action;
	
	/**
	 * Sets the controller and action
	 * variables
	 * 
	 * @package __construct()
	 *
	 */

	function __construct($controller, $action) {
		$this->_controller = $controller;
		$this->_action = $action;
	}
	
	/**
	 * This is pretty integral for the relationship
	 * between the controller and the view. When 
	 * working the controller you can call $this->set()
	 * with $name (the name of the variable to be used
	 * by the view. Eg: 'example' => '$example') and $value
	 * (the value of the above variable.) Then when working
	 * in the view file you can call $example and it will
	 * return what you set as $value. This is referenced by
	 * the controller.class.php file so you can type $this->set()
	 * instead of $this->_template->set()
	 *
	 * @package set()
	 *
	 **/

	function set($name, $value) {
		$this->_vars[$name] = $value;
	}

	/**
	 * This adds a value to the _errors
	 * array and then sets _errorsSet to
	 * true.
	 *
	 * @package errorAdd()
	 *
	 **/
	
	function errorAdd($value) {
		if(!$this->_errorsSet) {
			$this->_errorsSet = TRUE;
		}
		array_push($this->_errors, $value);
	}

	/**
	 * Used by the HTML file to display
	 * all the present errors.
	 *
	 * @package errors()
	 *
	 **/
	
	function errors() {
		if(!empty($this->errors)) {
			$errorsReturn = $this->_errors;
			$this->_errorsSet = FALSE;
			$this->_errors = array();

			return $errorsReturn;
		} else {
			return FALSE;
		}
	}
	
	
	/**
	 * This renders the final view. It includes
	 * the header, the main view and the footer
	 * as well as extracting all the variables set
	 * by $this->set() and creating a new instance
	 * of the HTML helper.
	 *
	 * @package render()
	 *
	 **/

	function render($showHeader = TRUE) {
		$html = new HTML();
		extract($this->_vars, EXTR_PREFIX_SAME, "wddx");
		
		if($showHeader) {
			if(file_exists(ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . 'header.php')) {
				include(ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . 'header.php');
			} else {
				include(ROOT . DS . 'application' . DS . 'views' . DS  . 'header.php');
			}
		}
	

		if(file_exists(ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php')) {
			include(ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');
		}
		
		if(file_exists(ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . 'footer.php')) {
			include(ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . 'footer.php');
		} else {
			include(ROOT . DS . 'application' . DS . 'views' . DS  . 'footer.php');
		}

	}
}