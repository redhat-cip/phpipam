<?php

/**
 * Script to print subnets
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* print all sections with delete / edit button */
print '<h3>Subnet management</h3>' . "\n";

print 'Select section to edit belonging Subnets:' . "\n";

/* first we need to fetch all sections */
$sections = fetchSections ();


/**
 * Foreach section fetch subnets and print it!
 */
foreach ($sections as $section) 
{
print '<div class="manageSubnets normalTable">' . "\n";
print '<table class="manageSubnets normalTable">' . "\n";

/* first headers */
print '<thead class="'. $section['id'] .'">' . "\n";
print '	<tr class="th"><td colspan="7"><h3>'. $section['name'] .'</h3></td>' . "\n";
print '</thead>' . "\n";
       
print '<tbody class="'. $section['id'] .'">' . "\n";
print '	<tr class="th">' . "\n";
print '		<th>Name</th>' . "\n";
print '		<th>Description</th>' . "\n";
print '		<th>Master Subnet</th>' . "\n";
print '		<th>VLAN</th>' . "\n";
print '		<th colspan=2>Requests</th>' . "\n";
print '		<th colspan=2></th>' . "\n";
print '	</tr>' . "\n";

/* print all subnets in section if they exist */
$subnets = fetchMasterSubnets ( $section['id'] );
    
if (!empty($subnets)) {

	# master subnets
	foreach ($subnets as $subnet) {

		print '	<tr class="masterSubnet">' . "\n";
        print '		<td>'. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</td>' . "\n";
       	print '		<td>'. $subnet['description'] .'</td>' . "\n";
		print '		<td>/</td>' . "\n";
		
		# VLAN
		if(empty($subnet['VLAN']) || $subnet['VLAN'] == 0) { $subnet['VLAN'] = ""; }
		print '		<td>'. $subnet['VLAN']        .'</td>' . "\n";
		
		# requests
		if($subnet['allowRequests'] == 1) 	{ print '<td class="allowRequests">enabled</td>'; }
		else 								{ print '<td class="allowRequests"></td>'; }
		
		# check if it is locked for writing
		if(isSubnetWriteProtected($subnet['id'])) 	{ print '<td class="edit lock" title="Subnet is locked for writing!"></td>';	} 
		else 										{ print '<td class="edit nolock"></td>';}
		

        print '		<td class="edit"><img src="css/images/edit.png"   class="Edit"   subnetId="'. $subnet['id'] .'" sectionId="'. $section['id'] .'" title="Edit subnet"></td>' . "\n";
        print '		<td class="edit"><img src="css/images/deleteIP.png" class="Delete" subnetId="'. $subnet['id'] .'" sectionId="'. $section['id'] .'" title="Delete subnet"></td>' . "\n";
        print '	</tr>' . "\n";
		
		
		# slaves
		$slaves = getAllSlaveSubnetsBySubnetId ($subnet['id']);
		
		if(sizeof($slaves) != 0) 
		{
			foreach($slaves as $slave) 
			{
		
				$master = getSubnetDetailsById ($slave['masterSubnetId']);
			
				print '	<tr class="slaveSubnet">' . "\n";
		        print '		<td class="subnet">'. transform2long($slave['subnet']) .'/'. $slave['mask'] .'</td>' . "\n";
		       	print '		<td>'. $slave['description'] .'</td>' . "\n";
				print '		<td class="masterSubnet">'. transform2long($master['subnet']) .'/'. $master['mask'] .'</td>' . "\n";
		
				# VLAN
				if(empty($slave['VLAN']) || $slave['VLAN'] == 0) { $slave['VLAN'] = ""; }
				print '		<td>'. $slave['VLAN']        .'</td>' . "\n";
		
				# requests
				if($slave['allowRequests'] == 1) 	{ print '<td class="allowRequests">enabled</td>'; }
				else 								{ print '<td class="allowRequests"></td>'; }
		
				# check if it is locked for writing
				if(isSubnetWriteProtected($slave['id'])) 	{ print '<td class="edit lock" title="Subnet is locked for writing!"></td>';	} 
				else 										{ print '<td class="edit nolock"></td>';}

    		    print '		<td class="edit"><img src="css/images/edit.png"   class="Edit"   subnetId="'. $slave['id'] .'" sectionId="'. $section['id'] .'" title="Edit subnet"></td>' . "\n";
    		    print '		<td class="edit"><img src="css/images/deleteIP.png" class="Delete" subnetId="'. $slave['id'] .'" sectionId="'. $section['id'] .'" title="Delete subnet"></td>' . "\n";
        		print '	</tr>' . "\n";
        	
        	
        		/* Check for L2 Slaves! */
        		$subSlaves = getAllSlaveSubnetsBySubnetId ($slave['id']);
        		
        		if(sizeof($subSlaves) != 0) 
        		{
        			foreach($subSlaves as $subSlave) 
        			{
 
						$master = getSubnetDetailsById ($subSlave['masterSubnetId']);
			
						print '	<tr class="slaveSubnet subSlaveSubnet">' . "\n";
		        		print '		<td class="subnet">'. transform2long($subSlave['subnet']) .'/'. $subSlave['mask'] .'</td>' . "\n";
		       			print '		<td>'. $slave['description'] .'</td>' . "\n";
						print '		<td class="masterSubnet">'. transform2long($master['subnet']) .'/'. $master['mask'] .'</td>' . "\n";
		
						# VLAN
						if(empty($subSlave['VLAN']) || $subSlave['VLAN'] == 0) { $subSlave['VLAN'] = ""; }
						print '		<td>'. $subSlave['VLAN']        .'</td>' . "\n";
		
						# requests
						if($subSlave['allowRequests'] == 1) 	{ print '<td class="allowRequests">enabled</td>'; }
						else 									{ print '<td class="allowRequests"></td>'; }
		
						# check if it is locked for writing
						if(isSubnetWriteProtected($subSlave['id'])) { print '<td class="edit lock" title="Subnet is locked for writing!"></td>';	} 
						else 										{ print '<td class="edit nolock"></td>';}

    		    		print '		<td class="edit"><img src="css/images/edit.png"   class="Edit"   subnetId="'. $subSlave['id'] .'" sectionId="'. $section['id'] .'" title="Edit subnet"></td>' . "\n";
    		    		print '		<td class="edit"><img src="css/images/deleteIP.png" class="Delete" subnetId="'. $subSlave['id'] .'" sectionId="'. $section['id'] .'" title="Delete subnet"></td>' . "\n";
        				print '	</tr>' . "\n";
        			
        			}
        		}
        	}
			
		}
	}
}

    /* add new link */
    print '	<tr class="addNew info add">' . "\n";
    print '		<td colspan="8" class="info">' . "\n";
    print '			<img src="css/images/add.png" class="Add" sectionId="'. $section['id'] .'" title="Add new subnet to '. $section['name'] .'"> Add new subnet to '. $section['name'] . "\n";
    print '		</td>' . "\n";
    print '	</tr>' . "\n";

    /* add / edit / delete holder */
    print '	<tr class="th">' . "\n";
    print '		<td colspan="5">' . "\n";
    print '			<div class="manageSubnetEdit">a</div>' . "\n";
    print '		</td>' . "\n";
    print '	</tr>' . "\n";

    /* end tbody */
    print '</tbody>' . "\n";

print '</table>' . "\n";
print '</div>' . "\n";
}
?>