<?php

/**
 * Print sorted IP addresses
 ***********************************************************************/
 
/* use required functions */
require_once('../config.php');
require_once('../functions/functions.php');

/* First chech referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get posted subnet, die if it is not provided! */
if($_REQUEST['subnetId']) {
	$subnetId = $_REQUEST['subnetId'];
}


/* format posted values! */
$tmp = explode("|", $_POST['direction']);

$sort['field'] 	   = $tmp[0];
$sort['direction'] = $tmp[1];

if($sort['direction'] == "asc") {
	$sort['directionNext'] = "desc";
}
else {
	$sort['directionNext'] = "asc";
}



/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


/**
 * Get all ip addresses in subnet and subnet details!
 */
$ipaddresses   = getIpAddressesBySubnetIdSort ($subnetId, $sort['field'], $sort['direction']);
$SubnetDetails = getSubnetDetailsById     ($subnetId);

/* die if empty! */
if(sizeof($SubnetDetails) == 0) {
	die('<div class="error">Subnet does not exist!</div>');
}



/** 
 * Parse IP addresses
 *
 * We provide subnet and mask, all other is calculated based on it (subnet, broadcast,...)
 */
$SubnetParsed = parseIpAddress ( transform2long($SubnetDetails['subnet']), $SubnetDetails['mask']);


?>


<!-- 
print IP address table  
-->

<table class="ipaddresses normalTable">

<!-- headers -->
<tr class="th">

<?php
	/* get all selected fields */
	$myFields = getCustomIPaddrFields();
	$myFieldsSize = sizeof($myFields);
	
	/* set colspan */
	$colspan['unused'] = sizeof($setFields) + $myFieldsSize + 1;
	$colspan['ipaddr'] = sizeof($setFields) + $myFieldsSize + 4;

	# IP address - mandatory
	if($sort['field'] == "ip_addr") {
		print '<th class="sorted s_ipaddr"><a href="" id="ip_addr|'. $sort['directionNext'] .'" class="sort" title="Sort by IP address" subnetId="'. $SubnetDetails['id'] .'">IP address <img src="css/images/sort_'. $sort['direction'] .'.png"></a></th>'. "\n";	
	}
	else {
		print '<th><a href="" id="ip_addr|asc" class="sort" title="Sort by IP address" subnetId="'. $SubnetDetails['id'] .'">IP address</a></th>'. "\n";
	}
	
	# hostname - mandatory
	if($sort['field'] == "dns_name") {
		print '<th class="sorted"><a href="" id="dns_name|'. $sort['directionNext'] .'" class="sort" title="Sort by IP hostname" subnetId="'. $SubnetDetails['id'] .'">Hostname <img src="css/images/sort_'. $sort['direction'] .'.png"></a></th>'. "\n";	
	}
	else {
		print '<th><a href="" id="dns_name|asc" class="sort" title="Sort by IP hostname" subnetId="'. $SubnetDetails['id'] .'">Hostname</a></th>'. "\n";
	}
	
	# MAC address	
	if(in_array('mac', $setFields)) {
		print '<th></th>'. "\n";
	}
	
	# Description- mandatory
	if($sort['field'] == "description") {
		print '<th class="sorted"><a href="" id="description|'. $sort['directionNext'] .'" class="sort" title="Sort by description" subnetId="'. $SubnetDetails['id'] .'">Description <img src="css/images/sort_'. $sort['direction'] .'.png"></a></th>'. "\n";	
	}
	else {
		print '<th><a href="" id="description|asc" class="sort" title="Sort by description" subnetId="'. $SubnetDetails['id'] .'">Description</a></th>'. "\n";
	}
	
	# note
	if(in_array('note', $setFields)) {
		print '<th></th>'. "\n";
	}
		
	# switch
	if(in_array('switch', $setFields)) {
		if($sort['field'] == "switch") {
			print '<th class="sorted"><a href="" id="switch|'. $sort['directionNext'] .'" class="sort" title="Sort by switch" subnetId="'. $SubnetDetails['id'] .'">Switch <img src="css/images/sort_'. $sort['direction'] .'.png"></a></th>'. "\n";	
		}
		else {
			print '<th><a href="" id="switch|asc" class="sort" title="Sort by switch" subnetId="'. $SubnetDetails['id'] .'">Switch</a></th>'. "\n";
		}
	}
	
	# port
	if(in_array('port', $setFields)) {
		if($sort['field'] == "port") {
			print '<th class="sorted"><a href="" id="port|'. $sort['directionNext'] .'" class="sort" title="Sort by port" subnetId="'. $SubnetDetails['id'] .'">Port <img src="css/images/sort_'. $sort['direction'] .'.png"></a></th>'. "\n";	
			}
		else {
			print '<th><a href="" id="port|asc" class="sort" title="Sort by port" subnetId="'. $SubnetDetails['id'] .'">Port</a></th>'. "\n";
		}
	}
	# owner
	if(in_array('owner', $setFields)) {
		if($sort['field'] == "owner") {
			print '<th class="sorted"><a href="" id="owner|'. $sort['directionNext'] .'" class="sort" title="Sort by owner" subnetId="'. $SubnetDetails['id'] .'">Owner <img src="css/images/sort_'. $sort['direction'] .'.png"></a></th>'. "\n";	
			}
		else {
			print '<th><a href="" id="owner|asc" class="sort" title="Sort by owner" subnetId="'. $SubnetDetails['id'] .'">Owner</a></th>'. "\n";
		}
	}
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) {
		
			if($sort['field'] == $myField['name']) {
				print '<th class="sorted"><a href="" id="'. $myField['name'] .'|'. $sort['directionNext'] .'" class="sort" title="Sort by '. $myField['name'] .'" subnetId="'. $SubnetDetails['id'] .'">'. $myField['name'] .' <img src="css/images/sort_'. $sort['direction'] .'.png"></a></th>'. "\n";	
			}
			else {
				print '<th><a href="" id="'. $myField['name'] .'|asc" class="sort" title="Sort by '. $myField['name'] .'" subnetId="'. $SubnetDetails['id'] .'">'. $myField['name'] .'</a></th>'. "\n";
			}		
		}
	}
