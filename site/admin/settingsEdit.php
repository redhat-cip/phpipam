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

/* verify ping status fields */
$settings['pingStatus'] = str_replace(" ", "", $settings['pingStatus']);		//remove possible spaces
$settings['pingStatus'] = str_replace(",", ";", $settings['pingStatus']);		//change possible , for ;
$statuses = explode(";", $settings['pingStatus']);

if(sizeof($statuses)!=2)										{ die('<div class="alert alert-error">'._("Invalid ping status intervals").'</idv>'); }
if(!is_numeric($statuses[0]) || !is_numeric($statuses[1]))		{ die('<div class="alert alert-error">'._("Invalid ping status intervals").'</idv>'); }

/* Update settings */
if(!updateSettings($settings)) 	{ die('<div class="alert alert-error">'._('Cannot update settings').'!</div>'); }
else 							{ print '<div class="alert alert-success">'._('Settings updated successfully').'!</div>';}
?>