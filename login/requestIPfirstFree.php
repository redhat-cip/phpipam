<?php

/*	return first free IP address in provided subnet
***************************************************/
/* require_once('../functions/functions.php'); */
require( dirname(__FILE__) . '/../functions/functions.php' );

//get requested subnetId
$subnetId = $_POST['subnetId'];

//get first free IP address
$firstIP = transform2long(getFirstAvailableIPAddress ($subnetId));


/* verify that it is not already requested - if so check again! */
while(isIPalreadyRequested(Transform2decimal($firstIP)) == true)
{
	$firstIP = transform2long(getFirstAvailableIPAddress ($subnetId));
}

print $firstIP;
?>