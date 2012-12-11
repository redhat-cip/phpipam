<?php

/*
 * Print edit sections form
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/**
 * Fetch section info
 */
$log = getLogById ($_POST['id']);


if ($log['severity'] == 0) {
	$log['severityText'] = "Informational";
	$color = "success";
}
else if ($log['severity'] == 1) {
	$log['severityText'] = "Notice";
	$color = "warning";
}
else {
	$log['severityText'] = "Warning";
	$color = "error";
}

/**
 * get user details
 */
$user = getUserDetailsByName($log['username']);
?>



<!-- header -->
<div class="pHeader">Log details</div>


<!-- content -->
<div class="pContent">

	<table class="table table-striped table-condensed">

	<tr>
		<th>Log ID</th>
		<td><?php print $log['id']; ?></td>
	</tr>	
	<tr>
		<th>Event</th>
		<td><?php print $log['command']; ?></td>
	</tr>
	<tr class="<?php print $color; ?>">
		<td><strong>Severity</strong></td>
		<td><?php print $log['severityText'] .' ('. $log['severity'] .")"; ?></td>
	</tr>
	<tr>
		<th>Date</th>
		<td><?php print $log['date']; ?></td>
	</tr>
	<tr>
		<th>User details</th>
		<td><?php print "$user[real_name] ($user[username])"; ?></td>
	</tr>
	<tr>
		<th>IP address</th>
		<td><?php print $log['ipaddr']; ?></td>
	</tr>
	<tr>
		<th>Details</th>
		<td><?php print $log['details']; ?></td>
	</tr>
	
	</table>

</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Close window</button>

	<!-- result holder -->
	<div class="sectionEditResult"></div>
</div>	
		