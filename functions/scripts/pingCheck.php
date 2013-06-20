<?php

require_once( dirname(__FILE__) . '/../../config.php' );
require_once( dirname(__FILE__) . '/../dbfunctions.php' );
require_once( dirname(__FILE__) . '/../functions-network.php' );

function pingHost ($hostAddress)
{
    exec("ping -c 1 -W 1 -n $hostAddress 1>/dev/null 2>&1", $output, $retval);
	return $retval;
}

function getTypeOfAddress($ip_addr) {
    if ( IdentifyAddress( $ip_addr ) == "IPv4") {
        $type = 0;
    }
    else {
        $type = 1;
    }
}

function pingSubnetById ($subnetId) {
    global $db;
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* get subnet */
    $query = 'SELECT ip_addr FROM ipaddresses WHERE subnetId = "'. $subnetId .'";';
    $subnets = $database->getArray($query);

    if (empty($subnets)) {
        print "The subnet is empty or does not exist!\n";
        return false;
    }

	$ipcount = count($subnets);

	/* $update1 query will only update addresses that DO ping but were set otherwise*/
	/* $update0 query will only update addresses that DON'T ping but were set otherwise */
    $update1 = 'UPDATE ipaddresses set state = \'1\' where (state = \'0\' or state = \'\') and ip_addr = \''; 
    $update0 = 'UPDATE ipaddresses set state = \'0\' where (state = \'1\' or state = \'\') and ip_addr = \'';

	for ($i = 0; $i < $ipcount; $i += 1) {
		$status = pingHost(Transform2long($subnets[$i]['ip_addr']));
		if ($status == '0') {
			$update = $update1.$subnets[$i]['ip_addr'].'\';';
		}
		else {
			$update = $update0.$subnets[$i]['ip_addr'].'\';';
		}
		$database->executeQuery($update);
	}
	$database->close();
}

$subnetId= $_REQUEST['subnetId'];
pingSubnetById($subnetId);
?>
