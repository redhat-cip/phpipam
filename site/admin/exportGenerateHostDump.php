<?php

/**
 *	Generate hostfile dump for /etc/hosts
 *********************************/
/* required functions */
require_once('../../functions/functions.php'); 

/* we dont need any errors! */
ini_set('display_errors', 0);

/* verify that user is admin */
checkAdmin();

//set filename
$filename = "phpipam_hosts_". date("Y-m-d");

//get subnet and section descriptions
$subnets = getAllSubnetsForExport();

//get hosts
foreach($subnets as $s) {
	$ips = getIpAddressesBySubnetId ($s['id']);
	
	if(sizeof($ips) > 0) {
		# create new array
		$res[] = "# $s[s_description] (".transform2long($s['subnet'])."/$s[mask]) - "._('Section')." $s[se_description]";
		
		foreach($ips as $ip) {
			# get lenth
			$diff = 17 - strlen(transform2long($ip['ip_addr']));
			
			# ipv6
			if($diff < 0) { $diff = 3; }
			
			# write host if dns name is set!
			if(strlen($ip['dns_name'])>0) {
				$res[] = transform2long($ip['ip_addr']).str_repeat(" ", $diff)."$ip[dns_name]";
			}
		}
		
		# break
		$res[] = "";
	}
}

/* join */
$content = implode("\n", $res);


/* headers */
header("Cache-Control: private");
header("Content-Description: File Transfer");
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename="'. $filename .'"');

print($content);
?>