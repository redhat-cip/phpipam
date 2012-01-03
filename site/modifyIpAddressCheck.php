<?php

/**
 * Script to check edited / deleted / new IP addresses
 * If all is ok write to database
 *************************************************/

/* include required scripts */
require_once('../functions/functions.php');


/* check referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated ();

/* viewers cannot edit IP address */
if(isUserViewer()) {
	die('<div class="error">Cannot edit IP address!</div>');
}


/* get posted values */
if ( !empty($_REQUEST['ip_addr']) ) {
	$ip['ip_addr'] = $_REQUEST['ip_addr'];
    }
else {
	$ip['ip_addr'] = "";
}

if ( !empty($_REQUEST['description']) ) {
    $ip['description'] = htmlentities($_REQUEST['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
}
else {
	$ip['description'] = "";
}

if ( !empty($_REQUEST['dns_name']) ) {
	$ip['dns_name'] = htmlentities($_REQUEST['dns_name'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
}
else {
	$ip['dns_name'] = "";
}

//mac
if ( !empty($_REQUEST['mac']) ) {
	$ip['mac'] = htmlentities($_REQUEST['mac'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
}
else {
	$ip['mac'] = "";
}

//owner
if ( !empty($_REQUEST['owner']) ) {
	$ip['owner'] = htmlentities($_REQUEST['owner'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
}
else {
	$ip['owner'] = "";
}

//switch
if ( !empty($_REQUEST['switch']) ) {
	$ip['switch'] = htmlentities($_REQUEST['switch'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
}
else {
	$ip['switch'] = "";
}

//port
if ( !empty($_REQUEST['port']) ) {
	$ip['port'] = htmlentities($_REQUEST['port'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
}
else {
	$ip['port'] = "";
}

//note
if ( !empty($_REQUEST['note']) ) {
	$ip['note'] = htmlentities($_REQUEST['note'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
}
else {
	$ip['note'] = "";
}

// those values must be present	
$ip['action']  = $_REQUEST['action'];
$ip['subnet']  = $_REQUEST['subnet'];
$ip['subnetId']= $_REQUEST['subnetId'];
$ip['section'] = $_REQUEST['section'];
$ip['id']      = $_REQUEST['id'];
$ip['state']   = $_REQUEST['state'];


/* check if range is being added? */
if (strlen(strstr($ip['ip_addr'],"-")) > 0) {

	//range
	
	/* remove possible spaces */
	$ip['ip_addr'] = str_replace(" ", "", $ip['ip_addr']);
	
	/* get start and stop */
	$range		 = explode("-", $ip['ip_addr']);
	$ip['start'] = $range[0];
	$ip['stop']  = $range[1];
	
	/* verify both IP addresses */
	$verify1 = VerifyIpAddress( $ip['start'], $ip['subnet'] );
	$verify2 = VerifyIpAddress( $ip['stop'] , $ip['subnet'] );
	
	/* die if wrong IP or not in correct subnet */
	if($verify1) {
		die('<div class="error">Error: '. $verify1 .' ('. $ip['start'] .')</div>');
	}
	if($verify2) {
		die('<div class="error">Error: '. $verify2 .' ('. $ip['stop']  .')</div>');
	}
	
	/* set update for update */
	$ip['type'] = "series";
	
	/* go from start to stop and insert / update / delete IPs */
	$start = transform2decimal($ip['start']);
	$stop  = transform2decimal($ip['stop']);

	/* we can add only 200 IP's at once! */
	$size = gmp_strval(gmp_sub($stop,$start));
	if($size > 200) {
		die('<div class="error">Only 200 IP addresses at once!</div>');
	}
	
	/* set limits */
	$m = gmp_strval($start);
	$n = gmp_strval(gmp_add($stop,1));
	
	/* for each IP */
	while (gmp_cmp($m, $n) != 0) {	
	
		//reset IP address field
		$ip['ip_addr'] = transform2long($m);
	
		//modify action - if delete ok, dynamically reset add / edit -> if IP already exists set edit
		if($ip['action'] != "Delete") {
		   	if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) {
			 	$ip['action'] = "Edit";
		   	}
		    else {
		    	$ip['action'] = "Add";
		    }
		}
	
		//if it fails set error log
		if (!modifyIpAddress($ip)) {
	        $errors[] = 'Cannot '. $ip['action']. ' IP address '. transform2long($m);
	    }			
			
		/* next IP */
		$m = gmp_strval(gmp_add($m,1));
	}
	
	/* print errors if they exist */
	if(isset($errors)) {
		print '<div class="error">';
		$log = prepareLogFromArray ($errors);
			
		print '</div>';
		updateLogTable ('Error '. $ip['action'] .' range '. $ip['start'] .' - '. $ip['stop'], $log, 2);
	}
	else {
		print '<div class="success">Range '. $ip['start'] .' - '. $ip['stop'] .' updated successfully!</div>';
		updateLogTable ('Range '. $ip['start'] .' - '. $ip['stop'] .' '. $ip['action'] .' successfull!', 'Range '. $ip['start'] .' - '. $ip['stop'] .' '. $ip['action'] .' successfull!', 1);
	}	
}
/* no range, single IP address */
else {

	/* verify ip address */
	$verify = VerifyIpAddress( $ip['ip_addr'], $ip['subnet'] );
	
	/* verify switches! */
	$switchVerify = true;
	if( (!empty($ip['switch'])) && ($ip['action'] != "Delete") ) {
		$switchVerify = verifySwitchByName($ip['switch']);
	}

	/* if errors are present print them, else execute query! */
	if($verify) {
		die('<div class="error">Error: '. $verify .' ('. $ip['ip_addr'] .')</div>');
	}
	else if (!$switchVerify) {
		die('<div class="error">Error: Switch does not exist! (Use blank or existing)</div>');
	}
	else {
	
		/* set update for update */
		$ip['type'] = "single";

		/* check for duplicate entry! needed only in case new IP address is added, otherwise the code is locked! */
	    if ( $ip['action'] == "Add" ) {  
	        if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) {
	            die ('<div class="error">IP address already existing in database!</div>');
	        }
	    }  
	    /* execute insert / update / delete query */    
	    if (!modifyIpAddress($ip)) {
	        print '<div class="error">Error inserting IP address!</div>';
	        updateLogTable ('Error '. $ip['action'] .' IP address '. $ip['ip_addr'], 'Error '. $ip['action'] .' IP address '. $ip['ip_addr'] .'<br>SubnetId: '. $ip['subnetId'], 2);
	    }
	    else {
	        print '<div class="success">'. $ip['action'] .' successful</div>';
	        updateLogTable ($ip['action'] .' of IP address '. $ip['ip_addr'] .' succesfull!', $ip['action'] .' of IP address '. $ip['ip_addr'] .' succesfull!<br>SubnetId: '. $ip['subnetId'], 1);
	    }
	}
}
?>