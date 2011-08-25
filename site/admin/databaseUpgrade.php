<?php

/*
 *	Script to upgrade database
 **************************************/

/* use required functions */
require_once('../../config.php');
require_once('../../functions/functions.php');

/* get all site settings */
$settings = getAllSettings();

/* display only to admin users */
if(!checkAdmin(false)) {
	die('<div class="error">Admin user required!</div>');
}

/* get version */
$version = $_POST['version'];

/* try to upgrade database */
if(upgradeDatabase($version)) {
	print '<div class="success">Database upgraded successfully!</div>';
}

?>