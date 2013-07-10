<?php

/** 
 * Function to set subnet permissions
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(true);

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/* get posted permissions */
foreach($_POST as $key=>$val) {
	if(substr($key, 0,5) == "group") {
		if($val != "0") {
			$perm[substr($key,5)] = $val;
		}
	}
}
/* save to json */
$update['permissions'] = json_encode($perm);

/* id */
$update['subnetId'] = $_POST['subnetId'];

/* get ALL slave subnet id's, then remove all subnets and IP addresses */
global $removeSlaves;
getAllSlaves ($_POST['subnetId'], true);
$update['slaves'] = array_unique($removeSlaves);

/* do action! */
if (updateSubnetPermissions ($update)) {
	if(sizeof($update['slaves'])>1) { print '<div class="alert alert-success">'._('Subnet permissions set for subnet and underlying subnets').'!</div>'; }
	else 							{ print '<div class="alert alert-success">'._('Subnet permissions set').'!</div>'; }
}

?>