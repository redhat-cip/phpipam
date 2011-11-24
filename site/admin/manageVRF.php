<?php

/**
 *	Print all available VRFs and configurations
 ************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


/* get all available VRFs */
$allVRFs = getAllVRFs ();
?>

<h3>Manage VRFs</h3>


<div class="normalTable vrfManagement">
<table class="normalTable vrfManagement">

<!-- headers -->
<tr>
	<th>Name</th>
	<th>RD</th>
	<th>Description</th>
	<th></th>
</tr>

<!-- vrfs -->
<?php

/* first check if they exist! */
if(!$allVRFs) {
	print '<tr class="th">'. "\n";
	print '	<td colspan="7">No VRFs configured!</td>'. "\n";
	print '</tr>'. "\n";
}
/* Print them out */
else {
	foreach ($allVRFs as $vrf) {
	
	//print details
	print '<tr>'. "\n";
	
	print '	<td class="name">'. $vrf['name'] .'</td>'. "\n";
	print '	<td class="rd">'. $vrf['rd'] .'</td>'. "\n";
	print '	<td class="description">'. $vrf['description'] .'</td>'. "\n";
	print '	<td class="actions">'. "\n";
	print '		<img src="css/images/edit.png" class="edit" vrfId="'. $vrf['vrfId'] .'" title="Edit VRF details">'. "\n";
	print '		<img src="css/images/deleteIP.png" class="delete" vrfId="'. $vrf['vrfId'] .'" title="Delete VRF">'. "\n";
	print '	</td>'. "\n";
	
	print '</tr>'. "\n";

	}
}
?>

<!-- add new -->
<tr class="add">
	<td colspan="4" class="info">
	<img src="css/images/add.png" class="add" title="Add new VRF">
	Add new VRF
	</td>
</tr>

</table>
</div>


<!-- edit result holder -->
<div class="vrfManagementEdit"></div>