<?php

/**
 * Script to confirm / reject IP address request
 ***********************************************/

require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get posted request */
$request = $_POST;


/* if action is reject set processed and accepted to 1 and 0 */
if($request['action'] == "reject") {
	if(!rejectIPrequest($request['requestId'], $request['adminComment'])) {
		print '<div class="error">Cannot update request!</div>';
		updateLogTable ('Cannot reject IP request', 'Cannot reject IP request for request id '. $request['requestId'] .'!', 2);
	}
	else {
		print '<div class="success">Request has beed rejected!</div>';
		updateLogTable ('Request has beed rejected!', 'IP request id '. $request['requestId'] .' ('. $request['ip_addr'] .') has been rejected!', 1);
		
		/* transform IP address */
		$request['ip_addr'] = Transform2decimal($request['ip_addr']);
	}
}
else {

	/* first check all the variables */
	$subnet = getSubnetDetailsById ($request['subnetId']);
	$subnet['subnet'] = Transform2long ($subnet['subnet']);
	$subnet = $subnet['subnet'] . "/" . $subnet['mask'];

	/* verify IP address and subnet */
	$validIP = VerifyIpAddress ($request['ip_addr'], $subnet);
	if(!empty($validIP)) {
		die('<div class="error">'. $validIP .'</div>');
	}
	/* verify that it is not yet used */
	if(checkDuplicate ($request['ip_addr'], $subnet)) {
		die('<div class="error">IP address '. $request['ip_addr'] .' already in use!<br>* First available IP address is <b>'. Transform2long(getFirstAvailableIPAddress ($request['subnet'])) .'</a>!</div>');
	}
	/* verify that it is not yet used */
	if(checkDuplicate ($request['ip_addr'], $request['subnetId'])) {
	die('<div class="error">IP address '. $request['ip_addr'] .' already in use!<br>* First available IP address is <b>'. Transform2long(getFirstAvailableIPAddress ($request['subnetId'])) .'</a>!</div>');
	}
	
	/* transform IP address */
	$request['ip_addr'] = Transform2decimal($request['ip_addr']);

	
	if(!acceptIPrequest($request)) {
		die('<div class="error">Cannot confirm IP address!</div>');
		updateLogTable ('Cannot confirm IP address!', 'Cannot accept IP request for request id '. Transform2long($request['requestId']) .'!', 2);
	}
	else {
		print '<div class="success">IP address request confirmed and added to database!</div>';
		updateLogTable ('IP address request confirmed and added to database!', 'IP request id '. $request['requestId'] .' ('. Transform2long($request['ip_addr']) .') has been accepted!', 0);
	}
}

/* send email */
$subnet = getSubnetDetailsById ($request['subnetId']);
$subnet['subnet'] = Transform2long ($subnet['subnet']);
$subnet = $subnet['subnet'] . "/" . $subnet['mask'];

include_once('manageRequestResultMail.php');

?>