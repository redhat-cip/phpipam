<?php

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