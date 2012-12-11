<?php

/**
 * Script to get all active IP requests
 ****************************************/


/* verify that user is admin */
checkAdmin();

/* get AD settings */
$adSettings = getADSettings();

/* get settings */
$settings = getallSettings();

/* set title */
if($settings['domainAuth'] == "2") {
	include('manageAD_LDAP.php');
}
else {
	include('manageAD_AD.php');
}
?>