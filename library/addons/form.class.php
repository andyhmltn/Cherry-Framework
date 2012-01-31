<?php

/**
 * This is the class for the form
 * helper.
 *
 * @package Form
 * 
 **/

class Form {
	
	/**
	 * The first function called when a new
	 * form helper is created. You can supply
	 * the url you would like to submit to with
	 * an array. EG: array('controller', 'action')
	 * the method can also be supplied.
	 *
	 * The additionals argument is in most of the
	 * form helper functions. You can supply it with
	 * an array of HTML attributes to pass to the object
	 * you are creating. For this instance you will be passing
	 * $additionals into the first <form> tag. So you could do
	 * something like this: array('class' => 'form') and instead
	 * of outputting <form> it would output <form class='form'>
	 * As mentioned earlier, the additionals variable is
	 * in most of the form helper methods and can be useful
	 * for supplying things such as classes or IDs.
	 *
	 * @package __construct()
	 * 
	 **/

	function __construct($action = "#", $method = 'POST', $additionals = array()){
		$additionals = $this->processAdds($additionals);
		if(is_array($action)) {
			if(isset($action[0])) {
				$controller = $action[0];
				if(isset($action[1])) {
					$_action = $action[1];
				} else {
					$_action = 'index';
				}
			} else {
				$controller = 'index';
				$_action = 'index';
			}
			$url = BASE_PATH . DS . $controller . DS . $_action;
		} else {
			$url = $action;
		}
		echo "<form action='$url' method='$method' $additionals >";
	}
	
	/**
	 * This is used to process and return
	 * the 'additionals' argument mentioned
	 * above. You shouldn't need to use this
	 * unless you're creating a new form helper
	 * method.
	 *
	 * @package processAdds()
	 *
	 **/

	function processAdds($args) {
		$_args = '';
		if(!is_array($args)) {
			$args = array();
		}
		foreach($args as $k => $v) {
			$_args .= "$k='$v' ";
		}
		return $_args;
	}

	/**
	 * This creates an 'input' tag. The only
	 * thing that needs explaining here is the
	 * placeholder argument. If this is true, it
	 * will output an input tag that, when clicked,
	 * clears the value like the HTML5 attribute. 
	 * 
	 * Please note: This doesn't work when the type 
	 * is specified as password.
	 *
	 * @package input()
	 *
	 **/

	function input($type, $name, $value = '', $placeHolder = TRUE, $additionals = array()) {
		$additionals = $this->processAdds($additionals);
		if(is_array($placeHolder)) {
			$additionals = $placeHolder;
			$placeHolder = TRUE;
		}
		
		if($placeHolder && strtolower($type) == 'text') {
			$valueString = "value='$value' " . 'onclick="' . "this.value=''" . '"';
		} else {
			$valueString = "value='$value'";
		}
		
		$inputString = "<input type='$type' name='$name' $valueString $additionals />";
		echo $inputString;
	}

	/**
	 * This just outputs a normal submit
	 * tag. If $end is set to true (true
	 * by default) then it will output a
	 * closing form tag after the submit
	 * button: </form>
	 *
	 * @package submit()
	 *
	 **/
	
	function submit($value = '', $end = TRUE, $additionals = array()){
		if(is_array($end)) {
			$additionals = $end;
			$end = TRUE;
		}
		
		$additionals = $this->processAdds($additionals);
		echo "<input type='submit' value='$value' $additionals />";
		if($end) { echo "</form>"; }
	}
	
	/**
	 * This just outputs a closing form
	 * tag. Not needed if a submit tag
	 * has been created with $end set
	 * to true.
	 *
	 * @package end()
	 *
	 **/

	function end(){
		echo '</form>';
	}
	
}
