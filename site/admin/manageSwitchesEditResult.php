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

/* sanitize post! */
$switch['hostname'] 	= htmlentities($subnetDetails['hostname'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['ip_addr'] 		= htmlentities($subnetDetails['ip_addr'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['vendor'] 		= htmlentities($subnetDetails['vendor'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['model'] 		= htmlentities($subnetDetails['model'], ENT_COMPAT | ENT_HTML401, "UTF-8");			# prevent XSS
$switch['version'] 		= htmlentities($subnetDetails['version'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['description'] 	= htmlentities($subnetDetails['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS


/* available switches */
foreach($switch as $key=>$line) {
	if (strlen(strstr($key,"section-"))>0) {
		$key2 = str_replace("section-", "", $key);
		$temp[] = $key2;
		
		unset($switch[$key]);
	}
}
/* glue sections together */
$switch['sections'] = implode(";", $temp);




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