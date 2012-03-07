<?php

/**
 * Script to display devices by hostname
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all IP addresses if hostname is not set! */
if(!isset($_POST['hostname'])) {
	$ipAddresses = fetchAllIPAddresses (true);
}
else {
	$_POST['hostname'] = str_replace("*", "", $_POST['hostname']);	//remove possible *
	$ipAddresses = fetchAllIPAddressesByName ($_POST['hostname']);
}


/* get all selected fields for filtering */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);
# set size!
$rowSize = sizeof($setFields) + 2;


/* get all custom fields */
$myFields = getCustomIPaddrFields();
$myFieldsSize = sizeof($myFields);
$rowSize = $rowSize + $myFieldsSize;


/* print */
print '<div class="normalTable">'. "\n";
print '<table class="normalTable hosts">'. "\n";
	
/* title */
print '<tr class="th">'. "\n";

# hostname - mandatory
	print '	<th>Hostname</th>'. "\n";
# IP address - mandatory
	print '	<th>IP address</th>'. "\n";
# mac
if(in_array('mac', $setFields)) {
	print '	<th></th>'. "\n";
}
# switch 
if(in_array('switch', $setFields)) {
	print '	<th>Switch</th>'. "\n";
}
# port
if(in_array('port', $setFields)) {
	print '	<th>Port</th>'. "\n";
}
# subnet - mandatory
	print '	<th>Subnet</th>'. "\n";
# description and note
if(in_array('note', $setFields)) {
	print '	<th colspan="2">Description</th>'. "\n";	
}
# description only
else {
	print '	<th>Description</th>'. "\n";	
}
# owner
if(in_array('owner', $setFields)) {
	print '	<th>Owner</th>'. "\n";
}


# custom fields
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		print '<th>'. $myField['name'] .'</th>'. "\n";
	}
}

print '</tr>'. "\n";

/* if nothing is found print it */
if(sizeof($ipAddresses) == 0) {
	print '<tr class="th"><td colspan="'. $rowSize .'">No results found for string "'. $_POST['hostname'] .'"</td></tr>';
}

$m = 0;
foreach($ipAddresses as $ip) {
	
	//get subnet details
	$subnet = getSubnetDetails ($ip['subnetId']);
	
	//get section details
	$section = getSectionDetailsById ($subnet['sectionId']);
	
	//check if hostname is the same as previous one
	$n = $m -1;
	if($ipAddresses[$m]['dns_name'] != $ipAddresses[$n]['dns_name']) {
		$class = "new";
	}
	else {
		$class = "";
	}
	
	print '<tr class="'. $class .'" id="'. $ip['id'] .'" subnetId="'. $ip['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n";
	
	/* don't show hostname if it is the same as first */
	if($ipAddresses[$m]['dns_name'] == $ipAddresses[$m-1]['dns_name']) {
		print '	<td class="dns"></td>'. "\n";
	}
	else { 
		print '	<td class="dns">'. $ip['dns_name'] .'</td>'. "\n";
	}
	
	# IP address
	print '	<td class="ip">'. transform2long($ip['ip_addr']) .'/'. $subnet['mask'] .'</td>'. "\n";
	
	if(in_array('mac', $setFields)) {
		print '	<td class="mac">'. "\n";
		if(isset($ip['mac'])) {
			print '<img class="info" src="css/images/lan.png" title="MAC: '. $ip['mac'] .'">'. "\n";
		}
		print '</td>'. "\n";
	}
	
	# switch
	if(in_array('switch', $setFields)) {
		print '	<td class="switch">'. $ip['switch'] .'</td>'. "\n";	
	}
	
	# port
	if(in_array('port', $setFields)) {
		print '	<td class="port">'. $ip['port'] .'</td>'. "\n";
	}
	
	# subnet
	print '	<td class="subnet">'. $subnet['description'] .'</td>'. "\n";	
	
	# description
	print '	<td class="description">'. $ip['description'] .'</td>'. "\n";
	
	# note
	if(in_array('note', $setFields)) {
		print '<td class="note">' . "\n";
		if(!empty($ip['note'])) {
			$ip['note'] = str_replace("\n", "<br>",$ip['note']);
			print '	<img class="info" src="css/images/note.png" title="'. $ip['note']. '">' . "\n";
		}
		print '</td>'. "\n";
	}
	
	# owner
	if(in_array('owner', $setFields)) {
		print '	<td class="owner">'. $ip['owner'] .'</td>'. "\n";
	}
	
	# custom
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) {
			print '<td class="customField">'. $ip[$myField['name']] .'</td>'. "\n";
		}
	}
	
	print '</tr>'. "\n";

	$m++;
}

print '</table>'. "\n";
print '</div>'. "\n";
	
?>