<?php

/*
 * Print resize subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 3) 	{ die('<div class="alert alert-error">'._('You do not have permissions to resize subnet').'!</div>'); }


/* verify post */
CheckReferrer();

# get all site settings
$settings = getAllSettings();

# get old subnet details
$subnetOld = getSubnetDetailsById ($_POST['subnetId']);

# get number - this tells us to how many subnets we want to split
$num = $_POST['number'];

# get new mask
switch($num) {
	case "2":  $maskDiff = 1; break;
	case "4":  $maskDiff = 2; break;
	case "8":  $maskDiff = 3; break;
	case "16": $maskDiff = 4; break;
}
$mask = $subnetOld['mask'] + $maskDiff;

# set number of subnets
$subNum = pow(2,$maskDiff);

# get mas hosts per subnet
$type = IdentifyAddress( transform2long($subnetOld['subnet']) );	# type for max resize
if($type == "IPv4")	{ $type = "0"; }
else				{ $type = "1"; }

# set max hosts per new subnet
$maxHosts = MaxHosts( $mask, $type ); 
$maxHosts = gmp_strval(gmp_add(2, $maxHosts));

# create array of new subnets based on number of subnets (number)
for($m=0; $m<$subNum; $m++) {
	$newsubnets[$m] 		 = $subnetOld;
	$newsubnets[$m]['id']    = $m;
	$newsubnets[$m]['mask']  = $mask;

	# if group is selected rewrite the masterSubnetId!
	if($_POST['group'] == "yes") {
		$newsubnets[$m]['masterSubnetId'] = $subnetOld['id'];
	}
	
	# recalculate subnet
	if($m>0) {
		$newsubnets[$m]['subnet'] = gmp_strval(gmp_add($newsubnets[$m-1]['subnet'], $maxHosts));
	}
}

# recalculate old hosts to put it to right subnet
$ipaddresses   = getIpAddressesBySubnetIdSort ($subnetOld['id'], "ip_addr", "asc");		# get all IP addresses
$subSize = sizeof($newsubnets);		# how many times to check
$n = 0;								# ip address count

foreach($ipaddresses as $ip) {
	# check to which it belongs
	for($m=0; $m<$subSize; $m++) {
	
		# check if between this and next - strict
		if($_POST['strict'] == "yes") {
			# check if last
			if(($m+1) == $subSize) {
				if($ip['ip_addr'] > $newsubnets[$m]['subnet']) {
					$ipaddresses[$n]['subnetId'] = $newsubnets[$m]['id'];
				}
			}
			else if( ($ip['ip_addr'] > $newsubnets[$m]['subnet']) && ($ip['ip_addr'] < $newsubnets[$m+1]['subnet']) ) {
				$ipaddresses[$n]['subnetId'] = $newsubnets[$m]['id'];
			}
		}
		# unstrict - permit network and broadcast
		else {
			# check if last
			if(($m+1) == $subSize) {
				if($ip['ip_addr'] >= $newsubnets[$m]['subnet']) {
					$ipaddresses[$n]['subnetId'] = $newsubnets[$m]['id'];
				}
			}
			else if( ($ip['ip_addr'] >= $newsubnets[$m]['subnet']) && ($ip['ip_addr'] < $newsubnets[$m+1]['subnet']) ) {
				$ipaddresses[$n]['subnetId'] = $newsubnets[$m]['id'];
			}			
		}
	}
	
	# if subnetId is still the same save to error
	if($ipaddresses[$n]['subnetId'] == $subnetOld['id']) {
		$errors[] = transform2long($ip['ip_addr']);
	}	
	
	# next IP address
	$n++;
}

# die if errors
if(isset($errors) || sizeof($errors) > 0) {
	print "<div class='alert alert-error'>"._('Wrong IP addresses (subnet or broadcast)')."<ul>";
	foreach($errors as $error) {
		print "<li>$error</li>";
	}
	print "</ul></div>";
	die();
}


# create new subnets and change subnetId for recalculated hosts
$m = 0;
foreach($newsubnets as $subnet) {
	# set action and subnet - must be in long format
	$subnet['action'] = "add";
	$subnet['description'] = $subnet['description']."/$m";
	$subnet['subnet'] = transform2long($subnet['subnet'])."/".$subnet['mask'];
	
	# create subnet and save last id
	$lastId = modifySubnetDetails ($subnet, true);		# true returns last id    
	# save all to array
	$lastIdArray[] = $lastId;
    
	# replace ID in IP addresses
	foreach($ipaddresses as $ip) {
		if($ip['subnetId'] == $m) {
			$ip['ip_addr']  = transform2long($ip['ip_addr']);		
			if(!moveIPAddress ($ip['id'], $lastId)) { $errors[] = $ip['ip_addr']; }
		}
	}
	# next
	$m++;
}


# if all good remove old subnet, else remove created subnets
if(isset($errors) || sizeof($errors) > 0) {
	print "<div class='alert alert-error'>"._('Wrong IP addresses (subnet or broadcast)')."<ul>";
	foreach($errors as $error) {
		print "<li>$error</li>";
	}
	print "</ul></div>";
	die();	
}
else {
	# if group is selected dont delete the master!
	if($_POST['group'] == "yes") {
		print "<div class='alert alert-success'>"._('Subnet splitted ok')."!</div>";
	}
	else {
		# no errors, remove old subnet!
		if(!deleteSubnet ($subnetOld['id']))	{ print "<div class='alert alert-error'>"._('Failed to remove old subnet')."!</div>"; }
		else									{ print "<div class='alert alert-success'>"._('Subnet splitted ok')."!</div>"; }
	}
}

?>