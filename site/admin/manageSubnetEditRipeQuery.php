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


# identify address
$type = IdentifyAddress($_REQUEST['subnet']);
	
# IPv4
if ($type == "IPv4") {
    # IPv4 functions */
    require_once('../../functions/PEAR/Net/IPv4.php'); 
    $Net_IPv4 = new Net_IPv4();
       
    # parse subnet
    $net = $Net_IPv4->parseAddress($_REQUEST['subnet']);
}
# IPv6
else {
    # IPv6 functions */
    require_once('../../functions/PEAR/Net/IPv6.php');
    $Net_IPv6 = new Net_IPv6();
    	
    /* remove netmask from subnet1 */
    $net = $Net_IPv6->removeNetmaskSpec ($_REQUEST['subnet']);
}


/* querry ripe db and parse result */
$url = "http://apps.db.ripe.net/whois/lookup/ripe/inetnum/$net->network-$net->broadcast.xml";
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