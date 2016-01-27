<?php 
// Block direct exec
defined('FWORK_BASE_PATH') OR exit('No direct script access allowed');

/*
 * App: FWork
 *
 * Filename: ~/fwork/system/class_logger.php
 *
 * Descr: Class logger
 *
 * (c) Gusev Maxim, 2015
 *
 */

class logger {
	private $log_file;
	private $subsystem_name;
	
	function __construct($_log_file, $_subsystem_name = NULL) {
		$this->log_file = $_log_file;
		$this->subsystem_name = $_subsystem_name;
	}
	
	/*
	 * function write2log()
	 * 
	 * @param string $_message
	 * 
	 */
	public function write2log($_message) {
		try {
			$ptr = fopen($this->log_file, FOPEN_APPEND);
			
			if( !$ptr) {
				throw new Exception("Fail to open log file: ".$this->log_file);
			}
		} catch ( Exception $e ) {
			return $e->getMessage();
		}
		
		$timestamp_string = $this->generate_current_timestamp_string();
		$log_message_full = $timestamp_string." ".$this->subsystem_name." ".$_message."\n\r";

		try {
			$fwrite_result = fwrite($ptr, $log_message_full);
			
			if( $fwrite_result ) {
				throw new Exception("Fail to write message to log file: ".$this->log_file);
			}

			fclose($ptr);
		} catch ( Exception $e ) {
			return $e->getMessage();
		} 
	}
	
	/*
	 * function generate_current_timestamp_string()
	 * 
	 * @return string Date/time string in special format
	 * 
	 */
	private function generate_current_timestamp_string() {
		return date("Y-m-d H:i:s", time() );
	}
}

?>