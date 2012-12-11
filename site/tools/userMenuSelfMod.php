<?php

/**
 * 
 * User selfMod check end execute
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify posted data */
CheckReferrer();

/* get changed details */
$modData = $_POST;

/* verify email */
if (!checkEmail($modData['email'])) 												{ $error = 'Email not valid!'; }

/* verify password if changed (not empty) */
if (strlen($modData['password1']) != 0) {

	/* Hash passwords */
	$modData['password1'] = md5($modData['password1']);
	$modData['password2'] = md5($modData['password2']);

	if ( (strlen($modData['password1']) < 8) && (!empty($modData['password1'])) ) 	{ $error = 'Password must be at least 8 characters long!'; }
	else if ($modData['password1'] != $modData['password2']) 						{ $error = 'Passwords do not match!'; }
}


/* Print errors if present and die, else update */
if ($error) { die('<div class="alert alert-error alert-absolute">Please fix the following error: <strong>'. $error .'<strong></div>'); }
else {
    if (!selfUpdateUser ($modData)) { die('<div class="alert alert-error alert-absolute">error updating!</div>'); }
    else 							{ print '<div class="alert alert-success alert-absolute">Details updated successfully!</div>'; }
}

?>