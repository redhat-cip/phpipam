<?php

/**
 *	Print all available VRFs and configurations
 ************************************************/

/* verify that user is admin */
checkAdmin();


/* get all available VRFs */
$allVRFs = getAllVRFs ();
?>

<h4><?php print _('Manage VRF'); ?></h4>
<hr><br>

<button class='btn btn-small vrfManagement' data-action='add'   data-vrfid=''  style='margin-bottom:10px;'><i class='icon-gray icon-plus'></i> <?php print _('Add VRF'); ?></button>

<!-- vrfs -->
<?php

/* first check if they exist! */
if(!$allVRFs) {
	print '	<div class="alert alert-warn alert-absolute">'._('No VRFs configured').'!</div>'. "\n";
}
/* Print them out */
else {

	print '<table id="vrfManagement" class="table table-striped table-top table-hover table-auto">'. "\n";

	# headers
	print '<tr>'. "\n";
	print '	<th>'._('Name').'</th>'. "\n";
	print '	<th>'._('RD').'</th>'. "\n";
	print '	<th>'._('Description').'</th>'. "\n";
	print '	<th></th>'. "\n";
	print '</tr>'. "\n";


	foreach ($allVRFs as $vrf) {	
	//print details
	print '<tr>'. "\n";
	
	print '	<td class="name">'. $vrf['name'] .'</td>'. "\n";
	print '	<td class="rd">'. $vrf['rd'] .'</td>'. "\n";
	print '	<td class="description">'. $vrf['description'] .'</td>'. "\n";

	print "	<td class='actions'>";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-small vrfManagement' data-action='edit'   data-vrfid='$vrf[vrfId]'><i class='icon-gray icon-pencil'></i></button>";
	print "		<button class='btn btn-small vrfManagement' data-action='delete' data-vrfid='$vrf[vrfId]'><i class='icon-gray icon-remove'></i></button>";
	print "	</div>";
	print "	</td>";	
	print '</tr>'. "\n";

	}

	print '</table>'. "\n";
}
?>

<!-- edit result holder -->
<div class="vrfManagementEdit"></div>