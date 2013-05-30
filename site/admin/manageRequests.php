<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* verify that user is admin */
checkAdmin();

/* get all */
$allActiveRequests = getAllActiveIPrequests();

?>

<h4><?php print _('List of all active IP addresses requests'); ?></h4>
<hr><br>


<table id="requestedIPaddresses" class="table table-striped table-condensed table-hover table-top">

<!-- headers -->
<tr>
	<th></th>
	<th><?php print _('Subnet'); ?></th>
	<th><?php print _('Hostname'); ?></th>
	<th><?php print _('Description'); ?></th>
	<th><?php print _('Requested by'); ?></th>
	<th><?php print _('Comment'); ?></th>
</tr>

<?php 
	# print requests
	foreach($allActiveRequests as $request) {
	
	//get subnet details
	$subnet = getSubnetDetailsById ($request['subnetId']);
	
	print '<tr>'. "\n";
	print "	<td><button class='btn btn-small' data-requestid='$request[id]'><i class='icon-gray icon-edit'></i> "._('Edit')."</button></td>";
	print '	<td>'. Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ('. $subnet['description'] .')</td>'. "\n";
	print '	<td>'. $request['dns_name'] .'</td>'. "\n";
	print '	<td>'. $request['description'] .'</td>'. "\n";
	print '	<td>'. $request['requester'] .'</td>'. "\n";
	print '	<td>'. $request['comment'] .'</td>'. "\n";
	print '</tr>'. "\n";
	}
?>

</table>

<?php
# no requests
if(sizeof($allActiveRequests) == 0) { print "<div class='alert alert-info'>"._('No IP address requests available')."!</div>"; }
?>

<!-- edit request holder -->
<div class="manageRequestEdit"></div>
