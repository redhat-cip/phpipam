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
print "<h4>$master[description] (".transform2long($master['subnet'])."/$master[mask]) "._('has')." $slaveNum "._('directly nested subnets').":</h4><hr><br>";

/* print HTML table */
print '<table class="slaves table table-striped table-condensed table-hover table-full table-top">'. "\n";

/* headers */
print "<tr>";
print "	<th class='small'>"._('VLAN')."</th>";
print "	<th class='small description'>"._('Subnet description')."</th>";
print "	<th>"._('Subnet')."</th>";
print "	<th class='small'>"._('Used')."</th>";
print "	<th class='small'>% "._('Free')."</th>";
print "	<th class='small'>"._('Requests')."</th>";
print " <th></th>";
print "</tr>";

/* print each slave */
$usedSum = 0;
$allSum = 0;

# for free space check
$slaveSize = sizeof($slaves);
$m = 0;

foreach ($slaves as $slave) {

	# if first check for free space
	if($m == 0) {
		# if master start != first slave start print free space
		if($master['subnet'] != $slave['subnet']) {
			# calculate diff
			$diff = gmp_strval(gmp_sub($slave['subnet'], $master['subnet']));
			
			print "<tr class='success'>";
			print "	<td></td>";
			print "	<td class='small description'>"._('Free space')."</td>";
			print "	<td colspan='5'>". transform2long($master['subnet']) ." - ". transform2long(gmp_strval(gmp_add($master['subnet'], gmp_sub($diff,1)))) ." ( ".$diff." )</td>";
			print "</tr>";
		}
	}

	
	# reformat empty VLAN
	if(empty($slave['VLAN']) || $slave['VLAN'] == 0 || strlen($slave['VLAN']) == 0) { $slave['VLAN'] = "/"; }
	
	# get VLAN details
	$slave['VLAN'] = subnetGetVLANdetailsById($slave['vlanId']);
	$slave['VLAN'] = $slave['VLAN']['number'];
	
	print "<tr>";
    print "	<td class='small'>$slave[VLAN]</td>";
    print "	<td class='small description'><a href='subnets/$section[id]/$slave[id]/'>$slave[description]</a></td>";
    print "	<td><a href='subnets/$section[id]/$slave[id]/'>".transform2long($slave['subnet'])."/$slave[mask]</a></td>";
    
    # count IP addresses
	$hasSlaves = getAllSlaveSubnetsBySubnetId ($slave['id']); 

	# slaves details are provided with ipaddressprintslaves script
	if(sizeof($hasSlaves)>0)	{ $ipCount = sizeof(getIpAddressesBySubnetIdSlavesSort ($slave['id'])); }	//ip count - slaves
	else 						{ $ipCount = countIpAddressesBySubnetId ($slave['id']);	}					//ip count - direct subnet  

    
	$calculate = calculateSubnetDetails ( gmp_strval($ipCount), $slave['mask'], $slave['subnet'] );
    print ' <td class="small">'. $calculate['used'] .'/'. $calculate['maxhosts'] .'</td>'. "\n";
    print '	<td class="small">'. $calculate['freehosts_percent'] .'</td>';
    
    # add to sum if IPv4
    if ( IdentifyAddress( $slave['subnet'] ) == "IPv4") {
		$usedSum = $usedSum + $calculate['used'];
		$allSum  = $allSum  + $calculate['maxhosts'];    
    }
	
	# allow requests
	if($slave['allowRequests'] == 1) 			{ print '<td class="allowRequests small">enabled</td>'; }
	else 										{ print '<td class="allowRequests small"></td>'; }
	
	# edit
	$subnetPerm = checkSubnetPermission ($slave['id']);
	if($subnetPerm == 3) {
		print "	<td class='small'>";
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-mini editSubnet'     data-action='edit'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='icon-gray icon-pencil'></i></button>";
		print "		<button class='btn btn-mini showSubnetPerm' data-action='show'   data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='icon-gray icon-tasks'></i></button>";
		print "		<button class='btn btn-mini editSubnet'     data-action='delete' data-subnetid='".$slave['id']."'  data-sectionid='".$slave['sectionId']."'><i class='icon-gray icon-remove'></i></button>";
		print "	</div>";
		print " </td>";
	}
	else {
		print "	<td class='small'>";
		print "	<div class='btn-group'>";
		print "		<button class='btn btn-mini disabled'><i class='icon-gray icon-pencil'></i></button>";
		print "		<button class='btn btn-mini disabled'><i class='icon-gray icon-tasks'></i></button>";
		print "		<button class='btn btn-mini disabled'><i class='icon-gray icon-remove'></i></button>";
		print "	</div>";
		print " </td>";		
	}

	print '</tr>' . "\n";
	
	
	# check if some free space between this and next subnet
	if(isset($slaves[$m+1])) {
		# get IP type
		if ( IdentifyAddress( $master['subnet'] ) == "IPv4") 	{ $type = 0; }
		else 													{ $type = 1; }
		
		# set $diffAdd based on mask!
		if($slaves[$m]['mask'] == "32")		{ $diffAdd = 0; }
		elseif($slaves[$m]['mask'] == "31")	{ $diffAdd = 0; }
		else								{ $diffAdd = 2; }
		
		# get max host for current
		$slave['maxip'] = gmp_strval(gmp_add(MaxHosts($slave['mask'],$type),$diffAdd));
		# calculate diff
		$diff = gmp_strval(gmp_sub($slaves[$m+1]['subnet'], gmp_strval(gmp_add($slave['subnet'],$slave['maxip']))));
		
		# if diff print free space
		if($diff > 0) {
			print "<tr class='success'>";
			print "	<td></td>";
			print "	<td class='small description'>"._('Free space')."</td>";
			print "	<td colspan='5'>". transform2long(gmp_strval(gmp_add($slave['maxip'], $slave['subnet']))) ." - ". transform2long(gmp_strval(gmp_add(gmp_add($slave['maxip'], $slave['subnet']), gmp_sub($diff,1)))) ." ( ".$diff." )</td>";
			print "</tr>";			
		}		
	}
	
	
	# next - for free space check
	$m++;	
	
	# if last check for free space
	if($m == $slaveSize) {
		# get IP type
		if ( IdentifyAddress( $master['subnet'] ) == "IPv4") 	{ $type = 0; }
		else 													{ $type = 1; }
		
		# set $diffAdd based on mask!
		if($slaves[$m-1]['mask'] == "32")		{ $diffAdd = 0; }
		elseif($slaves[$m-1]['mask'] == "31")	{ $diffAdd = 0; }
		else									{ $diffAdd = 2; }
		
		# calculate end of master and last slave
		$maxh_m = gmp_strval(gmp_add(MaxHosts( $master['mask'], $type ),2));
		$maxh_s = gmp_strval(gmp_add(MaxHosts( $slave['mask'],  $type ),$diffAdd));
		
		$max_m  = gmp_strval(gmp_add($master['subnet'], $maxh_m));
		$max_s  = gmp_strval(gmp_add($slave['subnet'],  $maxh_s));
		
		$diff   = gmp_strval(gmp_sub($max_m, $max_s));
	
		# if slave stop < master stop print free space
		if($max_m > $max_s) {			
			print "<tr class='success'>";
			print "	<td></td>";
			print "	<td class='small description'>"._('Free space')."</td>";
			print "	<td colspan='5'>". transform2long(gmp_strval(gmp_sub($max_m, $diff))) ." - ". transform2long(gmp_strval(gmp_sub($max_m, 1))) ." ( $diff )</td>";
			print "</tr>";
		}	
	}

}

# graph
include_once('subnetDetailsGraph.php');



print '</table>'. "\n";

?>