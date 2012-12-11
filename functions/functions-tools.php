<?php

/**
 * Functions for tools
 *
 */
 
 
 
 
 
/* @IPCalc functions ---------------- */


/**
 * ipCalc calculations
 */
function calculateIpCalcResult ($cidr)
{
    /* first verify address type */  
    $type = IdentifyAddress( $cidr );
    
    /* IPv4 */
    if ($type == "IPv4")
    {
        $net = Net_IPv4::parseAddress( $cidr );
        
        //set ip address type
        $out['Type']            = 'IPv4';
        
        //calculate network details
        $out['IP address']      = $net->ip;        // 192.168.0.50
        $out['Network']         = $net->network;   // 192.168.0.0
        $out['Broadcast']       = $net->broadcast; // 192.168.255.255
        $out['Subnet bitmask']  = $net->bitmask;   // 16
        $out['Subnet netmask']  = $net->netmask;   // 255.255.0.0
        
        //calculate min/max IP address
        $out['Min host IP']     = long2ip(ip2long($out['Network']) + 1);
        $out['Max host IP']     = long2ip(ip2long($out['Broadcast']) - 1);
        $out['Numbet of hosts'] = ip2long($out['Broadcast']) - ip2long($out['Min host IP']);
        
        //subnet class
        $out['Subnet Class']    = checkIpv4AddressType ($out['Network'], $out['Broadcast']);
        
        //if IP == subnet clear the Host fields
        if ($out['IP address'] == $out['Network']) {
            $out['IP address'] = "/";
        }
           
    }
    /* IPv6 */
    else
    {
        //set ip address type
        $out['Type']                      = 'IPv6';
        
        //calculate network details
/*         $out['Host address']              = Net_IPv6::removeNetmaskSpec ( $cidr );  */
        $out['Host address']              = $cidr;
        $out['Host address']              = Net_IPv6::compress ( $out['Host address'], 1 );
        $out['Host address (uncompressed)'] = Net_IPv6::uncompress ( $out['Host address'] );  
        
        $mask                             = Net_IPv6::getNetmaskSpec( $cidr );
        $subnet                           = Net_IPv6::getNetmask( $cidr );
        $out['Subnet prefix']             = Net_IPv6::compress ( $subnet ) .'/'. $mask;
        $out['Prefix length']             = Net_IPv6::getNetmaskSpec( $cidr );
        
        //if IP == subnet clear the Host fields
        if ($out['Host address'] == $out['Subnet prefix']) {
            $out['Host address']                = '/';
            $out['Host address (uncompressed)'] = '/';
        }
        
        //min / max hosts
        $maxIp = gmp_strval( gmp_add(gmp_sub(gmp_pow(2, 128 - $mask) ,1),ip2long6 ($subnet)));
        
        $out['Min host IP']               = long2ip6 ( gmp_strval (gmp_add(ip2long6($subnet),1)) );
        $out['Max host IP']               = long2ip6 ($maxIp);
        $out['Number of hosts']           = MaxHosts( $mask, 1);
        
        //address type
        $out['Address type']              = Net_IPv6::getAddressType( $cidr );
        $out['Address type']              = checkIpv6AddressType ($out['Address type']);
    }
    
    /* return results */
    return($out); 
}


/**
 * Check IPv4 class type
 */
function checkIpv4AddressType ($ipStart, $ipStop)
{
    /* define classes */
    $classes['private A']          = '10.0.0.0/8';
    $classes['private B']          = '172.16.0.0/12';
    $classes['private C']          = '192.168.0.0/16';
    
    $classes['Loopback']           = '127.0.0.0/8';
    $classes['Link-local']         = '169.254.0.0/16';
    $classes['Reserved (IANA)']    = '192.0.0.0/24';
    $classes['TEST-NET-1']         = '192.0.2.0/24';
    $classes['IPv6 to IPv4 relay'] = '192.88.99.0/24';
    $classes['Network benchmark']  = '198.18.0.0/15';
    $classes['TEST-NET-2']         = '198.51.100.0/24';
    $classes['TEST-NET-3']         = '203.0.113.0/24';
    
    $classes['Multicast']          = '224.0.0.0/4';         //Multicast
    $classes['Reserved']           = '240.0.0.0/4';         //Reserved - research
    
    /* check if it is in array */
    foreach( $classes as $key=>$class )
    {
        if (Net_IPv4::ipInNetwork($ipStart, $class))
        {
            if (Net_IPv4::ipInNetwork($ipStop, $class)) {
                return($key);
            }
        }
    }
    
    /* no match */
    return false;
}


