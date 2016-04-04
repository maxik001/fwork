<?php
/**
 * @package FWork
 * @subpackage index.php
 * @version 0.2
 * @author Maksim O. Gusev maxgusev@gmail.com
 * @copyright 2016 Maksim O. Gusev 
 * 
 */

/* We determine our location */

$current_folder = realpath(".");
$current_folder = rtrim($current_folder, '/').'/';

/* Check the availability and accessibility of a base FWork class */

$fwork_core_class_def = $current_folder."fwork/system/fwork.php";

if( is_readable($fwork_core_class_def) == FALSE ) {
	exit("Error! Execution is interrupted! Cant read FWork core class definition: ".$fwork_core_class_def);
}

/* Load FWork core class definition */ 
require_once($fwork_core_class_def);

/* Call FWork system */
$fwork = new fwork($current_folder);
$fwork->run();

?>