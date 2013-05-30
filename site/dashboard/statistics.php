<?php

/*
 * Script to print some stats on home page....
 *********************************************/

/* get number of sections */
$sectionNum = getNumberOfSections();

/* get number of subnets */
$subnetNum 	= getNumberOfSubnets();

/* get number of IP addresses, split IPv4 / IPv6 */
$IPv4Count = getNuberOfIPv4Addresses();
$IPv6Count = getNuberOfIPv6Addresses();

/* get All users */
$userCount = getNumberOfUsers();
?>


<!-- stats table -->
<table class="table table-striped table-condensed table-hover">

	<!-- sections -->
	<tr>
		<td class="title"><?php print _('Number of Sections'); ?></td>
		<td><?php print $sectionNum; ?></td>
	</tr>

	<!-- subnets -->
	<tr>
		<td class="title"><?php print _('Number of Subnets'); ?></td>
		<td><?php print $subnetNum; ?></td>
	</tr>

	<!-- IPv4 addresses -->
	<tr>
		<td class="title"><?php print _('Number of IPv4 addresses'); ?></td>
		<td><?php print $IPv4Count; ?></td>
	</tr>

	<!-- IPv6 addresses -->
	<tr>
		<td class="title"><?php print _('Number of IPv6 addresses'); ?></td>
		<td><?php print $IPv6Count; ?></td>
	</tr>

	<!-- All users - only for admin! -->
	<tr>
		<td class="title"><?php print _('Number of users'); ?></td>
		<td><?php print $userCount; ?></td>
	</tr>

</table>