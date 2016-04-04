<?php

/**
 * @package FWork
 * @subpackage fwork.php
 * @version 0.2
 * @author Maksim O. Gusev maxgusev@gmail.com
 * @copyright 2016 Maksim O. Gusev
 *
 */


class fwork 
{
	private $version;
	
	private $fwork_base_path;
	
	private $fwork_config_path;
	private $fwork_log_path;
	private $fwork_system_path;
	
	private $app_path;
	
	private $config;
	private $logger;
	
	function __construct($_base_path) 
	{
		$this->version = "0.2";
		
		$this->fwork_base_path = $_base_path;
		
		$this->app_path=$this->fwork_base_path."app/";
		$this->fwork_config_path=$this->fwork_base_path."app/config/";
		$this->fwork_log_path=$this->fwork_base_path."fwork/log/";
		$this->fwork_system_path=$this->fwork_base_path."fwork/system/";
		
		/* check the availability of the main folders */
		$this->check_folders();
				
		$this->load_classes();
	}
	
	private function cap() {
		/* Nothing to do */
	}
	
	private function check_folders() 
	{
		if( ! is_dir($this->app_path) ) {
			exit("Error! Execution is interrupted! FWork cant find app folder: ".$this->app_path);
		}

		if( ! is_dir($this->fwork_config_path) ) {
			exit("Error! Execution is interrupted! FWork cant find config folder: ".$this->fwork_config_path);
		}
		
	}
	
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
	}
	
	public function run() 
	{
		/* Try to create logger */
		try {
			$this->logger = new logger($this->fwork_log_path."fwork.log", log_level::ERROR, "fwork");
		} catch ( Exception $e ) {
			exit("[Exception] ".get_class($e)." ".$e->getFile().":".$e->getLine()." ".$e->getMessage());
		}
		
		/* Try to open logger */
		try {
			$this->logger->open();
		} catch ( Exception $e ) {
			exit("[Exception] ".get_class($e)." ".$e->getFile().":".$e->getLine()." ".$e->getMessage());
		}

		try {
			$this->logger->write_message("FWork version ".$this->version." start!", log_level::NOTICE);
		} catch ( Exception $e ) {
			$this->cap();
		}
		

		try {
			$this->logger->write_message("Error.", log_level::ERROR);
		} catch ( Exception $e ) {
			$this->cap();
		}
		
		try {
			$this->logger->write_message("Warning.", log_level::WARNING);
		} catch ( Exception $e ) {
			$this->cap();
		}
		
		try {
			$this->logger->write_message("FWork finished.", log_level::NOTICE);
		} catch ( Exception $e ) {
			$this->cap();
		}
		
		$this->logger->close();
	}
	
}

?>