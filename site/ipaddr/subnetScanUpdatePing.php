<?php

/*
 * Update alive status of all hosts in subnet
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 
require_once('../../functions/config-scan.php'); 

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-error">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all existing IP addresses
$addresses = getIpAddressesBySubnetId ($_POST['subnetId']);


# loop and check
foreach($addresses as $ip) {
	$m = 0;											//array count
	//if strictly disabled for ping
	if($ip['excludePing']=="1") {
		$ip[$m]['status'] = "excluded from check";
	}
	//ping
	else {
		$code = pingHost (transform2long($ip['ip_addr']), $count, false);
	}
	
	$m++;											//next array item
}
?>


<h5><?php print _('Scan results');?> (<?php print_r($_POST['pingType']) ?>):</h5>
<hr>