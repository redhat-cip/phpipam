<?php

/*
 *	Send notification mail to user if selected
 ***********************************************/

/* use required functions */
require_once('../../config.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

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


/* set content */
$mail['content']  = '<h3>' . $mail['subject'] . '</h3><br>' . "\n";
$mail['content'] .= '<table border="0">' . "\n";

$mail['content'] .= '<tr><td>Name</td>	  	<td>'. $userModDetails['real_name'] .'</td></tr>' . "\n";
$mail['content'] .= '<tr><td>Username</td>	<td>'. $userModDetails['username'] 	.'</td></tr>' . "\n";
$mail['content'] .= '<tr><td>Password</td>	<td>'. $userModDetails['plainpass'] 	.'</td></tr>' . "\n";
$mail['content'] .= '<tr><td>Email</td>		<td>'. $userModDetails['email'] 	.'</td></tr>' . "\n";

$mail['content'] .= '</table>' . "\n";

$mail['content'] .= '<br>You can login to IPAM with your username and password here: '. $settings['siteURL'] . '<br>' . "\n";
$mail['content'] .= 'If you have any issues please contact <a href="'. $settings['siteAdminMail'] .'">'. $settings['siteAdminName'] .'</a>';

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