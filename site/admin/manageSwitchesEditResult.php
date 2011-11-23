<?php 

/**
 * Script to print switches
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');


/* get modified details */
$switch = $_POST;

/* if we edit hostname we must also update all hosts! */
if($switch['action'] == "edit") {
	$oldHostname = getSwitchDetailsById($switch['switchId']);
	$oldHostname = $oldHostname['hostname'];
}

/* Hostname must be present! */
if($switch['hostname'] == "") {
	die('<div class="error">Hostname is mandatory!</div>');
}

/* update details */
if(!updateSwitchDetails($switch)) {
	print('<div class="error">Failed to '. $switch['action'] .' switch!</div>');
}
else {
	/* update IP addresses */
	if(!updateIPaddressesOnSwitchChange($oldHostname, $switch['hostname'])) {
		print('<div class="success">Switch '. $switch['action'] .' successfull!</div>');
		print('<div class="error">Failed to update ip address list!</div>');
	}
	else {
		print('<div class="success">Switch '. $switch['action'] .' successfull!</div>');
	}
}

?>