<?php

/**
 * Script to display switches
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all unique switches */
$switches = getAllUniqueSwitches();

/* add unspecified */
$switches[] = $switch['hostname'];


/* print */
foreach($switches as $switch) {

	/* Get all I addresses belonging to switch */
	$ipaddresses = getIPaddressesBySwitchName ( $switch['hostname'] );
	
	/* Get switch details */
	$switchDetails = getSwitchDetailsByHostname($switch['hostname']);
	
	if(sizeof($switchDetails['hostname']) == 0) {
		$switchDetails['hostname'] = 'Not specified';
	}
	else {
		$switchDetails['ip_addr'] = '(' . $switchDetails['ip_addr'] .')';
	}
	
	/* reformat if empty */
	if(empty($switch['hostname'])) {
		$switch['hostname'] = "Unspecified";
	}
	
	/* count size */
	$size = sizeof($ipaddresses);
	
	/* print table */
	print '<div class="normalTable">'. "\n";
	print '<table class="normalTable switches">'. "\n";
	
	/* Switch name */
	print '<tr class="th">'. "\n";
	print '	<th colspan="8"><h3>'. $switchDetails['hostname'] .' '. $switchDetails['ip_addr'] .'</h3></th>'. "\n";
	print '</tr>'. "\n";
	
	/* title */
	print '<tr class="th dashed">'. "\n";
	print '	<td>Port</td>'. "\n";
	print '	<td>IP address</td>'. "\n";
	print '	<td>Subnet</td>'. "\n";
	print '	<td colspan="2">Description</td>'. "\n";
	print '	<td>Hostname</td>'. "\n";
	print '	<td>Owner</td>'. "\n";
	print '</tr>'. "\n";
	
	foreach ($ipaddresses as $ip) {
	
		//get subnet details
		$subnet = getSubnetDetails ($ip['subnetId']);
		//get section details
		$section = getSectionDetailsById ($subnet['sectionId']);
	
		print '<tr id="'. $ip['id'] .'" subnetId="'. $ip['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n";
		print '	<td class="port">'. $ip['port'] .'</td>'. "\n";
		print '	<td class="ip">'. transform2long($ip['ip_addr']) .'/'. $subnet['mask'] .'</td>'. "\n";
		print '	<td class="subnet">'. $subnet['description'] .'</td>'. "\n";
		print '	<td class="description">'. $ip['description'] .'</td>'. "\n";

		// print info button for hover
		print '<td class="note">' . "\n";
		if(!empty($ip['note'])) {
			$ip['note'] = str_replace("\n", "<br>",$ip['note']);
			print '	<img class="info" src="css/images/infoIP.png" title="'. $ip['note']. '">' . "\n";
		}
		print '</td>'. "\n";
		
		print '	<td class="dns">'. $ip['dns_name'] .'</td>'. "\n";
		print '	<td class="owner">'. $ip['owner'] .'</td>'. "\n";
		print '</tr>'. "\n";
	
	}
	
	print '</table>'. "\n";
	print '</div>'. "\n";
}
?>