<?php

/**
 * @package FWork
 * @subpackage class_base_controller.php
 * @version 1.0b
 * @author Maksim O. Gusev maxgusev@gmail.com
 * @copyright 2016 Maksim O. Gusev
 */

abstract class base_controller 
{
	public function __construct() {
		
	}
	
	protected function get_app_path() {
		return fwork::$app_path;
	}
	
	protected function get_base_url() {
		return fwork::$base_url;		
	}
}