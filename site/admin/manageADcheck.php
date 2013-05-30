<?php 

/**
 * Script to check AD settings
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

ini_set('display_errors', 0);

/* verify that user is admin */
checkAdmin();

/* get settings */
$ad = $_POST;

/* reformat DC */
$dc = str_replace(" ", "", $ad['domain_controllers']);
$dcTemp = explode(";", $dc);
$ad['domain_controllers'] = $dcTemp;


/* open connection and print result */
include (dirname(__FILE__) . "/../../functions/adLDAP/src/adLDAP.php");
	
//open connection
try {	
	$adldap = new adLDAP(array( 'base_dn'=>$ad['base_dn'], 'account_suffix'=>$ad['account_suffix'], 
								'domain_controllers'=>$ad['domain_controllers'], 'use_ssl'=>$ad['use_ssl'],
								'use_tls'=> $ad['use_tls'], 'ad_port'=> $ad['ad_port']
	    						));
}
catch (adLDAPException $e) {
	die ('<div class="alert alert-error">'. $e .'</div>');
}


foreach($ad['domain_controllers'] as $line) {

	$fp = fsockopen($line, $ad['ad_port'], $errno, $errstr, 3); 
	if (!$fp) 	{ print '<div class="alert alert-error">'. $line .': '. $errstr .' ('. $errno .')</div>';}
	else 		{ print '<div class="alert alert-success">'. $line .': '._('AD network connection ok').'!</div>';}

}


?>