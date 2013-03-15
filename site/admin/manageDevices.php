<?php

/**
 * Script to print devices
 ***************************/

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get current devices */
$switches = getAllUniqueSwitches();

?>

<h4>Device management</h4>
<hr>
<button class='btn btn-small editSwitch' data-action='add'   data-switchid='' style='margin-bottom:10px;'><i class='icon-gray icon-plus'></i> Add new</button>

<?php
/* first check if they exist! */
if(sizeof($switches) == 0) {
	print '	<div class="alert alert-warn alert-absolute">No switches configured!</div>'. "\n";
}
/* Print them out */
else {

	print '<table id="switchManagement" class="table table-striped table-auto table-top">';

	#headers
	print '<tr>';
	print '	<th>Hostname</th>';
	print '	<th>IP address</th>';
	print '	<th>Type</th>';
	print '	<th>Vendor</th>';
	print '	<th>Model</th>';
	print '	<th>SW version</th>';
	print '	<th>Description</th>';
	print '	<th><i class="icon-gray icon-info-sign" rel="tooltip" title="Shows in which sections device will be visible for selection"></i> Sections</th>';
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
