<script type="text/javascript">
/* fix for ajax-loading tooltips */
$('body').tooltip({ selector: '[rel=tooltip]' });
</script>
<?php

/**
 * Script to display all slave IP addresses and subnets in content div of subnets table!
 ***************************************************************************************/

/* get master subnet ID */
$subnetId = $_REQUEST['subnetId'];

/* get all slaves */
$slaves = getAllSlaveSubnetsBySubnetId ($subnetId);

/* get master details */
$master = getSubnetDetailsById($subnetId);

/* get section details */
$section = getSectionDetailsById($master['sectionId']);

/* print title */
$slaveNum = sizeof($slaves);
print "<h4>$master[description] (".transform2long($master['subnet'])."/$master[mask]) has $slaveNum directly nested subnets:</h4><hr><br>";

/* print HTML table */
print '<table class="slaves table table-striped table-hover table-full table-top">'. "\n";

/* headers */
print "<tr>";
print "	<th>VLAN</th>";
print "	<th>Subnet description</th>";
print "	<th>Subnet</th>";
print "	<th>Used</th>";
print "	<th>% free</th>";
print "	<th>Requests</th>";
print "	<th>Locked</th>";
print "</tr>";

/* print each slave */
$usedSum = 0;
$allSum = 0;

foreach ($slaves as $slave) {

	$subnet = $slave;
	
	# reformat empty VLAN
	if(empty($slave['VLAN']) || $slave['VLAN'] == 0 || strlen($slave['VLAN']) == 0) { $slave['VLAN'] = "/"; }
	
	# get VLAN details
	$slave['VLAN'] = subnetGetVLANdetailsById($slave['vlanId']);
	$slave['VLAN'] = $slave['VLAN']['number'];
	
	print "<tr>";
    print "	<td>$slave[VLAN]</td>";
    print "	<td><a href='subnets/$section[id]/$slave[id]/'>$slave[description]</a></td>";
    print "	<td><a href='subnets/$section[id]/$slave[id]/'>".transform2long($slave['subnet'])."/$slave[mask]</a></td>";
    
    # details
    $ipCount = countIpAddressesBySubnetId ($slave['id']);
	$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $slave['mask'], $slave['subnet'] );
    print ' <td>'. $calculate['used'] .'/'. $calculate['maxhosts'] .'</td>'. "\n";
    print '	<td>'. $calculate['freehosts_percent'] .'</td>';
    
    # add to sum if IPv4
    if ( IdentifyAddress( $slave['subnet'] ) == "IPv4") {
		$usedSum = $usedSum + $calculate['used'];
		$allSum  = $allSum  + $calculate['maxhosts'];    
    }
	
	# allow requests
	if($slave['allowRequests'] == 1) 			{ print '<td class="allowRequests">enabled</td>'; }
	else 										{ print '<td class="allowRequests"></td>'; }
	
	# check if it is locked for writing
	if(isSubnetWriteProtected($slave['id'])) 	{ print '<td class="lock"><i class="icon-gray icon-lock" rel="tooltip" title="Subnet is locked for writing for non-admins!"></i></td>'; } 
	else 										{ print '<td class="nolock"></td>'; }

	print '</tr>' . "\n";
}

# graph
include_once('subnetDetailsGraph.php');



print '</table>'. "\n";

?>