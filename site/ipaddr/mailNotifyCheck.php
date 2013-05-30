<?php

/**
 * Script to verify posted data for mail notif
 *************************************************/

/* include required scripts */
require_once('../../functions/functions.php');
/* @mail functions ------------------- */
include_once('../../functions/functions-mail.php');

/* check referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated ();

/* verify mail recipients - multiple mails can be separated with ; */
$recipients_temp = explode(",", $_REQUEST['recipients']);

foreach ($recipients_temp as $rec) {
	//verify each email
	if(!checkEmail($rec)) {
		$errors[] = $rec;
	}
}


# if no errors send mail
if (!$errors) {
	if(!sendIPnotifEmail($_REQUEST['recipients'], $_REQUEST['subject'], $_REQUEST['content']))	{ print '<div class="alert alert-error">'._('Sending mail failed').'!</div>'; }
	else																						{ print '<div class="alert alert-success">'._('Sending mail succeeded').'!</div>'; }
}
else {
	print '<div class="alert alert-error">'._('Wrong recipients! (separate multiple with ,)').'</div>';
}


?>