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
	$log['severityText'] = _("Informational");
	$color = "success";
}
else if ($log['severity'] == 1) {
	$log['severityText'] = _("Notice");
	$color = "warning";
}
else {
	$log['severityText'] = _("Warning");
	$color = "error";
}

/**
 * get user details
 */
$user = getUserDetailsByName($log['username']);
?>



<!-- header -->
<div class="pHeader"><?php print _('Log details'); ?></div>


<!-- content -->
<div class="pContent">

	<table class="table table-striped table-condensed">

	<tr>
		<th><?php print _('Log ID'); ?></th>
		<td><?php print $log['id']; ?></td>
	</tr>	
	<tr>
		<th><?php print _('Event'); ?></th>
		<td><?php print $log['command']; ?></td>
	</tr>
	<tr class="<?php print $color; ?>">
		<td><strong><?php print _('Severity'); ?></strong></td>
		<td><?php print $log['severityText'] .' ('. $log['severity'] .")"; ?></td>
	</tr>
	<tr>
		<th><?php print _('Date'); ?></th>
		<td><?php print $log['date']; ?></td>
	</tr>
	<tr>
		<th><?php print _('User details'); ?></th>
		<td><?php print "$user[real_name] ($user[username])"; ?></td>
	</tr>
	<tr>
		<th><?php print _('IP address'); ?></th>
		<td><?php print $log['ipaddr']; ?></td>
	</tr>
	<tr>
		<th><?php print _('Details'); ?></th>
		<td><?php print $log['details']; ?></td>
	</tr>
	
	</table>

</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Close window'); ?></button>

	<!-- result holder -->
	<div class="sectionEditResult"></div>
</div>	
		