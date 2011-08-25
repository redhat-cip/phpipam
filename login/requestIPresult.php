<?php

/**
 *
 * Script to verify user requested input and verify it
 *
 */
 
 
/* functions */
require_once('../functions/functions.php'); 

/* get all posted variables */
$request = $_POST;

/* first get subnet details */
$subnet = getSubnetDetailsById ($request['subnetId']);
$subnet['subnet'] = Transform2long ($subnet['subnet']);
$subnet = $subnet['subnet'] . "/" . $subnet['mask'];

/* verify IP address and subnet */
$validIP = VerifyIpAddress ($request['ip_addr'], $subnet);
if(!empty($validIP)) {
	die('<div class="error">'. $validIP .'</div>');
}
/* verify that it is not yet used */
if(checkDuplicate ($request['ip_addr'], $subnet['subnet'])) {
	die('<div class="error">IP address '. $request['ip_addr'] .' already in use!<br>* First available IP address is <b>'. Transform2long(getFirstAvailableIPAddress ($request['subnet'])) .'</a>!</div>');
}
/* verify that it is not already requested */
if(isIPalreadyRequested(Transform2decimal($request['ip_addr']))) {
	die('<div class="error">IP address '. $request['ip_addr'] .' is already in request procedure!</div>');
}

/* verify email */
if(!checkEmail($request['requester']) ) {
	die('<div class="error">Please provide valid email address! (requester: <del>'. $request['requester'] .'</del>)</div>');	
}

/* insert new request to database */
$request['ip_addr'] = Transform2decimal($request['ip_addr']);

if(addNewRequest ($request)) {
	print '<div class="success">Request submitted successfully!</div>';
	
	/* send confirmation emails to requester and all admins! */
	include_once('requestIPmail.php');
}
else {
	print '<div class="error">Error submitting new IP address request!</div>';
}

print_r($validIP);

?>