/**
 * Check IPv6 address type
 */
function checkIpv6AddressType ($subnet)
{
    switch ($subnet) {

        case 10:    $response = "NET_IPV6_NO_NETMASK";      break;
/*         case 1 :    $response = "NET_IPV6_UNASSIGNED";      break; */
        case 1 :    $response = "NET_IPV6";      			break;
        case 11:    $response = "NET_IPV6_RESERVED";        break;
        case 12:    $response = "NET_IPV6_RESERVED_NSAP";   break;
        case 13:    $response = "NET_IPV6_RESERVED_IPX";    break;
        case 14:    $response = "NET_IPV6_RESERVED_UNICAST_GEOGRAPHIC";   break;
        case 22:    $response = "NET_IPV6_UNICAST_PROVIDER";break;
        case 31:    $response = "NET_IPV6_MULTICAST";       break;
        case 42:    $response = "NET_IPV6_LOCAL_LINK";      break;
        case 43:    $response = "NET_IPV6_LOCAL_SITE";      break;
        case 51:    $response = "NET_IPV6_IPV4MAPPING";     break;    
        case 51:    $response = "NET_IPV6_UNSPECIFIED";     break;    
        case 51:    $response = "NET_IPV6_LOOPBACK";        break;    
        case 51:    $response = "NET_IPV6_UNKNOWN_TYPE";    break;    
    }
    
    return $response;
}









/* @log functions ---------------- */


/**
 * Update log table
 */
function updateLogTable ($command, $details = NULL, $severity = 0)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set variable */
    $date = date("Y-m-d h:i:s");
    $user = getActiveUserDetails();
    $user = $user['username'];
    
    /* set query */
    $query  = 'insert into logs '. "\n";
    $query .= '(`severity`, `date`,`username`,`ipaddr`,`command`,`details`)'. "\n";
    $query .= 'values'. "\n";
    $query .= '("'.  $severity .'", "'. $date .'", "'. $user .'", "'. $_SERVER['REMOTE_ADDR'] .'", "'. $command .'", "'. $details .'");';
    
    
    /* execute */
    try {
    	$database->executeMultipleQuerries($query);
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">'. $error .'</div>');
	}
	
    return true;
}


/**
 * Get log details by Id 
 */
function getLogByID ($logId)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     
    /* set query */
    $query  = "select * from `logs` where `id` = '$logId';";
    
    /* execute */
    try {
    	$logs = $database->getArray($query);
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">'. $error .'</div>');
	}
	
    return $logs[0];
}


/**
 * Get all logs
 */
function getAllLogs($logCount, $direction = NULL, $lastId = NULL, $highestId = NULL, $informational, $notice, $warning)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* query start */
	$query  = 'select * from ('. "\n";
	$query .= 'select * from logs '. "\n";
	
	/* append severities */
	$query .= 'where (`severity` = "'. $informational .'" or `severity` = "'. $notice .'" or `severity` = "'. $warning .'" )'. "\n";
	
	/* set query based on direction */
	if( ($direction == "next") && ($lastId != $highestId) ) {
		$query .= 'and `id` < '. $lastId .' '. "\n";
		$query .= 'order by `id` desc limit '. $logCount . "\n";
	}
	else if( ($direction == "prev") && ($lastId != $highestId)) {
		$query .= 'and `id` > '. $lastId .' '. "\n";		
		$query .= 'order by `id` asc limit '. $logCount . "\n";
	}
	else {
		$query .= 'order by `id` desc limit '. $logCount . "\n";
	}
	
	/* append limit and order */
	$query .= ') as test '. "\n";
	$query .= 'order by `id` desc limit '. $logCount .';'. "\n";

    /* execute */
    try {
    	$logs = $database->getArray($query);
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">'. $error .'</div>');
	}

    
    /* return vlans */
    return $logs;
}