?>

	<!-- actions -->
	<th colspan="3" class="actions" width="10px"></th>

</tr>


<?php
/* content */
$n = 0;
$m = $CalculateSubnetDetails['used'] -1;

/* 
if result not empty use first IP address in subnet to identify type 
else use subnet
*/
$type = IdentifyAddress( $SubnetDetails['subnet'] );

/*
 if no IP is configured only display free subnet!
*/
if (!$ipaddresses) 
{
    $unused = FindUnusedIpAddresses ( Transform2decimal($SubnetParsed['network']), Transform2decimal($SubnetParsed['broadcast']), $type, 1 );
    print '<tr class="th"><td></td><td colspan="'. $colspan['unused'] .'" class="unused">'. $unused['ip'] . ' (' . reformatNumber ($unused['hosts']) .')</td><td colspan=2></td></tr>'. "\n";
}
else
{
    foreach($ipaddresses as $ipaddress) 
    {
        
        /*	set class for reserved and offline - if set!
        ***********************************************/
        $stateClass = "";
        if(in_array('state', $setFields)) {
	        if ($ipaddress['state'] == "0") 	 { $stateClass = "offline"; }
	        else if ($ipaddress['state'] == "2") { $stateClass = "reserved"; }
        }

        /*	print IP address
        ***********************************/
    	print '<tr class="'. $stateClass .'">'. "\n";
		print '<td class="ipaddress">'. Transform2long( $ipaddress['ip_addr']) .'</td>'. "\n";


		/*	resolve dns name if not provided, else print it - IPv4 only!
		*****************************************************************/
		if ( (empty($ipaddress['dns_name'])) and ($settings['enableDNSresolving'] == 1) and (IdentifyAddress($ipaddress['ip_addr']) == "IPv4") ) {
			$dnsResolved = ResolveDnsName ( $ipaddress['ip_addr'] );
		}
		else {
			$dnsResolved['class'] = "";
		  	$dnsResolved['name']  = $ipaddress['dns_name'];
		}
		print '<td class="'. $dnsResolved['class'] .' hostname">'. $dnsResolved['name'] 	.'</td>'. "\n";  		


		/*	Print mac address icon!
		*****************************************************************/
		if(in_array('mac', $setFields)) {
			print '<td class="mac">' . "\n";
			if(!empty($ipaddress['mac'])) {
				print '	<img class="info mac" src="css/images/lan.png" title="MAC: '. $ipaddress['mac']. '">' . "\n";
			}
			print '</td>'. "\n";
		}
		
        /*	print description - mandatory
        ***********************************/
	    if ( ($ipaddress['state'] == "0") || ($ipaddress['state'] == "2") ) {
			print '<td class="description">'. $ipaddress['description']. "\n"; 
			# state
			if(in_array('state', $setFields)) {
				print '('. reformatIPState($ipaddress['state']) .')'. "\n";
			}
			print '</td>';
		}
		else {
			print '<td class="description">'. $ipaddress['description'] .'</td>'. "\n";
		}	

		
		/*	print info button for hover
		**********************************/
		if(in_array('note', $setFields)) {
			print '<td>' . "\n";
			if(!empty($ipaddress['note'])) {
				$ipaddress['note'] = str_replace("\n", "<br>",$ipaddress['note']);
				print '	<img class="info" src="css/images/note.png" title="'. $ipaddress['note']. '">' . "\n";
			}
			print '</td>'. "\n";
		}
		  
	
		/*	print switch / port
		***********************/
		if(in_array('switch', $setFields)) {
			print '<td>'. $ipaddress['switch'] 	.'</td>' . "\n";		
		}
		if(in_array('port', $setFields)) {
			print '<td>'. $ipaddress['port'] 	.'</td>' . "\n";
		}

		/*	print owner
		*****************/
		if(in_array('owner', $setFields)) {
			print '<td>'. $ipaddress['owner'] .'</td>' . "\n";
		}


		/*	print custom fields 
		***************************************/
		if(sizeof($myFields) > 0) {
			foreach($myFields as $myField) {
				print '<td class="customField">'. $ipaddress[$myField['name']] .'</td>'. "\n";
			}
		}
		
		
		/*	print action links if user can edit 
		***************************************/
		if(!$viewer) {
		
		print '<td class="mail">' . "\n";
		print '	<img class="mail_ipaddress" src="css/images/mail1.png" id="'. $ipaddress['id']. '" title="Send mail notification">' . "\n";
		print '</td>'. "\n";

		print '<td class="edit">' . "\n";
		print '	<img class="edit_ipaddress" src="css/images/edit.png" id2="'. Transform2long($ipaddress['ip_addr']) .'" id="'. $ipaddress['id']. '" title="Edit IP address details">' . "\n";
		print '</td>'. "\n";
		
		print '<td class="delete">' . "\n";
		print '	<img class="delete_ipaddress" src="css/images/deleteIP.png" id2="'. Transform2long($ipaddress['ip_addr']) .'" id="'. $ipaddress['id']. '" title="Delete IP address">' . "\n";
		print '</td>'. "\n";
		
		}
		else {
    		print '<td colspan="3"></td>';
    	}
		
    	print '</tr>'. "\n";

    /* next IP address for free check */
    $n++;
    }
}

?>

</table>	<!-- end IP address table -->