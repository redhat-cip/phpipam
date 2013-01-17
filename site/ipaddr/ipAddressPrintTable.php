<script type="text/javascript">
/* fix for ajax-loading tooltips */
$('body').tooltip({ selector: '[rel=tooltip]' });
</script>

<?php

/**
 * Print sorted IP addresses
 ***********************************************************************/
 
/* get posted subnet, die if it is not provided! */
if($_REQUEST['subnetId']) { $subnetId = $_REQUEST['subnetId']; }

/* direct call */
if(!isset($_POST['direction'])) {
	$sort['direction'] = 'asc';
	$sort['field']	   = 'ip_addr';
	
	$sort['directionNext'] = "desc";
}
else {
	/* use required functions */
	require_once('../../functions/functions.php');
	
	/* format posted values! */
	$tmp = explode("|", $_POST['direction']);

	$sort['field'] 	   = $tmp[0];
	$sort['direction'] = $tmp[1];	

	if($sort['direction'] == "asc") { $sort['directionNext'] = "desc"; }
	else 							{ $sort['directionNext'] = "asc"; }	
	
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
$title = "IP addresses in subnet ";	# prefix for multiple subnets
if(sizeof($slaves) == 0) { $ipaddresses   = getIpAddressesBySubnetIdSort ($subnetId, $sort['field'], $sort['direction']);  }
else					 { $ipaddresses   = getIpAddressesBySubnetIdSlavesSort ($subnetId, $sort['field'], $sort['direction']);	$title = "All IP addresses belonging to ALL nested subnets "; }
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

/* 
if result not empty use first IP address in subnet to identify type 
else use subnet
*/
$type = IdentifyAddress( $SubnetDetails['subnet'] );

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

/* For page repeats */
$m = 1;
# how many per page
if(sizeof($settings) == 0) { $settings = getAllSettings(); }
$pageLimit = $settings['printLimit'];

if($pageLimit == "0")		{ $pageLimit = "100000000"; }
else if(empty($pageLimit)) 	{ $pageLimit = "10"; }

$sizeIP = sizeof($ipaddresses);				# number of all
$repeats   = ceil($sizeIP / $pageLimit); # times to repeat body

?>
<br>
<h4><?php print $title; ?>
<?php if($sizeIP  > $pageLimit) { print "(<span class='stran'>Page 1/$repeats</span>)"; }  ?>
<?php
# next / previous
$colspanStran['unused'] = $colspan['unused']+1;
if($sizeIP  > $pageLimit) { ?>
<div class='btn-toolbar pull-right'>
	<div class="btn-group">
		<a href="" class="btn btn-mini" id="prevItem" rel="tooltip" title="Previous page"><i class="icon-gray icon-chevron-left"></i></a>
		<a href="" class="btn btn-mini" id="nextItem" rel="tooltip" title="Next page"><i class="icon-gray icon-chevron-right"></i></a>
	</div>
</div>
<?php } ?>

<?php
# jump to page
if($sizeIP  > $pageLimit) { 
	print "<div class='pull-right'>";
	print "<select name='jumptoPage' class='jumptoPage' style='width:auto;'>";
	for($m=0; $m<$repeats; $m++) {
		$p = $m+1;
		print "<option value='page-$m'>Page $p</option>";
	}
	print "</select>";
	print "</div>";
}
?>
</h4>

<table class="ipaddresses normalTable table table-striped table-condensed table-hover table-full table-top">

<!-- headers -->
<tbody>
<tr class="th">

<?php
	# set sort icon!
	if($sort['direction'] == 'asc') 	{ $icon = "<i class='icon-gray icon-chevron-down'></i> "; }
	else								{ $icon = "<i class='icon-gray icon-chevron-up'></i> "; }

	# IP address - mandatory
										  print "<th class='s_ipaddr'><a href='' data-id='ip_addr|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip' title='Sort by IP address'>IP address "; 	if($sort['field'] == "ip_addr") 	print $icon;  print "</a></th>";
	# hostname - mandatory
										  print "<th><a href='' data-id='dns_name|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='Sort by hostname'					>Hostname "; 	if($sort['field'] == "dns_name") 	print $icon;  print "</a></th>";
	# MAC address	
	if(in_array('mac', $setFields)) 	{ print "<th></th>"; }
	# Description - mandatory
										  print "<th><a href='' data-id='description|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='Sort by description'			>Description "; if($sort['field'] == "description") print $icon;  print "</a></th>";
	# note
	if(in_array('note', $setFields)) 	{ print "<th></th>"; }	
	# switch
	if(in_array('switch', $setFields)) 	{ print "<th><a href='' data-id='switch|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='Sort by switch'						>Device "; 		if($sort['field'] == "switch") 		print $icon;  print "</a></th>"; }	
	# port
	if(in_array('port', $setFields)) 	{ print "<th><a href='' data-id='port|$sort[directionNext]'   class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='Sort by port'  						>Port "; 		if($sort['field'] == "port") 		print $icon;  print "</a></th>"; }
	# owner
	if(in_array('owner', $setFields)) 	{ print "<th><a href='' data-id='owner|$sort[directionNext]'  class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip'  title='Sort by owner' 						>Owner "; 		if($sort['field'] == "owner") 		print $icon;  print "</a></th>"; }
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) 	{ print "<th><a href='' data-id='$myField[name]|$sort[directionNext]' class='sort' data-subnetId='$SubnetDetails[id]' rel='tooltip' title='Sort by $myField[name]'	>$myField[name] ";  if($sort['field'] == $myField['name']) print $icon;  print "</a></th>"; }
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

