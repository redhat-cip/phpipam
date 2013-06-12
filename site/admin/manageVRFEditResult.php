<?php 

/**
 * Script to edit VRF
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get modified details */
$vrf = $_POST;

/* sanitize post! */
$vrf['name'] 		= htmlentities($vrf['name'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$vrf['rd'] 			= htmlentities($vrf['rd'], ENT_COMPAT | ENT_HTML401, "UTF-8");			# prevent XSS
$vrf['description'] = htmlentities($vrf['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS


/* Hostname must be present! */
if($vrf['name'] == "") { die('<div class="alert alert-error">'._('Name is mandatory').'!</div>'); }

/* update details */
if(!updateVRFDetails($vrf)) { print('<div class="alert alert-error">'._("Failed to $vrf[action] VRF").'!</div>'); }
else 						{ print('<div class="alert alert-success">'._("VRF $vrf[action] successfull").'!</div>'); }

?>