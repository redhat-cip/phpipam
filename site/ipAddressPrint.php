<?php

/**
 * Main script to display IP addresses in content div of subnets table!
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


/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


/**
 * Get all ip addresses in subnet and subnet details!
 */
$ipaddresses   = getIpAddressesBySubnetId ($subnetId); 
$SubnetDetails = getSubnetDetailsById     ($subnetId);

/* reset VLAN number! */
$SubnetDetails['VLAN'] = subnetGetVLANdetailsById($SubnetDetails['vlanId']);

/** 
 * Parse IP addresses
 *
 * We provide subnet and mask, all other is calculated based on it (subnet, broadcast,...)
 */
$SubnetParsed = parseIpAddress ( transform2long($SubnetDetails['subnet']), $SubnetDetails['mask']);

/* Calculate free / used etc */
$CalculateSubnetDetails = calculateSubnetDetails ( gmp_strval(sizeof($ipaddresses)), $SubnetDetails['mask'], $SubnetDetails['subnet'] );

/* /31 fix! */
if($CalculateSubnetDetails['maxhosts'] == 0) {
	$CalculateSubnetDetails['maxhosts']  = 2;
	$CalculateSubnetDetails['freehosts'] = 2;
}

/* /32 fix! */
if($CalculateSubnetDetails['maxhosts'] == -1) {
	$CalculateSubnetDetails['maxhosts']  = 1;
	$CalculateSubnetDetails['freehosts'] = 1;
}
?>

<!--
content print!
-->

<div class="ipaddresses">	<!-- overlay div -->


<!-- for adding IP address! -->
<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>

<!-- 
subnet details upper table 
-->

