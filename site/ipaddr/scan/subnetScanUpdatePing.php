<?php

/*
 * Update alive status of all hosts in subnet
 ***************************/

/* required functions */
require_once('../../../functions/functions.php'); 
require_once('../../../functions/dbfunctions.php');

global $db;
$database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-error">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all existing IP addresses
$addresses = getIpAddressesBySubnetId ($_POST['subnetId']);

$queryOffline = 'UPDATE ipaddresses SET state = 0 WHERE ip_addr IN (';
$queryOnline = 'UPDATE ipaddresses SET state = 1 WHERE ip_addr IN (';
$online = false;
$offline = false;
# loop and check
foreach($addresses as $ip) {
	$m = 0;											//array count
	//if strictly disabled for ping
	if($ip['excludePing']=="1") {
		$ip[$m]['status'] = "excluded from check";
	}
	//ping
	else {
		$code = pingHost (transform2long($ip['ip_addr']));
	}

	if ( intval($ip['state']) == $code )
	{
		if ($code == 0)
		{
			$queryOnline = $queryOnline.'\''.$ip['ip_addr'].'\''.",";
			$offline = true;
		}
		else
		{
			$queryOffline = $queryOffline.'\''.$ip['ip_addr'].'\''.",";
			$online = true;
		}
	}

	$m++;											//next array item
}
if ($offline == true) {$database->executeQuery(substr_replace($queryOffline, ')', -1));}
if ($online == true) {$database->executeQuery(substr_replace($queryOnline, ')', -1));}
?>


<h5><?php print _('Scan results');?> (<?php print_r($_POST['pingType']) ?>):</h5>
<?php echo '<head> <meta http-equiv="refresh" content="0"> </head>'?>
<hr>
