<?php

/**
 * Script to display all slave IP addresses and subnets in content div of subnets table!
 ***************************************************************************************/

/* use required functions */
require_once('../functions/functions.php');

/* get master subnet ID */
$subnetId = $_POST['subnetId'];

/* get all slaves */
$slaves = getAllSlaveSubnetsBySubnetId ($subnetId);

/* get master details */
$master = getSubnetDetailsById($subnetId);

/* get section details */
$section = getSectionDetailsById($master['sectionId']);

/* print title */
$slaveNum = sizeof($slaves);
print '<h3>'. $master['description'] .' ('. transform2long($master['subnet']) .'/'. $master['mask'] .') has '. $slaveNum .' nested subnets:</h3>';

/* print HTML table */
print '<div class="normalTable">';
print '<table class="normalTable slaveSubnet">'. "\n";

/* headers */
print '<tr class="th dashed">' . "\n";
print '	<th>VLAN</th>' . "\n";	
print '	<th>Description</th>' . "\n";
print '	<th>Subnet</th>' . "\n";
print '	<th>Used</th>' . "\n";
print '	<th>% free</th>' . "\n";
print '	<th>Requests</th>' . "\n";
print '	<th>Locked</th>' . "\n";
print '</tr>' . "\n";


/* print each slave */
foreach ($slaves as $slave) {

	$subnet = $slave;
	
	//reformat empty VLAN
	if(empty($slave['VLAN']) || $slave['VLAN'] == 0) {
		$slave['VLAN'] = "";
	}
	
	print '<tr class="tt" sectionId="'. $section['id'] .'" subnetId="'. $slave['id'] .'" link="'. $section['name'] .'|'. $slave['id'] .'">' . "\n";
    print '	<td><dd>'. $slave['VLAN'] 	   .'</dd></td>' . "\n";
    print '	<td><dd>'. $slave['description'] .'</dd></td>' . "\n";
    print '	<td>'. transform2long($slave['subnet']) .'/'. $slave['mask'] .'</td>' . "\n";
    
    //details
    $ipCount = countIpAddressesBySubnetId ($slave['id']);
	$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $slave['mask'], $slave['subnet'] );
    print ' <td>'. $calculate['used'] .'/'. $calculate['maxhosts'] .'</td>'. "\n";
    print '	<td>'. $calculate['freehosts_percent'] .'</td>';
	
	//allow requests
	if($slave['allowRequests'] == 1) {
		print '<td class="allowRequests requests" title="IP requests are enabled">enabled</td>';
	}
	else {
		print '<td class="allowRequests"></td>';
	}
	
	//check if it is locked for writing
	if(isSubnetWriteProtected($slave['id'])) {
		print '<td class="lock" title="Subnet is locked for writing!"></td>';	
	} else {
		print '<td class="nolock"></td>';
	}

	print '</tr>' . "\n";
}

print '</table>'. "\n";
print '</div>'. "\n";


?>