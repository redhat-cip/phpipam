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
if ($action == "Add") 		{ $subject	= _('New ipam account created'); }
else if ($action == "Edit") { $subject	= _('User ipam account updated'); }
else 						{ $subject	= _('IPAM account details'); }

# send mail
if(!sendUserAccDetailsEmail($userModDetails, $subject))	{ print '<div class="alert alert-error"><div class="alert alert-error">'._('Sending notification mail for new account failed').'!</div></div>'; }
else													{ print '<div class="alert alert-success">'._('Notification mail for new account sent').'!</div>'; }


?>