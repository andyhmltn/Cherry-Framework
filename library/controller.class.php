<?php

/**
 * Controller class.
 * Blueprint for a new controller.
 *
 * @package Controller
 * 
 **/


class Controller {
	
	/**
	 * Declare the variables:
	 * Explanations next to them
	 *
	 * @package Variables
	 * 
	 **/
	
	protected $_action;		#The action to load
	protected $_controller; #This controllers name
	protected $_model;		#The model linked to this controller
	protected $_template;	#The template to render
	protected $_finish;		#Set to true when everything has gone as expected.
	
	public $showHeader;	#Set to true by default. If set to false, disables the header rendering.
	
	/**
	 * Used by Global.php to create a new
	 * controller and perform the required operations
	 *
	 * @package __Construct
	 * 
	 **/
	
	function __construct($model, $controller, $action) {
		global $log; #Grabs the logging variable from global.php
		
		$this->_action = $action;
		$this->_controller = $controller;
		$this->_model = $model;
		$this->showHeader = TRUE;
		
		/**
	     * If a model is not found it is set to 'none'
		 * This makes sure it's not called if so.
		 **/
		
		if($this->_model !== 'none') { 
			$this->$model = new $model;
		}
		
		if($this->_action == NULL || $this->_action == '') {
			$this->_action = 'index'; #If the action is not set or is blank: Use index;
		} 
		
		if(!method_exists($this,$this->_action)) {
			$log->logError('Action not found ' . $this->_action, 'ACTION');
			$this->_finish = FALSE;
			header ("Location: " . BASE_PATH . "/errors/404");
			exit();
		} else {
			$this->_finish = TRUE;
			$this->_template = new Template($this->_controller,$this->_action, $this->showHeader);
		}
		
		
	}
	
	/**
	 * Sets a variable for use within the
	 * template rendered after the action is run
	 *
	 * @package set()
	 * 
	 **/
	
	
	function set($name, $value, $error = FALSE) {
		if($error) {
			$log->logError($value, $name);
			$this->_template->errorAdd($value);
		} else {
			$this->_template->set($name, $value);
		}
	}
	
	/**
	 * Generates a link to a controller/action
	 * Also in the 'html' add on.
	 *
	 * @package Generate link
	 * 
	 **/
	

	function generateLink($controller, $action = NULL) {
		if($_SERVER["SERVER_PORT"] != "80") {
			$pageURL = 'http://'.$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL = $_SERVER["SERVER_NAME"];
		}
	}
	
	/**
	 * Checks everything is perfect and 
	 * then renders the template.
	 *
	 * @package __Destruct
	 * 
	 **/
	
	function __destruct() {
		if($this->_finish) {
			$this->_template->render($this->showHeader);
		}
	}

}