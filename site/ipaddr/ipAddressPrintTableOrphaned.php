<?php

/**
 * Print sorted IP addresses
 ***********************************************************************/
 
/* get posted subnet, die if it is not provided! */
if($_REQUEST['subnetId']) { $subnetId = $_REQUEST['subnetId']; }

/* direct call */
if(isset($_POST['direction'])) {
	/* use required functions */
	require_once('../../functions/functions.php');
	
	/** 
	* Parse IP addresses
	*
	* We provide subnet and mask, all other is calculated based on it (subnet, broadcast,...)
	*/
	$SubnetParsed = parseIpAddress ( transform2long($SubnetDetails['subnet']), $SubnetDetails['mask']);
}

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


/**
 * Get all ip addresses in subnet and subnet details!
 */
$ipaddresses   = getIpAddressesBySubnetIdSort ($subnetId, "ip_addr", "asc");
$SubnetDetails = getSubnetDetailsById     ($subnetId);

/* die if empty! */
if(sizeof($SubnetDetails) == 0) {
	die('<div class="alert alert-error">Subnet does not exist!</div>');
}

/* get all selected fields */
$myFields = getCustomIPaddrFields();
$myFieldsSize = sizeof($myFields);
	
/* set colspan */
$colspan['unused'] = sizeof($setFields) + $myFieldsSize + 1;
$colspan['ipaddr'] = sizeof($setFields) + $myFieldsSize + 4;

/* remove myFields if all empty! */
foreach($myFields as $field) {
	$sizeMyFields[$field['name']] = 0;				# default value
	# check against each IP address
	foreach($ipaddresses as $ip) {
		if(strlen($ip[$field['name']]) > 0) {
			$sizeMyFields[$field['name']]++;		# +1
		}
	}	
	# unset if valie == 0
	if($sizeMyFields[$field['name']] == 0) {
		unset($myFields[$field['name']]);
	}
}



if(sizeof($ipaddresses) > 0) {
?>
<br><hr>
<h4><div class="alert alert-warn">Orphaned IP addresses for subnet <strong><?php print $SubnetDetails['description'] ?></strong> (<?php print sizeof($ipaddresses); ?>) <i class="icon-gray icon-info-sign" rel="tooltip" title="This happens if subnet had IP addresses<br>when new nested subnet was added."></i></div></h4>

<table class="ipaddresses table table-striped table-condensed table-hover table-full table-top">

<!-- headers -->
<tbody>
<tr>

<?php
	# IP address - mandatory
										  print "<th class='s_ipaddr'>IP address";
	# hostname - mandatory
										  print "<th>Hostname</th>";
	# MAC address	
	if(in_array('mac', $setFields)) 	{ print "<th></th>"; }
	# Description - mandatory
										  print "<th>Description</th>";
	# note
	if(in_array('note', $setFields)) 	{ print "<th></th>"; }	
	# switch
	if(in_array('switch', $setFields)) 	{ print "<th>Switch</th>"; }	
	# port
	if(in_array('port', $setFields)) 	{ print "<th>Port</th>"; }
	# owner
	if(in_array('owner', $setFields)) 	{ print "<th>Owner</th>"; }
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) 	{ print "<th>$myField[name]</th>"; }
	}
?>

	<!-- actions -->
	<th class="actions" width="10px"></th>

</tr>
</tbody>


<?php
/* content */
$n = 0;
$m = $CalculateSubnetDetails['used'] -1;

$n = 0;		# count for IP addresses - $n++ per IP address


/* print IP addresses 
 ********************/	
foreach($ipaddresses as $ipaddress)  
{       
		    print "<tr>";
		    print "	<td class='ipaddress'>".Transform2long( $ipaddress['ip_addr']);
		    if(in_array('state', $setFields)) 				{ print reformatIPState($ipaddress['state']); }	
		    print "</td>";

		    # resolve dns name if not provided, else print it - IPv4 only!
		    if ( (empty($ipaddress['dns_name'])) and ($settings['enableDNSresolving'] == 1) and (IdentifyAddress($ipaddress['ip_addr']) == "IPv4") ) {
			    $dnsResolved = ResolveDnsName ( $ipaddress['ip_addr'] );
			}
			else {
				$dnsResolved['class'] = "";
				$dnsResolved['name']  = $ipaddress['dns_name'];
			}														  print "<td class='$dnsResolved[class] hostname'>$dnsResolved[name]</td>";  		

			# Print mac address icon!
			if(in_array('mac', $setFields)) {
				if(!empty($ipaddress['mac'])) 					{ print "<td class='mac'><img class='info mac' src='/css/images/lan.png' rel='tooltip' title='MAC: ".$ipaddress['mac']."'></td>"; }
				else 												{ print "<td class='mac'></td>"; }
			}
		
			# print description - mandatory
        													  		  print "<td class='description'>".$ipaddress['description']."</td>";	
		
       		# print info button for hover
       		if(in_array('note', $setFields)) {
        		if(!empty($ipaddress['note'])) 					{ print "<td><i class='icon-gray icon-comment' rel='tooltip' title='".str_replace("\n", "<br>",$ipaddress['note'])."'></td>"; }
        		else 											{ print "<td></td>"; }
        	}
	
        	# print switch
        	if(in_array('switch', $setFields)) 					{ 
	        	# get switch details
	        	$switch = getSwitchById ($ipaddress['switch']);
																  print "<td>".$switch['hostname']."</td>";
																}
		
			# print port
			if(in_array('port', $setFields)) 					{ print "<td>".$ipaddress['port']."</td>"; }
		
			# print owner
			if(in_array('owner', $setFields)) 					{ print "<td>".$ipaddress['owner']."</td>"; }
		
			# print custom fields 
			if(sizeof($myFields) > 0) {
				foreach($myFields as $myField) 					{ print "<td class='customField'>".$ipaddress[$myField['name']]."</td>"; }
			}
		
			# print action links if user can edit 
			if(!$viewer) {		
				print "<td class='btn-actions'>";
				print "	<div class='btn-toolbar'>";
				print "	<div class='btn-group'>";
				print "		<a class='move_ipaddress   btn btn-mini moveIPaddr' data-action='move'   data-subnetId='$SubnetDetails[id]' data-id='".$ipaddress['id']."' href='#' rel='tooltip' title='Move to different subnet'>				<i class='icon-gray icon-edit'>  </i></a>";
				print "		<a class='delete_ipaddress btn btn-mini modIPaddr'  data-action='delete' data-subnetId='$SubnetDetails[id]' data-id='".$ipaddress['id']."' href='#' rel='tooltip' title='Delete IP address'>				<i class='icon-gray icon-remove'>  </i></a>";
				print "	</div>";
				print "	</div>";
				print "</td>";		
			}
			else {
				print '<td></td>';
			}
		
			print '</tr>'. "\n";
	   
            /* next IP address for free check */
	        $n++;         
}	
}
?>

</table>	<!-- end IP address table -->