<?php

/*
 *	Send notification mail to user if selected
 ***********************************************/

/* use required functions */
require_once('../../config.php');
/* @mail functions ------------------- */
include_once('../../functions/functions-mail.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get settings */
$settings = getAllSettings();

/* set mail values */
$mail['recipients'] = $userModDetails['email'];
$mail['from']		= 'IPAM@' . $settings['siteDomain'];

# set subject
if ($action == "Add") 		{ $subject	= 'New ipam account created'; }
else if ($action == "Edit") { $subject	= 'User ipam account updated'; }
else 						{ $subject	= 'IPAM account details'; }

# send mail
if(!sendUserAccDetailsEmail($userModDetails, $subject))	{ print '<div class="alert alert-error"><div class="alert alert-error">Sending notification mail for new account failed!</div></div>'; }
else													{ print '<div class="alert alert-success">Notification mail for new account sent!</div>'; }


?>