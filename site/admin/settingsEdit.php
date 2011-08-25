<?php

/**
 *	Site settings
 **************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* fetch all settings */
$settings = $_POST;

/* Update settings */
if(!updateSettings($settings)) {
	die('<div class="error">Cannot update settings!</div>');
}
else {
	print '<div class="success">Settings updated successfully!</div>';
}
?>