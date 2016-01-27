<?php 
// Block direct exec
defined('FWORK_BASE_PATH') OR exit('No direct script access allowed');

/*
 * App: FWork
 * 
 * Filename: ~/fwork/system/fwork.php
 *  
 * Descr: FWork system core
 * 
 * (c) Gusev Maxim, 2015
 * 
 */

/*
 * Load FWork constants
 */
if(file_exists(FWORK_BASE_PATH.'config/constants.php')) {
	require_once(FWORK_BASE_PATH.'config/constants.php');
}

/*
 * Load FWork global functions
 */
if(file_exists(FWORK_BASE_PATH.'system/global_functions.php')) {
	require_once(FWORK_BASE_PATH.'system/global_functions.php');
}

/*
 * Load class logger
 */
if(file_exists(FWORK_BASE_PATH.'system/class_logger.php')) {
	require_once(FWORK_BASE_PATH.'system/class_logger.php');
} else {
	exit('Error! FWork cant find logger class!');
}

$fwork_logger = new logger(FWORK_BASE_PATH.'log/fwork.log', 'fwork_core');
$fwork_logger->write2log('FWork version '.FWORK_VERSION.' start! Logger loaded!');


/*
 * Load class config
 */
if(file_exists(FWORK_BASE_PATH.'system/class_config.php')) {
	require_once(FWORK_BASE_PATH.'system/class_config.php');
	$fwork_logger->write2log('Config class loaded.');
} else {
	exit('Error! FWork cant find config class!');
}

/*
 * Load class router
 */
if(file_exists(FWORK_BASE_PATH.'system/class_router.php')) {
	require_once(FWORK_BASE_PATH.'system/class_router.php');
	$fwork_logger->write2log('Router class loaded.');
} else {
	exit('Error! FWork cant find router class!');
}

/*
 * Try to route
 */
$fwork_logger->write2log('Transfer control to router.');
router::get_instance()->do_routing();
$fwork_logger->write2log('Back from router.');

$fwork_logger->write2log('FWork finished.');

?>