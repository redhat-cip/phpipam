<?php

/**
 *
 * Script to verify user requested input and verify it
 *
 */
 
 
/* functions */
if(!function_exists(getSubnetDetailsById)) { require_once('../../functions/functions.php'); }

/* @mail functions ------------------- */
include_once('../../functions/functions-mail.php');

# First chech referer and requested with 
CheckReferrer();

/* get all posted variables */
$request = $_POST;

/* first get subnet details */
$subnet = getSubnetDetailsById ($request['subnetId']);
$subnet2 = $subnet;												//for later check
$subnet['subnet'] = Transform2long ($subnet['subnet']);
$subnet = $subnet['subnet'] . "/" . $subnet['mask'];

/* verify IP address and subnet */
$validIP = VerifyIpAddress ($request['ip_addr'], $subnet);
if(!empty($validIP)) 											{ die('<div class="alert alert-error">'. $validIP .'</div>'); }
/* verify that it is not yet used */
if(checkDuplicate ($request['ip_addr'], $subnet2['id'])) 		{ die('<div class="alert alert-error">IP address '. $request['ip_addr'] .' already in use!<br>* First available IP address is <b>'. Transform2long(getFirstAvailableIPAddress ($request['subnetId'])) .'</a>!</div>');}
/* verify that it is not already requested */
if(isIPalreadyRequested(Transform2decimal($request['ip_addr']))) { die('<div class="alert alert-error">IP address '. $request['ip_addr'] .' is already in request procedure!</div>'); }
/* verify email */
if(!checkEmail($request['requester']) ) 						{ die('<div class="alert alert-error">Please provide valid email address! (requester: <del>'. $request['requester'] .'</del>)</div>');	 }

/* insert new request to database */
$request['ip_addr'] = Transform2decimal($request['ip_addr']);

if(addNewRequest ($request)) {
	print '<div class="alert alert-success">Request submitted successfully!</div>';
	
	/* send confirmation emails to requester and all admins! */
	# get all admins
	$admins = getAllAdminUsers ();

	# set recipients
	$to  = $request['requester'];
	foreach($admins as $admin) 	{ $to .= ', '. $admin['email']; }

	# set subnject
	$subject	= 'New IP address request ('. Transform2long($request['ip_addr']) .')';

	# send mail
	if(!sendIPReqEmail($to, $subject, $request))	{ print '<div class="alert alert-error">Sending mail for new IP request failed!</div>'; }
	else											{ print '<div class="alert alert-success">Sending mail for IP request succeeded!</div>'; }
}
else {
	print '<div class="alert alert-error">Error submitting new IP address request!</div>';
}

?>