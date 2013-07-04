<?php

/*
 * Discover new hosts with ping
 *******************************/

/* required functions */
require_once('../../../functions/functions.php'); 

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 		{ die('<div class="alert alert-error">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

# verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all existing IP addresses
$addresses = getIpAddressesBySubnetId ($_POST['subnetId']);

# set start and end IP address
$calc = calculateSubnetDetailsNew ( $subnet['subnet'], $subnet['mask'], 0, 0, 0, 0 );
$max = $calc['maxhosts'];

# loop and get all IP addresses for ping
for($m=1; $m<=$max;$m++) {
	// create array of IP addresses (if they do not already exist!)
	if (!checkDuplicate (transform2long($subnet['subnet']+$m), $_POST['subnetId'])) {
		$ip[] = $subnet['subnet']+$m;
	}
}
# create 1 line for $argv
if(count($ip) == 0)
{
	print "<div class='alert alert-info'>All host already listed</div>";
	exit;
}
$ip = implode(";", $ip);

# get php exec path
$phpPath = getPHPExecutableFromPath();
# set script
$script = dirname(__FILE__) . '/../../../functions/scan/'.$_REQUEST['pingType'].'Script.php';

# invoke CLI with threading support
$cmd = "$phpPath $script '$ip'";

# save result to $output
exec($cmd, $output, $retval);

# format result - alive
$alive = json_decode(trim($output[0]));

# if not numeric means error, print it!
if(!is_numeric($alive[0]))	{
	$error = $alive[0];
}

#verify that pign path is correct
if(!file_exists($pathPing)) { $pingError = true; }

?>


<h5><?php print _('Scan results');?>:</h5>
<hr>

<?php
# error?
if(isset($error)) {
	print "<div class='alert alert-error'><strong>Error: </strong>$error</div>";
}
# wrong ping path
elseif($pingError) {
	print '<div class="alert alert-error">'._("Invalid ping path")."<hr>". _("You can set parameters for scan under functions/scan/config-scan.php").'</div>';
}
# empty
elseif(sizeof($alive)==0) {
	print "<div class='alert alert-info'>No alive host found!</div>";
}
# found alive
else {
	print "<form name='".$_REQUEST['pingType']."Form' class='".$_REQUEST['pingType']."Form'>";
	print "<table class='table table-striped table-top table-condensed'>";
	
	// titles
	print "<tr>";
	print "	<th>"._("IP")."</th>";
	print "	<th>"._("Description")."</th>";
	print "	<th>"._("Hostname")."</th>";
	print "	<th></th>";
	print "</tr>";
	
	// alive
	$m=0;
	foreach($alive as $ip) {
	
		//resolve?
		if($scanDNSresolve) {
			$dns = gethostbyaddr ( transform2long($ip) );
		}
		else {
			$dns = "test";
		}
		
		print "<tr class='result$m'>";
		//ip
		print "<td>".transform2long($ip)."</td>";
		//description
		print "<td>";
		print "	<input type='text' name='description$m'>";
		print "	<input type='hidden' name='ip$m' value=".transform2long($ip).">";
		print "</td>";
		//hostname
		print "<td>";
		print "	<input type='text' name='dns_name$m' value='".@$dns."'>";
		print "</td>";
		//remove button
		print 	"<td><a href='' class='btn btn-mini btn-danger resultRemove' data-target='result$m'><i class='icon-white icon-remove'></i></a></td>";
		print "</tr>";
		
		$m++;
	}
	
	//result
	print "<tr>";
	print "	<td colspan='4'>";
	print "<div id='subnetScanAddResult'></div>";
	print "	</td>";
	print "</tr>";	
	
	//submit
	print "<tr>";
	print "	<td colspan='4'>";
	print "		<a href='' class='btn btn-small btn-success pull-right' id='saveScanResults' data-script='".$_REQUEST['pingType']."' data-subnetId='".$_REQUEST['subnetId']."'><i class='icon-white icon-plus'></i> "._("Add discovered hosts")."</a>";
	print "	</td>";
	print "</tr>";
	
	print "</table>";
	print "</form>";
}
?>
