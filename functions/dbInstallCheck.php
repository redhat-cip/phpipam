<?php

/**
 * Check for fresh installation
 ****************************************************/
if(!tableExists("ipaddresses")) { 
	if(defined('BASE')) { header("Location: ".BASE."install/"); }
	else 				{ header("Location: /install/");} 
	die();
}
?>