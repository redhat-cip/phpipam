<?php 

/**
 * Edit switch result
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get modified details */
$device = $_POST;

/* sanitize post! */
$device['hostname'] 	= htmlentities($device['hostname'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['ip_addr'] 		= htmlentities($device['ip_addr'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['vendor'] 		= htmlentities($device['vendor'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['model'] 		= htmlentities($device['model'], ENT_COMPAT | ENT_HTML401, "UTF-8");			# prevent XSS
$device['version'] 		= htmlentities($device['version'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$device['description'] 	= htmlentities($device['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS


/* available switches */
foreach($device as $key=>$line) {
	if (strlen(strstr($key,"section-"))>0) {
		$key2 = str_replace("section-", "", $key);
		$temp[] = $key2;
		
		unset($device[$key]);
	}
}
/* glue sections together */
if(sizeof($temp) > 0) {
	$device['sections'] = implode(";", $temp);
}

/* Hostname must be present! */
if($device['hostname'] == "") {
	die('<div class="alert alert-error">'._('Hostname is mandatory').'!</div>');
}

# we need old hostname
if(($device['action'] == "edit") || ($device['action'] == "delete") ) {
	
	# get old switch name
	$oldHostname = getSwitchDetailsById($device['switchId']);
	$oldHostname = $oldHostname['hostname'];

	# if delete new hostname = ""
	if(($device['action'] == "delete")) {
		$device['hostname'] = "";
	}
}


/* update details */
if(!updateSwitchDetails($device)) {
	print('<div class="alert alert-error">'._("Failed to $device[action] device").'!</div>');
}
else {
	print('<div class="alert alert-success">'._("Device $device[action] successfull").'!</div>');
}

?>