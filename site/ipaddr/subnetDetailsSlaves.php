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
if(sizeof($SubnetDetails) == 0) { die('<div class="alert alert-error">'._('Subnet does not exist').'!</div>'); }

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

# set rowspan
$rowSpan = 10 + $customSubnetFieldsSize;

# permissions
$permission = checkSubnetPermission ($subnetId);

# section permissions
$permissionsSection = checkSectionPermission ($SubnetDetails['sectionId']);

# if 0 die
if($permission == "0")	{ die("<div class='alert alert-error'>"._('You do not have permission to access this network')."!</div>"); }
?>

<!-- content print! -->

<!-- for adding IP address! -->
<div id="subnetId" style="display:none"><?php print $subnetId; ?></div>

<!-- subnet details upper table -->
<h4><?php print _('Subnet details'); ?></h4>
<hr>

<table class="ipaddress_subnet table-condensed table-full">
	<tr>
		<th><?php print _('Subnet details'); ?></th>
		<td><?php print '<b>'. transform2long($SubnetDetails['subnet']) ."/$SubnetDetails[mask]</b> ($SubnetParsed[netmask])"; ?></td>
		<td rowspan="<?php print $rowSpan; ?>" style="vertical-align:top;align:left">
		<!-- container -->
		<div id="pieChart" style="height:200px;width:400px;float:right;"></div>
		</td>
	</tr>
	<tr>
		<th><?php print _('Hierarchy'); ?></th>
		<td>
			<?php printBreadCrumbs($_REQUEST); ?>
		</td>
	</tr>
	<tr>
		<th><?php print _('Subnet description'); ?></th>
		<td><?php print html_entity_decode($SubnetDetails['description']); ?></td>
	</tr>
	<tr>
		<th><?php print _('Permission'); ?></th>
		<td><?php print parsePermissions($permission); ?></td>
	</tr>
	<tr>
		<th><?php print _('VLAN'); ?></th>
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
		print "	<th>"._('VRF')."</th>";
		print "	<td>$vrfText</td>";
		print "</tr>";
	}
	
	/* print custom subnet fields if any */
	if(sizeof($customSubnetFieldsSize) > 0) {
		foreach($customSubnetFields as $key=>$field) {
			if(strlen($SubnetDetails[$key]) > 0) {
			print "<tr>";
			print "	<th>$key</th>";
			print "	<td>$SubnetDetails[$key]</td>";
			print "</tr>";
			}
		}
	}
	
	
	/* action button groups */
	print "<tr>";
	print "	<th>"._('Actions')."</th>";
	print "	<td>";

	print "	<div class='btn-toolbar'>";
	print "	<div class='btn-group'>";
	
	# admin and operator
	if($permission == "1") {
		print "<button class='btn btn-small btn-inverse disabled' 	href='' rel='tooltip' title='"._('You do not have permissions to edit subnet')."'>	<i class='icon-lock icon-white'></i></button> ";	# lock info
		print "<a class='disabled btn btn-small'  					href=''>																			<i class='icon-pencil'></i></a>";					# edit subnet
		if($permissionsSection == 3) {
		print "<a class='edit_subnet btn btn-small '				href='' rel='tooltip' title='"._('Add new nested subnet')."' 	data-subnetId='$SubnetDetails[id]' data-action='add' data-id='' data-sectionId='$SubnetDetails[sectionId]'> 	<i class='icon-plus-sign'></i></a> ";		# add new child subnet
		}
		else {
		print "<a class='btn btn-small disabled' 					href=''> 																			<i class='icon-plus-sign'></i></a> ";			# add new child subnet
		}
	}
	else if ($permission == 2) {
		print "<a class='btn btn-small disabled' 					href='' rel='tooltip' title='"._('Edit subnet properties')."'		>				<i class='icon-pencil'></i></a>";		# edit subnet
		if($permissionsSection == 3) {
		print "<a class='edit_subnet btn btn-small '				href='' rel='tooltip' title='"._('Add new nested subnet')."' 		data-subnetId='$SubnetDetails[id]' data-action='add' data-id='' data-sectionId='$SubnetDetails[sectionId]'> <i class='icon-plus-sign'></i></a> ";		# add new child subnet
		}
		else {
		print "<a class='btn btn-small disabled' 					href='' rel='tooltip' title='"._('Add new nested subnet')."'		> 			<i class='icon-plus-sign'></i></a> ";			# add new child subnet
		}	
	}
	else if ($permission == 3) {
		print "<a class='edit_subnet btn btn-small' 				href='' rel='tooltip' title='"._('Edit subnet properties')."'		data-action='edit' data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]'>			<i class='icon-pencil'></i></a>";		# edit subnet
		if(checkAdmin (false, false)) {
		print "<a class='showSubnetPerm btn btn-small' 				href='' rel='tooltip' title='"._('Manage subnet permissions')."'	data-subnetId='$SubnetDetails[id]' data-sectionId='$SubnetDetails[sectionId]' data-action='show'>			<i class='icon-tasks'></i></a>";			# edit subnet
		}
		if($permissionsSection == 3) {
		print "<a class='edit_subnet btn btn-small '				href='' rel='tooltip' title='"._('Add new nested subnet')."' 		data-subnetId='$SubnetDetails[id]' data-action='add' data-id='' data-sectionId='$SubnetDetails[sectionId]'> <i class='icon-plus-sign'></i></a> ";		# add new child subnet
		}
		else {
		print "<a class='btn btn-small disabled' 					href=''> 			<i class='icon-plus-sign'></i></a> ";			# add new child subnet
		}	
	}
	
	print "	</div>";
	print "	</div>";
	
	print "	</td>";
	print "</tr>";
	
	?>

</table>	<!-- end subnet table -->
<br>