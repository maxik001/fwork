<?php 

/**
 * @package FWork
 * @subpackage class_config.php
 * @version 1.0b
 * @author Maksim O. Gusev maxgusev@gmail.com
 * @copyright 2016 Maksim O. Gusev
 *
 */

class config 
{
	// All config items
	private $config_items = array();
	
	private $config_files_folder = array();
	
	/**
	 * 
	 * @param string $_folder
	 */
	function __construct($_folders) 
	{
		if( !is_array($_folders) ) {
			$this->config_files_folder = array_merge( $this->config_files_folder, (array)$_folders );
		} else {
			$this->config_files_folder = array_merge( $this->config_files_folder);
		}
	}
	
	/**
	 * 
	 * @param string $_name
	 * @param string $_sub_config
	 * @return mixed
	 */
	public function item($_name, $_sub_config = '') 
	{
		$item = FALSE;
		
		if($_sub_config == '') 
		{
			if( !isset($this->config_items[$_name]) ) {
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
	
	/**
	 * 
	 * @param string $_config_name
	 * @param string $_use_sub_config
	 * @return boolean
	 */
	public function load($_config_name, $_use_sub_config = FALSE) 
	{
		if($_config_name == '') 
		{
			return FALSE;
		}
		
		$flag_found = FALSE;
		
		foreach($this->config_files_folder as $path) 
		{
			// Filename with full path
			$config_file_wfp = $path.$_config_name.'.php';

			if( file_exists($config_file_wfp) ) 
			{
				$flag_found = TRUE;
				break;
			}
		}
		
		if($flag_found === FALSE) 
		{
			return FALSE;
		}
		
		include($config_file_wfp);
		
		// Included file must contain $config array
		if( ! isset($config) OR ! is_array($config) ) 
		{
			return FALSE;
		}
		
		if($_use_sub_config === TRUE) {
			if( isset($this->config_items[$_config_name]) ) 
			{
				$this->config_items[$_config_name] = array_merge($this->config_items[$_config_name], $config);
			} else 
			{
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