<?php

/*	database connection details
 ******************************/
$db['host'] = "localhost";
$db['user'] = "user";
$db['pass'] = "password";
$db['name'] = "dbname";

/* glpi database connection details
   LEAVE EMPTY IF NOT USING GLPI
************************************/
$db['glpi_host'] = "";
$db['glpi_user'] = "";
$db['glpi_pass'] = "";
$db['glpi_name'] = "";

/* GLPI URL
   e.g. www.myglpi.com
		192.168.1.1
************************************/
$glpiurl = "";


/**
 * php debugging on/off
 *
 * 1 = SHOW all php errors
 * 0 = HIDE all php errors
 ******************************/
$debugging = false;

/**	
 *	BASE definition if phpipam 
 * 	is not in root directory (e.g. /phpipam/)
 *
 *  Also change 
 *	RewriteBase / in .htaccess
 ******************************/
define('BASE', "/");

?>
