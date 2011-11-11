<?php

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