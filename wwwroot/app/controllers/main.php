<?php
// Block direct exec
defined('FWORK_BASE_PATH') OR exit('No direct script access allowed');


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

class main {
	function __construct() {
	}
	
	function index() {
		// Exec view
		require_once(APP_BASE_PATH.'views/template-main.php');
	}
}


?>