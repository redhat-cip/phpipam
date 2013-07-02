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
if(!checkAdmin(false)) { die('<div class="alert alert-error">Admin user required!</div>'); }

/* get version */
$version = $settings['version'];

/* try to upgrade database */
if(upgradeDatabase($version)) {
	print '<div class="alert alert-success">Database upgraded successfully!</div>';
	print '<a href="login/"><button class="btn btn-small">Go to login</button></a>';
}

?>