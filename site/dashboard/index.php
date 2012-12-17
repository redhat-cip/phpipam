<?php

/**
 * HomePage display script
 *  	show somw statistics, links, help,...
 *******************************************/

/* verify login and permissions */
isUserAuthenticated(); 

?>
<script type="text/javascript">
//show clock
$(function($) {
	$('span.jclock').jclock();
});
</script>


<!-- charts -->
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.categories.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot/excanvas.min.js"></script><![endif]-->


<div class="welcome">
<b><?php $user = getActiveUserDetails(); print_r($user['real_name']); ?></b>, welcome to your IPAM dashboard. <span class="jclock pull-right"></span>
</div>

<?php
/* print number of requests if admin and if they exist */
$requestNum = countRequestedIPaddresses();
if( ($requestNum != 0) && (checkAdmin(false,false))) {
	print '<div class="alert alert-info">There are <b><a href="/administration/manageRequests/" id="adminRequestNotif">'. $requestNum .' requests</a></b> for IP address waiting for your approval!</div>';
}
?>

<div class="row-fluid">

	<!-- statistics -->
	<div class="span6" id="homeStatistics">
	<div class="inner">
		<h4>Statistics</h4>
		<div class="hContent">
			<?php $type = "IPv4"; include('statistics.php'); ?>
		</div>
	</div>
	</div>

	<!-- IPv4 percentage -->
	<div class="span6" id="homePercentage">
	<div class="inner">
		<h4>Top 10 IPv4 subnets by usage percentage</h4>
		<div class="hContent">
			<?php $type = "IPv4"; include('top10_percentage.php'); ?>
		</div>
	</div>
	</div>
	
</div>

<div class="row-fluid">
	<!-- IPv4 hosts -->
	<div class="span6" id="homeIPv4Hosts">
	<div class="inner">
		<h4>Top 10 IPv4 subnets by number of hosts</h4>
		<div class="hContent">
			<?php $type = "IPv4"; include('top10_hosts.php'); ?>
		</div>
	</div>
	</div>
	
	<!-- IPv4 percentage -->
	<div class="span6" id="homeIPv6Hosts">
	<div class="inner">
		<h4>Top 10 IPv6 subnets by number of hosts</h4>
		<div class="hContent">
			<?php $type = "IPv6"; include('top10_hosts.php');?>
		</div>
	</div>	
	</div>
		
</div>


<?php if(checkAdmin(false,false)) { ?>
<div class="row-fluid">
	<!-- IPv4 hosts -->
	<div class="span6" id="homeAccessLogs">
	<div class="inner">
		<h4>Last 5 informational logs</h4>
		<div class="hContent">
			<?php include('access_logs.php'); ?>
		</div>
	</div>
	</div>
	
	<!-- IPv4 percentage -->
	<div class="span6" id="homeIPv6Hosts">
	<div class="inner">
		<h4>Last 5 warning / error logs</h4>
		<div class="hContent">
			<?php include('error_logs.php');?>
		</div>
	</div>	
	</div>
<?php  } ?>
		
</div>