<table class="ipaddress_subnet">
	<tr>
		<th>Subnet details</th>
		<td><?php print '<b>'. transform2long($SubnetDetails['subnet']) .'/'. $SubnetDetails['mask'] .'</b> ('. $SubnetParsed['netmask'] .')'; ?></td>
		<td rowspan="9" style="vertical-align:top;align:left"><?php include('subnetDetailsGraph.php'); ?></td>
	</tr>
	<tr>
		<th>Subnet Usage</th>
		<td>
			<?php print 'Used:  '. reformatNumber ($CalculateSubnetDetails['used']) .' | 
						 Free:  '. reformatNumber ($CalculateSubnetDetails['freehosts']) .' ('. $CalculateSubnetDetails['freehosts_percent']  .'%) | 
						 Total: '. reformatNumber ($CalculateSubnetDetails['maxhosts']); 
			?>
		</td>
	</tr>
	<tr>
		<th>Subnet description</th>
		<td><?php print $SubnetDetails['description']; ?></td>
	</tr>
	<tr>
		<th>VLAN</th>
		<td>
		<?php 
		if(empty($SubnetDetails['VLAN']['number']) || $SubnetDetails['VLAN']['number'] == 0) {
			$SubnetDetails['VLAN']['number'] = "/"; 
		}
		print $SubnetDetails['VLAN']['number'];
		
		if(!empty($SubnetDetails['VLAN']['name'])) {
			print ' - '.$SubnetDetails['VLAN']['name'];
		}
		if(!empty($SubnetDetails['VLAN']['description'])) {
			print ' ['. $SubnetDetails['VLAN']['description'] .']'; 
		}
		?>
		</td>
	</tr>
	
	<?php
	if(!empty($SubnetDetails['vrfId'])) {
		//get vrf details
		$vrf = getVRFdetailsById($SubnetDetails['vrfId']);
		//set text
		$vrfText = $vrf['name'];
		if(!empty($vrf['description'])) {
		$vrfText .= ' ['. $vrf['description'] .']';
		}
	
		print '<tr>'. "\n";
		print '	<th>VRF</th>'. "\n";
		print '	<td>'. $vrfText .'</td>'. "\n";
		print '</tr>'. "\n";	
	}
	
	?>

	<?php
	/* Are IP requests allowed? */
	
	/* get all site settings */
	$settings = getAllSettings();

	if ($settings['enableIPrequests'] == 1) {
		print '<tr>' . "\n";
		print '	<th>IP requests</th>' . "\n";
		if($SubnetDetails['allowRequests'] == 1) {
			print '	<td>enabled</td>' . "\n";
		}
		else {
			print '	<td>disabled</td>' . "\n";			
		}
		print '</tr>' . "\n";
	}	
	
	/* CSV import subnet if user not Operator */
	
	/* check if user can modify settings */
	$viewer = isUserViewer();
	
	if(!$viewer) {
		print '<tr class="info csvImport">'. "\n";
		print '	<td><img src="css/images/upload.png" class="csvImport" title="Import IP addresses to subnet from XLS / CSV"></td>'. "\n";
		print '	<td>Import IP addresses to subnet from XLS / CSV file</td>'. "\n";
		print '</tr>'. "\n";
	}
	
	/* XLS export if size > 0 */	
	if((sizeof($ipaddresses) > 0) && !$viewer ) {
	
		print '<tr class="info csvExport">' . "\n";
		print '	<td><img src="css/images/download.png" class="csvExport" title="Export IP addresses in this subnet to XLS" subnetId="'. $SubnetDetails['id'] .'"></td>' . "\n";
		print '	<td>Export IP addresses in this subnet to XLS</td>' . "\n";
		print '</tr>' . "\n";
	
	

		//selected fields
		print '<tr class="" style="display:none" id="selectFields">'. "\n";
		print '	<td></td>'. "\n";
		print '	<td style="padding:10px 10px;">Select fields to export:'. "\n";
		
		print '<form id="selectExportFields">';
		
		# IP addr - mandatory
			print '<input type="checkbox" name="ip_addr"	checked> IP address<br>';
		# state
		if(in_array('state', $setFields)) {
			print '<input type="checkbox" name="state" 		checked> IP state<br>';
		}
		# description - mandatory
		print '<input type="checkbox" name="description"checked> Description<br>';
		# hostname - mandatory
		print '<input type="checkbox" name="dns_name" 	checked> Hostname<br>';
		# mac 
		if(in_array('mac', $setFields)) {
		print '<input type="checkbox" name="mac" 		checked> MAC address<br>';
		}
		# owner
		if(in_array('owner', $setFields)) {
		print '<input type="checkbox" name="owner" 		checked> Owner<br>';
		}
		# switch
		if(in_array('switch', $setFields)) {
		print '<input type="checkbox" name="switch" 	checked> Switch<br>';
		}
		# port
		if(in_array('port', $setFields)) {
		print '<input type="checkbox" name="port" 		checked> Port<br>';
		}
		# note
		if(in_array('note', $setFields)) {
		print '<input type="checkbox" name="note" 		checked> Note<br>';
		}

		print '<input type="submit" value="Export">';
		
		print '</form>';
		
		print '</td>'. "\n";
		print '</tr>'. "\n";	
	
	
	}
	
	/* Edit subnet for admins */
	if(checkAdmin(false)) {
		print '<tr class="info edit_subnet">'. "\n";
		print '	<td><img src="css/images/edit.png" class="edit_subnet" title="Edit subnet properties"></td>'. "\n";
		print '	<td>Edit properties for this subnet</td>'. "\n";
		print '</tr>'. "\n";		
	}
		
	/* add IP address */
	if(!$viewer) {
		print '<tr class="info add_ipaddress">' . "\n";
		print '	<td>'. "\n";
		
		if( (isSubnetWriteProtected($SubnetDetails['id'])) && !checkAdmin(false)) {
		print ' <img class="add_ipaddress_lock" src="css/images/lock.png" title="Subnet is locked for writing"></td>'. "\n";
		print '	<td> Subnet is locked for writing '. "\n";
		}
		else {
		print ' <img class="add_ipaddress" src="css/images/add.png" id2="" title="Add new IP address"></td>' . "\n";
		print '	<td> Add new IP address '. "\n";
		}
		
		/* if locked subnet user is not admin or user is viewer they can request IP address!  */
		if( (isSubnetWriteProtected($SubnetDetails['id'])) && !checkAdmin(false) ) {
		print '<tr class="info request_ipaddress">' . "\n";
		print ' <td><img class="request_ipaddress" src="css/images/add.png" title="Request new IP address"></td>'. "\n";
		print '	<td> Request new IP address </td>'. "\n";
		print '</tr>'. "\n";		
		}
		
		print '</td>' . "\n";
		print '</tr> ' . "\n";
	
		print '<!-- addnew holder -->' . "\n";
		print '<tr class="addnew">' . "\n";
		print '	<td></td>' . "\n";
		print '	<td><div class="addnew normalTable"></div></td>' . "\n";
		print '</tr>' . "\n";
	}
	/* viewer can request ip addresses! */
	else {
		print '<tr class="info request_ipaddress">' . "\n";
		print ' <td><img class="request_ipaddress" src="css/images/add.png" title="Request new IP address"></td>'. "\n";
		print '	<td> Request new IP address </td>'. "\n";
		print '</tr>'. "\n";	
		
		print '<!-- addnew holder -->' . "\n";
		print '<tr class="addnew">' . "\n";
		print '	<td></td>' . "\n";
		print '	<td><div class="addnew normalTable"></div></td>' . "\n";
		print '</tr>' . "\n";			
	}
	
	/* Edit subnet holder */
	if(checkAdmin(false)) {
		print '<!-- addnew holder -->' . "\n";
		print '<tr class="edit_subnet">' . "\n";
		print '	<td></td>' . "\n";
		print '	<td><div class="edit_subnet"></div></td>' . "\n";
		print '</tr>' . "\n";		
	}
	
	?>

