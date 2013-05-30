<?php

/**
 * Script to display available VLANs
 */

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all VLANs and subnet descriptions */
$vlans = getAllVlans (true);

/* get custom fields */
$custom = getCustomVLANFields();

# title
print "<h4>"._('Available VLANs:')."</h4>";
print "<hr><br>";

# table
print "<table id='vlans' class='table table-striped table-condensed table-top'>";

/* headers */
print '<tr">' . "\n";
print ' <th>'._('Number').'</th>' . "\n";
print ' <th>'._('Name').'</th>' . "\n";
print ' <th>'._('Description').'</th>' . "\n";
print ' <th>'._('Belonging subnets').'</th>' . "\n";
print ' <th>'._('Section').'</th>' . "\n";
print ' <th>'._('Used').'</th>' . "\n";
print ' <th>'._('free').' [%]</th>' . "\n";
if(sizeof($custom) > 0) {
	foreach($custom as $field) {
		print "	<th>$field[name]</th>";
	}
}
print '</tr>' . "\n";


$m = 0;
foreach ($vlans as $vlan) {
	
	# new change detection
	if($m>0) {
		if($vlans[$m]['number']==$vlans[$m-1]['number'] &&  $vlans[$m]['name']==$vlans[$m-1]['name'] && $vlans[$m]['description']==$vlans[$m-1]['description'])	{ $change = 'nochange'; }
		else																																					{ $change = 'change'; }
	}
	# first
	else 																																						{ $change = 'change';	 }

	/* get section details */
	$section = getSectionDetailsById($vlan['sectionId']);

	/* check if it is master */
	if(!isset($vlan['masterSubnetId'])) {
																				{ $masterSubnet = true;}
	}
	else {
		if( ($vlan['masterSubnetId'] == 0) || (empty($vlan['masterSubnetId'])) ) { $masterSubnet = true;}
		else 																	 { $masterSubnet = false;}	
	}


	# check permission
	$permission = checkSubnetPermission ($vlan['id']);
		
	if($permission != "0") {
		
		print "<tr class='$change'>";

		/* print first 3 only if change happened! */
		if($change == "change") {
			print ' <td>'. $vlan['number']         .'</td>' . "\n";
			print ' <td>'. $vlan['name']           .'</td>' . "\n";
			print ' <td>'. $vlan['description'] .'</td>' . "\n";			
		}
		else {
			print '<td></td>';
			print '<td></td>';
			print '<td></td>';	
		}

		if ($vlan['subnetId'] != null) {
			# subnet
			print " <td><a href='/subnets/$section[id]/$vlan[subnetId]/'>". transform2long($vlan['subnet']) ."/$vlan[mask]</a></td>";

			# section
			print " <td><a href='/subnets/$section[id]/'>$section[name]</a></td>";

			# details
			if( (!$masterSubnet) || (!subnetContainsSlaves($vlan['subnetId']))) {
        		$ipCount = countIpAddressesBySubnetId ($vlan['subnetId']);
        		$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $vlan['mask'], $vlan['subnet'] );

        		print ' <td class="used">'. reformatNumber($calculate['used']) .'/'. reformatNumber($calculate['maxhosts']) .'</td>'. "\n";
        		print ' <td class="free">'. reformatNumber($calculate['freehosts_percent']) .' %</td>';
        	}
        	else {
        		print '	<td class="used">---</td>'. "\n";
        		print '	<td class="free">---</td>'. "\n";
        	}
        }
        else {
        	print '<td>---</td>'. "\n";
        	print '<td class="free">---</td>'. "\n";
        	print '<td class="used">---</td>'. "\n";
        	print '<td class="free">---</td>';
        }
    
        # custom
        if(sizeof($custom) > 0) {
	   		foreach($custom as $field) {
	    		if($change == "change") { print "	<td>".$vlan[$field['name']]."</td>"; }
	    		else					{ print "	<td></td>";}
	    	}
	    }    
	    print '</tr>' . "\n";
	}

	# next VLAN
	$m++;
}


print '</table>';
?>
