<?php

/*	database connection details
 ******************************/
$db['host'] = "localhost";
$db['user'] = "ipv6";
$db['pass'] = "ipv6admin";
$db['name'] = "phpipam";

/**
 * php debugging on/off
 *
 * 1 = SHOW all php errors
 * 0 = HIDE all php errors
 ******************************/
$debugging = 0;

/**	
 *	BASE definition if phpipam 
 * 	is not in root directory
 *
 *  Also change 
 *	RewriteBase / in .htaccess
 ******************************/
define('BASE', "/");

?>
