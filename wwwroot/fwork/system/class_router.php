<?php 
// Block direct exec
defined('FWORK_BASE_PATH') OR exit('No direct script access allowed');

/*
 * App: FWork
 *
 * Filename: ~/fwork/system/class_router.php
 *
 * Descr: Class router
 *
 * (c) Gusev Maxim, 2015
 *
 */

class router {
	private static $instance;

	private $default_404_controller;
	private $default_controller;
	private $default_method;
	
	private $logger;
	
	private function __construct() {}
	private function __clone() {}
	private function __wakeup() {}
	
	/*
	 * function getInstance()
	 */
	public static function get_instance() {
		if( empty(self::$instance) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/*
	 * function do_routing()
	 */
	public function do_routing() {
		// Create log
		$this->logger = new logger(FWORK_BASE_PATH.'log/router.log', 'fwork_router');
		$this->logger->write2log('Router start.');
		
		// Create config
		$this->logger->write2log('Configure router.');
		$config = new config();
		$config->load('site', TRUE);
		$config->load('router', TRUE);

		// Get some items from config
		$base_url = $config->item('base_url', 'site');
		define('SITE_BASE_URL', $config->item('base_url', 'site'));
		
		$this->default_404_controller = $config->item('404_controller', 'router');
		$this->default_controller = $config->item('default_controller', 'router');
		$this->default_method = $config->item('default_method', 'router');

		// Aggregate full URL 
		
		// Cut $_GET vars from url
		$full_url = $base_url.$_SERVER['REQUEST_URI'];
		
		// Log some data
		$this->logger->write2log('Full URL is: '.$full_url);
		
		// Validate URL
		$this->logger->write2log('Try to validate URL.');
		$url_validate_result = filter_var(
			$full_url,
			FILTER_VALIDATE_URL
		);

		// URL is not valid
		// Route to 404 and finish
		if( $url_validate_result == FALSE) {
			$this->logger->write2log('Url looks like not valid!');
			$this->logger->write2log('Try to route to 404 controller.');
			$this->route404();
			$this->logger->write2log('Router finised.');
			return TRUE;
		}
		
		// Url is valid
		
		// Split URL to parts
		$this->logger->write2log('Try to detect controller and method.');
		$url_parts = explode('/', explode("?", $_SERVER['REQUEST_URI'], 2)[0]);
		
		// Convert URL parts to lower chars
		foreach($url_parts as &$item) {
			$item = mb_strtolower($item);
		}	

		if( count($url_parts) < 2 || count($url_parts) > 3 ) {
			// Execute 404
			$this->logger->write2log('Url parts count is invalid!');
			$this->logger->write2log('Try to route to 404 controller.');
			$this->route404();
			$this->logger->write2log('Router finised.');
			return TRUE;
		}
		
		if( count($url_parts) == 2 ) {
			if($url_parts[1] == '') {
				$this->logger->write2log('Controller is not set. Use default.');
				$this->logger->write2log('Method is not set. Use default.');
				$c_name = $this->default_controller;
				$m_name = $this->default_method;
			} else {
				$this->logger->write2log('Controller is not set. Use default.');
				$c_name = $this->default_controller;
				// Cut model name before symbol "?"
				$m_name = explode("?", $url_parts[1], 2)[0];
			}	
		}
		
		if( count($url_parts) == 3 ) {
			if($url_parts[2] == '') {
				$this->logger->write2log('Method is not set. Use default.');
				$c_name = $url_parts[1];
				$m_name = $this->default_method;
			} else {								
				$c_name = $url_parts[1];
				// Cut model name before symbol "?" 
				$m_name = explode("?", $url_parts[2], 2)[0];
			}
		}

		$this->logger->write2log('Detect controller: ' . $c_name);
		$this->logger->write2log('Detect method: ' . $m_name);
		
		/*
		 * Load class controller
		*/
		if(file_exists(APP_BASE_PATH.'controllers/'.$c_name.'.php')) {
			require_once(APP_BASE_PATH.'controllers/'.$c_name.'.php');
			$this->logger->write2log('Controller class loaded.');
		} else {
			$this->logger->write2log('Cant load controller. Try to route to 404.');
			$this->route404();
			$this->logger->write2log('Router finised.');
			return TRUE;
		}
		
		$c_obj = new $c_name();
		
		if( ! is_object($c_obj) ) {
			$this->logger->write2log('Controller class is not generated object. Try to route to 404.');
			$this->route404();
			$this->logger->write2log('Router finised.');
			return TRUE;
		}
		
		if( ! method_exists($c_obj, $m_name) ) {
			$this->logger->write2log('Method not found in controller. Try to route to 404.');
			$this->route404();
			$this->logger->write2log('Router finised.');
			return TRUE;
		}
		
		$this->logger->write2log('Transfer control to '.$c_name.'->'.$m_name);
		$c_obj->$m_name();
		
		$this->logger->write2log('Router finised.');
	}
	
	/*
	 * function route404()
	 * 
	 * This function exec 404 controller
	 */
	private function route404() {
		$this->logger->write2log('404 router start.');
		
		$c_name = $this->default_404_controller;
		$m_name = $this->default_method;
		
		/*
		 * Load class controller
		 */
		if(file_exists(APP_BASE_PATH.'controllers/'.$c_name.'.php')) {
			require_once(APP_BASE_PATH.'controllers/'.$c_name.'.php');
			$this->logger->write2log('404 controller class loaded.');
		} else {
			$this->logger->write2log('Cant load controller. Try to route to 404.');
			$this->logger->write2log('404 router finised.');
			return TRUE;
					}
		
		$c_obj = new $c_name();		
		
		if( ! is_object($c_obj) ) {
			$this->logger->write2log('Controller class is not generated object.');
			$this->logger->write2log('404 router finised.');
			return TRUE;
		}
		
		if( ! method_exists($c_obj, $m_name) ) {
			$this->logger->write2log('Method in 404 controller not found.');
			$this->logger->write2log('404 router finised.');
			return TRUE;
		}
		
		$c_obj->$m_name();
		
		$this->logger->write2log('404 router finished.');
	}
}
