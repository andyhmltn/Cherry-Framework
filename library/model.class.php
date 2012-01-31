<?php

/**
 * This is the blueprint for a new model.
 * The only function is __construct which sets
 * the modelname, table and connects to the DB
 * 
 * @package Model
 * 
 **/

class Model extends SQLFrame {

	protected $_model;
	
	function __construct() {
		$this->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		$this->_model = get_class($this);
		$this->_table = strtolower($this->_model)."s";
	}
	
}