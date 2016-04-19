<?php
/*
 * App: Custom
*
* Filename: ~/fwork/app/controllers/main.php
*
* Descr: Class main
*
* (c) Gusev Maxim, 2015
*
*/

class main extends base_controller {
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		// Exec view
		require_once($this->get_app_path().'views/template-main.php');
	}
}


?>