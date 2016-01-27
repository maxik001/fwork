<?php 
// Block direct exec
defined('FWORK_BASE_PATH') OR exit('No direct script access allowed');

/*
 * App: FWork
 *
 * Filename: ~/fwork/system/class_config.php
 *
 * Descr: Class config
 *
 * (c) Gusev Maxim, 2015
 *
 */

class config {
	// All config items
	var $config_items = array();
	// Path to folders
	var $config_path = array(APP_BASE_PATH);
	
	/*
	 * Constructor
	 */
	function __construct() {
		
	}
	
	/*
	 * function item()
	 */
	public function item($_name, $_sub_config = '') {
		$item = FALSE;
		
		if($_sub_config == '') {
			if( isset($this->config_items[$_name]) ) {
				return FALSE;
			}
			$item = $this->config_items[$_name];
		} else {
			if( ! isset($this->config_items[$_sub_config]) ) {
				return FALSE;
			} 
			
			if( ! isset($this->config_items[$_sub_config][$_name]) ) {
				return FALSE;
			}
			
			$item = $this->config_items[$_sub_config][$_name];
		}
		
		return $item;
	}
	
	/*
	 * function load()
	 */
	public function load($_config_name, $_use_sub_config = FALSE) {
		if($_config_name == '') {
			return FALSE;
		}
		
		$flag_found = FALSE;
		
		foreach($this->config_path as $path) {
			// Filename with full path
			$config_file_wfp = $path.'config/'.$_config_name.'.php';
			
			if( file_exists($config_file_wfp) ) {
				$flag_found = TRUE;
				break;
			}
		}
		
		if($flag_found === FALSE) {
			return FALSE;
		}
		
		include($config_file_wfp);
		
		// Included file must contain $config array
		if( ! isset($config) OR ! is_array($config) ) {
			return FALSE;
		}
		
		if($_use_sub_config === TRUE) {
			if( isset($this->config_items[$_config_name]) ) {
				$this->config_items[$_config_name] = array_merge($this->config_items[$_config_name], $config);
			} else {
				$this->config_items[$_config_name] = $config;
			}
		} else {
			$this->config_items = array_merge($this->config_items, $config);
		}
		
		unset($config);
		
		return TRUE;
	}
}

?>