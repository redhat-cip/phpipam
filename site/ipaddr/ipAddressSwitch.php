<div class="ipaddresses">

<?php

/**
 *	Script to show nested subnets / hierarchy or print IP address list and subnet details
 */


# fetch subnet details
$slaves = getAllSlaveSubnetsBySubnetId ($_REQUEST['subnetId']);


# print subnet and ip addresses
if(sizeof($slaves) == 0) 	{ 
	# print subnets
	print "<div class='subnetDetails'>";
	include_once("subnetDetails.php");
	print "</div>";
	
	# IP address table  
	print '<div class="ipaddresses_overlay">';
	include_once('ipAddressPrintTable.php');
	print '</div>';
}
# print slaves
else { 
	# subnet details for slaves
	print "<div class='subnetDetails'>";
	include_once("subnetDetailsSlaves.php");
	print "</div>";
	
	# subnet slaves print subnets
	print "<div class='subnetSlaves'>";
	include_once("ipAddressPrintTableSlaves.php"); 
	print "</div>";

	# IP address table  
	print '<div class="ipaddresses_overlay">';
	include_once('ipAddressPrintTable.php');
	print '</div>';

	# IP address table - orphaned slaves
	print '<div class="ipaddresses_overlay">';
	include_once('ipAddressPrintTableOrphaned.php');
	print '</div>';
}

?>

</div>