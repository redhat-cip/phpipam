<?php

/**
 * Script to verify posted data for mail notif
 *************************************************/

/* include required scripts */
require_once('../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all site settings */
$settings = getAllSettings();

/* get posted values */
$mail['recipients'] = $_REQUEST['recipients'];
$mail['from']		= 'IPAM@' . $settings['siteDomain'];
$mail['subject']	= $_REQUEST['subject'];

/* wrap content in <pre> to preserver tabbing */
$mail['content']    = $_REQUEST['content'];

/* reformat content to HTML */
$tr = explode("\n", $_REQUEST['content']);

$mail['content'] = '<table border="0">'; 
foreach ($tr as $line) {

	$mail['content'] .= '<tr>';
	//separate tabs
	$line = explode("\t", $line);
	
	//if no tabs are present colspan=2
	if(sizeof($line) == 1) {
		$mail['content'] .=  '<td colspan=2>'. $line[0] .'</td>';
	}
	else {
		foreach ($line as $key) {
			if (!empty($key)) {
				$mail['content'] .=  '<td>'. $key .'</td>';
			}
		}
	}
	$mail['content'] .=  '</tr>';
}
$mail['content'] .= '</table>';


/* set additional headers */
$mail['headers']	= 'From: ' . $mail['from'] . "\r\n";
$mail['headers']   .= 'Reply-To: '. $settings['siteAdminMail'] . "\r\n";
$mail['headers']   .= "Content-type: text/html; charset=utf8" . "\r\n";
$mail['headers']   .= 'X-Mailer: PHP/' . phpversion();

/* verify mail recipients - multiple mails can be separated with ; */
$recipients_temp = explode(",", $mail['recipients']);

foreach ($recipients_temp as $rec) {
	//verify each email
	if(!checkEmail($rec)) {
		$errors[] = $rec;
	}
}

/* if no errors are present send email! */
if (!$errors) {
	if (!mail($mail['recipients'], $mail['subject'], $mail['content'], $mail['headers'] )) {
		print '<div class="error">Sending mail failed!</div>';
		//write log
		$text = 'Sending notification mail to '. $mail['recipients'] . ' failed!';
		updateLogTable ($text, $severity = 2);
	}
	else {
		print '<div class="success">Sending mail succeeded!</div>';
		//write log
		$text = 'Sending notification mail to '. $mail['recipients'] . ' succeeded!';
		updateLogTable ($text, $severity = 1);
	}
}
else {
	print '<div class="error">Wrong recipients! (separate multiple with ,)</div>';
}

?>