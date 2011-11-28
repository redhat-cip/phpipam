<?php

/**
 * Script to print switches
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* title */
print '<h3>Switch management</h3>'. "\n";

/* get current switches */
$switches = getAllUniqueSwitches();

?>


<div class="normalTable switchManagement">
<table class="normalTable switchManagement">

<!-- headers -->
<tr class="th">
	<th>Hostname</th>
	<th>IP address</th>
	<th>Vendor</th>
	<th>Model</th>
	<th>SW version</th>
	<th>Description</th>
	<th>Sections</th>
	<th></th>
</tr>


<!-- shitches -->
<?php

/* first check if they exist! */
if(sizeof($switches) == 0) {
	print '<tr class="th">'. "\n";
	print '	<td colspan="8">No switches configured!</td>'. "\n";
	print '</tr>'. "\n";
}
/* Print them out */
else {
	foreach ($switches as $switch) {

	//get switch details
	$switchDetails = getSwitchDetailsByHostname($switch['hostname']);
	
	//print details
	print '<tr>'. "\n";
	
	print '	<td>'. $switchDetails['hostname'] .'</td>'. "\n";
	print '	<td>'. $switchDetails['ip_addr'] .'</td>'. "\n";
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
			print '<div class="switchSections">'. $section['name'] .'</div>'. "\n";
		}
		}
	
	print '	</td>'. "\n";
	
	print '	<td class="actions">'. "\n";
	print '		<img src="css/images/edit.png" class="edit" switchId="'. $switchDetails['id'] .'" title="Edit switch details">'. "\n";
	print '		<img src="css/images/deleteIP.png" class="delete" switchId="'. $switchDetails['id'] .'" title="Delete switch">'. "\n";
	print '	</td>'. "\n";
	
	print '</tr>'. "\n";

	}
}
?>

<!-- add new -->
<tr class="add th">
	<td colspan="8" class="info">
	<img src="css/images/add.png" class="add" title="Add new switch">
	Add new switch
	</td>
</tr>

</table>
</div>


<!-- edit result holder -->
<div class="switchManagementEdit"></div>