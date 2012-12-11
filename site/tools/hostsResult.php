<?php

/**
 * Script to display devices by hostname
 *
 */

/* include required scripts */
if(!function_exists('isUserAuthenticated')) { require_once('../../functions/functions.php'); }

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all IP addresses if hostname is not set! */
if(!isset($_REQUEST['hostname'])) { $ipAddresses = fetchAllIPAddresses (true); }
else {
	$_REQUEST['hostname'] = str_replace("*", "", $_REQUEST['hostname']);	//remove possible *
	$ipAddresses = fetchAllIPAddressesByName ($_REQUEST['hostname']);
}


/* get all selected fields for filtering */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);
# set size!
$rowSize = sizeof($setFields) + 3;


/* get all custom fields */
$myFields = getCustomIPaddrFields();
$myFieldsSize = sizeof($myFields);
$rowSize = $rowSize + $myFieldsSize;

# print if filtered
if(strlen($_REQUEST['hostname']) > 0) { print "<div class='alert alert-info'>Applied filter: <strong>$_REQUEST[hostname]</strong></div>"; }


# table
print '<table id="hosts" class="table table-striped table-hover table-condensed table-top">'. "\n";
	
# title
print "<tr>";
										  print '	<th>Hostname</th>'. "\n";						# hostname - mandatory
										  print '	<th>IP address</th>'. "\n";						# IP address - mandatory
	if(in_array('mac', $setFields)) 	{ print '	<th></th>'. "\n"; }								# mac
	if(in_array('switch', $setFields)) 	{ print '	<th>Switch</th>'. "\n";}						# switch 
	if(in_array('port', $setFields)) 	{ print '	<th>Port</th>'. "\n";}							# port
										  print '	<th>Subnet</th>'. "\n";							# subnet - mandatory
										  print '	<th>Section</th>'. "\n";						# sexction - mandatory
	if(in_array('note', $setFields)) 	{ print '	<th colspan="2">Description</th>'. "\n";	}	# description and note
	else 								{ print '	<th>Description</th>'. "\n";}					# description only
	if(in_array('owner', $setFields)) 	{ print '	<th>Owner</th>'. "\n"; }						# owner only
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) 	{ print '<th>'. $myField['name'] .'</th>'. "\n"; }			# custom fields
	}
print '</tr>'. "\n";

# if nothing is found print it
if(sizeof($ipAddresses) == 0) {
	print '<tr class="th"><td colspan="'. $rowSize .'"><div class="alert alert-warn">No results found for string "'. $_REQUEST['hostname'] .'"</div></td></tr>';
}
$m = 0;
foreach($ipAddresses as $ip) {
	
	# get subnet details
	$subnet = getSubnetDetails ($ip['subnetId']);
	# get section details
	$section = getSectionDetailsById ($subnet['sectionId']);
	# check if hostname is the same as previous one
	$n = $m -1;
	if($ipAddresses[$m]['dns_name'] != $ipAddresses[$n]['dns_name']) 	{ $class = "new";}
	else 																{ $class = "same";}
	
	print '<tr class="'. $class .'">'. "\n";
	
	# don't show hostname if it is the same as first */
	if($ipAddresses[$m]['dns_name'] == $ipAddresses[$m-1]['dns_name']) 	{ print '	<td class="dns"></td>'. "\n"; }
	else 																{ print '	<td class="dns">'. $ip['dns_name'] .'</td>'. "\n"; }
	
	# IP address
	print "	<td>". transform2long($ip['ip_addr']) ."/$subnet[mask]</td>";
	
	if(in_array('mac', $setFields)) {
		print '	<td class="mac">'. "\n";
		if(strlen($ip['mac']) > 0) { print '<i class="icon-mac" rel="tooltip" title="MAC: '. $ip['mac'] .'"></i>'. "\n"; }
		print '</td>'. "\n";
	}
	# switch
	if(in_array('switch', $setFields)) 	{ print '	<td class="switch">'. $ip['switch'] .'</td>'. "\n";	 }
	# port
	if(in_array('port', $setFields)) 	{ print '	<td class="port">'. $ip['port'] .'</td>'. "\n"; }
	# subnet
	print "	<td><a href='/subnets/$section[id]/$subnet[id]/'>$subnet[description]</a></td>";	
	# section
	print "	<td><a href='/subnets/$section[id]/'>$section[description]</a></td>";	
	# description
	print '	<td>'. $ip['description'] .'</td>'. "\n";
	# note
	if(in_array('note', $setFields)) {
		print '<td class="note">' . "\n";
		if(!empty($ip['note'])) {
			$ip['note'] = str_replace("\n", "<br>",$ip['note']);
			print '	<i class="icon-gray icon-comment" rel="tooltip" title="'. $ip['note'] .'"></i>' . "\n";
		}
		print '</td>'. "\n";
	}
	# owner
	if(in_array('owner', $setFields)) 	{ print '	<td class="owner">'. $ip['owner'] .'</td>'. "\n"; }
	# custom
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) 	{ print '<td class="customField">'. $ip[$myField['name']] .'</td>'. "\n"; }
	}
	print '</tr>'. "\n";

	$m++;
}
print '</table>'. "\n";
	
?>