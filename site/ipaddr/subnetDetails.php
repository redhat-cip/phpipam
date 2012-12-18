<?php
/**
 * Main script to display IP addresses in content div of subnets table!
 ***********************************************************************/

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get posted subnet, die if it is not provided! */
if($_REQUEST['subnetId']) { $subnetId = $_REQUEST['subnetId']; }

/* get custom subnet fields */
$customSubnetFields = getCustomSubnetFields();
$customSubnetFieldsSize = sizeof($customSubnetFields);

/**
 * Get all ip addresses in subnet and subnet details!
 */
$ipaddresses   = getIpAddressesBySubnetId ($subnetId); 	# for stats only
$SubnetDetails = getSubnetDetailsById     ($subnetId);

# die if empty!
if(sizeof($SubnetDetails) == 0) { die('<div class="alert alert-error">Subnet does not exist!</div>'); }

# reset VLAN number!
$SubnetDetails['VLAN'] = subnetGetVLANdetailsById($SubnetDetails['vlanId']);

# get all site settings
$settings = getAllSettings();

/** 
 * Parse IP addresses
 *
 * We provide subnet and mask, all other is calculated based on it (subnet, broadcast,...)
 */
$SubnetParsed = parseIpAddress ( transform2long($SubnetDetails['subnet']), $SubnetDetails['mask']);

/* Calculate free / used etc */
$CalculateSubnetDetails = calculateSubnetDetails ( gmp_strval(sizeof($ipaddresses)), $SubnetDetails['mask'], $SubnetDetails['subnet'] );

$rowSpan = 10 + $customSubnetFieldsSize;
?>

<!-- content print! -->

<!-- for adding IP address! -->
<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>

<!-- subnet details upper table -->
<h4>Subnet details</h4>
<hr>

