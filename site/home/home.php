<?php

/**
 * HomePage display script
 *  	show somw statistics, links, help,...
 *******************************************/

/* site config and functions */
require('../../functions/functions.php');

/* verify login and permissions */
isUserAuthenticated(); 

?>
<script type="text/javascript">
//show clock
$(function($) {
	$('span.jclock').jclock();
});
</script>


<b><?php $user = getActiveUserDetails(); print_r($user['real_name']); ?></b>, welcome to your IPAM dashboard. <span class="jclock"></span>

<?php
/* print number of requests if admin and if they exist */
$requestNum = countRequestedIPaddresses();
if( ($requestNum != 0) && (checkAdmin(false,false))) {
	print '<div class="success">'. $user['real_name'] .', there are <b><a href="#Administration|manageRequests" id="adminRequestNotif">'. $requestNum .' requests</a></b> for IP address waiting for your approval!</div>';
}
?>

<!-- home table -->
<div class="normalTable">
<!-- home table -->
<table class="normalTable homeTable">

<!-- IPv4 usage -->
<tr class="th">
	<th class="homeTitle">Some statistics and tools</th>
	<th class="homeTitle">Top 10 IPv4 subnets by usage percentage</th>
</tr>

<tr class="th">
	<td style="width:50%"><?php $type = "IPv4"; include('statistics.php'); 			?></td>
	<td style="width:50%"><?php $type = "IPv4"; include('top10_percentage.php'); 	?></td>
</tr>


<!-- IPv6 usage -->
<tr class="th">
	<th class="homeTitle">Top 10 IPv4 subnets by number of hosts</th>
	<th class="homeTitle">Top 10 IPv6 subnets by number of hosts</th>
</tr>

<tr class="th">
	<td style="width:50%"><?php $type = "IPv4"; include('top10_hosts.php'); 		?></td>
	<td style="width:50%"><?php $type = "IPv6"; include('top10_hosts.php'); 		?></td>
</tr>


</table>	<!-- end home table -->
</div>		<!-- end home table overlay div -->