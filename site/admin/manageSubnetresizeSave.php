<?php

/*
 * Print resize subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* verify post */
CheckReferrer();

# get old subnet details
$subnetOld = getSubnetDetailsById ($_POST['subnetId']);

# get all site settings
$settings = getAllSettings();

/* 
 * now we need to check each host against new subnet
 */
$ipaddresses   = getIpAddressesBySubnetIdSort ($_POST['subnetId'], "ip_addr", "asc");		# get all IP addresses

foreach($ipaddresses as $ip) {
	# check against new subnet
	$error = VerifyIpAddress( transform2long($ip['ip_addr']), transform2long($subnetOld['subnet'])."/".$_POST['newMask'] );
	
	if(!$error) {}	# ok - false returns if no error is found
	else {
		$errors[] = $error;
	} 
}

/* 
 * if strict mode is enabled check that is is still inside master subnet!
 */
if($settings['strictMode'] == 1) {
    if ( (!$overlap = verifySubnetNesting($subnetOld['masterSubnetId'], transform2long($subnetOld['subnet'])."/".$_POST['newMask'])) && $subnetOld['masterSubnetId']!=0) {
    	# get master details
    	$master = getSubnetDetailsById($subnetOld['masterSubnetId']);
		$master = Transform2long($master['subnet']) . "/" . $master['mask'];
    	$errors[] = "New subnet not in master subnet! ($master)";
    }
}


/* if no errors edit! */
if(sizeof($errors) > 0) {
	print "<div class='alert alert-error'><ul>";
	foreach($errors as $error) {
		print "<li>$error</li>";
	}
	print "</ul></div>";
}
# all good, edit subnet!
else {
	# failed
    if (!modifySubnetMask ($_POST['subnetId'], $_POST['newMask'])) 	{ print '<div class="alert alert-error">Error resizing subnet!</div>'; }
    # all good
    else 															{ print '<div class="alert alert-success">Subnet resized successfully!</div>'; } 
}

?>