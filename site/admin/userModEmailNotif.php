<?php

/*
 *	Send notification mail to user if selected
 ***********************************************/

/* use required functions */
require_once('../../config.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get settings */
$settings = getAllSettings();

/* set mail values */
$mail['recipients'] = $userModDetails['email'];
$mail['from']		= 'IPAM@' . $settings['siteDomain'];

/* set subject */
if ($action == "Add") {
	$mail['subject']	= 'New IPAM account created';
}
else if ($action == "Edit") {
	$mail['subject']	= 'User account updated';
}
else {
	$mail['subject']	= 'IPAM account details';
}

/* set additional headers */
$mail['headers']	= 'From: "IPAM" <' . $mail['from'] . '>' . "\r\n";
$mail['headers']   .= "Content-type: text/html; charset=utf8" . "\r\n";
$mail['headers']   .= 'X-Mailer: PHP/' . phpversion();


/* set html headers */
$mail['content']  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'. "\n";
$mail['content'] .= '<html>'. "\n";

/* body */
$mail['content'] .= '<body style="margin:0px;padding:0px;color:#2E2E2E;">'. "\n";
$mail['content'] .= '<div style="background:#1d2429;color:white;padding:10px;text-align:left;font:18px Arial;border-bottom:2px solid #c0c0c0;margin-bottom:1px;">' . $mail['subject'] . '</div>' . "\n";

/* table with details */
$mail['content'] .= '<div style="background:#e6eaef;padding:10px">'. "\n";
$mail['content'] .= '<table border="0"">' . "\n";
$mail['content'] .= '<tr><th style="padding: 2px 10px;text-align:left; border-right: 1px solid #c0c0c0">Name</th>	  	<td style="padding-top:3px;padding-bottom:3px;padding-left: 10px;padding-right: 10px;">'. $userModDetails['real_name'] .'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding: 2px 10px;text-align:left; border-right: 1px solid #c0c0c0"">Username</th>	<td style="padding-top:3px;padding-bottom:3px;padding-left: 10px;padding-right: 10px;">'. $userModDetails['username'] 	.'</td></tr>' . "\n";
# we dont need pass for domain account
if($userModDetails['domainUser'] == 0) {
$mail['content'] .= '<tr><th style="padding: 2px 10px;text-align:left; border-right: 1px solid #c0c0c0"">Password</th>	<td style="padding-top:3px;padding-bottom:3px;padding-left: 10px;padding-right: 10px;">'. $userModDetails['plainpass'] .'</td></tr>' . "\n";
}
$mail['content'] .= '<tr><th style="padding: 2px 10px;text-align:left; border-right: 1px solid #c0c0c0"">Email</th>		<td style="padding-top:3px;padding-bottom:3px;padding-left: 10px;padding-right: 10px;">'. $userModDetails['email'] 	.'</td></tr>' . "\n";
$mail['content'] .= '<tr><th style="padding: 2px 10px;text-align:left; border-right: 1px solid #c0c0c0"">Role</th>		<td style="padding-top:3px;padding-bottom:3px;padding-left: 10px;padding-right: 10px;">'. $userModDetails['role'] 	.'</td></tr>' . "\n";
$mail['content'] .= '</table>' . "\n";

if($userModDetails['domainUser'] == 0) {
$mail['content'] .= '<br>You can login to IPAM with your username and password here: <a href="http://'. $settings['siteURL'] .'">'. $settings['siteURL'] . '</a><br>' . "\n";
}
else {
$mail['content'] .= '<br>You can login to IPAM with your <b>DOMAIN</b> username and password here: <a href="http://'. $settings['siteURL'] .'">'. $settings['siteURL'] . '</a><br>' . "\n";
}

$mail['content'] .= '</div>'. "\n";


/* footer */
$mail['content'] .= '<div style="padding:8px;text-align:center;background:#1d2429;color:#D5D5D5;border-top:1px solid white;border-bottom:1px solid #c0c0c0;">'.  "\n";
$mail['content'] .= '<a href="https://sourceforge.net/projects/phpipam/" style="color:#D5D5D5;">phpIPAM</a> IP address management [v'. $settings['version']. '] &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; Site admin: <a href="mailto:'. $settings['siteAdminMail'] .'" style="color:#D5D5D5;">'. $settings['siteAdminName'] .'</a>' . "\n";
$mail['content'] .= '</div>'. "\n";

/* end html */
$mail['content'] .= '</body>'. "\n";
$mail['content'] .= '</html>'. "\n";


/* send mail */
if (!mail($mail['recipients'], $mail['subject'], $mail['content'], $mail['headers'] )) {
	print '<div class="error">Sending notification mail for new account failed!</div>';
	//write log
	$text = 'Sending notification mail for new account to '. $mail['recipients'] . ' failed!';
	updateLogTable ($text, $severity = 2);
}
else {
	print '<div class="success">Notification mail for new account sent!</div>';
	//write log
	$text = 'Sending notification mail for new account to '. $mail['recipients'] . ' succeeded!';
	updateLogTable ($text, $severity = 1);
}


?>