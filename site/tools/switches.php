<?php

/**
 * Script to display switches
 *
 */


/* verify that user is authenticated! */
isUserAuthenticated ();

/* die if viewer */
if(isUserViewer()) { die('<div class="alert alert-error">You do not have permissions to access this page!</div>'); }

/* get all unique switches */
$switches = getAllUniqueSwitches();

/* add unspecified */
$switches[] = array("id"=>"","hostname"=>"");

/* switch count for collapse / extend */
$m = 0;

# title
print "<h4>List of network devices</h4>";
print "<hr>";

# main table frame
print "<table id='switchMainTable' class='switches table table-striped table-top table-condensed'>";

/* print */
foreach($switches as $switch) {

	/* Get all IP addresses belonging to switch */
	$ipaddresses = getIPaddressesBySwitchName ( $switch['id'] );
	
	/* Get switch details */
	$switchDetails = getSwitchDetailsByHostname($switch['hostname']);
	
	if(empty($switchDetails['hostname'])) 		{ 
		$switchDetails['hostname'] = 'Switch not specified'; 
		$switchDetails['ip_addr']  = "";
	}
	else 										{ 
		$switchDetails['ip_addr'] = "($switchDetails[ip_addr])";
	}
	
	/* reformat if empty */
	if(empty($switch['hostname'])) 				{ $switch['hostname'] = "Unspecified";}
	
	# count size
	$size = sizeof($ipaddresses);
	
	# print name
	print "<tbody id='switch-$m'>";
	print "<tr class='switch-title'>";
	print "	<th colspan='7'>";
	print "		<h4><button class='btn btn-small' id='switch-$m' rel='tooltip' title='click to show/hide belonging IP addresses'><i class='icon-gray icon-chevron-right'></i></button> $switchDetails[hostname] $switchDetails[ip_addr]</h4>";
	print "	</th>";
	print "</tr>";
	print "</tbody>";
	
	# collapsed div with details
	print "<tbody id='content-switch-$m'>";
		
	# headers
	print "<tr>";
	print "	<th>IP address</th>";
	print "	<th>Port</th>";
	print "	<th>Subnet</th>";
	print "	<th colspan='2'>Description</th>";
	print "	<th>Hostname</th>";
	print "	<th>Owner</th>";
	print "</tr>";
	
	# IP addresses
	foreach ($ipaddresses as $ip) {
	
		# get subnet details for belonging IP
		$subnet = getSubnetDetails ($ip['subnetId']);
		# get section details
		$section = getSectionDetailsById ($subnet['sectionId']);
	
		# print
		print "<tr>";
		print "	<td class='ip'>".transform2long($ip['ip_addr'])."/$subnet[mask]</td>";
		print "	<td class='port'>$ip[port]</td>";
		print "	<td class='subnet'><a href='/subnets/$section[id]/$subnet[id]/'>$subnet[description]</a></td>";
		print "	<td class='description'>$ip[description]</td>";

		# print info button for hover
		print "<td class='note'>";
		if(!empty($ip['note'])) {
			$ip['note'] = str_replace("\n", "<br>",$ip['note']);
			print "	<i class='icon-gray icon-comment' rel='tooltip' title='$ip[note]'></i>";
		}
		print "</td>";
		
		print "	<td class='dns'>$ip[dns_name]</td>";
		print "	<td class='owner'>$ip[owner]</td>";
		print "</tr>";
	
	}
	
	print "</tr>";
	print "</tbody>";
	
	$m++;
}

print "</table>";			# end major table
?>