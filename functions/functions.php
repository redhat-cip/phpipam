<?php

/* @database functions ------------------- */
require_once( dirname(__FILE__) . '/../config.php' );
require_once( dirname(__FILE__) . '/dbfunctions.php' );



/* @debugging functions ------------------- */
if ($debugging == 0) {
  	ini_set('display_errors', 1);
    error_reporting(E_ERROR | E_WARNING);
}
else{
    ini_set('display_errors', 1); 
    error_reporting(E_ALL ^ E_NOTICE);
}


/**
 * Translations
 *
 * recode .po to .mo > msgfmt env_cp.po -o env_cp.mo
 */
 
/* Get user lang */
$query    = 'select `l_code` from `users` as `u`,`lang` as `l` where `l_id` = `lang` and `username` = "'.$_SESSION['ipamusername'].'";;';
$database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

/* execute */
try { $details = $database->getArray( $query ); }
catch (Exception $e) { 
    $error =  $e->getMessage(); 
    print ("<div class='alert alert-error'>"._('Error').": $error</div>");
}
$lang = $details[0]['l_code'];

if(strlen($lang)>0) 	{ 
	putenv("LC_ALL=$lang");
	setlocale(LC_ALL, $lang);							# set language		
	bindtextdomain("phpipam", "./functions/locale");	# Specify location of translation tables
	textdomain("phpipam");								# Choose domain
}



/* set latest version */
define("VERSION", "0.8");

/* @general functions ------------------- */
include_once('functions-common.php');

/* @network functions ------------------- */
include_once('functions-network.php');

/* @tools functions --------------------- */
include_once('functions-tools.php');

/* @admin functions --------------------- */
include_once('functions-admin.php');

/* @upgrade functions ------------------- */
include_once('functions-upgrade.php');

?>