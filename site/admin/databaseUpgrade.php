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
$version = $_POST['version'];

/* try to upgrade database */
if(upgradeDatabase($version)) {
	print '<div class="alert alert-success">Database upgraded successfully!</div>';
	print '<a href="login/"><button class="btn btn-small">Go to login</button></a>';

	/* update vlans and switches from v 0.4 */
	if($version < "0.5") {
		 updateVLANsFromOldVersions();
		 updateSwitchFromOldVersions();
	}
	/* update VLANS from version 0.5 */
	else if($version < "0.6") {
		 updateVLANsFromOldVersions();
	}
	
	/* update Switch associations */
	if ($version < "0.7") {
		updateSwitchFromOldVersionsToId();
		addHTTP();
	}
}

?>