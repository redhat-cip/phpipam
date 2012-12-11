<?php

/**
 * Script to display available VLANs
 */

/* verify that user is authenticated! */
isUserAuthenticated ();

/* die if viewer */
if(isUserViewer()) { die('<div class="alert alert-error">You do not have permissions to access this page!</div>');}

/* get all VLANs and subnet descriptions */
$vlans = getAllVlans (true);

/* get custom fields */
$custom = getCustomVLANFields();

# title
print "<h4>Available VLANs:</h4>";
print "<hr><br>";

# table
print "<table id='vlans' class='table table-striped table-condensed table-hover table-top'>";

/* headers */
print '<tr">' . "\n";
print ' <th>Number</th>' . "\n";
print ' <th>VLAN Name</th>' . "\n";
print ' <th>VLAN Description</th>' . "\n";
print ' <th>Belonging subnets</th>' . "\n";
print ' <th>Section</th>' . "\n";
print ' <th>Used</th>' . "\n";
print ' <th>free [%]</th>' . "\n";
if(sizeof($custom) > 0) {
	foreach($custom as $field) {
		print "	<th>$field[name]</th>";
	}
}
print '</tr>' . "\n";

# change detection
$vlanOld = 0;

foreach ($vlans as $vlan) {

	# detect change
	$vlanNew = $vlan['number'];

	if($vlanNew == $vlanOld) { $change = 'nochange'; }
	else 					 { $change = 'change'; $vlanOld = $vlanNew; }

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

        //details
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


print '</table>';
?>
