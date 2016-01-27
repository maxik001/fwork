<?php
// Block direct exec
defined('FWORK_BASE_PATH') OR exit('No direct script access allowed');

/*
 * App: FWork
*
* Filename: ~/fwork/system/global_functions.php
*
* Descr: FWork global functions
*
* (c) Gusev Maxim, 2015
*
*/

/*
 * Example function 
 * 
 * It try to echo Hello World.
 * 
 */
if( ! function_exists('hello_world') ) {
		
	function hello_world() {
		echo "Hello World!\n\r";		
	}
}


/*
 * DEPRICATED
 * 
 * function load_class()
 */
if( ! function_exists('load_class_outdated') ) {
	
	function load_class_outdated($_class_name, $_class_type) {
		$available_class_types = array('controller', 'model', 'view');
		
		// Check that correct type of class
		if( array_search($_class_type, $available_class_types) === FALSE ) {
			return FALSE;
		}
		
		// Set dest folder 
		$path_to_class = '';
		switch($_class_type) {
			case $available_class_types[0]:
				$path_to_class = APP_BASE_PATH.'controllers/';
				break;
			case $available_class_types[1]:
				$path_to_class = APP_BASE_PATH.'models/';
				break;
			case $available_class_types[2]:
				$path_to_class = APP_BASE_PATH.'views/';
				break;
		}
		
		$flag_found = FALSE;
		
		// Filename with full path
		$config_file_wfp = $path_to_class.$_class_name.'.php';
			
		if( file_exists($config_file_wfp) ) {
			$flag_found = TRUE;
		}
		
		// Cant find required class declaration
		if($flag_found === FALSE) {
			return FALSE;
		}
		
		include($path_to_class.$_class_name.'.php');
		
		$obj = new $_class_name();
		
		return $obj;
	}
	
}

/*
 * function var_dump_html()
 */
if( ! function_exists('var_dump_html') ) {
	function var_dump_html($_var) {
		echo "<pre>";
		var_dump($_var);
		echo "</pre>";
	}
}

?>