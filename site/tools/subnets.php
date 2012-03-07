<?php

/**
 * Script to display available VLANs
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();



/* get all sections */
$sections = fetchSections ();


# title
print '<h3>Available subnets</h3>'. "\n";

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
	print '	<td>Subnet</td>' . "\n";
	print '	<td>Description</td>' . "\n";
	print '	<td>VLAN</td>' . "\n";	
	print '	<td>Master Subnet</td>' . "\n";
	print '	<td>Used</td>' . "\n";
	print '	<td>free [%]</td>' . "\n";
	print '	<td>Requests</td>' . "\n";
	print '	<td class="lock" title="Admin lock"></td>' . "\n";
	print '</tr>' . "\n";

		
	/* get and print all vlans */
	$subnets = fetchSubnets ($section['id']);

	foreach ($subnets as $subnet) {
	
		/* check if it is master */
		if( ($subnet['masterSubnetId'] == 0) || (empty($subnet['masterSubnetId'])) ) {
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
	
		//VLANs
		$subnet['VLAN'] = subnetGetVLANdetailsById($subnet['vlanId']);
		$subnet['VLAN'] = $subnet['VLAN']['number'];
	
		//reformat empty VLAN
		if(empty($subnet['VLAN']) || $subnet['VLAN'] == 0) {
			$subnet['VLAN'] = "";
		}
	
		print ' sectionId="'. $section['id'] .'" subnetId="'. $subnet['id'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">' . "\n";

	    print '	<td>'. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</td>' . "\n";
	    print '	<td><dd>'. $subnet['description'] .'</dd></td>' . "\n";
	    print '	<td><dd>'. $subnet['VLAN'] 	   .'</dd></td>' . "\n";
    
   		if($masterSubnet) {
			print '	<td>/</td>' . "\n";
		}
		else {
			$master = getSubnetDetailsById ($subnet['masterSubnetId']);
    	  	print '	<td>'. transform2long($master['subnet']) .'/'. $master['mask'] .'</td>' . "\n";
		}
	
		//details
		if( (!$masterSubnet) || (!subnetContainsSlaves($subnet['id']))) {
		    $ipCount = countIpAddressesBySubnetId ($subnet['id']);
			$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $subnet['mask'], $subnet['subnet'] );

    		print ' <td class="used">'. reformatNumber($calculate['used']) .'/'. reformatNumber($calculate['maxhosts']) .'</td>'. "\n";
    		print '	<td class="free">'. reformatNumber($calculate['freehosts_percent']) .' %</td>';
		}
		else {
			print '<td></td>'. "\n";
			print '<td></td>'. "\n";
		}
	
		//allow requests
		if($subnet['allowRequests'] == 1) {
			print '<td class="allowRequests requests" title="IP requests are enabled">enabled</td>';
		}
		else {
			print '<td class="allowRequests"></td>';
		}
	
		//check if it is locked for writing
		if(isSubnetWriteProtected($subnet['id'])) {
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