<?php 

/**
 * Edit switch result
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get modified details */
$switch = $_POST;

/* sanitize post! */
$switch['hostname'] 	= htmlentities($switch['hostname'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['ip_addr'] 		= htmlentities($switch['ip_addr'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['vendor'] 		= htmlentities($switch['vendor'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['model'] 		= htmlentities($switch['model'], ENT_COMPAT | ENT_HTML401, "UTF-8");			# prevent XSS
$switch['version'] 		= htmlentities($switch['version'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$switch['description'] 	= htmlentities($switch['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS


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

/* Hostname must be present! */
if($switch['hostname'] == "") {
	die('<div class="error">Hostname is mandatory!</div>');
}

# we need old hostname
if(($switch['action'] == "edit") || ($switch['action'] == "delete") ) {
	
	# get old switch name
	$oldHostname = getSwitchDetailsById($switch['switchId']);
	$oldHostname = $oldHostname['hostname'];

	# if delete new hostname = ""
	if(($switch['action'] == "delete")) {
		$switch['hostname'] = "";
	}
}


/* update details */
if(!updateSwitchDetails($switch)) {
	print('<div class="error">Failed to '. $switch['action'] .' switch!</div>');
}
else {
	/* update IP addresses on edit and delete */
	if(($switch['action'] == "edit") || ($switch['action'] == "delete") ) {
	
		# update hosts
		if(!updateIPaddressesOnSwitchChange($oldHostname, "")) {
			print('<div class="success">Switch '. $switch['action'] .' successfull!</div>');
			print('<div class="error">Failed to update ip address list!</div>');
		}
		else {
			print('<div class="success">Switch '. $switch['action'] .' successfull!</div>');
		}
	}
	/* on add do nothing */
	else {
		print('<div class="success">Switch '. $switch['action'] .' successfull!</div>');
	}
}

?>