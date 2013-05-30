<?php

/**
 * Script to print devices
 ***************************/

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get current devices */
$switches = getAllUniqueSwitches();

?>

<h4><?php print _('Device management'); ?></h4>
<hr>
<button class='btn btn-small editSwitch' data-action='add'   data-switchid='' style='margin-bottom:10px;'><i class='icon-gray icon-plus'></i> <?php print _('Add device'); ?></button>

<?php
/* first check if they exist! */
if(sizeof($switches) == 0) {
	print '	<div class="alert alert-warn alert-absolute">'._('No devices configured').'!</div>'. "\n";
}
/* Print them out */
else {

	print '<table id="switchManagement" class="table table-striped table-auto table-top">';

	#headers
	print '<tr>';
	print '	<th>'._('Hostname').'</th>';
	print '	<th>'._('IP address').'</th>';
	print '	<th>'._('Type').'</th>';
	print '	<th>'._('Vendor').'</th>';
	print '	<th>'._('Model').'</th>';
	print '	<th>'._('SW version').'</th>';
	print '	<th>'._('Description').'</th>';
	print '	<th><i class="icon-gray icon-info-sign" rel="tooltip" title="'._('Shows in which sections device will be visible for selection').'"></i> '._('Sections').'</th>';
	print '	<th></th>';
	print '</tr>';

	foreach ($switches as $switch) {

	//get switch details
	$switchDetails = getSwitchDetailsByHostname($switch['hostname']);
	
	//print details
	print '<tr>'. "\n";
	
	print '	<td>'. $switchDetails['hostname'] .'</td>'. "\n";
	print '	<td>'. $switchDetails['ip_addr'] .'</td>'. "\n";
	print '	<td>'. TransformSwitchType($switchDetails['type']) .'</td>'. "\n";
	print '	<td>'. $switchDetails['vendor'] .'</td>'. "\n";
	print '	<td>'. $switchDetails['model'] .'</td>'. "\n";
	print '	<td>'. $switchDetails['version'] .'</td>'. "\n";
	print '	<td class="description">'. $switchDetails['description'] .'</td>'. "\n";
	
	//sections
	print '	<td class="sections">';
		$temp = explode(";",$switchDetails['sections']);
		if( (sizeof($temp) > 0) && (!empty($temp[0])) ) {
		foreach($temp as $line) {
			$section = getSectionDetailsById($line);
			if(!empty($section)) {
			print '<div class="switchSections">'. $section['name'] .'</div>'. "\n";
			}
		}
		}
	
	print '	</td>'. "\n";
	
	print '	<td class="actions">'. "\n";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-small editSwitch' data-action='edit'   data-switchid='$switchDetails[id]'><i class='icon-gray icon-pencil'></i></button>";
	print "		<button class='btn btn-small editSwitch' data-action='delete' data-switchid='$switchDetails[id]'><i class='icon-gray icon-remove'></i></button>";
	print "	</div>";
	print '	</td>'. "\n";
	
	print '</tr>'. "\n";

	}
	print '</table>';
}

?>


<!-- edit result holder -->
<div class="switchManagementEdit"></div>
