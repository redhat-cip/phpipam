<?php

/*	return first free IP address in provided subnet
***************************************************/
/* require_once('../functions/functions.php'); */
require( dirname(__FILE__) . '/../../functions/functions.php' );

//get requested subnetId
$subnetId = $_POST['subnetId'];

//get first free IP address
$firstIP = transform2long(getFirstAvailableIPAddress ($subnetId));

//get first free IP address
$firstIP = transform2long(getFirstAvailableIPAddress ($subnetId));

print $firstIP;
?>