<?php

/**
 * Script to display available VLANs
 */

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all sections */
$sections = fetchSections ();

/* get custom fields */
$custom = getCustomSubnetFields();

# title
print "<h4>Available subnets</h4>";
print "<hr>";

# table
print "<table id='subnets' class='table table-striped table-condensed table-top'>";

# print vlans in each section
foreach ($sections as $section) {

	# check permission
	$permission = checkSectionPermission ($section['id']);
	if($permission != "0") {

		# set colspan
		$colSpan = 8 + (sizeof($custom));

		# section names
		print "<tbody>";
		print "	<tr class='subnets-title'>";
		print "		<th colspan='$colSpan'><h4>$section[name] section [$section[description]]</h4></th>";
		print "	</tr>";
		print "</tbody>";	

		# body
		print "<tbody>";

		# headers
		print "	<tr>";
		print "	<th>Subnet</th>";
		print "	<th>Description</th>";
		print "	<th>VLAN</th>";	
		print "	<th>Master Subnet</th>";
		print "	<th>Used</th>";
		print "	<th>free [%]</th>";
		print "	<th>Requests</th>";
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				print "	<th>$field[name]</th>";
			}
		}
		print "</tr>";
	
		# get all subnets in section
		$subnets = fetchSubnets ($section['id']);

		foreach ($subnets as $subnet) {
		
			# check permission
			$permission = checkSubnetPermission ($subnet['id']);
			if($permission != "0") {
		
				# check if it is master */
				if( ($subnet['masterSubnetId'] == 0) || (empty($subnet['masterSubnetId'])) ) { $masterSubnet = true; }
				else 																		 { $masterSubnet = false; }
	
				# VLAN details
				$subnet['VLAN'] = subnetGetVLANdetailsById($subnet['vlanId']);
				$subnet['VLAN'] = $subnet['VLAN']['number'];
	
				# reformat empty VLAN
				if(empty($subnet['VLAN']) || $subnet['VLAN'] == 0) { $subnet['VLAN'] = ""; }
	
				print "<tr>";	
				print "	<td><a href='subnets/$section[id]/$subnet[id]/'>".transform2long($subnet['subnet']) ."/$subnet[mask]</a></td>";
				print "	<td>$subnet[description]</td>";
				print "	<td>$subnet[VLAN]</td>";
    
				if($masterSubnet) { print '	<td>/</td>' . "\n"; }
				else {
					$master = getSubnetDetailsById ($subnet['masterSubnetId']);
					print "	<td><a href='subnets/$subnet[sectionId]/$master[id]/'>".transform2long($master['subnet']) .'/'. $master['mask'] .'</a></td>' . "\n";
				}
	
				# details - usage
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
	
				# allow requests
				if($subnet['allowRequests'] == 1) 			{ print '<td class="allowRequests requests" title="IP requests are enabled">enabled</td>'; }
				else 										{ print '<td class="allowRequests"></td>'; }

				# custom
				if(sizeof($custom) > 0) {
			   		foreach($custom as $field) {
			    		print "	<td>".$subnet[$field['name']]."</td>"; 
			    	}
			    }
			    print '</tr>' . "\n";
			}
		}

		print '</tbody>';
	
	}	# end permission check
}
?>

</table>