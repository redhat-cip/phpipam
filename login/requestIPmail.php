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
$mail['headers'] .= 'Reply-To: '. $settings['siteAdminMail'] . "\r\n";
$mail['headers'] .= "Content-type: text/html; charset=utf8" . "\r\n";
$mail['headers'] .= 'X-Mailer: PHP/' . phpversion();


/* content */
$mail['content']  	= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'. "\n";
$mail['content'] 	.= '<html>'. "\n";

/* body */
$mail['content'] .= '<body style="margin:0px;padding:0px;color:#2E2E2E;">'. "\n";
$mail['content'] .= '<div style="background:#1d2429;color:white;padding:10px;margin-bottom:1px;text-align:left;font: 18px Arial;border-bottom:2px solid #c0c0c0;">New IP address request - '. Transform2long($request['ip_addr']) .'</div>' . "\n";

$mail['content'] .= '<div style="padding:20px;font:13px Arial;color:#2E2E2E;border-bottom: 1px solid #c0c0c0;background:#e6eaef;">'. "\n";
$mail['content'] .= '<table style="border-collapse:collapse">' . "\n"; 

$mail['content'] .= '<tr><th style="padding:3px 10px;text-align:left;border-right: 1px solid #c0c0c0;">Requested section   	</th><td style="padding: 3px 10px;">'. $subnet .'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding:3px 10px;text-align:left;border-right: 1px solid #c0c0c0;">Requested IP address	</th><td style="padding: 3px 10px;">'. Transform2long($request['ip_addr']) .'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding:3px 10px;text-align:left;border-right: 1px solid #c0c0c0;">Description		 	</th><td style="padding: 3px 10px;">'. $request['description'] .'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding:3px 10px;text-align:left;border-right: 1px solid #c0c0c0;">Hostname			 	</th><td style="padding: 3px 10px;">'. $request['dns_name'] .'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding:3px 10px;text-align:left;border-right: 1px solid #c0c0c0;">Owner				</th><td style="padding: 3px 10px;">'. $request['owner'] .'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding:3px 10px;text-align:left;border-right: 1px solid #c0c0c0;">Requested from		</th><td style="padding: 3px 10px;">'. $request['requester'] .'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding:3px 10px;text-align:left;border-right: 1px solid #c0c0c0;">Comment			 	</th><td style="padding: 3px 10px;">'. $request['comment'] .'</td></tr>' . "\n";

$mail['content'] .= '</table>' . "\n"; 

//date, IP
$mail['content'] .= '<div style="color:#c0c0c0;text-style:italic;font:11px Arial;padding-top:10px;">* Request was submitted on ' . date("Y/m/d H:i:s") . ' from IP address '.  $_SERVER['REMOTE_ADDR'] .'.</div>' . "\n";
$mail['content'] .= '</div>'. "\n";

/* footer */
$mail['content'] .= '<div style="padding:8px;text-align:center;background:#1d2429;color:#D5D5D5;border-top:1px solid white;border-bottom:1px solid #c0c0c0;">'.  "\n";
$mail['content'] .= '<a href="https://sourceforge.net/projects/phpipam/" style="color:#D5D5D5;">phpIPAM</a> IP address management [v'. $settings['version']. '] &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; Site admin: <a href="mailto:'. $settings['siteAdminMail'] .'" style="color:#D5D5D5;">'. $settings['siteAdminName'] .'</a>' . "\n";
$mail['content'] .= '</div>'. "\n";

/* end html */
$mail['content'] .= '</body>'. "\n";
$mail['content'] .= '</html>'. "\n";




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