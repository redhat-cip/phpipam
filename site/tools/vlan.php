<?php

/**
 * Script to display available VLANs
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* die if viewer */
if(isUserViewer()) {
	die('<div class="error">You do not have permissions to access this page!</div>');
}

/* get all VLANs and subnet descriptions */
$vlans = getAllVlans (true);

# title
print '<h3>Available VLANs:</h3>'. "\n";

/*  print VLANs */
print '<div class="normalTable vlans">';
print '<table class="normalTable vlans">';

/* headers */
print '<tr class="th">' . "\n";
print ' <th>Number</th>' . "\n";
print ' <th>VLAN Name</th>' . "\n";
print ' <th>VLAN Description</th>' . "\n";
print ' <th>Belonging subnets</th>' . "\n";
print ' <th>Section</th>' . "\n";
print ' <th>Used</th>' . "\n";
print ' <th>free [%]</th>' . "\n";
print '</tr>' . "\n";

# change detection
$vlanOld = 0;

foreach ($vlans as $vlan) {

# detect change
$vlanNew = $vlan['number'];

if($vlanNew == $vlanOld) { $change = ''; }
else 					 { $change = 'style="border-top:1px dashed white"'; $vlanOld = $vlanNew; }

/* get section details */
$section = getSectionDetailsById($vlan['sectionId']);

/* check if it is master */
if( ($vlan['masterSubnetId'] == 0) || (empty($vlan['masterSubnetId'])) ) { $masterSubnet = true;}
else 																	 { $masterSubnet = false;}


print '<tr class="vlanLink" '. $change .' sectionId="'. $section['id'] .'" subnetId="'. $vlan['subnetId'] .'" link="'. $section['name'] .'|'. $vlan['subnetId'] .'">' . "\n";

/* print first 3 only if change happened! */
if(strlen($change) > 0) {
	print ' <td><dd>'. $vlan['number']         .'</dd></td>' . "\n";
	print ' <td><dd>'. $vlan['name']           .'</dd></td>' . "\n";
	print ' <td><dd>'. $vlan['description'] .'</dd></td>' . "\n";			
}
else {
	print '<td></td>';
	print '<td></td>';
	print '<td></td>';	
} 

if ($vlan['subnetId'] != null) {
		# subnet
        print ' <td>'. transform2long($vlan['subnet']) .'/'. $vlan['mask'] .'</td>' . "\n";

		# section
		print ' <td>'. $section['name'] .'</td>'. "\n";

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




print '</tr>' . "\n";

}


print '</table>';
print '</div>';

print '<div class="error" hidden></div>';
?>
