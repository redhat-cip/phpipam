<?php

/*
 * Script to print some stats on home page....
 *********************************************/

/* required functions */
require_once('../../functions/functions.php'); 

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
	if ($ipAddress['ip_addr'] < 4294967295 ) {
		$IPv4Count++;
	}
	else {
		$IPv6Count++;
	}
}

/* get All users */
$userCount = sizeof(getAllUsers());

?>


<!-- stats table -->
<div class="normalTable homeStats" style="margin-bottom: 10px; float:left; width:50%">
<table class="homeStats" style="width:100%;">

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
</div>


<!-- search -->
<div class="normalTable homeStats" style="float:right;width:48%">
<table class="homeStats" style="width:100%;">

	<tr class="th">
		<td>Search for IP address: </td>
	</tr>
	<tr>
		<td>
			<form name="homeIPSearch" id="homeIPSearch">
				<input type="text" name="ip" id="ipCalc" style="width:130px">
				<input type="submit" value="Search">
			</form>
		</td>
	</tr>
	

</table>
</div>

<br>

<!-- Instructions link -->
<div class="normalTable homeStats" style="float:right;width:48%;margin-top:5px">
<table class="homeStats" style="width:100%;">

	<tr class="th">
		<td style="width:16px"><img src="css/images/info2.png" class="instructions"></td>
		<td style="vertical-align:middle">
			<a href="#tools|instructions" class="instructions">IP address instructions</a></td>
	</tr>

</table>
</div>


<br>

<!-- actions -->
<div class="normalTable homeStats" style="float:left; width:100%">
<table class="homeStats" style="width:100%">

	<!-- manage -->
	<?php
	$userDetails = getActiveUserDetails ();
	if ($userDetails['role'] == "Administrator") {
		print '<tr class="th">' . "\n";
		print '	<td class="title">Manage</td>' . "\n";
		print '	<td class="HomeManage">' . "\n";
		print '		<a href="#Administration|manageSection">	<input type="button" name="manageSection" value="Sections"> </a>' . "\n";
		print '		<a href="#Administration|manageSubnet">		<input type="button" name="manageSubnet"  value="Subnets">	</a>' . "\n";
		print '		<a href="#Administration|userMod">			<input type="button" name="userMod"    	  value="Users">	</a>' . "\n";
		print '		<a href="#Administration|log">				<input type="button" name="log" 	  	  value="Logs">		</a>' . "\n";
		print '	</td>' . "\n";
		print '</tr>' . "\n";
	}
	?>

	<!-- tools -->
	<tr class="th">
		<td class="title">Tools</td>
		<td class="HomeTools">
			<a href="#tools|ipCalc">  <input type="button" name="ipCalc"  value="IPCalc">	 </a>
			<a href="#tools|vlan">	  <input type="button" name="vlan" 	  value="VLAN table"></a>
			<a href="#tools|userMenu"><input type="button" name="userMenu"value="My account"></a>
		</td>
	</tr>

</table>
</div>
