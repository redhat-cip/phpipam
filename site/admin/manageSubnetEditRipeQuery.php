<?php

/** 
 * Function to get RIPe info for network
 ********************************************/

# required functions */
require_once('../../functions/functions.php'); 
# verify that user is admin
checkAdmin();
# verify post
CheckReferrer();

/* http://apps.db.ripe.net/whois/lookup/ripe/inetnum/212.58.224.0-212.58.255.255.html.xml */
/* http://apps.db.ripe.net/whois/lookup/ripe/inet6num/2102:840::/32.xml */


# identify address and set proper url
$type = IdentifyAddress($_REQUEST['subnet']);
	
if ($type == "IPv4") 	{ $url = "http://apps.db.ripe.net/whois/lookup/ripe/inetnum/$_REQUEST[subnet].xml"; }
else 					{ $url = "http://apps.db.ripe.net/whois/lookup/ripe/inet6num/$_REQUEST[subnet].xml"; }

/* querry ripe db and parse result */
$xml = simplexml_load_file($url);

foreach($xml->objects->object[0]->attributes->children() as $m=>$subtag) {
    $a = (string) $subtag->attributes()->name;
    $b = (string) $subtag->attributes()->value;
    
    # replace - with _
    $a = str_replace("-", "_", $a);
    
    $out["$a"] .= $b.'\n';
}

# replace last newlines
foreach($out as $key=>$val) {
	$out[$key] = rtrim($val, "\\n");
}

/* save to json and return */
header("Content-type: text/javascript");
echo json_encode($out);

?>