/**
 * Get all logs for export
 */
function getAllLogsForExport()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* increase memory size */
	ini_set('memory_limit', '512M');
	
	/* query start */
	$query = 'select * from `logs` order by `id` desc;'. "\n";

    /* execute */
    try {
    	$logs = $database->getArray($query);
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">'. $error .'</div>');
	}
    /* return vlans */
    return $logs;
}


/**
 * Clear all logs
 */
function clearLogs()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     
	
	/* query start */
	$query  = 'truncate table logs;'. "\n";

    /* execute */
    try {
    	$logs = $database->executeQuery($query);
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">'. $error .'</div>');
	}

    /* return result */
    return true;
}


/**
 * Count all logs
 */
function countAllLogs ()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

    /* set query */
    $query = 'select count(*) from logs;';   
    $logs       = $database->getArray($query);  
    
    /* return vlans */
    return $logs[0]['count(*)'];
}


/**
 *	Prepare log file from array
 */
function prepareLogFromArray ($logs)
{
	$result = "";
	
	/* reformat */
    foreach($logs as $key=>$req) {
    
    	//ignore __ and PHPSESSID
    	if( (substr($key,0,2) == '__') || (substr($key,0,9) == 'PHPSESSID') ) {
		}
    	else {
    		$result .= " ". $key . ": " . $req . "<br>";
    	}
	}
	
	/* return result */
	return $result;
}


/**
 * Get highest log id
 */
function getHighestLogId()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

    /* set query */
    $query = 'select id from logs order by id desc limit 1;';   
    $logs       = $database->getArray($query);  
    
    /* return vlans */
    return $logs[0]['id'];
}










/* @ search functions ---------------- */

/**
 * Search function
 */
function searchAddresses ($query)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
    $logs       = $database->getArray($query);  
    
    /* return result */
    return $logs;
}


/**
 * Search subnets
 */
function searchSubnets ($searchterm, $searchTermEdited = "")
{
	
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set query */    
	$query = 'select * from `subnets` where `description` like "%'. $searchterm .'%" or `subnet` between "'. $searchTermEdited['low'] .'" and "'. $searchTermEdited['high'] .'";';

	/* execute query */
    $search = $database->getArray($query); 
    
    /* return result */
    return $search;
}



/**
 * Search VLANS
 */
function searchVLANs ($searchterm)
{
	
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set query */    
	$query = 'select * from `vlans` where `name` like "%'. $searchterm .'%" or `description` like "%'. $searchterm .'%" or `number` like "%'. $searchterm .'%";';

	/* execute query */
    $search = $database->getArray($query); 
    
    /* return result */
    return $search;
}



/**	
 * Reformat incomplete IPv4 address to decimal for search!
 */
