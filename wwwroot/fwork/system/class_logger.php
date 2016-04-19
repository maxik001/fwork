<?php

/**
 * @package FWork
 * @subpackage class_logger.php
 * @version 1.0b
 * @author Maksim O. Gusev maxgusev@gmail.com
 * @copyright 2016 Maksim O. Gusev
 */

/**
 * Class logger exception 
 */
class logger_exception extends Exception
{
	public function __construct($_message, $_code = 0, Exception $_previous = null) 
	{
		parent::__construct($_message, $_code, $_previous);
	}
}

/**
 * Class log level
 */
class log_level 
{
	const NOTSET	= 'notset';
	const NOTICE	= 'notice';
	const WARNING	= 'warning';
	const ERROR		= 'error';
	const DEBUG		= 'debug';
}

/**
 * Class logger
 */
class logger 
{
	private $log_levels_available = array(
		log_level::NOTSET	=> 0,
		log_level::ERROR	=> 10,
		log_level::WARNING	=> 20,
		log_level::NOTICE	=> 30,
		log_level::DEBUG	=> 40
	);
	
	private $log_level_default = log_level::NOTICE;

	private $log_level_threshold;

	private $date_format = "Y-m-d H:i:s";
	
	private $log_filename;
	private $log_file_ptr;
	private $log_file_write_mode = 'a';
	
	private $subsystem_name;
	
	/**
	 * Class constructor
	 * 
	 * @param string $_log_filename
	 * @param string $_log_level_threshold
	 * @param unknown $_subsystem_name
	 * @throws logger_exception
	 */
	public function __construct($_log_filename, $_log_level_threshold = log_level::NOTICE, $_subsystem_name = NULL) 
	{
		/* Set params */
		$this->log_filename = $_log_filename;
		$this->subsystem_name = $_subsystem_name;
		
		/* Check destination */
		if( is_dir($this->log_filename) ) {
			throw new logger_exception("It is not a file: ".$this->log_filename);
		}
		
		/* Try to create log file */
		if( ! file_exists ($this->log_filename) ) {
			try {
				$touch_result = @touch($this->log_filename);
			} catch(Exception $e) {
				throw new logger_exception("Cant create file : ".$this->log_filename. " ". $e->getMessage());
			}
			if( $touch_result == FALSE ) {
				throw new logger_exception("Cant create file ".$this->log_filename);
			}
		}
			
		if( ! is_writable($this->log_filename) ) {
			throw new logger_exception("File ".$this->log_filename." is not writable.");
		}
				
		/* Set log level threshold */
		if( array_key_exists( $_log_level_threshold, $this->log_levels_available) ) {
			$this->log_level_threshold = $this->log_levels_available[$_log_level_threshold];
		} else {
			$this->log_level_threshold = $this->log_levels_available[$this->log_level_default];
		}
				
	}
	
	/**
	 * Class destructor
	 */
	public function __destruct() 
	{
		$close_file_result = FALSE;
		
		if( is_resource($this->log_file_ptr) ) {
			$close_file_result = fclose($this->log_file_ptr);
		}
		
		return $close_file_result;
	}

	/**
	 * Class private functions 
	 */
	
	private function format_message($_text, $_log_level) 
	{
		$message = "";
		
		$timestamp = $this->get_current_timestamp();
		$message .= $timestamp;
		$message .= " [".$_log_level."]";
		$message .= " <".$this->subsystem_name.">";
		$message .= " ".$_text.PHP_EOL;

		return $message;
	}
	
	/**
	 * Return date/time in preset format
	 */
	private function get_current_timestamp() 
	{
		
		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		
		return date( $this->date_format.".".$micro, $t); 
	}

	/**
	 * Class public functions
	 */
	
	/**
	 * @throws logger_exception
	 */
	public function open()
	{
		try {
			$this->log_file_ptr = fopen($this->log_filename, $this->log_file_write_mode);
				
			if( !$this->log_file_ptr ) {
				throw new logger_exception("Fail to open log file: ".$this->log_filename);
			}
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
		
	}
	
	/**
	 * 
	 * @param string $_message
	 * @throws logger_exception
	 */
	public function write_message($_text, $_log_level) 
	{
		/* If threshold level lower that in message - skip output */
		if( $this->log_level_threshold < $this->log_levels_available[$_log_level] ) {
			return;
		}
		
		if( !is_resource($this->log_file_ptr) ) {
			throw new logger_exception("Cant find log file resource. Log filename: ".$this->log_file);
		}
		
		$log_message = $this->format_message($_text, $_log_level);
		
		try {
			$fwrite_result = fwrite($this->log_file_ptr, $log_message);
			
			if( $fwrite_result ) {
				throw new logger_exception("Fail to write message to log file: ".$this->log_filename);
			}
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}
	
}

?>