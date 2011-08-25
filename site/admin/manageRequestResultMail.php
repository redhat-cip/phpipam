<?php

/**
 * Script to notify requester and admins that new IP request has been accepted / rejected
 */

/* verify that user is admin */
checkAdmin();

/* get all admins */
$admins = getAllAdminUsers ();

/* get all site settings */
$settings = getAllSettings();

/* set mail values */
$mail['recipients']  = $request['requester'];
foreach($admins as $admin) {
$mail['recipients'] .= ', '. $admin['email'];
}

/* reformat IP */
$request['ip_addr'] = transform2long($request['ip_addr']);

$mail['from']		= 'IPAM@' . $settings['siteDomain'];
$mail['subject']	= 'IP address request ('. $request['ip_addr'] .') '. $request['action'];

/* set additional headers */
$mail['headers']	= 'From: ' . $mail['from'] . "\r\n";
$mail['headers']   .= 'Reply-To: '. $settings['siteAdminMail'] . "\r\n";
$mail['headers']   .= "Content-type: text/html; charset=utf8" . "\r\n";
$mail['headers']   .= 'X-Mailer: PHP/' . phpversion();


/* content */
$mail['content']    = '<h2>IP address request - '. $request['ip_addr'] .' '. $request['action'] .'</h2>' . "\n";


//accept / reject reason
$mail['content']   .= 'Comment: '. $request['adminComment'] . '<br><br>' . "\n";

$mail['content']   .= '<table border=1 style="border-collapse:collapse">' . "\n"; 
//section
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Requested section</td>' . "\n";
$mail['content']   .= '<td>'. $subnet .'</td>' . "\n";
$mail['content']   .= '</tr>' . "\n";
//ip address
$mail['content']   .= '<tr>' . "\n";
$mail['content']   .= '<td>Requested IP address</td>' . "\n";
$mail['content']   .= '<td>'. $request['ip_addr'] .'</td>' . "\n";
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


$mail['content']   .= '</table>' . "\n"; 

//date, IP
$mail['content']   .= '<br><i>*Reply was sent on ' . date("Y-m-d H:m:s") .'.</i>' . "\n";


if (!mail($mail['recipients'], $mail['subject'], $mail['content'], $mail['headers'] )) {
	print '<div class="error">Sending notification mail failed!</div>';
	//write log
	$text = 'Sending notification mail to '. $mail['recipients'] . ' failed!';
	updateLogTable ("IP request reject mail sending failed", $text, $severity = 2);
}
else {
	print '<div class="success">Sending notification mail succeeded!</div>';
	//write log
	$text = 'Sending notification mail to '. $mail['recipients'] . ' succeeded!';
	updateLogTable ("IP request accept mail sent ok.", $text, $severity = 1);
}


?>