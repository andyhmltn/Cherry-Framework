<?php

/**
 * This is the class for the HTML
 * helper.
 *
 * @package HTML
 * 
 **/

class HTML {

	/**
 	 * This outputs a link. The arguments
 	 * are self explanitory. Please see the
 	 * form helper from an explanation of the
 	 * $additionals argument.
	 *
 	 * @package Link()
 	 * 
 	 **/
	
	function link($contents, $link, $additionals = array()) {
		$_additionals = '';
		foreach($additionals as $key => $value) {
			$_additionals .= $key . '="' . $value . '" ';
		}
		$data = '<a href="' . $link . '" ' . $_additionals . '>' . $contents .  '</a>';
		return $data;
	}

	/**
 	 * This is used to output a URL when just
 	 * a controller and action are supplied.
 	 * it uses the BASE_PATH constant.
	 *
 	 * @package mvcURL()
 	 * 
 	 **/
	
	function mvcURL($controller, $action = '') {
		$url = BASE_PATH . DS . $controller . DS . $action;
		if(func_get_args() > 2) {
			$args = array_slice(func_get_args(), 2);
			foreach($args as $_arg) {
				$url .= DS . $_arg;
			}
		}
		return $url;
	}
	
	/**
 	 * This is used to include CSS
 	 * files from the public/css directory.
 	 * You can supply multiple arguments to
 	 * include multiple CSS files. EG:
 	 *
 	 * $HTML->_Css('reset', 'style');
 	 *
 	 * That would include both the 'reset.css'
 	 * and 'style.css' files. (Includes in the
	 * order supplied.)
	 *
 	 * @package _Css()
 	 * 
 	 **/

	function _Css() {
		if(func_num_args() == 0) { return FALSE; }
		foreach(func_get_args() as $csslink) {
			echo "<link rel='stylesheet' href='". BASE_PATH . "/css/" . $csslink . ".css' type='text/css' />\n";
		}
	}

	/**
 	 * This is used to include JavaScript
 	 * files from the public/js directory.
 	 * You can supply multiple arguments to
 	 * include multiple JavaScript files. EG:
 	 *
 	 * $HTML->_Js('jquery', 'script');
 	 *
 	 * That would include both the 'jquery.js'
 	 * and 'scriot.js' files. (Includes in the
	 * order supplied.)
	 *
 	 * @package _Js()
 	 * 
 	 **/
	
	function _Js() {
		if(func_num_args() == 0) { return FALSE; }
		foreach(func_get_args() as $jslink) {
			echo "<script src='". BASE_PATH . "/js/" . $jslink . ".js'></script>\n";
		}
	}

	/**
 	 * Echos an image with the Src as
 	 * the $imglink argument.
	 *
 	 * @package _Img()
 	 * 
 	 **/
	
	function _Img($imglink, $additionals = array()) {
		$_additionals = '';
		foreach($additionals as $key => $value) {
			$_additionals .= $key . '="' . $value . '" ';
		}
		echo "<img src='" . BASE_PATH . "/img/" . $imglink .  "' $_additionals />";
	}	
	
}

