<?php

/*
 * App: FWork
 * 
 * Filename: index.php
 * 
 * Descr: Entry point
 * 
 * (c) Gusev Maxim, 2015
 * 
 */

/*
 * Determine site root path
 */
define('SITE_ROOT_PATH', realpath('.'));

/*
 * Determine fwork folder
 */

$fwork_folder = 'fwork';

if(realpath($fwork_folder) !== FALSE) {
	$fwork_folder = realpath($fwork_folder).'/';
}

// Dont forget trailing slash
$fwork_folder = rtrim($fwork_folder, '/').'/';

// Check that FWork root folder is real folder
if( ! is_dir($fwork_folder) ) {
	exit("Error! Something wrong with FWork root folder: ".$fwork_folder.". Check it!");
}

// Path to the fwork system folder
define('FWORK_BASE_PATH', str_replace("\\", "/", $fwork_folder));


/*
 * Determine app folder
 */

$app_folder = 'app';

if(is_dir($app_folder)) {
	define('APP_BASE_PATH', $app_folder.'/');
} else {
	exit("Error! Cant find app folder: ".$app_folder.". Nothing to do!");
}

/*
 * Call FWork system
 */

require_once(FWORK_BASE_PATH.'system/fwork.php');

?>