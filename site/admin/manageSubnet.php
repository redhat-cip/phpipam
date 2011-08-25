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
$subnets = fetchSubnets ( $section['id'] );
    
if (!empty($subnets)) {

	foreach ($subnets as $subnet) {
	
		/* check if it is master */
		if( ($subnet['masterSubnetId'] == 0) || (empty($subnet['masterSubnetId'])) ) {
			$masterSubnet = true;
			$class = "masterSubnet";
		}
		else {
			$masterSubnet = false;
			$class = "slaveSubnet";
		}

		print '	<tr class="'. $class .'">' . "\n";
        print '		<td>'. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</td>' . "\n";
       	print '		<td>'. $subnet['description'] .'</td>' . "\n";

		if($masterSubnet) {
			print '		<td>/</td>' . "\n";
		}
		else {
			$master = getSubnetDetailsById ($subnet['masterSubnetId']);
       		print '		<td>'. transform2long($master['subnet']) .'/'. $master['mask'] .'</td>' . "\n";
		}

		//VLAN
		if(empty($subnet['VLAN']) || $subnet['VLAN'] == 0) {
			$subnet['VLAN'] = "";
		}
		print '		<td>'. $subnet['VLAN']        .'</td>' . "\n";
		
		//requests
		if($subnet['allowRequests'] == 1) {
			print '<td class="allowRequests">enabled</td>';
		}
		else {
			print '<td class="allowRequests"></td>';
		}
		
		//check if it is locked for writing
		if(isSubnetWriteProtected($subnet['id'])) {
			print '<td class="edit lock" title="Subnet is locked for writing!"></td>';	
		} 
		else {
			print '<td class="edit nolock"></td>';
		}
		

        print '		<td class="edit"><img src="css/images/edit.png"   class="Edit"   subnetId="'. $subnet['id'] .'" sectionId="'. $section['id'] .'" title="Edit subnet"></td>' . "\n";
        print '		<td class="edit"><img src="css/images/deleteIP.png" class="Delete" subnetId="'. $subnet['id'] .'" sectionId="'. $section['id'] .'" title="Delete subnet"></td>' . "\n";
        print '	</tr>' . "\n";
        }
    }

    /* add new link */
    print '	<tr class="addNew info">' . "\n";
    print '		<td colspan=5 class="info">' . "\n";
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