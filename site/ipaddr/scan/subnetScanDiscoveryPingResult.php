<?php

/*
 * Discover new hosts with ping
 *******************************/

/* required functions */
require_once('../../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(true);

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 		{ die('<div class="alert alert-error">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

# verify post
CheckReferrer();

# ok, lets get results form post array!
foreach($_REQUEST as $key=>$line) {
	// IP address
	if(substr($key, 0,2)=="ip") 			{ $res[substr($key, 2)]['ip_addr']  	= $line; }
	// description
	if(substr($key, 0,11)=="description") 	{ $res[substr($key, 11)]['description'] = $line; }
	// dns name 
	if(substr($key, 0,8)=="dns_name") 		{ $res[substr($key, 8)]['dns_name']  	= $line; }
}

# insert entries
if(sizeof($res)>0) {
	if(insertScanResults($res, $_REQUEST['subnetId'])) {
		print "<div class='alert alert-success'>"._("Scan results added to database")."!</div>";
	}
}
# error
else {
	print "<div class='alert alert-error'>"._("Error")."</div>";
}
?>