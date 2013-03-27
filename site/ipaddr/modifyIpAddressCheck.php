<?php

/**
 * Script to check edited / deleted / new IP addresses
 * If all is ok write to database
 *************************************************/
 
/* include required scripts */
require_once('../../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated ();

/* verify that user has write access */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm != "2") 	{ die('<div class="alert alert-error">Cannot edit IP address!</div>'); }

/* get posted values */
if ( !empty($_REQUEST['ip_addr']) ) 	{ $ip['ip_addr'] = $_REQUEST['ip_addr']; }
else 									{ $ip['ip_addr'] = "";}
//description
if ( !empty($_REQUEST['description']) ) { $ip['description'] = htmlentities($_REQUEST['description'], ENT_COMPAT | ENT_HTML401, "UTF-8"); } //prevent XSS
else 									{ $ip['description'] = ""; }
//hostname
if ( !empty($_REQUEST['dns_name']) ) 	{ $ip['dns_name'] = htmlentities($_REQUEST['dns_name'], ENT_COMPAT | ENT_HTML401, "UTF-8"); }	//prevent XSS
else 									{ $ip['dns_name'] = ""; }
//mac
if ( !empty($_REQUEST['mac']) ) 		{ $ip['mac'] = htmlentities($_REQUEST['mac'], ENT_COMPAT | ENT_HTML401, "UTF-8"); }			//prevent XSS
else 									{ $ip['mac'] = ""; }
//owner
if ( !empty($_REQUEST['owner']) ) 		{ $ip['owner'] = htmlentities($_REQUEST['owner'], ENT_COMPAT | ENT_HTML401, "UTF-8"); }		//prevent XSS
else 									{ $ip['owner'] = ""; }
//switch
if ( !empty($_REQUEST['switch']) ) 		{ $ip['switch'] = htmlentities($_REQUEST['switch'], ENT_COMPAT | ENT_HTML401, "UTF-8"); }	//prevent XSS
else 									{ $ip['switch'] = ""; }
//port
if ( !empty($_REQUEST['port']) ) 		{ $ip['port'] = htmlentities($_REQUEST['port'], ENT_COMPAT | ENT_HTML401, "UTF-8"); }		//prevent XSS
else 									{ $ip['port'] = ""; }
//note
if ( !empty($_REQUEST['note']) ) 		{ $ip['note'] = htmlentities($_REQUEST['note'], ENT_COMPAT | ENT_HTML401, "UTF-8"); }		//prevent XSS
else 									{ $ip['note'] = ""; }
//custom
$myFields = getCustomIPaddrFields();
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		# replace possible ___ back to spaces!
		$myField['nameTest']      = str_replace(" ", "___", $myField['name']);
		
		if(isset($_POST[$myField['nameTest']])) { $ip[$myField['name']] = $_POST[$myField['nameTest']];}
	}
}

// those values must be present	
$ip['action']  = $_REQUEST['action'];
$ip['subnet']  = $_REQUEST['subnet'];
$ip['subnetId']= $_REQUEST['subnetId'];
$ip['section'] = $_REQUEST['section'];
$ip['id']      = $_REQUEST['id'];
$ip['state']   = $_REQUEST['state'];


//delete form visual
if(isset($_REQUEST['action-visual'])) {
	/* replace action to delete if action-visual == delete */
	if($_REQUEST['action-visual'] == "delete") { $ip['action'] = "delete"; }	
}

//detect proper hostname
if(strlen($_POST['dns_name']>0) && !validateHostname($_REQUEST['dns_name'])) {
	die('<div class="alert alert-error">Invalid hostname!</div>');
}


