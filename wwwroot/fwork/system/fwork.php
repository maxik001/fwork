<?php

/**
 * @package FWork
 * @subpackage fwork.php
 * @version 1.0b
 * @author Maksim O. Gusev maxgusev@gmail.com
 * @copyright 2016 Maksim O. Gusev
 *
 */

class fwork 
{
	private $version = "1.0b";
	
	private $fwork_base_path;
	
	private $fwork_config_path;
	private $fwork_log_path;
	private $fwork_system_path;
	
	public static $base_url;
	
	public static $app_path;
	
	private $config;
	private $logger;
	
	/**
	 * Class constructor
	 * 
	 * @param string $_base_path
	 */
	public function __construct($_base_path) 
	{
		$this->fwork_base_path = $_base_path;
		
		fwork::$app_path=$this->fwork_base_path."app/";
		$this->fwork_config_path=$this->fwork_base_path."app/config/";
		$this->fwork_log_path=$this->fwork_base_path."fwork/log/";
		$this->fwork_system_path=$this->fwork_base_path."fwork/system/";
		
		/* Check the availability of the main folders */
		$this->check_folders();
				
		$this->load_classes();
	}
	
	public static function get_base_path() {
		return fwork::$fwork_base_path;
	}
	
	/**
	 * Cap function. Nothing to do.
	 */
	private function cap() {
		/* Nothing to do */
	}
	
	/**
	 * 
	 */
	private function check_folders() 
	{
		if( ! is_dir(fwork::$app_path) ) {
			exit("Error! Execution is interrupted! FWork cant find app folder: ".fwork::$app_path);
		}

		if( ! is_dir($this->fwork_config_path) ) {
			exit("Error! Execution is interrupted! FWork cant find config folder: ".$this->fwork_config_path);
		}
		
	}
	
	/**
	 * 
	 */
	private function load_classes() 
	{
		if( file_exists( $this->fwork_system_path.'class_logger.php' ) ) {
			require_once( $this->fwork_system_path.'class_logger.php' );
		} else {
			exit("Error! Execution is interrupted! FWork cant find logger class: ".$this->fwork_system_path.'class_logger.php');
		}

		if( file_exists( $this->fwork_system_path.'class_config.php' ) ) {
			require_once( $this->fwork_system_path.'class_config.php' );
		} else {
			exit("Error! Execution is interrupted! FWork cant find config class: ".$this->fwork_system_path.'class_config.php');
		}
		
		if( file_exists( $this->fwork_system_path.'class_base_controller.php' ) ) {
			require_once( $this->fwork_system_path.'class_base_controller.php' );
		} else {
			exit("Error! Execution is interrupted! FWork cant find base controller class: ".$this->fwork_system_path.'class_base_controller.php');
		}
	}
	
	/**
	 * This function executed when need to terminate program on exception
	 * 
	 * @param exception $_exception
	 */
	private function terminate($_exception) 
	{
		exit("[Exception] ".get_class($_exception)." ".$_exception->getFile().":".$_exception->getLine()." ".$_exception->getMessage());
	}
	
	/**
	 * 
	 */
	public function run() 
	{
		/* Try to create logger */
		$log_file = $this->fwork_log_path."fwork.log";
		
		try {
			$this->logger = new logger($log_file, log_level::ERROR, get_class($this));
		} catch ( Exception $e ) {
			$this->terminate($e);
		}
		
		echo "<pre>";
		var_dump($this->logger);
		echo "</pre>";
		
		/* Try to open log file */
		try {
			$this->logger->open();
		} catch ( Exception $e ) {
			$this->terminate($e);
		}

		$this->logger->write_message("I started! Version ".$this->version, log_level::NOTICE);

		/* Try to load config */ 
		$this->config = new config($this->fwork_config_path);
		
		$this->logger->write_message("Load config \"router\"", log_level::NOTICE);
		$this->config->load('router');
		
		$this->logger->write_message("Get from config default_controller: ".$this->config->item('default_controller'), log_level::NOTICE);
		$this->logger->write_message("Get from config default_method: ".$this->config->item('default_method'), log_level::NOTICE);
		$this->logger->write_message("Get from config 404_controller: ".$this->config->item('404_controller'), log_level::NOTICE);
		
		$this->logger->write_message("Load config \"site\"", log_level::NOTICE);
		$this->config->load('site');
		
		$this->logger->write_message("Get from config base_url: ".$this->config->item('base_url'), log_level::NOTICE);
		
		/* Validate url */
		fwork::$base_url = $this->config->item('base_url');
		$full_url = $this->config->item('base_url').$_SERVER['REQUEST_URI'];
		
		$this->logger->write_message("Try to validate full url: ".$full_url, log_level::NOTICE);
		
		$full_url_is_valid = filter_var(
			$full_url,
			FILTER_VALIDATE_URL
		);
		
		if( $full_url_is_valid == FALSE ) {
			$this->logger->write_message("Full url is not valid", log_level::WARNING);
		}
		
		/* Define controller and method name */
		$c_name = $this->config->item('default_controller');
		$m_name = $this->config->item('default_method');
		
		/* Parse url */
		
		$this->logger->write_message("Try to parse url", log_level::NOTICE);
		
		$url_cut = explode("?", $_SERVER['REQUEST_URI'], 2)[0];
		$url_parts = explode('/', $url_cut);

		$this->logger->write_message("Url without GET variables: ". $url_cut, log_level::NOTICE);
		
		/* Convert URL parts to lower chars */
		foreach($url_parts as $key => $item) {
			$url_parts[$key] = mb_strtolower($item);
		}

		/* Parse url parts */
		if( count($url_parts) < 2 || count($url_parts) > 3 ) {
			$c_name = $this->config->item('404_controller');
		}
		
		if( count($url_parts) == 2 ) {
			if( strcmp($url_parts[1], '') !== 0 ) {
				$m_name = $url_parts[1];
			}
		}
		
		if( count($url_parts) == 3 ) {
			$c_name = $url_parts[1];
			
			if( strcmp($url_parts[2], '') !== 0 ) {
				$m_name = $url_parts[2];
			}
		}		

		$this->logger->write_message("Parsed controller from url: ". $c_name, log_level::NOTICE);
		$this->logger->write_message("Parsed method from url: ". $m_name, log_level::NOTICE);

		/* Try to load controller */
		$c_class_file = fwork::$app_path.'controllers/'.$c_name.'.php';
		if( file_exists($c_class_file) ) {
			$this->logger->write_message("Controller file found. Require filename: ". $c_class_file, log_level::NOTICE);
			require_once($c_class_file);
		} else {
			$this->logger->write_message("Controller file not found. Filename: ". $c_class_file, log_level::WARNING);
			$this->logger->write_message("Try to find 404 controller.", log_level::NOTICE);
			
			/* Try to route to 404 */
			$c_name = $this->config->item('404_controller');
			
			$c404_class_file = fwork::$app_path.'controllers/'.$c_name.'.php';

			if( file_exists($c404_class_file) ) {
				
				$this->logger->write_message("Controller 404 file found. Require filename: ". $c404_class_file, log_level::NOTICE);
				require_once($c404_class_file);
			} else {
				$this->logger->write_message("Controller 404 file not found. Nothing to do.", log_level::WARNING);
				exit(1);
			}
			
		}
                
		$c_obj = new $c_name($m_name);
		
		$this->logger->write_message("I finished work", log_level::NOTICE);
	}
	
}

?>