# if no IP is configured only display free subnet!
if (sizeof($ipaddresses) == 0) {
    $unused = FindUnusedIpAddresses ( Transform2decimal($SubnetParsed['network']), Transform2decimal($SubnetParsed['broadcast']), $type, 1, "networkempty", $SubnetDetails['mask'] );
    print '<tr class="th"><td></td><td colspan="'. $colspan['unused'] .'" class="unused">'. $unused['ip'] . ' (' . reformatNumber ($unused['hosts']) .')</td><td colspan=2></td></tr>'. "\n";
}
# print IP address
else {
	$ipaddress = $ipaddresses;
    # break into 4 arrays
	$ipaddressesChunk = (array_chunk($ipaddresses, $pageLimit, true));

	$c = 0;		# count for print for pages - $c++ per page
	$n = 0;		# count for IP addresses - $n++ per IP address
	$g = 0;		# count for compress consecutive class
	
	foreach($ipaddressesChunk as $ipaddresses2) {

		if($c == 0) { print "<tbody class='ipPart page-$c'>"; }
		else 		{ print "<tbody class='ipPart page-$c' style='display:none;'>"; }
	
		foreach($ipaddresses2 as $ipaddress2)  
		{
        	/*	if first set network as first ip,  else provide current + previous 
	       	****************************************************************************/
	       	if ( $n == 0 ) 	{ $unused = FindUnusedIpAddresses ( Transform2decimal($SubnetParsed['network']), $ipaddresses[$n]['ip_addr'], $type, 0, "network", $SubnetDetails['mask']  ); }
	       	else 			{ $unused = FindUnusedIpAddresses ( $ipaddresses[$n-1]['ip_addr'], $ipaddresses[$n]['ip_addr'], $type, 0, "", $SubnetDetails['mask'] ); }
	       	
	       	/*	compress DHCP / Offline / Reserved - under constr!!!!
	       	******************************************/
	       	$compress = true;
	       	if($compress) {
	       	
		       	# hide if previous is same type
		       	if($n == 0) {
		       		if($ipaddresses[$n]['state'] == "3" && $ipaddresses[$n+1]['state'] == "3") 			{ $hiddenClass = "dhcp-hidden-$g"; }
		       		else																				{ $hiddenClass = ""; $g++; }  			       	
		       	}
		       	else {
		       		if($ipaddresses[$n-1]['state'] == "3" && $ipaddresses[$n]['state'] == "3") 			{ $hiddenClass = "dhcp-hidden-$g"; }
		       		else if ($ipaddresses[$n]['state'] == "3" && $ipaddresses[$n+1]['state'] == "3")	{ $hiddenClass = "dhcp-hidden-$g"; }
		       		else																				{ $hiddenClass = ""; $g++; }  	
		       	}
	       	}
    
	       	
	       	/*	if there is some result for unused print it - if sort == ip_addr
		    ****************************************************/
		    if ( $unused && ($sort['field'] == 'ip_addr') && $sort['direction'] == "asc" ) { 
        		print "<tr class='th'><td></td><td colspan='$colspan[ipaddr]' class='unused'>$unused[ip] ($unused[hosts])</td></tr>"; 
        	}
            
            
        	/* print IP address 
	        ********************/
        
	        /*	set class for reserved and offline - if set!
		    ***********************************************/
		    $stateClass = "";
	        if(in_array('state', $setFields)) {
		        if ($ipaddress[$n]['state'] == "0") 	 { $stateClass = "offline"; }
		        else if ($ipaddress[$n]['state'] == "2") { $stateClass = "reserved"; }
		        else if ($ipaddress[$n]['state'] == "3") { $stateClass = "DHCP"; }
		    }

		    # print IP address
		    # 
		    print "<tr class='$stateClass $hiddenClass'>";
		    print "	<td class='ipaddress'>".Transform2long( $ipaddress[$n]['ip_addr']);
		    if(in_array('state', $setFields)) 				{ print reformatIPState($ipaddress[$n]['state']); }	
		    print "</td>";

		    # resolve dns name if not provided, else print it - IPv4 only!
		    if ( (empty($ipaddress[$n]['dns_name'])) and ($settings['enableDNSresolving'] == 1) and (IdentifyAddress($ipaddress[$n]['ip_addr']) == "IPv4") ) {
			    $dnsResolved = ResolveDnsName ( $ipaddress[$n]['ip_addr'] );
			}
			else {
				$dnsResolved['class'] = "";
				$dnsResolved['name']  = $ipaddress[$n]['dns_name'];
			}														  print "<td class='$dnsResolved[class] hostname'>$dnsResolved[name]</td>";  		

			# Print mac address icon!
			if(in_array('mac', $setFields)) {
				if(!empty($ipaddress[$n]['mac'])) 					{ print "<td class='mac'><img class='info mac' src='css/images/lan.png' rel='tooltip' title='MAC: ".$ipaddress[$n]['mac']."'></td>"; }
				else 												{ print "<td class='mac'></td>"; }
			}
		
			# print description - mandatory
        													  		  print "<td class='description'>".$ipaddress[$n]['description']."</td>";	
		
       		# print info button for hover
       		if(in_array('note', $setFields)) {
        		if(!empty($ipaddress[$n]['note'])) 					{ print "<td><i class='icon-gray icon-comment' rel='tooltip' data-html='true' title='".str_replace("\n", "<br>",$ipaddress[$n]['note'])."'></td>"; }
        		else 											{ print "<td></td>"; }
        	}
	
        	# print switch
        	if(in_array('switch', $setFields)) 					{ 
	        	# get switch details
	        	$switch = getSwitchById ($ipaddress[$n]['switch']);
																  print "<td>".$switch['hostname']."</td>";
																}
		
			# print port
			if(in_array('port', $setFields)) 					{ print "<td>".$ipaddress[$n]['port']."</td>"; }
		
			# print owner
			if(in_array('owner', $setFields)) 					{ print "<td>".$ipaddress[$n]['owner']."</td>"; }
		
			# print custom fields 
			if(sizeof($myFields) > 0) {
				foreach($myFields as $myField) 					{ print "<td class='customField'>".$ipaddress[$n][$myField['name']]."</td>"; }
			}
		
			# print action links if user can edit 
			if(!$viewer) {		
				print "<td class='btn-actions'>";
				print "	<div class='btn-toolbar'>";
				print "	<div class='btn-group'>";
				#locked for writing
				if( (isSubnetWriteProtected($SubnetDetails['id'])) && !checkAdmin(false)) {
					print "		<a class='edit_ipaddress   btn btn-mini disabled' rel='tooltip' title='Edit IP address details (disabled)'>			<i class='icon-gray icon-pencil'>  </i></a>";
					print "		<a class='search_ipaddress btn btn-mini         "; if(strlen($dnsResolved['name']) == 0) { print "disabled"; } print "' href='tools/search/$dnsResolved[name]' "; if(strlen($dnsResolved['name']) != 0) { print "rel='tooltip' title='Search same hostnames in db'"; } print ">	<i class='icon-gray icon-search'></i></a>";
					print "		<a class='mail_ipaddress   btn btn-mini          ' href='#' data-id='".$ipaddress[$n]['id']."' rel='tooltip' title='Send mail notification'>		<i class='icon-gray icon-envelope'></i></a>";
					print "		<a class='delete_ipaddress btn btn-mini disabled' rel='tooltip' title='Delete IP address (disabled)'>				<i class='icon-gray icon-remove'>  </i></a>";										
				}
				# unlocked
				else {
					print "		<a class='edit_ipaddress   btn btn-mini modIPaddr' data-action='edit'   data-subnetId='".$ipaddress[$n]['subnetId']."' data-id='".$ipaddress[$n]['id']."' href='#' 												   rel='tooltip' title='Edit IP address details'>		<i class='icon-gray icon-pencil'>  </i></a>";
					print "		<a class='search_ipaddress btn btn-mini         "; if(strlen($dnsResolved['name']) == 0) { print "disabled"; } print "' href='tools/search/$dnsResolved[name]' "; if(strlen($dnsResolved['name']) != 0) { print "rel='tooltip' title='Search same hostnames in db'"; } print ">	<i class='icon-gray icon-search'></i></a>";
					print "		<a class='mail_ipaddress   btn btn-mini          ' href='#' data-id='".$ipaddress[$n]['id']."' rel='tooltip' title='Send mail notification'>		<i class='icon-gray icon-envelope'></i></a>";
					print "		<a class='delete_ipaddress btn btn-mini modIPaddr' data-action='delete' data-subnetId='".$ipaddress[$n]['subnetId']."' data-id='".$ipaddress[$n]['id']."' href='#' id2='".Transform2long($ipaddress[$n]['ip_addr'])."' rel='tooltip' title='Delete IP address'>				<i class='icon-gray icon-remove'>  </i></a>";					
				}
				print "	</div>";
				print "	</div>";
				print "</td>";		
			}
			else {
				print '<td></td>';
			}
		
			print '</tr>'. "\n";
	            
			/*	if last one return ip address and broadcast IP 
			****************************************************/
			if ( $n == $m ) 
			{   
            	$unused = FindUnusedIpAddresses ( $ipaddresses[$n]['ip_addr'], Transform2decimal($SubnetParsed['broadcast']), $type, 1, "broadcast", $SubnetDetails['mask'] );
            	if ( $unused  ) {
            	    print '<tr class="th"><td></td><td colspan="'. $colspan['unused'] .'" class="unused">'. $unused['ip'] . ' (' . $unused['hosts'] .')</td><td colspan=2></td></tr>'. "\n";
            	}    
            }	   
            
            /* next IP address for free check */
	        $n++;         
        }
    
    print "</tbody>";	
	$c++;
	}	
}
?>

</table>	<!-- end IP address table -->

<?php
# next / previous
$colspanStran['unused'] = $colspan['unused']+1;
if($sizeIP  > $pageLimit) { ?>
<hr>
<div class='btn-toolbar pull-right toolbar-ip'>
	<div class="btn-group">
		<a href="" class="btn btn-mini" id="prevItem" rel="tooltip" title="Previous page"><i class="icon-gray icon-chevron-left"></i></a>
		<a href="" class="btn btn-mini" id="nextItem" rel="tooltip" title="Next page"><i class="icon-gray icon-chevron-right"></i></a>
	</div>
</div>
<?php } ?>


<?php
# visual display of used IP addresses
if($settings['visualLimit'] > 0) {
	if($settings['visualLimit'] <= $SubnetDetails['mask']) {
		include_once('ipAddressPrintVisual.php');
	}
}
?>