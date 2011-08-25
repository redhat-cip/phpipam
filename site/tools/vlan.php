<?php

/**
 * Script to display available VLANs
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all VLANs and subnet descriptions */
$vlans = getAllVlans ();




/* get all sections */
$sections = fetchSections ();

/* print vlans in each section */
foreach ($sections as $section) {

	/*  print VLANs */
	print '<div class="normalTable vlans">';
	print '<table class="normalTable vlans">';

	/* section names */
	print '<tr class="th">' . "\n";
    print '	<th colspan="8"><h3>'. $section['name'] .' section [ '. $section['description'] .' ]</h3></th>' . "\n";
	print '</tr>';	


	/* headers */
	print '	<tr class="th dashed">' . "\n";
	print '	<td>VLAN</td>' . "\n";	
	print '	<td>Description</td>' . "\n";
	print '	<td>Subnet</td>' . "\n";
	print '	<td>Master Subnet</td>' . "\n";
	print '	<td>Used</td>' . "\n";
	print '	<td>free [%]</td>' . "\n";
	print '	<td>Requests</td>' . "\n";
	print '	<td class="lock" title="Admin lock"></td>' . "\n";
	print '</tr>' . "\n";

		
	/* get and print all vlans */
	$vlans = fetchSubnets ($section['id']);

	foreach ($vlans as $vlan) {
	
	/* check if it is master */
	if( ($vlan['masterSubnetId'] == 0) || (empty($vlan['masterSubnetId'])) ) {
		$masterSubnet = true;
	}
	else {
		$masterSubnet = false;
	}
	
	//identify slaves for CSS
	if(!$masterSubnet) {
		print '<tr class="vlanLink slaveSubnet"';
	}
	else {
		print '<tr class="vlanLink masterSubnet"';
	}
	
	//reformat empty VLAN
	if(empty($vlan['VLAN']) || $vlan['VLAN'] == 0) {
		$vlan['VLAN'] = "";
	}
	
	print ' sectionId="'. $section['id'] .'" subnetId="'. $vlan['id'] .'" link="'. $section['name'] .'|'. $vlan['id'] .'">' . "\n";
    print '	<td><dd>'. $vlan['VLAN'] 	   .'</dd></td>' . "\n";
    print '	<td><dd>'. $vlan['description'] .'</dd></td>' . "\n";
    print '	<td>'. transform2long($vlan['subnet']) .'/'. $vlan['mask'] .'</td>' . "\n";
    
   	if($masterSubnet) {
		print '	<td>/</td>' . "\n";
	}
	else {
		$master = getSubnetDetailsById ($vlan['masterSubnetId']);
      	print '	<td>'. transform2long($master['subnet']) .'/'. $master['mask'] .'</td>' . "\n";
	}
	
	//details
	if( (!$masterSubnet) || (!subnetContainsSlaves($vlan['id']))) {
	    $ipCount = countIpAddressesBySubnetId ($vlan['id']);
		$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $vlan['mask'], $vlan['subnet'] );

    	print ' <td class="used">'. reformatNumber($calculate['used']) .'/'. reformatNumber($calculate['maxhosts']) .'</td>'. "\n";
    	print '	<td class="free">'. reformatNumber($calculate['freehosts_percent']) .' %</td>';
	}
	else {
		print '<td></td>'. "\n";
		print '<td></td>'. "\n";
	}
	
	//allow requests
	if($vlan['allowRequests'] == 1) {
		print '<td class="allowRequests requests" title="IP requests are enabled">enabled</td>';
	}
	else {
		print '<td class="allowRequests"></td>';
	}
	
	//check if it is locked for writing
	if(isSubnetWriteProtected($vlan['id'])) {
		print '<td class="lock" title="Subnet is locked for writing!"></td>';	
	} else {
		print '<td class="nolock"></td>';
	}
	
    
	print '</tr>' . "\n";
	
	}


	print '</table>';
	print '</div>';
}
?>