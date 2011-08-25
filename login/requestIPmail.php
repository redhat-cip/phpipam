<?php

/**
 * Script to notify requester and admins that new IP request has arrived
 */


/* use required functions */
require_once('../config.php');
require_once('../functions/functions.php');

/* First chech referer and requested with */
CheckReferrer();

/* get all admins */
$admins = getAllAdminUsers ();

/* get all site settings */
$settings = getAllSettings();

/* set mail values */
$mail['recipients']  = $request['requester'];
foreach($admins as $admin) {
$mail['recipients'] .= ', '. $admin['email'];
}

$mail['from']		= 'IPAM@' . $settings['siteDomain'];
$mail['subject']	= 'New IP address request ('. Transform2long($request['ip_addr']) .')';

/* set additional headers */
$mail['headers']	= 'From: ' . $mail['from'] . "\r\n";
$mail['headers']   .= 'Reply-To: '. $settings['siteAdminMail'] . "\r\n";
$mail['headers']   .= "Content-type: text/html; charset=utf8" . "\r\n";
$mail['headers']   .= 'X-Mailer: PHP/' . phpversion();


/* content */
$mail['content']    = '<h2>New IP address request - '. Transform2long($request['ip_addr']) .'</h2>' . "\n";

$mail['content']   .= '<table border=1 style="border-collapse:collapse">' . "\n"; 
//section
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Requested section</td>' . "\n";
$mail['content']   .= '<td>'. $subnet .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";
//ip address
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Requested IP address</td>' . "\n";
$mail['content']   .= '<td>'. Transform2long($request['ip_addr']) .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";
//Description
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Description</td>' . "\n";
$mail['content']   .= '<td>'. $request['description'] .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";
//DNS name
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Hostname</td>' . "\n";
$mail['content']   .= '<td>'. $request['dns_name'] .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";
//owner
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Owner</td>' . "\n";
$mail['content']   .= '<td>'. $request['owner'] .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";
//requester
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Requested from</td>' . "\n";
$mail['content']   .= '<td>'. $request['requester'] .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";
//comment
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Comment</td>' . "\n";
$mail['content']   .= '<td>'. $request['comment'] .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";

$mail['content']   .= '</table>' . "\n"; 

//date, IP
$mail['content']   .= '<br><i>*Request was submitted on ' . date("Y-m-d H:m:s") . ' from IP address '.  $_SERVER['REMOTE_ADDR'] .'.</i>' . "\n";



if (!mail($mail['recipients'], $mail['subject'], $mail['content'], $mail['headers'] )) {
	print '<div class="error">Sending notification mail failed!</div>';
	//write log
	$text = 'Sending notification mail to '. $mail['recipients'] . ' failed!';
	updateLogTable ("New IP request mail sending failed", $text, $severity = 2);
}
else {
	print '<div class="success">Sending notification mail succeeded!</div>';
	//write log
	$text = 'Sending notification mail to '. $mail['recipients'] . ' succeeded!';
	updateLogTable ("New IP request mail sent ok", $text, $severity = 1);
}


?>