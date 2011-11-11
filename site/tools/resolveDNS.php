<?php

/**
 * 	Resolve DNS name from ip address
 */
$hostname = gethostbyaddr($_POST['ipaddress']);

print $hostname;
?>