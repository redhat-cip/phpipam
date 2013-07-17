<?php

/* database connection details
*******************************/
$db['host'] = "localhost";
$db['user'] = "root";
$db['pass'] = "1qaz2wsx";
$db['name'] = "3ipam";

/* glpi database connection details
   LEAVE EMPTY IF NOT USING GLPI
************************************/
$db['glpi_host'] = "";
$db['glpi_user'] = "";
$db['glpi_pass'] = "";
$db['glpi_name'] = "";

/* glpi url
   e.g. www.myglpi.com
		192.168.1.1
************************************/
$glpiurl = '';

/* glpi discovery subnets
   $glpisubnets = '192.168.132.0/24,127.0.0.1/30'
   separate the subnets with a coma
**************************************************/
$glpisubnets = '';

/**
 * php debugging on/off
 *
 * true  = SHOW all php errors
 * false = HIDE all php errors
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
