<?php

/**
 *	Print all available VLANs and configurations
 ************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


/* get all available VLANSs */
$vlans = getAllVLANs ();
?>

<h3>Manage VLANs</h3>


<div class="normalTable vlanManagement">
<table class="normalTable vlanManagement">

<!-- headers -->
<tr class="th">
	<th>Name</th>
	<th>Number</th>
	<th>Description</th>
	<th></th>
</tr>

<!-- VLANs -->
<?php

/* first check if they exist! */
if(!$vlans) {
	print '<tr class="th">'. "\n";
	print '	<td colspan="7">No VLANs configured!</td>'. "\n";
	print '</tr>'. "\n";
}
/* Print them out */
else {
	foreach ($vlans as $vlan) {
	
	//print details
	print '<tr>'. "\n";
	
	print '	<td class="name">'. $vlan['name'] .'</td>'. "\n";
	print '	<td class="number">'. $vlan['number'] .'</td>'. "\n";
	print '	<td class="description">'. $vlan['description'] .'</td>'. "\n";
	print '	<td class="actions">'. "\n";
	print '		<img src="css/images/edit.png" class="edit" vlanId="'. $vlan['vlanId'] .'" title="Edit VLAN details">'. "\n";
	print '		<img src="css/images/deleteIP.png" class="delete" vlanId="'. $vlan['vlanId'] .'" title="Delete VLAN">'. "\n";
	print '	</td>'. "\n";
	
	print '</tr>'. "\n";

	}
}
?>

<!-- add new -->
<tr class="add th">
	<td colspan="4" class="info">
	<img src="css/images/add.png" class="add" title="Add new VLAN">
	Add new VLAN
	</td>
</tr>

</table>
</div>


<!-- edit result holder -->
<div class="vlanManagementEdit"></div>