function reformatIPv4forSearch ($ip)
{
	//remove % sign if present
	$ip = str_replace("%", "", $ip);
	//remove last .
	$size 	  = count($ip);
	$lastChar = substr($ip, -1);
	if ($lastChar == ".") {
		$ip = substr($ip, 0, - 1);
	}
	
	/* check if subnet provided, then we have all we need */
	$subnet = strpos($ip, "/");
	
	if ($subnet) {
		require_once 'PEAR/Net/IPv4.php';
		$net = Net_IPv4::parseAddress($subnet);
		
		$result['low']   = $net->network;
		$result['high']	 = $net->broadcast;
	}
	else {
		/* if subnet is not provided maye wildcard is, so explode it to array */
		$ip = explode(".", $ip);
	
		//4 is ok
		if (sizeof($ip) == 4) {
			$temp = implode(".", $ip);
			$result['low'] = $result['high'] = transform2decimal($temp);
		}
		//3 we need to modify
		else if (sizeof($ip) == 3) {
			$ip[3]	= 0;	
			$result['low']  = transform2decimal(implode(".", $ip));
	
			$ip[3]	= 255;
			$result['high'] = transform2decimal(implode(".", $ip));
		}
		//2 also
		else if (sizeof($ip) == 2) {
			$ip[2]	= 0;
			$ip[3]	= 0;
			$result['low']  = transform2decimal(implode(".", $ip));		
			
			$ip[2]	= 255;
			$ip[3]	= 255;
			$result['high'] = transform2decimal(implode(".", $ip));
		}
		//1 also
		else if (sizeof($ip) == 1) {
			$ip[1]	= 0;
			$ip[2]	= 0;
			$ip[3]	= 0;
			$result['low']  = transform2decimal(implode(".", $ip));		
	
			$ip[1]	= 255;		
			$ip[2]	= 255;
			$ip[3]	= 255;
			$result['high'] = transform2decimal(implode(".", $ip));
		}
		//else return same value
		else {
			$result['low']  = implode(".", $ip);
			$result['high'] = implode(".", $ip);
		}
	}
	
	//return result!
	return $result;
}


/**	
 * Reformat incomplete IPv6 address to decimal for search!
 */
function reformatIPv6forSearch ($ip)
{
	//split network and subnet part
	$ip = explode("/", $ip);

	//if subnet is not provided we are looking for host!	
	if (sizeof($ip) < 2) {
		$return['low']  = Transform2decimal($ip[0]);
		$return['high'] = Transform2decimal($ip[0]);
	}
	
	//if network part ends with :: we must search the complete provided subnet!
	$lastChars = substr($ip[0], -2);
	
	if ($lastChars == "::") {
		$return['low']  = Transform2decimal ($ip[0]);
		
		//set highest IP address
		$subnet = substr($ip[0], 0, -2);
		$subnet = Transform2decimal ($subnet);
		
		//calculate all possible hosts in subnet mask
		$maskHosts = gmp_strval(gmp_sub(gmp_pow(2, 128 - $ip[1]) ,1));
		
		$return['high'] = gmp_strval(gmp_add($return['low'], $maskHosts));
	}
	
	return $return;
}









/* @ IP requests -------------- */

/**
 * Is IP already requested?
 */
function isIPalreadyRequested($ip)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from requests where `ip_addr` = "'. $ip .'" and `processed` = 0;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $details  = $database->getArray($query); 
    
    /* return true is IP already in procedure */
    if(sizeof($details) != 0) {
    	return true;
    }
    else {
    	return false;
    }
}


/**
 * Count number of requested IP addresses
 */
function countRequestedIPaddresses()
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select count(*) from requests where `processed` = 0;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $details  = $database->getArray($query); 
    
    return $details[0]['count(*)'];
}


/**
 * Get all active IP requests
 */
function getAllActiveIPrequests()
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from requests where `processed` = 0 order by `id` desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $activeRequests  = $database->getArray($query); 
    
    return $activeRequests;
}


/**
 * Get all IP requests
 */
function getAllIPrequests($limit = 20)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from requests order by `id` desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $activeRequests  = $database->getArray($query); 
    
    return $activeRequests;
}


/**
 * Get IP request by id
 */
function getIPrequestById ($id)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from requests where `id` = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $activeRequests  = $database->getArray($query); 
    
    return $activeRequests[0];
}

/**
 * Insert new IP request 
 */
