<?php

/**
 * Script to notify requester and admins that new IP request has been accepted / rejected
 */

# @mail functions ------------------- */
include_once('../../functions/functions-mail.php');

# verify that user is admin
checkAdmin();

# send mail
if(!sendIPResultEmail($request))	{ print '<div class="alert alert-error">'._('Sending mail for new IP request failed').'!</div>'; }
else								{ print '<div class="alert alert-success">'._('Sending mail for IP request succeeded').'!</div>'; }

?>