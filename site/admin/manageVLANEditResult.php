<?php 

/**
 * Script to edit VLAN details
 *******************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get modified details */
$vlan = $_POST;


/*
print_r($vlan);
die('error');
*/

/* sanitize post! */
$vlan['name'] 		 = htmlentities($vlan['name'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$vlan['number'] 	 = htmlentities($vlan['number'], ENT_COMPAT | ENT_HTML401, "UTF-8");			# prevent XSS
$vlan['description'] = htmlentities($vlan['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS


/* Hostname must be present! */
if($vlan['number'] == "") {
	die('<div class="error">Number is mandatory!</div>');
}

/* update details */
if(!updateVLANDetails($vlan)) {
	print('<div class="error">Failed to '. $vlan['action'] .' VLAN!</div>');
}
else {
	print('<div class="success">VLAN '. $vlan['action'] .' successfull!</div>');
}

?>