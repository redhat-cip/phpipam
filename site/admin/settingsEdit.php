<?php

/**
 *	Site settings
 **************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin(false);

/* fetch all settings */
$settings = $_POST;

/* check for http/https */
if ( (strpos($settings['siteURL'],'http://') !== false) || (strpos($settings['siteURL'],'https://') !== false) ) {}
else {
	$settings['siteURL'] = "http://".$settings['siteURL'];
}

/* Update settings */
if(!updateSettings($settings)) 	{ die('<div class="alert alert-error">'._('Cannot update settings').'!</div>'); }
else 							{ print '<div class="alert alert-success">'._('Settings updated successfully').'!</div>';}
?>