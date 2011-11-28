<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all */
$allActiveRequests = getAllActiveIPrequests();

if(sizeof($allActiveRequests) == 0) {
	die('<h3>No active IP address requests!</h3>');
}
?>

<h3>List of all active IP addresses requests</h3>

<div class="normalTable requestedIPaddresses">
<table class="normalTable requestedIPaddresses">

<!-- headers -->
<tr class="th">
	<th></th>
	<th>IP address</th>
	<th>Subnet</th>
	<th>Hostname</th>
	<th>Description</th>
	<th>Requested by</th>
</tr>

<?php  
	
	foreach($allActiveRequests as $request) {
	
	//get subnet details
	$subnet = getSubnetDetailsById ($request['subnetId']);

	//subnet	
	print '<tr>'. "\n";

	print '<td><img src="css/images/edit.png" requestId="'. $request['id'] .'"></td>' . "\n";
	print '<td class="requestedIPedit"><a href="" class="requestedIPedit">'. Transform2long($request['ip_addr']) .'</a></td>'. "\n";
	print '<td>'. Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ('. $subnet['description'] .')</td>'. "\n";
	print '<td>'. $request['dns_name'] .'</td>'. "\n";
	print '<td>'. $request['description'] .'</td>'. "\n";
	print '<td>'. $request['requester'] .'</td>'. "\n";
	
	print '</tr>'. "\n";
	
	}
?>

</table>
</div>

<!-- edit request holder -->
<div class="manageRequestEdit"></div>
