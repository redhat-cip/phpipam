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

/* get active user name */
$sender = getActiveUserDetails();

/* get all site settings */
$settings = getAllSettings();

/* get posted values */
$mail['recipients'] = $_REQUEST['recipients'];
$mail['from']		= 'IPAM@' . $settings['siteDomain'];
$mail['subject']	= $_REQUEST['subject'];

/* wrap content in <pre> to preserver tabbing */
$mail['content']    = $_REQUEST['content'];

/* reformat content to HTML */
$mail['userContent']	= str_replace("\n", "<br>", $mail['content']);


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

	/* set mail content! */
	$mail['content']  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'. "\n";
	$mail['content'] .= '<html>'. "\n";

	/* body */
	$mail['content'] .= '<body style="margin:0px;padding:0px;color:#2E2E2E;">'. "\n";
	$mail['content'] .= '<div style="background:#1d2429;color:white;padding:10px;margin-bottom:1px;text-align:left;font: 18px Arial;border-bottom:2px solid #c0c0c0;">IP address details</div>' . "\n";

	/* print content as provided by user! */
	$mail['content'] .= '<div style="padding:20px;font:13px Arial;color:#2E2E2E;border-bottom: 1px solid #c0c0c0;background:#e6eaef;">'. "\n";
	$mail['content'] .= $mail['userContent'] . "\n";
	$mail['content'] .= '<br><br>'. "\n";
	$mail['content'] .= '<div style="color:silver;font-size:11px;"><i>Sent by user '. $sender['real_name'] .' at '. date('Y/m/d H:i') .'.</i></div>'. "\n";
	$mail['content'] .= '</div>'. "\n";	
	
	/* footer */
	$mail['content'] .= '<div style="padding:8px;text-align:center;background:#1d2429;color:#D5D5D5;border-top:1px solid white;border-bottom:1px solid #c0c0c0;">'.  "\n";
	$mail['content'] .= '<a href="https://sourceforge.net/projects/phpipam/" style="color:#D5D5D5;">phpIPAM</a> IP address management [v'. $settings['version']. '] &nbsp;&nbsp;&nbsp; | &nbsp;&nbsp;&nbsp; Site admin: <a href="mailto:'. $settings['siteAdminMail'] .'" style="color:#D5D5D5;">'. $settings['siteAdminName'] .'</a>' . "\n";
	$mail['content'] .= '</div>'. "\n";

	/* end html */
	$mail['content'] .= '</body>'. "\n";
	$mail['content'] .= '</html>'. "\n";


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