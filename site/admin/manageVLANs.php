<?php

/**
 *	Print all available VLANs and configurations
 ************************************************/

/* verify that user is admin */
checkAdmin();

/* get all available VLANSs */
$vlans = getAllVlans ();

/* get custom fields */
$custom = getCustomVLANFields();
?>

<h4><?php print _('Manage VLANs'); ?></h4>
<hr><br>

<!-- add new -->
<button class="btn btn-small editVLAN" data-action="add" data-vlanid="" style="margin-bottom:10px;"><i class="icon-gray icon-plus"></i> <?php print _('Add VLAN'); ?></button>

<?php
/* first check if they exist! */
if(!$vlans) {
	print '	<div class="alert alert-info alert-absolute">'._('No VLANs configured').'!</div>'. "\n";
}
else {
?>

<table id="vlanManagement" class="table table-striped table-top table-auto">
	<!-- headers -->
	<tr>
		<th><?php print _('Name'); ?></th>	
		<th><?php print _('Number'); ?></th>
		<th><?php print _('Description'); ?></th>
		<?php
		if(sizeof($custom) > 0) {
			foreach($custom as $field) {
				print "<th>$field[name]</th>";
			}
		}
		?>
		<th></th>
	</tr>

	<!-- VLANs -->
	<?php
	foreach ($vlans as $vlan) {
	
	//print details
	print '<tr>'. "\n";
	
	print '	<td class="name">'. $vlan['name'] .'</td>'. "\n";
	print '	<td class="number">'. $vlan['number'] .'</td>'. "\n";
	print '	<td class="description">'. $vlan['description'] .'</td>'. "\n";	
	
	if(sizeof($custom) > 0) {
		foreach($custom as $field) {
			print "<td>".$vlan[$field['name']]."</td>";
		}
	}
	
	print "	<td class='actions'>";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-small editVLAN' data-action='edit'   data-vlanid='$vlan[vlanId]'><i class='icon-gray icon-pencil'></i></button>";
	print "		<button class='btn btn-small editVLAN' data-action='delete' data-vlanid='$vlan[vlanId]'><i class='icon-gray icon-remove'></i></button>";
	print "	</div>";
	print "	</td>";	
	print '</tr>'. "\n";

	}
}
?>
</table>