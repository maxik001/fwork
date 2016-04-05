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
	
	/**
	 * Class constructor
	 * 
	 * @param string $_base_path
	 */
	public function __construct($_base_path) 
	{
		$this->version = "0.2";
		
		$this->fwork_base_path = $_base_path;
		
		$this->app_path=$this->fwork_base_path."app/";
		$this->fwork_config_path=$this->fwork_base_path."app/config/";
		$this->fwork_log_path=$this->fwork_base_path."fwork/log/";
		$this->fwork_system_path=$this->fwork_base_path."fwork/system/";
		
		/* Check the availability of the main folders */
		$this->check_folders();
				
		$this->load_classes();
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
		if( ! is_dir($this->app_path) ) {
			exit("Error! Execution is interrupted! FWork cant find app folder: ".$this->app_path);
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
		
		
		
		$this->logger->write_message("I finished work", log_level::NOTICE);
	}
	
}

?>