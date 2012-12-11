<?php

/*
 * Script to print some stats on home page....
 *********************************************/

/* get number of sections */
$sectionNum = sizeof(fetchSections ());

/* get number of subnets */
$subnetNum 	= sizeof(fetchAllSubnets ());

/* get number of IP addresses, split IPv4 / IPv6 */
$ipAddresses = fetchAllIPAddresses ();
$ipCount = sizeof($ipAddresses);
$IPv4Count = 0;
$IPv6Count = 0;

foreach ($ipAddresses as $ipAddress) {
	if ($ipAddress['ip_addr'] < 4294967295 ) 	{ $IPv4Count++; }
	else 										{ $IPv6Count++; }
}

/* get All users */
$userCount = sizeof(getAllUsers());

?>


<!-- stats table -->
<table class="table table-striped table-condensed table-hover">

	<!-- sections -->
	<tr>
		<td class="title">Number of Sections</td>
		<td><?php print $sectionNum; ?></td>
	</tr>

	<!-- subnets -->
	<tr>
		<td class="title">Number of Subnets</td>
		<td><?php print $subnetNum; ?></td>
	</tr>

	<!-- IPv4 addresses -->
	<tr>
		<td class="title">Number of IPv4 addresses</td>
		<td><?php print $IPv4Count; ?></td>
	</tr>

	<!-- IPv6 addresses -->
	<tr>
		<td class="title">Number of IPv6 addresses</td>
		<td><?php print $IPv6Count; ?></td>
	</tr>

	<!-- All users - only for admin! -->
	<tr>
		<td class="title">Number of users</td>
		<td><?php print $userCount; ?></td>
	</tr>

</table>