<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/

/* use required functions */

/* redirect */
if($settings['version'] < VERSION) { 
	if(defined('BASE')) { header("Location: ".BASE."upgrade/"); }
	else 				{ header("Location: /upgrade/");} 
	die();
}
?>