function addNewRequest ($request)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set query */
    $query  = 'insert into requests ' . "\n"; 
    $query .= '(`subnetId`, `ip_addr`,`description`,`dns_name`,`owner`,`requester`,`comment`,`processed`) ' . "\n"; 
    $query .= 'values ' . "\n";
    $query .= '("'. $request['subnetId'] .'", "'. $request['ip_addr'] .'", "'. $request['description'] .'", '. "\n";
    $query .= ' "'. $request['dns_name'] .'", "'. $request['owner'] .'",   "'. $request['requester'] .'", "'. $request['comment'] .'", "0");';

	/* set log file */
	$log = prepareLogFromArray ($request);

    /* execute query */
    if (!$database->executeQuery($query)) {
    	updateLogTable ('Failed to add new IP request', $log, 2);
        return false;
    }
    else {
    	updateLogTable ('New IP request added', $log, 1);
        return true;  
    }
}


/**
 * reject IP request
 */
function rejectIPrequest($id, $comment)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set query */
    $query  = 'update requests set `processed` = "1", `accepted` = "0", `adminComment` = "'. $comment .'" where `id` = "'. $id .'";' . "\n";
	
    /* execute query */
    if (!$database->executeQuery($query)) {
    	updateLogTable ('Failed to reject IP address id '. $id, 'Failed to reject IP address id '. $id, 2);
        return false;
    }
    else {
    	updateLogTable ('IP address id '. $id .' rejected', 'IP address id '. $id . " rejected with comment". $comment, 1);
        return true;  
    }
}


/**
 * accept IP request
 */
function acceptIPrequest($request)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query  = 'update requests set `processed` = "1", `accepted` = "1", `adminComment` = "'. $request['adminComment'] .'" where `id` = "'. $request['requestId'] .'";' . "\n";
    
    /* insert to ip database */
    $query .= "insert into ipaddresses ";
	$query .= "(`subnetId`,`description`,`ip_addr`, `dns_name`, `owner`, `state`, `switch`, `port`) ";
	$query .= "values ";
	$query .= "('". $request['subnetId'] ."', '". $request['description'] ."', '". $request['ip_addr'] ."', '". $request['dns_name'] ."', '". $request['owner'] ."', '". $request['state'] ."', '". $request['switch'] ."', '". $request['port'] ."');";    

	/* set log file */
    foreach($request as $key=>$req) {
		$log .= " ". $key . ": " . $req . "<br>";
	}
    
    /* execute query */
    if (!$database->executeMultipleQuerries($query)) {
        updateLogTable ('Failed to accept IP request', $log, 2);
        return false;
    }
    else {
    	updateLogTable ('IP request accepted', $log, 1);
        return true; 
    } 
}











/* @switch functions ------------------- */



/**
 * Get all unique devices
 */
function getAllUniqueSwitches () 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
    $query   = 'SELECT `hostname`,`id`,`sections` FROM `switches` order by `hostname` ASC;';
    $devices = $database->getArray($query);  
    
    /* return unique devices */
    return $devices;
}


/**
 * Get switch details by hostname
 */
function getSwitchDetailsByHostname($hostname) 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
    $query = 'SELECT * FROM `switches` where `hostname` = "'. $hostname .'" limit 1;';
    $ip    = $database->getArray($query);  
    
    /* return details */
    if($ip) {
    	return $ip[0];
    }
    else {
    	return false;
    }
}


/**
 * Get switch details by id
 */
function getSwitchDetailsById($id) 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
    $query = 'SELECT * FROM `switches` where `id` = "'. $id .'";';
    $switch    = $database->getArray($query);  
    
    /* return details */
    if($switch) {
    	return $switch[0];
    }
    else {
    	return false;
    }
}











/* @other functions ------------------- */

/**
 *	fetch instructions
 */
function fetchInstructions () 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query 			= "select * from instructions;";
    $instructions   = $database->getArray($query);  
    
    /* return result */
    return $instructions;
}


/**
 * version check
 */
function getLatestPHPIPAMversion() 
{
	/* fetch page */
	$handle = fopen("http://mihap.si/phpipamversion.php", "r");
	while (!feof($handle)) {
		$version = fgets($handle);		
	}
	fclose($handle);
	
	/* return version */
	if(is_numeric($version)) {
		return $version;
	}
	else {
		return false;
	}
}




?>