//no strict checks - for range networks and /31, /32
if(isset($_POST['nostrict'])) {
	if($_POST['nostrict'] == "yes") { $nostrict = true; }
	else							{ $nostrict = false; }
}
else 								{ $nostrict = false; }


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
	$verify1 = VerifyIpAddress( $ip['start'], $ip['subnet'], $nostrict );
	$verify2 = VerifyIpAddress( $ip['stop'] , $ip['subnet'], $nostrict );
	
	/* die if wrong IP or not in correct subnet */
	if($verify1) { die('<div class="alert alert-error">Error: '. $verify1 .' ('. $ip['start'] .')</div>'); }
	if($verify2) { die('<div class="alert alert-error">Error: '. $verify2 .' ('. $ip['stop']  .')</div>'); }
	
	/* set update for update */
	$ip['type'] = "series";
	
	/* go from start to stop and insert / update / delete IPs */
	$start = transform2decimal($ip['start']);
	$stop  = transform2decimal($ip['stop']);

	/* we can add only 200 IP's at once! */
	$size = gmp_strval(gmp_sub($stop,$start));
	if($size > 255) { die('<div class="alert alert-error">Only 255 IP addresses at once!</div>'); }
	
	/* set limits */
	$m = gmp_strval($start);
	$n = gmp_strval(gmp_add($stop,1));
	
	/* for each IP */
	while (gmp_cmp($m, $n) != 0) {	
	
		//reset IP address field
		$ip['ip_addr'] = transform2long($m);
	
		//modify action - if delete ok, dynamically reset add / edit -> if IP already exists set edit
		if($ip['action'] != "delete") {
		   	if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) 	{ $ip['action'] = "edit"; }
		    else 													{ $ip['action'] = "add"; }
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
		print '<div class="alert alert-error">';
		$log = prepareLogFromArray ($errors);
		print $log;
		print '</div>';
		updateLogTable ('Error '. $ip['action'] .' range '. $ip['start'] .' - '. $ip['stop'], $log, 2);
	}
	else {
		print '<div class="alert alert-success">Range '. $ip['start'] .' - '. $ip['stop'] .' updated successfully!</div>';
		updateLogTable ('Range '. $ip['start'] .' - '. $ip['stop'] .' '. $ip['action'] .' successfull!', 'Range '. $ip['start'] .' - '. $ip['stop'] .' '. $ip['action'] .' successfull!', 0);
	}	
}
/* no range, single IP address */
else {

	/* unique */
	if(isset($_POST['unique'])) {
		if($_POST['unique'] == "1" && strlen($_POST['dns_name'])>0) {
			# check if unique
			if(!isHostUnique($_POST['dns_name'])) {
				die('<div class="alert alert-error">Hostname is not unique!</div>');
			}
		}
	}

	/* verify ip address */
	if($ip['action'] == "move")	{ 
		$subnet = getSubnetDetailsById($_REQUEST['newSubnet']);
		$subnet = transform2long($subnet['subnet'])."/".$subnet['mask'];
		$verify = VerifyIpAddress( $ip['ip_addr'], $subnet, $nostrict ); 
		
		$ip['newSubnet'] = $_REQUEST['newSubnet'];
	}
	else { 
		$verify = VerifyIpAddress( $ip['ip_addr'], $ip['subnet'], $nostrict ); 
	}

	/* if errors are present print them, else execute query! */
	if($verify) 				{ die('<div class="alert alert-error">Error: '. $verify .' ('. $ip['ip_addr'] .')</div>'); }
	else {
		/* set update for update */
		$ip['type'] = "single";

		/* check for duplicate entry! needed only in case new IP address is added, otherwise the code is locked! */
	    if ($ip['action'] == "add") {  
	        if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) {
	            die ('<div class="alert alert-error">IP address '. $ip['ip_addr'] .' already existing in database!</div>');
	        }
	    }  

		/* check for duplicate entry on edit! */
	    if ($ip['action'] == "edit") {  
	    	# if IP is the same than it can already exist!
	    	if($ip['ip_addr'] != $_REQUEST['ip_addr_old']) {
	        	if (checkDuplicate ($ip['ip_addr'], $ip['subnetId'])) {
	        	    die ('<div class="alert alert-error">IP address '. $ip['ip_addr'] .' already existing in database!</div>');
	        	}	
	    	}
	    } 
	    /* move checks */
	    if($ip['action'] == "move") {
		    # check if not already used in new subnet
	        if (checkDuplicate ($ip['ip_addr'], $ip['newSubnet'])) {
	            die ('<div class="alert alert-error">Duplicate IP address '. $ip['ip_addr'] .' already existing in selected network!</div>');
	        }		   
	    }

	    /* execute insert / update / delete query */    
	    if (!modifyIpAddress($ip)) {
	        print '<div class="alert alert-error">Error inserting IP address!</div>';
	        updateLogTable ('Error '. $ip['action'] .' IP address '. $ip['ip_addr'], 'Error '. $ip['action'] .' IP address '. $ip['ip_addr'] .'<br>SubnetId: '. $ip['subnetId'], 2);
	    }
	    else {
	        print '<div class="alert alert-success">'. ucwords($ip['action']) .' successful!</div>';
	        updateLogTable ($ip['action'] .' of IP address '. $ip['ip_addr'] .' succesfull!', $ip['action'] .' of IP address '. $ip['ip_addr'] .' succesfull!<br>SubnetId: '. $ip['subnetId'], 0);
	    }
	}
}
?>