</table>	<!-- end subnet table -->
<br>


<!-- 
print IP address table  
-->

<div class="ipaddresses_overlay normalTable">
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
	print '<th>IP address</th>'. "\n";
	# hostname - mandatory
	print '<th>Hostname</th>'. "\n";
	# MAC address	
	if(in_array('mac', $setFields)) {
		print '<th></th>'. "\n";
	}
	# Description- mandatory
	print '<th>Description</th>'. "\n";
	# note
	if(in_array('note', $setFields)) {
		print '<th></th>'. "\n";
	}	
	# switch
	if(in_array('switch', $setFields)) {
		print '<th>Switch</th>'. "\n";
	}	
	# port
	if(in_array('port', $setFields)) {
		print '<th>Port</th>'. "\n";
	}
	# owner
	if(in_array('owner', $setFields)) {
		print '<th>Owner</th>'. "\n";
	}
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) {
			print '<th>'. $myField['name'] .'</th>'. "\n";
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
        /*	if first set network as first ip,  else provide current + previous 
		****************************************************************************/
        if ( $n == 0 ) {
            $unused = FindUnusedIpAddresses ( Transform2decimal($SubnetParsed['network']), $ipaddresses[$n]['ip_addr'], $type );   
        }
        else {
            $unused = FindUnusedIpAddresses ( $ipaddresses[$n-1]['ip_addr'], $ipaddresses[$n]['ip_addr'], $type );
        }
    
        /*	if there is some result for unused print it 
        ****************************************************/
        if ( $unused  ) {
            print '<tr class="th"><td></td><td colspan="'. $colspan['ipaddr'] .'" class="unused">'. $unused['ip'] . ' (' . $unused['hosts'] .')</td></tr>'. "\n";
        }
        
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
				print '<td>'. $ipaddress[$myField['name']] .'</td>'. "\n";
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

        /*	if last one return ip address and broadcast IP 
        ****************************************************/
        if ( $n == $m ) 
        {   
            $unused = FindUnusedIpAddresses ( $ipaddresses[$n]['ip_addr'], Transform2decimal($SubnetParsed['broadcast']), $type, 1 );
            if ( $unused  ) {
                print '<tr class="th"><td></td><td colspan="'. $colspan['unused'] .'" class="unused">'. $unused['ip'] . ' (' . $unused['hosts'] .')</td><td colspan=2></td></tr>'. "\n";
            }    
        }

    /* next IP address for free check */
    $n++;
    }
}

?>

</table>	<!-- end IP address table -->
</div>		<!-- end IP address table overlay div -->

</div>		<!--  end ipaddresses overlay --> 


<!-- slide to top -->
<script type="text/javascript" src="js/jquery.slideto.v1.1.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('img.edit_ipaddress,img.delete_ipaddress,img.mail_ipaddress').slideto({
		target : '.header', 
		speed  : 'fast'
	});
});
</script>