<?php

/* include required scripts */
require_once('../../functions/functions.php');

/* no errors */
ini_set('display_errors', 0); 

/* verify that user is authenticated! */
isUserAuthenticated ();

/**
 * 	Resolve DNS name from ip address
 */
$hostname = gethostbyaddr($_POST['ipaddress']);

if($hostname == $_POST['ipaddress']) {
	print "";
}
else {
	print $hostname;
}
?>