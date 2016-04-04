<?php

/**
 * @package FWork
 * @subpackage class_logger.php
 * @version 0.2
 * @author Maksim O. Gusev maxgusev@gmail.com
 * @copyright 2016 Maksim O. Gusev
 *
 */

/* Custom exception for class */
class logger_exception extends Exception
{
	public function __construct($_message, $_code = 0, Exception $_previous = null) 
	{
		parent::__construct($_message, $_code, $_previous);
	}
}

class log_level 
{
	const NOTSET	= 'notset';
	const NOTICE	= 'notice';
	const WARNING	= 'warning';
	const ERROR		= 'error';
}

class logger 
{
	private $log_levels_available = array(
		log_level::NOTSET	=> 0,
		log_level::NOTICE	=> 10,
		log_level::WARNING	=> 20,
		log_level::ERROR	=> 30
	);
	
	private $log_level_default = log_level::NOTICE;

	private $log_level;
	
	private $log_filename;
	private $log_file_ptr;
	
	private $subsystem_name;
	
	function __construct($_log_filename, $_log_level =  log_level::NOTICE, $_subsystem_name = NULL) 
	{
		$this->log_filename = $_log_filename;
		$this->subsystem_name = $_subsystem_name;
		
		if( is_dir($this->log_filename) ) {
			throw new logger_exception("It is not a file: ".$this->log_filename);
		}
		
		if( ! is_writable($this->log_filename) ) {
			throw new logger_exception("File ".$this->log_filename." is not writable.");
		}
				
		if( array_key_exists( $_log_level, $this->log_levels_available) ) {
			$this->log_level = $this->log_levels_available[$_log_level];
		} else {
			$this->log_level = $this->log_levels_available[$this->log_level_default];
		}
				
	}

	private function generate_current_timestamp_string() 
	{
		return date("Y-m-d H:i:s", time() );
	}

	public function close()
	{
		$close_result = fclose($this->log_file_ptr);
		
		return $close_result;
	}
	
	public function open()
	{
		try {
			$this->log_file_ptr = fopen($this->log_filename, 'a');
				
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
		if( $this->log_level < $this->log_levels_available[$_log_level] ) {
			return;
		}
		
		
		if( !is_resource($this->log_file_ptr) ) {
			throw new logger_exception("Cant find log file resource. Log filename: ".$this->log_file);
		}
		
		$timestamp_string = $this->generate_current_timestamp_string();
		$log_message_full = $timestamp_string." ".$this->subsystem_name." ".$_text."\n\r";

		try {
			$fwrite_result = fwrite($this->log_file_ptr, $log_message_full);
			
			if( $fwrite_result ) {
				throw new logger_exception("Fail to write message to log file: ".$this->log_filename);
			}
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
	}
	
}

?>