<table class="ipaddress_subnet table-condensed table-full">
	<tr>
		<th>Subnet details</th>
		<td><?php print '<b>'. transform2long($SubnetDetails['subnet']) ."/$SubnetDetails[mask]</b> ($SubnetParsed[netmask])"; ?></td>
		<td rowspan="<?php print $rowSpan; ?>" style="vertical-align:top;align:left">
		<!-- container -->
		<div id="pieChart" style="height:200px;width:400px;float:right;"></div>
		<?php include('subnetDetailsGraph.php'); ?>
		</td>
	</tr>
	<tr>
		<th>Hierarchy</th>
		<td>
			<?php printBreadCrumbs($_REQUEST); ?>
		</td>
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
		<td><?php print html_entity_decode($SubnetDetails['description']); ?></td>
	</tr>
	<tr>
		<th>VLAN</th>
		<td>
		<?php 
		if(empty($SubnetDetails['VLAN']['number']) || $SubnetDetails['VLAN']['number'] == 0) { $SubnetDetails['VLAN']['number'] = "/"; }	# Display fix for emprt VLAN
		print $SubnetDetails['VLAN']['number'];
		
		if(!empty($SubnetDetails['VLAN']['name'])) 		  { print ' - '.$SubnetDetails['VLAN']['name']; }									# Print name if provided
		if(!empty($SubnetDetails['VLAN']['description'])) { print ' ['. $SubnetDetails['VLAN']['description'] .']'; }						# Print description if provided
		?>
		</td>
	</tr>
	
	<?php
	if(!empty($SubnetDetails['vrfId'])) {
		# get vrf details
		$vrf = getVRFdetailsById($SubnetDetails['vrfId']);
		# set text
		$vrfText = $vrf['name'];
		if(!empty($vrf['description'])) { $vrfText .= " [$vrf[description]]";}
	
		print "<tr>";
		print "	<th>VRF</th>";
		print "	<td>$vrfText</td>";
		print "</tr>";
	}

	/* Are IP requests allowed? */
	if ($settings['enableIPrequests'] == 1) {
		print "<tr>";
		print "	<th>IP requests</th>";
		if($SubnetDetails['allowRequests'] == 1) 	{ print "	<td>enabled</td>"; }		# yes
		else 										{ print "	<td>disabled</td>";}		# no
		print "</tr>";
	}
	
	/* print custom subnet fields if any */
	if(sizeof($customSubnetFieldsSize) > 0) {
		foreach($customSubnetFields as $key=>$field) {
			if(strlen($SubnetDetails[$key])>0) {
			print "<tr>";
			print "	<th>$key</th>";
			print "	<td>$SubnetDetails[$key]</td>";
			print "</tr>";
			}
		}
	}
	
	
	/* action button groups */
	print "<tr>";
	print "	<th>Actions</th>";
	print "	<td>";

	print "	<div class='btn-toolbar'>";
	print "	<div class='btn-group'>";
	# import form XLS
	$viewer = isUserViewer();
	
	# we have:
	#	add new		| admin, operator, viewer print locked
	#	csv import  | admin, operator (if locked disabled and print disabled)
	#	locked		| admin, for other print that locked
	#	CSV export	| admin, operator, viewer disabled
	#	edit subnet	| admin, operator (if not locked
	# 	request new | operator only, other disabled
	
	
	# admin and operator
	if(!$viewer) {
		# if locked disable for operators
		if(isSubnetWriteProtected($SubnetDetails['id'])) 
		{
			# admin - locked
			if(checkAdmin(false)) 
			{
				print "<a class='modIPaddr btn btn-small btn-success'		href='' rel='tooltip' title='Add new IP address' data-action='add' data-subnetId='$SubnetDetails[id]' data-id=''> 																<i class='icon-plus icon-plus icon-white'></i></a> ";	# add IP
				print "<button class='btn btn-small btn-inverse disabled' 	href='' rel='tooltip' title='Subnet is locked for writing for non-admins'>																										<i class='icon-lock icon-white'></i></button> ";		# lock info
				print "<a class='edit_subnet btn btn-small' 				href='' rel='tooltip' title='Edit subnet properties'						data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]' data-action='edit'>	<i class='icon-pencil'></i></a>";						# edit subnet
				print "<a class='csvImport btn btn-small'     				href='' rel='tooltip' title='Import IP addresses'							data-subnetId='$SubnetDetails[id]'>																	<i class='icon-upload'></i></a>";						# import
				print "<a class='csvExport btn btn-small' 					href='' rel='tooltip' title='Export IP addresses'							data-subnetId='$SubnetDetails[id]'>																	<i class='icon-download'></i></a>";						# export		
			}
			# operator - locked
			else 
			{
				print "<button class='btn btn-small btn-inverse disabled' 	href='' rel='tooltip' title='Subnet is locked for writing'>												<i class='icon-lock icon-white'></i></button> ";# lock info
				print "<a class='disabled btn btn-small btn-success' 		href='' rel='tooltip' title='Add new IP address (not allowed)' > 										<i class='icon-plus icon-white'></i></a> ";		# add IP
				print "<a class='disabled btn btn-small'   					href='' rel='tooltip' title='Edit subnet properties (not allowed)'>										<i class='icon-pencil'></i></a>";				# edit subnet
				print "<a class='disabled btn btn-small'     				href='' rel='tooltip' title='Import IP addresses (not allowed)'		data-subnetId='$SubnetDetails[id]'>	<i class='icon-upload'></i></a>";				# import
				print "<a class='csvExport btn btn-small'     				href='' rel='tooltip' title='Export IP addresses'					data-subnetId='$SubnetDetails[id]'>	<i class='icon-download'></i></a>";				# export
				if($SubnetDetails['allowRequests'] == 1) 
				{
					print "<a class='request_ipaddress btn btn-small btn-success' 	href='' rel='tooltip' title='Request new IP address' 				data-subnetId='$SubnetDetails[id]'>	<i class='icon-plus-sign icon-white'>  </i></a>";	# request					
				}
			}		
		}
		# enable all  not locked
		else 
		{
			# admin - unlocked
			if(checkAdmin(false)) 
			{
				print "<a class='modIPaddr btn btn-small btn-success' 		href='' rel='tooltip' title='Add new IP address' 					data-subnetId='$SubnetDetails[id]' data-action='add' data-id=''>									<i class='icon-plus icon-white'></i></a> ";		# add IP
				print "<a class='edit_subnet btn btn-small' 				href='' rel='tooltip' title='Edit subnet properties'				data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]' data-action='edit'>	<i class='icon-pencil'></i></a>";						# edit subnet
				print "<a class='csvImport btn btn-small'     				href='' rel='tooltip' title='Import IP addresses'					data-subnetId='$SubnetDetails[id]'>																	<i class='icon-upload'></i></a>";						# import
				print "<a class='csvExport btn btn-small' 					href='' rel='tooltip' title='Export IP addresses'					data-subnetId='$SubnetDetails[id]'>																	<i class='icon-download'></i></a>";						# export		
			}
			# operator - unlocked
			else {
				print "<a class='modIPaddr btn btn-small btn-success' 		href='' rel='tooltip' title='Add new IP address' 					data-subnetId='$SubnetDetails[id]' data-action='add' data-id=''>									<i class='icon-plus icon-white'></i></a> ";		# add IP
				print "<a class='btn btn-small disabled' 					href='' rel='tooltip' title='Edit subnet properties (not allowed)'	data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]' data-action='edit'>	<i class='icon-pencil'></i></a>";				# edit subnet
				print "<a class='btn btn-small dsabled'     				href='' rel='tooltip' title='Import IP addresses (not allowed)'		data-subnetId='$SubnetDetails[id]'>																	<i class='icon-upload'></i></a>";				# import
				print "<a class='csvExport btn btn-small'     				href='' rel='tooltip' title='Export IP addresses'					data-subnetId='$SubnetDetails[id]'>																	<i class='icon-download'></i></a>";				# export
			}
		}
	}
	# viewer
	else 
	{
				print "<a class='btn btn-small disabled' 					href='' rel='tooltip' title='Add new IP address (not allowed)' >		<i class='icon-plus icon-locked'></i></a> ";	# add IP
				print "<a class='btn btn-small disabled'   					href='' rel='tooltip' title='Edit subnet properties (not allowed)'>		<i class='icon-pencil'></i></a>";				# edit subnet
				print "<a class='btn btn-small disabled'     				href='' rel='tooltip' title='Import IP addresses (not allowed)'>		<i class='icon-upload'></i></a>";				# import
				print "<a class='btn btn-small disabled'     				href='' rel='tooltip' title='Export IP addresses (not allowed)'>		<i class='icon-download'></i></a>";				# export
				if($SubnetDetails['allowRequests'] == 1) 
				{
				print "<a class='request_ipaddress btn btn-small btn-success' href='' rel='tooltip' title='Request new IP address' data-subnetId='$SubnetDetails[id]'>	<i class='icon-plus-sign icon-white'>  </i></a>";	# request					
				}
	}
	
	print "	</div>";
	print "	</div>";
	
	print "	</td>";
	print "</tr>";
	
	?>

</table>	<!-- end subnet table -->
<br>