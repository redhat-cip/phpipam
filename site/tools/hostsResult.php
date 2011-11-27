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

/* print */
print '<div class="normalTable">'. "\n";
print '<table class="normalTable hosts">'. "\n";
	
/* title */
print '<tr class="th">'. "\n";
print '	<th>Hostname</th>'. "\n";
print '	<th>IP address</th>'. "\n";
print '	<th></th>'. "\n";			//mac
print '	<th>Switch</th>'. "\n";
print '	<th>Port</th>'. "\n";
print '	<th>Subnet</th>'. "\n";
print '	<th colspan="2">Description</th>'. "\n";
print '	<th>Owner</th>'. "\n";
print '</tr>'. "\n";

/* if nothing is found print it */
if(sizeof($ipAddresses) == 0) {
	print '<tr class="th"><td colspan="8">No results found for string "'. $_POST['hostname'] .'"</td></tr>';
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
	
	print '	<td class="ip">'. transform2long($ip['ip_addr']) .'/'. $subnet['mask'] .'</td>'. "\n";
	
	print '	<td class="mac">'. "\n";
	if(isset($ip['mac'])) {
	print '<img class="info" src="css/images/lan.png" title="MAC: '. $ip['mac'] .'">'. "\n";
	}
	print '</td>'. "\n";
	
	print '	<td class="switch">'. $ip['switch'] .'</td>'. "\n";
	print '	<td class="port">'. $ip['port'] .'</td>'. "\n";
	print '	<td class="subnet">'. $subnet['description'] .'</td>'. "\n";	
	print '	<td class="description">'. $ip['description'] .'</td>'. "\n";
	
	// print info button for hover
	print '<td class="note">' . "\n";
	if(!empty($ip['note'])) {
		$ip['note'] = str_replace("\n", "<br>",$ip['note']);
		print '	<img class="info" src="css/images/note.png" title="'. $ip['note']. '">' . "\n";
	}
	print '</td>'. "\n";
	
	print '	<td class="owner">'. $ip['owner'] .'</td>'. "\n";
	print '</tr>'. "\n";

	$m++;
}

print '</table>'. "\n";
print '</div>'. "\n";
	
?>