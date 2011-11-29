<?php

#
# include database functions
#
require( dirname(__FILE__) . '/../config.php' );
require( dirname(__FILE__) . '/dbfunctions.php' );


/**
 * php debugging on/off - ignore notices
 */
if ($debugging == 0) {
    ini_set('display_errors', 0);
}
else{
    ini_set('display_errors', 1); 
/*     error_reporting( E_ALL ); */
    error_reporting( E_ALL & ~E_NOTICE );
}


/****************************************
 * 
 *      general functions
 *
 ***************************************/

/**
 * referer check
 * We can only request stuff from own URL and through XMLHttpRequest
 *
 * $_SERVER['HTTP_X_REQUESTED_WITH'] must be done through XMLHttpRequest
 * $_SERVER['HTTP_ORIGIN'] request must come from same IP as server is on! 
 *
 */
function CheckReferrer() 
{
    if ( ($_SERVER['HTTP_X_REQUESTED_WITH'] != "XMLHttpRequest") && ($_SERVER['HTTP_ORIGIN'] != $_SERVER['HTTP_HOST'] ) ) {
        updateLogTable ('Page not referred properly', "", 2);
        die();
    }
}


/**
 * Functions to check if user is authenticated properly for ajax-loaded pages
 *
 */
function isUserAuthenticated() 
{
    /* open session and get username / pass */
	if (!isset($_SESSION)) { 
		session_start();
	}
    
    /* redirect if not authenticated */
    if (empty($_SESSION['ipamusername'])) {
        die('<div class="error">Please <a href="login">login</a> first!</div>');
    }
    /* close session */
    session_write_close();
}


/**
 * Functions to check if user is authenticated properly
 *
 * If not redirect to login!
 */
function isUserAuthenticatedNoAjax () 
{
    /* open session and get username / pass */
	if (!isset($_SESSION)) { 
		session_start();
	}
    
    /* redirect if not authenticated */
    if (empty($_SESSION['ipamusername'])) {
        header("Location:login");
    }
    /* close session */
    session_write_close();    
}


/**
 * Resolve reverse DNS name if blank
 * Return class and name
 *
 */
function ResolveDnsName ( $ip ) 
{

    // format to dotted representation
    $ip = Transform2long ( $ip );
    
    // resolve dns name if it is empty and configured
    if ( empty($dns_name) ) {
        $return['class'] = "resolved";
        $return['name']  = gethostbyaddr( $ip );
    }
        
    // if nothing resolves revert to blank
    if ($return['name'] ==  $ip) {
        $return['name'] = "";
    }

    /* return result */
    return($return);
}


/**
 * validate email
 */
function checkEmail($email) {
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
		return false;
    }
    else {
    	return true;
    }
}


/**
 * Shorten text
 */
function ShortenText($text, $chars = 25) {  
	//count input text size
	$startLen = strlen($text);
	//cut onwanted chars
    $text = substr($text,0,$chars); 
	//count output text size
	$endLen = strlen($text);

	//append dots if it was cut
	if($endLen != $startLen) {
		$text = $text."..."; 
	}
	
    return $text; 
} 


/**
 * Present numbers in pow 10, only for IPv6
 */
function reformatNumber ($number)
{

	$length = strlen($number);
	$pos	= $length - 3;
	
	if ($length > 8) {
		$number = "~". substr($number, 0, $length - $pos) . "&middot;10^<sup>". $pos ."</sup>";
	}
	
	return $number;
}


/**
 *	Reformat IP address state
 */
function reformatIPState ($state)
{
	/* 
	0 = not active
	1 = active
	2 = reserved
	*/
	switch ($state)
	{
		case "0": return "Offline"; break;
		case "1": return " "; 		break;
		case "2": return "Reserved";break;
		default: return $state;
	}	
}


/**
 *	Validate VLAN number
 */
function validateVlan ($vlan)
{
	/* must be number:
		not 1
		reserved 1002-1005
		not higher that 4094
	*/
	if(empty($vlan)) {
		return 'ok';
	}
	else if(!is_numeric($vlan)) {
		return 'VLAN must be numeric value!';
	}
	else if ($vlan == 1) {
		return 'Cannot use VLAN number 1';
	}
	else if ( ($vlan > 1001 ) && ($vlan < 1006) ) {
		return 'Reserved VLAN number (1002 - 1005)';
	}
	else if ($vlan > 4094) {
		return 'Vlan number can be max 4094';
	}
	else {
		return 'ok';
	}
}


/****************************************
 * 
 *      IP calculation functions
 *
 ***************************************/

/**
 * Transform IP address from decimal to dotted (167903488 -> 10.2.1.0)
 */
function Transform2long ($ip) 
{
    if (IdentifyAddress($ip) == "IPv4" ) {
        return(long2ip($ip));
    }
    else {
        return(long2ip6($ip));
    }
} 


/**
 * Transform IP address from dotted to decimal (10.2.1.0 -> 167903488)
 */
function Transform2decimal ($ip) 
{
    if (IdentifyAddress($ip) == "IPv4" ) {
        return( sprintf("%u", ip2long($ip)) );
    }
    else {
        return(ip2long6($ip));
    }
} 
 

/**
 * Calculate maximum number of IPv4 / IPv6 hosts per subnet
 */
function MaxHosts( $mask, $type = 0 ) 
{
    /* IPv4 address */
    if($type == 0) {
		return pow(2, (32 - $mask)) -2;
    }
     /* IPv6 address */
	else {
	   return gmp_strval(gmp_sub(gmp_pow(2, 128 - $mask) ,1));
    }
}


/**
 * identify ip address type - ipv4 or ipv6?
 *
 * first we need to find representation - decimal or dotted?
 */
function IdentifyAddress( $subnet ) 
{   
    /* dotted */
    if (strpos($subnet, ":")) {
        return 'IPv6';
    }
    else if (strpos($subnet, ".")) {
        return 'IPv4';
    } 
    /* decimal */
    else  {
        /* IPv4 address */
        if(strlen($subnet) < 12) {
    		return 'IPv4';
        }
        /* IPv6 address */
    	else {
    		return 'IPv6';
        }
    }
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


/**
 * verify ip address from edit / add
 */
function VerifyIpAddress( $ip , $subnet ) 
{
	/* First identify it */
	$type = IdentifyAddress( $ip );
	$type = IdentifyAddress( $subnet );
	
	/* IPv4 verification */
	if ( $type == 'IPv4' )
	{
        require_once 'PEAR/Net/IPv4.php';
        
		// is it valid?
		if (!Net_IPv4::validateIP($ip)) {
			$error = "IP address not valid!";
		}
		// it must be in provided subnet
		else if (!Net_IPv4::ipInNetwork($ip, $subnet)) {
			$error = "IP address not in selected subnet!";
		}
		// It cannot be subnet or broadcast
		else {
            $net = Net_IPv4::parseAddress($subnet);
            
            if ($net->network == $ip) {
                $error = "Cannot add subnet as IP address!";   
            }
            else if ($net->broadcast == $ip) {
                $error = "Cannot add broadcast as IP address!"; 
            }
		}
	}
	
	/* IPv6 verification */
	else 
	{
        require_once 'PEAR/Net/IPv6.php';
        
        //remove /xx from subnet
        $subnet_short = Net_IPv6::removeNetmaskSpec($subnet);
		
		// is it valid?
		if (!Net_IPv6::checkIPv6($ip)) {
			$error = "IP address not valid!";
		}
		// it must be in provided subnet
		else if (!Net_IPv6::isInNetmask($ip, $subnet)) {
			$error = "IP address not in selected subnet!";
		}
		//it cannot be subnet
		else if ($ip == $subnet_short) {
		      $error = "Cannot add subnet as IP address!";  
		}
	}
	
	/* return results */
	if( isset($error) )
        return $error;
	else {
		return false;
    }
}


/**
 * verify ip address /mask 10.10.10.10./24 - CIDR 
 *
 * if subnet == 0 we dont check if IP is subnet -> needed for ipCalc
 */
function verifyCidr( $cidr , $subnet = 1 ) 
{
    /* split it to network and subnet */
    $temp = explode("/", $cidr);
    
    $network = $temp[0];
    $netmask = $temp[1];
    
    //if one part is missing die
    if (empty($network) || empty($netmask)) {
        $errors[] = "Invalid CIDR format!";
    }

	/* Identify address type */
	$type = IdentifyAddress( $network );
	
	/* IPv4 verification */
	if ( $type == 'IPv4' )
	{
        require_once 'PEAR/Net/IPv4.php';

        if ($net = Net_IPv4::parseAddress ($cidr)) {
            //validate IP
            if (!Net_IPv4::validateIP ($net->ip)) {
                $errors[] = "Invalid IP address!";
            }
            //network must be same as provided IP address
            else if (($net->network != $net->ip) && ($subnet == 1)) {
                $errors[] = "IP address cannot be subnet! (Consider using ". $net->network .")";
            }
            //validate netmask
            else if (!Net_IPv4::validateNetmask ($net->netmask)) {
                $errors[] = 'Invalid netmask ' . $net->netmask;
            }            
        }
        else {
            $errors[] = 'Invalid CIDR format!';
        }
	}	
	/* IPv6 verification */
	else 
	{
        require_once 'PEAR/Net/IPv6.php';

        //validate IPv6
        if (!Net_IPv6::checkIPv6 ($cidr) ) {
            $errors[] = "Invalid IPv6 address!";
        }
        else {
            
            //validate subnet
            $subnet = Net_IPv6::getNetmask($cidr);
            $subnet = Net_IPv6::compress($subnet);

            $subnetParse = explode("/", $cidr);
            $subnetMask  = $subnetParse[1];
            $subnetNet   = $subnetParse[0];
        
            if ( ($subnetParse[0] != $subnet) && ($subnet == 1) ) {
                $errors[] = "IP address cannot be subnet! (Consider using ". $subnet ."/". $subnetMask .")";
            }
	   }
    }
    
	/* return array of errors */
	return($errors);
}


/**
 * Verify that switch exists
 */
function verifySwitchByName ($hostname)
{
    /* get variables from config file */
    global $db;
    
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
    $query = 'select * from `switches` where `hostname` = "'. $hostname .'";';
    
    /* fetch role */
    $role = $database->getRow($query);

    /* close database connection */
    $database->close();
    
    /* return true if viewer, else false */
    if (!$role) {
        return false;
    }
    else {
        return true;
    }

}



/**
 * parse IP address
 *
 * IP must be in  CIDR format - '192.168.0.50/16'
 */
function parseIpAddress( $ip, $mask )
{
    /* IPv4 address */
    if ( IdentifyAddress( $ip ) == "IPv4" )
    {
        
        require('PEAR/Net/IPv4.php');
        $net = Net_IPv4::parseAddress( $ip .'/'. $mask );
        
        $out['network']   = $net->network;   // 192.168.0.0
        $out['ip']        = $net->ip;        // 192.168.0.50
        $out['broadcast'] = $net->broadcast; // 192.168.255.255
        $out['bitmask']   = $net->bitmask;   // 16
        $out['netmask']   = $net->netmask;   // 255.255.0.0

    }
    /* IPv6 address */
    else
    {
        require('PEAR/Net/IPv6.php');  

        $out['network']   = $ip;         // 2a34:120:feel::
        $out['bitmask']   = $mask;         // 48
        $out['netmask']   = $mask;         // 48 - we just duplicate it
        
        //broadcast - we fake it with highest IP in subnet
        $net = Net_IPv6::parseaddress( $ip .'/'. $mask );
        
        $out['broadcast'] = $net['end'];    // 2a34:120:feel::ffff:ffff:ffff:ffff:ffff      
    }
    
    return( $out );
} 


/**
 * Calculate subnet details
 *
 * Calculate subnet details based on input!
 *
 * We must provide used hosts and subnet mask to calculate free hosts, and subnet to identify type
 */
function calculateSubnetDetails ( $usedhosts, $bitmask, $subnet )
{

    // number of used hosts
    $SubnetCalculateDetails['used']              = $usedhosts;
    
    // calculate max hosts
    if ( IdentifyAddress( $subnet ) == "IPv4") {
        $type = 0;
    }
    else {
        $type = 1;
    }
    
    $SubnetCalculateDetails['maxhosts']          = MaxHosts( $bitmask, $type ); 
    
    // calculate free hosts
    $SubnetCalculateDetails['freehosts']         = gmp_strval( gmp_sub ($SubnetCalculateDetails['maxhosts'] , $SubnetCalculateDetails['used']) );

	//reset maxhosts for /31 and /32 subnets
	if (gmp_cmp($SubnetCalculateDetails['maxhosts'],1) == -1) {
		$SubnetCalculateDetails['maxhosts'] = "1";
	}

    // calculate use percentage
    $SubnetCalculateDetails['freehosts_percent'] = round( ( ($SubnetCalculateDetails['freehosts'] * 100) / $SubnetCalculateDetails['maxhosts']), 2 );
 
    return( $SubnetCalculateDetails );
}


/**
 * Find unused ip addresses between two provided
 *
 * subnet must be without subnet
 */
function FindUnusedIpAddresses ($ip1, $ip2, $type, $broadcast = 0 ) 
{              
    /* calculate difference */
    $diff = gmp_strval(gmp_sub($ip2, $ip1));
    
    /* if diff is less than 2 return false */
    if ( $diff < 2 ) {
        return false;
    }
    /* if diff is 2 return 1 IP address in the middle */
    else if ( $diff == 2 ) 
    {
        if ($type == "IPv4") 
        {   //ipv4
			$result['ip'] 	 = long2ip($ip1 +1);
			$result['hosts'] = "1";
        }
        else 
        {   //ipv6
            $ip1_return = gmp_strval(gmp_add($ip1,1));
            
			$result['ip'] 	 = long2ip6( $ip1_return );
			$result['hosts'] = "1";
        }
    }
    /* if diff is more than 2 return pool */
    else 
    {
        if ($type == "IPv4") 
        {   //ipv4
            $free = long2ip($ip1 +1) . ' - ' . long2ip($ip2 -1);
            
			$result['ip'] 	 = $free;
			$result['hosts'] = gmp_strval(gmp_sub($diff, 1));;
        }
        else 
        {   //ipv6
            $ip1_return = gmp_strval(gmp_add($ip1,1));
            
            //No broadcast in IPv6
            if ($broadcast == 0) 
            { 
                $ip2_return = gmp_strval(gmp_sub($ip2,1));
            }
            else
            {
                $ip2_return = gmp_strval($ip2);           
            }
            
            $free = long2ip6( $ip1_return ) . ' - ' . long2ip6( $ip2_return );
            
				$result['ip'] 	 = $free;
				$result['hosts'] = gmp_strval(gmp_sub($diff, 1));
        }
    }
    
    /* return result array with IP range and free hosts */
    return $result;
}


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


/**
 * Functions to transform IPv6 to decimal and back
 *
 */
function ip2long6 ($ipv6) 
{ 
	if($ipv6 == ".255.255.255") {
		return false;
	}
    $ip_n = inet_pton($ipv6); 
    $bits = 15; // 16 x 8 bit = 128bit 
    $ipv6long = "";
    
    while ($bits >= 0) 
    { 
        $bin = sprintf("%08b",(ord($ip_n[$bits]))); 
        $ipv6long = $bin.$ipv6long; 
        $bits--; 
    } 
    return gmp_strval(gmp_init($ipv6long,2),10); 
} 

function long2ip6($ipv6long) 
{ 
    $bin = gmp_strval(gmp_init($ipv6long,10),2); 
    $ipv6 = "";
    
    if (strlen($bin) < 128) { 
        $pad = 128 - strlen($bin); 
        for ($i = 1; $i <= $pad; $i++) { 
            $bin = "0".$bin; 
        } 
    } 
  
    $bits = 0; 
    while ($bits <= 7) 
    { 
        $bin_part = substr($bin,($bits*16),16);         
        $ipv6 .= dechex(bindec($bin_part)).":"; 
        $bits++; 
    } 
    // compress result
    return inet_ntop(inet_pton(substr($ipv6,0,-1))); 
} 




/****************************************
 * 
 * Functions for database communication
 *
 ***************************************/

/**
 * set insert / update / delete query for addin IP address
 * based on provided array
 */
function SetInsertQuery( $ip ) 
{
	/* insert */
	if( $ip['action'] == "Add" ) 
	{
		$query  = "insert into ipaddresses ";
		$query .= "(`subnetId`,`description`,`ip_addr`, `dns_name`,`mac`, `owner`, `state`, `switch`, `port`, `note`) ";
		$query .= "values ";
		$query .= "('". $ip['subnetId'] ."', '". $ip['description'] ."', '". Transform2decimal( $ip['ip_addr'] ) ."', ". "\n"; 
		$query .= " '". $ip['dns_name'] ."', '". $ip['mac'] ."', '". $ip['owner'] ."', '". $ip['state'] ."', ". "\n";
		$query .= " '". $ip['switch'] ."', '". $ip['port'] ."', '". $ip['note'] ."');";
	}
	/* edit multiple */
	else if( ($ip['action'] == "Edit") && ($ip['type'] == "series") ) 
	{
		$query  = "update ipaddresses ";
		$query .= "set `description` = '". $ip['description'] ."', ";
		$query .= "`dns_name` = '". $ip['dns_name'] ."' ,"; 
		$query .= "`mac` = '". $ip['mac'] ."' ,"; 
		$query .= "`owner` = '". $ip['owner'] ."' ,"; 
		$query .= "`state` = '". $ip['state'] ."',";
		$query .= "`switch` = '". $ip['switch'] ."',";
		$query .= "`port` = '". $ip['port'] ."',";
		$query .= "`note` = '". $ip['note'] ."' ";
		$query .= "where `subnetId` = '". $ip['subnetId'] ."' and `ip_addr` = '". Transform2decimal( $ip['ip_addr'] ) ."';";	
	}
	/* edit */
	else if( $ip['action'] == "Edit" ) 
	{
		$query  = "update ipaddresses ";
		$query .= "set `description` = '". $ip['description'] ."', `dns_name` = '". $ip['dns_name'] ."' , `mac` = '". $ip['mac'] ."', ". "\n"; 
		$query .= "`owner` = '". $ip['owner'] ."' , `state` = '". $ip['state'] ."', `switch` = '". $ip['switch'] ."', ". "\n"; 
		$query .= "`port` = '". $ip['port'] ."', `note` = '". $ip['note'] ."' ";
		$query .= "where `id` = '". $ip['id'] ."';";	
	}
	/* delete multiple */
	else if( ($ip['action'] == "Delete") && ($ip['type'] == "series") ) {
		$query = "delete from ipaddresses where `subnetId` = '". $ip['subnetId'] ."' and `ip_addr` = '". Transform2decimal( $ip['ip_addr'] ) ."';";	
	}
	/* delete */
	else if( $ip['action'] == "Delete" ) {
		$query = "delete from ipaddresses where `id` = '". $ip['id'] ."';";	
	}
	
	/* return query */		
	return $query;
}


/**
 * Check if user is admin
 */
function checkAdmin ($die = true, $startSession = true) 
{
    /* get variables from config file */
    global $db;
    
    /* first get active username */
    if($startSession == true) {
    	session_start();
    }
    $ipamusername = $_SESSION['ipamusername'];
    session_write_close();
    
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);

    /* Check connection */
    if ($database->connect_error) {
		header('Location: login');
	}

	/* set query if database exists! */
    $query = 'select role from users where username = "'. $ipamusername .'";';
    
    /* fetch role */
    $role = $database->getRow($query);

    /* close database connection */
    $database->close();
    
    /* return true if admin, else false */
    if ($role[0] == "Administrator") {
        return true;
    }
    else {
    	//die
    	if($die == true) {
	        die('<div class="error">Not admin!</div>');
    	}
    	//return false if called
    	else {
    		return false;
    	}
    }
         
}


/**
 * Check if user is admin or operator
 */
function isUserViewer () 
{
    /* get variables from config file */
    global $db;
    
    /* first get active username */
	if (!isset($_SESSION)) { 
		session_start();
	}
	
    $ipamusername = $_SESSION['ipamusername'];
    session_write_close();
    
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
    $query = 'select role from users where username = "'. $ipamusername .'";';
    
    /* fetch role */
    $role = $database->getRow($query);

    /* close database connection */
    $database->close();
    
    /* return true if viewer, else false */
    if ( ($role[0] == "Administrator") || ($role[0] == "Operator") ) {
        return false;
    }
    else {
        return true;
    }
         
}


/**
 * Get active users username - from session!
 */
function getActiveUserDetails ()
{
/*     session_start(); */
	if (!isset($_SESSION)) { 
		session_start();
	}

	if(isset($_SESSION['ipamusername'])) {
    	return getUserDetailsByName ($_SESSION['ipamusername']);
    }
    session_write_close();
}


/**
 * Get all sections
 */
function fetchSections ()
{
    /* get variables from config file */
    global $db;

    /* set query */
    $query 	  = 'select * from sections;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* fetch results */
    $sections  = $database->getArray($query); 

    /* close database connection */
    $database->close();

    /* return subnets array */
    return($sections);
}


/**
 * Get all subnets
 */
function fetchAllSubnets ()
{
    /* get variables from config file */
    global $db;

    /* set query */
    $query 	  = 'select * from subnets;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* fetch results */
    $sections  = $database->getArray($query); 

    /* close database connection */
    $database->close();

    /* return subnets array */
    return($sections);
}


/**
 * Get all IP addresses
 */
function fetchAllIPAddresses ($hostnameSort = false)
{
    /* get variables from config file */
    global $db;
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* set query */
    if(!$hostnameSort) {
    	$query 	  = 'select * from ipaddresses;'; 
    }
    else {
/*     	$query 	   = 'select * from ipaddresses where `dns_name` != "" order by dns_name asc;';  */
    	$query 	   = 'select * from ipaddresses order by dns_name desc;'; 
    }

    /* fetch results */
    $ipaddresses  = $database->getArray($query); 

    /* close database connection */
    $database->close();

    /* return subnets array */
    return($ipaddresses);
}


/**
 * Get all IP addresses by hostname
 */
function fetchAllIPAddressesByName ($hostname)
{
    /* get variables from config file */
    global $db;

    /* set query */
    $query 	  = 'select * from ipaddresses where `dns_name` like "%'. $hostname .'%" order by `dns_name` desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* fetch results */
    $ipaddresses  = $database->getArray($query); 

    /* close database connection */
    $database->close();

    /* return subnets array */
    return($ipaddresses);
}



/**
 * Get all subnets in provided sectionId
 */
function fetchSubnets ($sectionId)
{
    /* get variables from config file */
    global $db;

    /* set query, open db connection and fetch results */
    $query 	  = 'select * from subnets where sectionId = "'. $sectionId .'" ORDER BY subnet ASC;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $subnets  = $database->getArray($query);
    $database->close();

    /* return subnets array */
    return($subnets);
}


/**
 * Get all master subnets in provided sectionId
 */
function fetchMasterSubnets ($sectionId)
{
    /* get variables from config file */
    global $db;

    /* set query, open db connection and fetch results */
    $query 	  = 'select * from subnets where sectionId = "'. $sectionId .'" and (`masterSubnetId` = "0" or `masterSubnetId` IS NULL) ORDER BY subnet ASC;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $subnets  = $database->getArray($query);
    $database->close();

    /* return subnets array */
    return($subnets);
}


/**
 * Get all slave subnets in provided subnetId
 */
function getAllSlaveSubnetsBySubnetId ($subnetId)
{
    /* get variables from config file */
    global $db;

    /* set query, open db connection and fetch results */
    $query 	  = 'select * from subnets where `masterSubnetId` = "'. $subnetId .'" ORDER BY subnet ASC;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $subnets  = $database->getArray($query);
    $database->close();

    /* return subnets array */
    return($subnets);
}


/**
 * Get section details - provide section id
 */
function getSectionDetailsById ($id)
{
    /* get variables from config file */
    global $db;

    /* set query, open db connection and fetch results */
    $query 	  = 'select * from sections where id = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $subnets  = $database->getArray($query);
    $database->close();

    /* return subnets array */
    return($subnets[0]);
}


/**
 * Get sectionId for requested name - needed for hash page loading
 */
function getSectionIdFromSectionName ($sectionName) 
{
    /* get variables from config file */
    global $db;
    
    /* set query, open db connection and fetch results */
    $query         = 'select id from sections where name = "'. $sectionName .'";';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $SubnetDetails = $database->getArray($query);
    $database->close();

    /* return subnet details - only 1st field! We cannot do getRow because we need associative array */
    return($SubnetDetails[0]['id']); 

}


/**
 * Get all ip addresses in requested subnet bt provided Id
 */
function getIpAddressesBySubnetId ($subnetId) 
{
    /* get variables from config file */
    global $db;
    
    /* set query, open db connection and fetch results */
    $query       = 'select * from ipaddresses where subnetId = "'. $subnetId .'" order by ip_addr ASC;';
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $ipaddresses = $database->getArray($query);
    $database->close();

    /* return ip address array */
    return($ipaddresses);       
}


/**
 * Count number of ip addresses in provided subnet
 */
function countIpAddressesBySubnetId ($subnetId) 
{
    /* get variables from config file */
    global $db;
    
    /* set query, open db connection and fetch results */
    $query       = 'select count(*) from ipaddresses where subnetId = "'. $subnetId .'" order by subnetId ASC;';
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $count 		 = $database->getArray($query);
    $database->close();
    
    /* we only need count field */
    $count	= $count[0]['count(*)'];
    
    /* return ip address array */
    return($count);       
}


/**
 * Get details for requested subnet by Id
 *
 * *** OLD ***  - not used anymore!!!
 */
function getSubnetDetails ($subnetId)
{
    /* get variables from config file */
    global $db;
    
    /* set query, open db connection and fetch results */
    $query         = 'select * from subnets where id = "'. $subnetId .'";';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $SubnetDetails = $database->getArray($query);
    $database->close();

    /* return subnet details - only 1st field! We cannot do getRow because we need associative array */
    return($SubnetDetails[0]); 
}


/**
 * Get details for requested subnet by ID
 */
function getSubnetDetailsById ($id)
{
    /* get variables from config file */
    global $db;
    
    /* set query, open db connection and fetch results */
    $query         = 'select * from subnets where id = "'. $id .'";';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $SubnetDetails = $database->getArray($query);
    $database->close();

    /* return subnet details - only 1st field! We cannot do getRow because we need associative array */
    return($SubnetDetails[0]); 
}


/**
 * Check for duplicates on add
 */
function checkDuplicate ($ip, $subnetId)
{
    /* get variables from config file */
    global $db;
    
    /* we need to put IP in decimal format */
    $ip = Transform2decimal ($ip);
    
    /* set query, open db connection and fetch results */
    $query         = 'select * from ipaddresses where ip_addr = "'. $ip .'" and subnetId = "'. $subnetId .'" ;';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $unique        = $database->getArray($query);
    $database->close();

    /* return false if it exists */
    if (sizeof($unique) != 0 ) {
        return true;
    }
    else {
        return false;
    }
}


/**
 * Modify ( add / edit / delete ) IP address
 */
function modifyIpAddress ($ip) 
{
    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $query    = SetInsertQuery($ip);
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    if ( !$database->executeQuery($query) ) {
        return false;
    }
    else {
        return true;
    }
}


/**
 * Get IP address details
 */
function getIpAddrDetailsById ($id) 
{
    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $query    = 'select * from ipaddresses where id = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $details  = $database->getArray($query); 
    
    //we only fetch 1 field
    $details  = $details[0];
	//change IP address formatting to dotted(long)
	$details['ip_addr'] = Transform2long( $details['ip_addr'] ); 
	   
    /* return result */
    return($details);
}

/**
 * Set Query for update section
 */
function setUpdateSectionQuery ($update) 
{
	/* add */
    if ($update['action'] == "Add")
    {
        $query = 'Insert into sections (`name`,`description`) values ("'. $update['name'] .'", "'. $update['description'] .'");';
    }
    /* edit */
    else if ($update['action'] == "Edit")
    {
        /* We need old section name first! - we nneed ['name'] field */
        $section_old = getSectionDetailsById ( $update['id'] );
        
        /* Update section name */
        $query   = 'update sections set `name` = "'. $update['name'] .'", `description` = "'. $update['description'] .'" where `id` = "'. $update['id'] .'";';	
		
    }
	/* delete */
	else if( $update['action'] == "Delete" ) 
	{
        /* we must delete many entries - section, all belonging subnets and ip addresses */
        $sectionId = $update['id'];
        
        // delete sections
		$query  = "delete from sections where `id` = '". $sectionId ."';";
		// delete belonging subnets
		$query .= "delete from subnets where `sectionId` = '". $sectionId ."';";
		// delete IP addresses
		$subnets = fetchSubnets ( $sectionId );
		
		if (sizeof($subnets) != 0)
		{
            foreach ($subnets as $subnet) 
            {
            $query .= 'delete from ipaddresses where subnetId = "'. $subnet['id'] .'";';
            }
        }
    }
    
    /* return query */
    return $query;
}


/**
 * Update section
 */
function UpdateSection ($update) 
{
    /* first we ned to set query */
    $query = setUpdateSectionQuery ($update);
    
    /* name must be provided! */
    if (!$update['name']) {
        die('<div class="error">Name is mandatory!</div>');
    }

    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* set log file */
	$log = prepareLogFromArray ($update);

    /* delete and edit requires multiquery */
    if ( ( $update['action'] == "Delete") || ( $update['action'] == "Edit") )
    {
        if (!$result  = $database->executeMultipleQuerries($query)) {
            die('<div class="error">Cannot '. $update['action'] .' all entries!</div>');
            updateLogTable ('Section ' . $update['action'] .' failed ('. $update['name'] . ')', $log, 2);
        }
        else {
            updateLogTable ('Section '. $update['name'] . ' ' . $update['action'] .' ok', $log, 1);
            return true;
        }
    }
    /* add is single querry */
    else 
    {
        if (!$result  = $database->executeQuery($query)) {
            updateLogTable ('Adding section '. $update['name'] .'failed', $log, 2);
            die('<div class="error">Cannot update database!</div>');
        }
        else {
            updateLogTable ('Section '. $update['name'] .' added succesfully', $log, 1);
            return true;
        }
    }
}

/**
 * Get all users
 */
function getAllUsers ()
{
    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $query    = 'select * from users order by id desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $details  = $database->getArray($query); 
	   
    /* return results */
    return($details);
}

/**
 * Get all admin users
 */
function getAllAdminUsers ()
{
    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $query    = 'select * from users where `role` = "Administrator" order by id desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $details  = $database->getArray($query); 
	   
    /* return results */
    return($details);
}


/**
 * Get user details by ID
 */
function getUserDetailsById ($id)
{
    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $query    = 'select * from users where id = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $details  = $database->getArray($query); 
    
    //we only need 1st field
    $details = $details[0];
    
    /* return results */
    return($details);
}


/**
 * Get user details by name
 */
function getUserDetailsByName ($username)
{
    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $query    = 'select * from users where username LIKE BINARY "'. $username .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $details  = $database->getArray($query); 
    
    //we only need 1st field
    $details = $details[0];
    
    /* return results */
    return($details);
}


/**
 * Verify Input on add
 */
function verifyUserModInput ($userModDetails)
{
    //real name must be entered
    if (!$userModDetails['real_name']) {
        $errors[] = 'Real name field is mandatory!';
    }
    
    //Both passwords must be same
    if ($userModDetails['password1'] != $userModDetails['password2']) {
        $errors[] = "Passwords do not match!";
    }
    //pass must be at least 8 chars long for non-domain users
    if($userModDetails['domainUser'] != 1 ) { 
    	if ((strlen($userModDetails['password1']) < 8 ) && (strlen($userModDetails['password1']) != 0)) {
    	    $errors[] = "Password must be at least 8 characters long!";
    	}
    	else if (($userModDetails['action'] == "Add") && (strlen($userModDetails['password1']) < 8 )) {
        	$errors[] = "Password must be at least 8 characters long!";    
    	}
    }
    
    //email format must be valid
    if (!checkEmail($userModDetails['email'])) {
        $errors[] = "Invalid email address!";
    }
    
    //username must not already exist (if action is add)
    if ($userModDetails['action'] == "Add") {
        /* get variables from config file */
        global $db;
        /* set query, open db connection and fetch results */
        $query    = 'select * from users where username = "'. $userModDetails['username'] .'";';
        $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
        $details  = $database->getArray($query); 
    
        if (sizeof($details) != 0) {
            $errors[] = "User ". $userModDetails['username'] ." already exists!";
        }
    }
    
    /* return errors */
    return($errors);
}

/**
 * Delete user by ID
 */
function deleteUserById($id, $name = "")
{
    /* get variables from config file */
    global $db;
    /* set query, open db connection and fetch results */
    $query    = 'delete from users where id = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('Cannot delete user '. $name, 'Cannot delete user '. $name , 2);
        return false;
    }
    else {
        updateLogTable ('User '. $name .' deleted ok', 'User '. $name .' deleted ok', 1);
        return true;
    }
}

/**
 * Update user by ID - if id is empty add new user!
 */
function updateUserById ($userModDetails) {

    /* get variables from config file */
    global $db;
    /* open db connection */
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* set query */
    if (empty($userModDetails['userId'])) {
        $query  = 'insert into users ' . "\n";
        $query .= '(`username`, `password`, `role`, `real_name`, `email`, `domainUser`) '. "\n"; 
        $query .= 'values '. "\n"; 
        $query .= '("'. $userModDetails['username'] .'", "'. $userModDetails['password1'] .'", "'. $userModDetails['role'] .'", '. "\n";
        $query .= ' "'. $userModDetails['real_name'] .'", "'. $userModDetails['email'] .'", "'. $userModDetails['domainUser'] .'" );';
    }
    else {
        $query  = 'update users set '. "\n"; 
        $query .= '`username` = "'. $userModDetails['username'] .'", '. "\n"; 
        if (strlen($userModDetails['password1']) != 0) {
        $query .= '`password` = "'. $userModDetails['password1'] .'", '. "\n"; 
        }
        $query .= '`role`     = "'. $userModDetails['role'] .'", '. "\n"; 
        $query .= '`real_name`= "'. $userModDetails['real_name'] .'", '. "\n"; 
        $query .= '`email`    = "'. $userModDetails['email'] .'", '. "\n"; 
        $query .= '`domainUser`= "'. $userModDetails['domainUser'] .'" '. "\n"; 
        $query .= 'where id   = "'. $userModDetails['userId'] .'";';
    }
    
    /* set log file */
	$log = prepareLogFromArray ($userModDetails);
    
    /* execute query */
    if (!$database->executeQuery($query)) {
        updateLogTable ('Cannot modify user '. $userModDetails['username'], $log, 2);
        return false;
    }
    else {
        updateLogTable ('User '. $userModDetails['username'] .' updated ok', $log, 1);
        return true;
    }
}


/**
 * User self-update
 */
function selfUpdateUser ($userModDetails)
{
    /* get variables from config file */
    global $db;
    /* open db connection */
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

    /* set query */
    $query  = 'update users set ' . "\n";
    if(strlen($userModDetails['password1']) != 0) {
    $query .= '`password` = "'. $userModDetails['password1'] .'",' . "\n";
    }
    $query .= '`real_name`= "'. $userModDetails['real_name'] .'", ' . "\n";
    $query .= '`email`    = "'. $userModDetails['email'] .'"' . "\n";
    $query .= 'where id   = "'. $userModDetails['userId'] .'";';
    
    /* set log file */
    $log = prepareLogFromArray ($userModDetails);
                    
    /* execute query */
    if (!$database->executeQuery($query)) {
        updateLogTable ('User '. $userModDetails['real_name'] . ' selfupdate failed', $log,  2);
        return false;
    }
    else {
        updateLogTable ('User '. $userModDetails['real_name'] . ' selfupdate ok', $log, 1);
        return true;
    }
}


/**
 * Check if subnet already exists in section!
 * 
 * Subnet policy:
 *      - inside section subnets cannot overlap!
 *      - same subnet can be configured in different sections
 */
function verifySubnetOverlapping ($sectionId, $subnetNew) 
{
    /* we need to get all subnets in section */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    
    /* first we must get all subnets in section (by sectionId) */
    $querySubnets     = 'select subnet,mask from subnets where sectionId = "'. $sectionId .'";';  
    $allSubnets       = $database->getArray($querySubnets);   

    /* set new Subnet array */
    $subnet['subnet'] = $subnetNew;
    
    /* IPv4 or ipv6? */
    $type = IdentifyAddress( $subnet['subnet'] );

    /* we need network and broadcast address and check for both if the exist in any network!*/
    if ($type == "IPv4")
    {
        /* verify new against each existing if they exist */
        if (!empty($allSubnets)) {
            foreach ($allSubnets as $existingSubnet) {
            	
            	/* we need cidr format! */
            	$existingSubnet['subnet'] = Transform2long($existingSubnet['subnet']) .'/'. $existingSubnet['mask'];
                                
                if ( verifyIPv4SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
                    return 'Subnet overlapps with '. $existingSubnet['subnet'];
                }
            }
        }
    }
    else
    {      
        /* verify new against each existing */
        foreach ($allSubnets as $existingSubnet) {
            
            /* we need cidr format! */
            $existingSubnet['subnet'] = Transform2long($existingSubnet['subnet']) .'/'. $existingSubnet['mask'];
            
            if ( verifyIPv6SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
                return 'Subnet overlapps with '. $existingSubnet['subnet'];
            }
        }
    }
    
    return false;
}


/**
 * Check if nested subnet already exists in section!
 * 
 * Subnet policy:
 *      - inside section subnets cannot overlap!
 *      - same subnet can be configured in different sections
 */
function verifyNestedSubnetOverlapping ($sectionId, $subnetNew) 
{
    /* we need to get all subnets in section */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    
    /* first we must get all subnets in section (by sectionId) */
    $querySubnets     = 'select subnet,mask from subnets where sectionId = "'. $sectionId .'" and `masterSubnetId` != "0" and `masterSubnetId` IS NOT NULL;';  
    $allSubnets       = $database->getArray($querySubnets);   

    /* set new Subnet array */
    $subnet['subnet'] = $subnetNew;
    
    /* IPv4 or ipv6? */
    $type = IdentifyAddress( $subnet['subnet'] );

    /* we need network and broadcast address and check for both if the exist in any network!*/
    if ($type == "IPv4")
    {
        /* verify new against each existing if they exist */
        if (!empty($allSubnets)) {
            foreach ($allSubnets as $existingSubnet) {
            	
            	/* we need cidr format! */
            	$existingSubnet['subnet'] = Transform2long($existingSubnet['subnet']) .'/'. $existingSubnet['mask'];
                                
                if ( verifyIPv4SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
                    return 'Subnet overlapps with '. $existingSubnet['subnet'];
                }
            }
        }
    }
    else
    {      
        /* verify new against each existing */
        foreach ($allSubnets as $existingSubnet) {
            
            /* we need cidr format! */
            $existingSubnet['subnet'] = Transform2long($existingSubnet['subnet']) .'/'. $existingSubnet['mask'];
            
            if ( verifyIPv6SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
                return 'Subnet overlapps with '. $existingSubnet['subnet'];
            }
        }
    }
    
    return false;
}


/**
 * Check if subnet contains slaves
 */
function subnetContainsSlaves($subnetId)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all ip addresses in subnet */
    $query 		  = 'SELECT count(*) from subnets where `masterSubnetId` = "'. $subnetId .'";';    
    $slaveSubnets = $database->getArray($query);  
	
	if($slaveSubnets[0]['count(*)']) {
		return true;
	}
	else {
		return false;
	}
}


/**
 * Verify IPv4 subnet overlapping
 *
 * both must be in CIDR format (10.4.5.0/24)!
 * 
 */
function verifyIPv4SubnetOverlapping ($subnet1, $subnet2)
{
    /* IPv4 functions */
    require_once('PEAR/Net/IPv4.php'); 
       
    /* subnet 2 needs to be parsed to get subnet and broadcast */
    $net1 = Net_IPv4::parseAddress( $subnet1 );
    $net2 = Net_IPv4::parseAddress( $subnet2 );

    /* network and broadcast */
    $nw1  = $net1->network;
    $nw2  = $net2->network;
    $bc1  = $net1->broadcast; 
    $bc2  = $net2->broadcast; 
    
    /* network and broadcast in decimal format */
    $nw1_dec  = Transform2decimal( $net1->network);
    $nw2_dec  = Transform2decimal( $net2->network);
    $bc1_dec  = Transform2decimal( $net1->broadcast); 
    $bc2_dec  = Transform2decimal( $net2->broadcast); 
    
    /* calculate delta */
    $delta1 = $bc1_dec - $nw1_dec;
    $delta2 = $bc2_dec - $nw2_dec;
    
    /* calculate if smaller is inside bigger */
    if ($delta1 < $delta2) 
    {
        /* check smaller nw and bc against bigger network */
        if ( Net_IPv4::ipInNetwork($nw1, $subnet2) || Net_IPv4::ipInNetwork($bc1, $subnet2) ) {
            return true;
        }
    }
    else
    {
        /* check smaller nw and bc against bigger network */
        if ( Net_IPv4::ipInNetwork($nw2, $subnet1) || Net_IPv4::ipInNetwork($bc2, $subnet1) ) {
            return true;
        }    
    }  
    return false;
}


/**
 * Verify IPv6 subnet overlapping
 *
 * both must be in CIDR format (2001:fee1::/48)!
 *      subnet1 will be checked against subnet2
 * 
 */
function verifyIPv6SubnetOverlapping ($subnet1, $subnet2)
{
    /* IPv6 functions */
    require_once('PEAR/Net/IPv6.php');
    
    /* remove netmask from subnet1 */
    $subnet1 = Net_IPv6::removeNetmaskSpec ($subnet1);
    
    /* verify */
    if (Net_IPv6::isInNetmask ( $subnet1 , $subnet2 ) ) {
        return true;
    }

    return false;
}


/**
 * Verify that new nested subnet is inside master subnet!
 *
 * $root = root subnet
 * $new  = new subnet that we wish to add to root subnet
 */
function verifySubnetNesting ($rootId, $new)
{
	//first get details for root subnet
	$rootDetails = getSubnetDetailsById($rootId);
	$rootDetails = Transform2long($rootDetails['subnet']) . "/" . $rootDetails['mask'];
	
    /* IPv4 or ipv6? */
    $type1 = IdentifyAddress( $rootDetails );
    $type2 = IdentifyAddress( $new );
    
    /* both must be IPv4 or IPv6 */
	if($type1 != $type2) {
		return false;
		die();
	}

    /* we need network and broadcast address and check for both if the exist in any network!*/
    if(isSubnetInsideSubnet ($new, $rootDetails)) {
    	return true;
    }
    else {
    	return false;
    }
}


/**
 * Verify that subnet a is inside subnet b!
 *
 * both subnets must be in ip format (e.g. 10.10.10.0/24)
 */
function isSubnetInsideSubnet ($subnetA, $subnetB)
{
	$type = IdentifyAddress( $subnetA );
	
	/* IPv4 */
	if ($type == "IPv4") {

    	/* IPv4 functions */
    	require_once('PEAR/Net/IPv4.php'); 
       
    	/* subnet A needs to be parsed to get subnet and broadcast */
    	$net = Net_IPv4::parseAddress( $subnetA );

		//both network and broadcast must be inside root subnet!
		if( (Net_IPv4::ipInNetwork($net->network, $subnetB)) && (Net_IPv4::ipInNetwork($net->broadcast, $subnetB)) ) {
			return true;
		}
		else {
			return false;
		}
	}
	/* IPv6 */
	else {
    	/* IPv6 functions */
    	require_once('PEAR/Net/IPv6.php');
    	
    	/* remove netmask from subnet1 */
    	$subnetA = Net_IPv6::removeNetmaskSpec ($subnetA);
    
	    /* verify */
    	if (Net_IPv6::isInNetmask ( $subnetA, $subnetB ) ) {
        	return true;
    	}
    	else {
    		return false;
    	}
	}
}



/**
 * Get first available IP address
 */
function getFirstAvailableIPAddress ($subnetId)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all ip addresses in subnet */
    $query 		 = 'SELECT ip_addr from ipaddresses where subnetId = "'. $subnetId .'" order by ip_addr ASC;';    
    $ipAddresses = $database->getArray($query);  

    /* get subnet */
    $query 	= 'SELECT subnet from subnets where id = "'. $subnetId .'";';    
    $subnet = $database->getArray($query); 
    $subnet = $subnet[0]['subnet'];
    
    /* create array of IP addresses */
    $ipaddressArray[]	  = $subnet;
    foreach($ipAddresses as $ipaddress) {
    	$ipaddressArray[] = $ipaddress['ip_addr'];
    }
    //get array size
    $size = sizeof($ipaddressArray);
    $curr = 0;
    
    //if size = 0 return subnet +1
    if($size == 1) {
    	$firstAvailable[] = gmp_strval(gmp_add($ipaddressArray[0], 1));
    }
    else {
   	 
    	for($m=1; $m <= $size -1; $m++) {
    	
    		$delta = gmp_strval(gmp_sub($ipaddressArray[$m],$ipaddressArray[$m-1]));
    
    		//compare with previous
    		if ($delta != 1 ) {
    			$firstAvailable[] = gmp_strval(gmp_add($ipaddressArray[$m-1],1));
    		}
    	}
    	
    	//no delta found
    	if (empty($firstAvailable)) {
    		$firstAvailable[] = gmp_strval(gmp_add($ipaddressArray[$size-1],1));
    	} 
    }
    
    /* return first available IP address */
    return $firstAvailable[0];
}


/**
 * Add new subnet - set query
 */
function setModifySubnetDetailsQuery ($subnetDetails)
{
    /* add new subnet */
    if ($subnetDetails['subnetAction'] == "Add")
    {
        /* remove netmask and calculate decimal values! */
        $subnetDetails['subnet_temp'] = explode("/", $subnetDetails['subnet']);
        $subnetDetails['subnet']      = Transform2decimal ($subnetDetails['subnet_temp'][0]);
        $subnetDetails['mask']        = $subnetDetails['subnet_temp'][1];
        
        $query  = 'insert into subnets '. "\n";
        $query .= '(`subnet`, `mask`, `sectionId`, `description`, `VLAN`, `vrfId`, `masterSubnetId`, `allowRequests`, `adminLock`) ' . "\n";
        $query .= 'values (' . "\n";
        $query .= ' "'. $subnetDetails['subnet'] 		 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['mask'] 			 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['sectionId'] 	 .'", ' . "\n"; 
        $query .= ' "'. htmlentities($subnetDetails['description']) .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['VLAN'] 			 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['vrfId'] 		 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['masterSubnetId'] .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['allowRequests']  .'", ' . "\n";
        $query .= ' "'. $subnetDetails['adminLock']  	 .'" ' . "\n";
        $query .= ' );';
    }
    /* Delete */
    else if ($subnetDetails['subnetAction'] == "Delete")
    {
        /* first delete subnets and then belonging IP addresses! */
        $query  = 'delete from subnets where id = "'. $subnetDetails['subnetId'] .'";';;
        $query .= 'delete from ipaddresses where subnetId = "'. $subnetDetails['subnetId'] .'"';
    }
    /* Edit */
    else
    {
        $query  = 'update subnets set '. "\n";
        $query .= '`description` 	= "'. htmlentities($subnetDetails['description']) .'", '. "\n";
        $query .= '`VLAN`        	= "'. $subnetDetails['VLAN'] 			.'", '. "\n";
        $query .= '`vrfId`        	= "'. $subnetDetails['vrfId'] 			.'", '. "\n";
        $query .= '`masterSubnetId` = "'. $subnetDetails['masterSubnetId'] 	.'", '. "\n";
        $query .= '`allowRequests`  = "'. $subnetDetails['allowRequests'] 	.'", '. "\n";
        $query .= '`adminLock` 		= "'. $subnetDetails['adminLock'] 		.'"  '. "\n";
        $query .= 'where id      	= "'. $subnetDetails['subnetId'] .'";';
    }
    
    /* return query */
    return $query;
}


/**
 * Add new subnet
 */
function modifySubnetDetails ($subnetDetails) 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    
    /* set modify subnet details query */
    $query = setModifySubnetDetailsQuery ($subnetDetails);
    
    /* set log details */
	$log = prepareLogFromArray ($subnetDetails);

    /* execute query */
    if (!$database->executeMultipleQuerries($query)) {
        updateLogTable ('Subnet ('. $subnetDetails['description'] .') '. $subnetDetails['subnetAction'] .' failed', $log, 2);
        return false;
    }
    else {
        updateLogTable ('Subnet ('. $subnetDetails['description'] .') '. $subnetDetails['subnetAction'] .' ok', $log, 1);
        return true;
    }
}


/**
 * Get all avaialble VLANS
 */
function getAllVlans () 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
    $query = 'SELECT vlan,description,subnet,mask FROM subnets WHERE VLAN > 0 ORDER BY vlan ASC;';
    
    $vlans       = $database->getArray($query);  
    
    /* return vlans */
    return $vlans;
}


/**
 * Get all unique devices
 */
function getAllUniqueSwitches () 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
/*     $query   = 'SELECT distinct(switch) FROM ipaddresses order by switch DESC;'; */
    $query   = 'SELECT `hostname` FROM `switches` order by `hostname` ASC;';
    $devices = $database->getArray($query);  
    
    /* return unique devices */
    return $devices;
}



/**
 * Get all avaialble devices
 */
function getIPaddressesBySwitchName ( $name ) 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
    $query = 'SELECT * FROM ipaddresses where `switch` = "'. $name .'" order by port ASC;';
    $ip    = $database->getArray($query);  
    
    /* return vlans */
    return $ip;
}


/**
 * Get switch details by hostname
 */
function getSwitchDetailsByHostname($hostname) 
{
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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


/**
 * Update IP address list when switch hostname changes
 */
function updateIPaddressesOnSwitchChange($old, $new) 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
    $query = 'update `ipaddresses` set `switch` = "'. $new .'" where `switch` = "'. $old .'";';
    
    /* update */
    $switch    = $database->executeQuery($query);  
    
    /* return details */
    if($switch) {
    	return true;
    }
    else {
    	return false;
    }
}


/**
 * Update switch details
 */
function updateSwitchDetails($switch)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set querry based on action */
    if($switch['action'] == "add") {
    	$query  = 'insert into `switches` '. "\n";
    	$query .= '(`hostname`,`ip_addr`,`vendor`,`model`,`version`,`description`,`sections`) values '. "\n";
   		$query .= '("'. $switch['hostname'] .'", "'. $switch['ip_addr'] .'", "'. $switch['vendor'] .'", '. "\n";
   		$query .= ' "'. $switch['model'] .'", "'. $switch['version'] .'", "'. $switch['description'] .'", "'. $switch['sections'] .'" );'. "\n";
    }
    else if($switch['action'] == "edit") {
    	$query  = 'update `switches` set '. "\n";    
    	$query .= '`hostname` = "'. $switch['hostname'] .'", `ip_addr` = "'. $switch['ip_addr'] .'", `vendor` = "'. $switch['vendor'] .'", '. "\n";    
    	$query .= '`model` = "'. $switch['model'] .'", `version` = "'. $switch['version'] .'", `description` = "'. $switch['description'] .'", '. "\n";    
    	$query .= '`sections` = "'. $switch['sections'] .'" '. "\n"; 
    	$query .= 'where `id` = "'. $switch['switchId'] .'";'. "\n";    
    }
    else if($switch['action'] == "delete") {
    	$query  = 'delete from `switches` where id = "'. $switch['switchId'] .'";'. "\n";
    }
    
    /* execute query */
    $switch    = $database->executeQuery($query);  
    
    /* return details */
    if($switch) {
    	return true;
    }
    else {
    	return false;
    }
}


/**
 * reformat sections!
 *		sections are separated with ;
 */
function reformatSwitchSections ($sections) {

	if(sizeof($sections != 0)) {
	
		//first reformat
		$temp = explode(";", $sections);

		foreach($temp as $section) {
			//we have sectionId, so get its name
			$out = getSectionDetailsById($section);
			$out = $out['name'];
			
			//format output
			$result[$out] = $section;
		}
	}
	
	//return result if it exists
	if($result) {
		return $result;
	}
	else {
		return false;
	}
}


/**
 * Since 0.5 the switch management changed, so if upgrading from old version
 * we must get all existing switch names and insert it to switch table!
 */
function updateSwitchFromOldVersions() 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all existing switches */
    $query 	  = 'select distinct(`switch`) from `ipaddresses` where `switch` not like "";';
    $switches = $database->getArray($query); 
        
    /* import each to database */
    foreach($switches as $switch) {
    	$query 	  = 'insert into `switches` (`hostname`) values ("'. $switch['switch'] .'");';
    	$database->executeQuery($query);
    }
    
    return true;
}




/**
 * Update log table
 */
function updateLogTable ($command, $details = NULL, $severity = 0)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set variable */
    $date = date("Y-m-d h:i:s");
    $user = getActiveUserDetails();
    $user = $user['username'];
    
    /* set query */
    $query  = 'insert into logs '. "\n";
    $query .= '(`severity`, `date`,`username`,`command`,`details`)'. "\n";
    $query .= 'values'. "\n";
    $query .= '("'.  $severity .'", "'. $date .'", "'. $user .'", "'. $command .'", "'. $details .'");';
    
    
    /* execute */
    try {
    	$database->executeMultipleQuerries($query);
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="error">'. $error .'</div>');
	}
	
    return true;
}


/**
 * Get all logs
 */
function getAllLogs($logCount, $direction = NULL, $lastId = NULL, $highestId = NULL, $informational, $notice, $warning)
{
    /* get variables from config file */
    global $db;
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
    	die('<div class="error">'. $error .'</div>');
	}

    
    /* return vlans */
    return $logs;
}


/**
 * Count all logs
 */
function countAllLogs ()
{
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

    /* set query */
    $query = 'select id from logs order by id desc limit 1;';   
    $logs       = $database->getArray($query);  
    
    /* return vlans */
    return $logs[0]['id'];
}



/**
 *	Get all users for autocomplete
 */
function getUniqueUsers ()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
    $query    	= 'select distinct owner from ipaddresses;';  

	/* execute query */
    $users       = $database->getArray($query);  
    
    /* return result */
    return $users;
}


/**
 *	Get unique hostnames for host search
 */
function getUniqueHosts ()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
    $query    	= 'select distinct dns_name from `ipaddresses` order by `dns_name` desc;';  

	/* execute query */
    $hosts       = $database->getArray($query);  
    
    /* return result */
    return $hosts;
}


/**
 * Search function
 */
function searchAddresses ($query)
{
    /* get variables from config file */
    global $db;
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
	
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set query */    
	$query = 'select * from `subnets` where `description` like "%'. $searchterm .'%" or `subnet` between "'. $searchTermEdited['low'] .'" and "'. $searchTermEdited['high'] .'";';

	/* execute query */
    $search = $database->getArray($query); 
    
    /* return result */
    return $search;
}


/**
 *	fetch instructions
 */
function fetchInstructions () 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query 			= "select * from instructions;";
    $instructions   = $database->getArray($query);  
    
    /* return result */
    return $instructions;
}


/**
 *	Write instructions
 */
function writeInstructions ($instructions) 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	$instructions = $database->real_escape_string($instructions);	//this hides code
	
	/* execute query */
	$query 			= "update `instructions` set `instructions` = '". $instructions ."';";
    
  	/* update database */
   	if ( !$database->executeQuery($query) ) {
        updateLogTable ('Instructions update failed', $instructions, 2);
        return false;
    }
    else {
        updateLogTable ('Instructions update succeeded', $instructions, 1);
        return true;
    }
}


/**
 *	get all VRFs
 */
function getAllVRFs () 
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query = "select * from `vrf`;";
    
  	/* update database */
   	$vrfs = $database->getArray($query);
   	
   	/* return false if none, else list */
	if(sizeof($vrfs) == 0) {
		return false;
	}
	else {
		return $vrfs;
	}
}


/**
 *	get vrf details by id
 */
function getVRFDetailsById ($vrfId)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query = 'select * from `vrf` where `vrfId` = "'. $vrfId .'";';
    
  	/* update database */
   	$vrf = $database->getArray($query);
   	
   	/* return false if none, else list */
	if(sizeof($vrf) == 0) {
		return false;
	}
	else {
		return $vrf[0];
	}
}



/**
 *	get all subnets belonging to vrf
 */
function getAllSubnetsInVRF($vrfId)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query = 'select * from `subnets` where `vrfId` = "'. $vrfId .'";';
    
  	/* update database */
   	$vrf = $database->getArray($query);
   	
   	/* return false if none, else list */
	if(sizeof($vrf) == 0) {
		return false;
	}
	else {
		return $vrf;
	}
}


/**
 * Update switch details
 */
function updateVRFDetails($vrf)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set querry based on action */
    if($vrf['action'] == "add") {
    	$query  = 'insert into `vrf` '. "\n";
    	$query .= '(`name`,`rd`,`description`) values '. "\n";
   		$query .= '("'. $vrf['name'] .'", "'. $vrf['rd'] .'", "'. $vrf['description'] .'" ); '. "\n";
    }
    else if($vrf['action'] == "edit") {
    	$query  = 'update `vrf` set '. "\n";    
    	$query .= '`name` = "'. $vrf['name'] .'", `rd` = "'. $vrf['rd'] .'", `description` = "'. $vrf['description'] .'" '. "\n";     
    	$query .= 'where `vrfId` = "'. $vrf['vrfId'] .'";'. "\n";    
    }
    else if($vrf['action'] == "delete") {
    	$query  = 'delete from `vrf` where `vrfId` = "'. $vrf['vrfId'] .'";'. "\n";
    }
    
    /* execute query */
    $vrf    = $database->executeQuery($query);  
    
    /* return details */
    if($vrf) {
    	return true;
    }
    else {
    	return false;
    }
}


/**
 * CSV import IP address
 *
 *		provided input is CSV line!
 */
function importCSVline ($line, $subnetId)
{
	/* array */
	$line = explode(",", $line);

    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get subnet details by Id */
    $subnetDetails = getSubnetDetailsById ($subnetId);
    $subnet = Transform2long($subnetDetails['subnet']) . "/" . $subnetDetails['mask'];
   
    /* verify! */
    if (VerifyIpAddress( $line[0] , $subnet )) {
    	return false;
    } 
    
    /* check for duplicates */
    if (checkDuplicate ($line[0], $subnetId)) {
    	return false;
    }
    
    /* reformat state */
    switch($line[5]) {
    	case "Active": 		$line[5] = "1";	break;
    	case "Reserved": 	$line[5] = "2";	break;
    	case "Offline": 	$line[5] = "0";	break;
    }
    
	
	/* all ok, set query */
	$query  = "insert into ipaddresses ";
	$query .= "(`subnetId`, `ip_addr`, `state`, `description`, `dns_name`, `mac`, `owner`, `switch`, `port`, `note` ) ";
	$query .= "values ";
	$query .= "('". $subnetId ."', '". Transform2decimal( $line[0] ) ."', '". $line[1] ."', '". $line[2] ."', '". $line[3] ."', '". $line[4] ."', '". $line[5] ."', '". $line[6] ."', '". $line[7] ."', '". $line[8] ."');";
	
	/* set log details */
	$log = prepareLogFromArray ($line);
	
	/* insert IP address */
    if ( !$database->executeQuery($query) ) {
        updateLogTable ('CSV import of IP address '. $line[1] .' failed', $log, 2);
        return $query;
    }
    else {
        updateLogTable ('CSV import of IP address '. $line[1] .' succeeded', $log, 1);
        return true;
    }
}


/**
 * Is IP already requested?
 */
function isIPalreadyRequested($ip)
{
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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
    /* get variables from config file */
    global $db;
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


/**
 * Get all site settings
 */
function getAllSettings()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* first check if table settings exists */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "settings";';
    $count	  = $database->getArray($query); 
  
	/* return true if it exists */
	if($count[0]['count'] == 1) {
	
		/* select database */
		$database->selectDatabase($db['name']);
	
	    /* first update request */
	    $query    = 'select * from settings where id = 1';
	    $settings = $database->getArray($query); 
  
		/* return settings */
		return($settings[0]);
	}
	else {
		return false;
	}
}


/**
 * update site settings
 */
function updateSettings($settings)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'update `settings` set ' . "\n";
    $query   .= '`siteTitle` 		  = "'. $settings['siteTitle'] .'", ' . "\n";
    $query   .= '`siteDomain` 		  = "'. $settings['siteDomain'] .'", ' . "\n";
    $query   .= '`siteURL` 			  = "'. $settings['siteURL'] .'", ' . "\n";
    $query   .= '`siteAdminName` 	  = "'. $settings['siteAdminName'] .'", ' . "\n";
    $query   .= '`siteAdminMail` 	  = "'. $settings['siteAdminMail'] .'", ' . "\n";
	$query   .= '`domainAuth` 		  = "'. $settings['domainAuth'] .'", ' . "\n";
/* 	$query   .= '`domainAuth` 		  = "0", ' . "\n"; */
	$query   .= '`showTooltips`		  = "'. $settings['showTooltips'] .'", ' . "\n";
	$query   .= '`enableIPrequests`   = "'. $settings['enableIPrequests'] .'", ' . "\n";
	$query   .= '`enableVRF`   		  = "'. $settings['enableVRF'] .'", ' . "\n";
	$query   .= '`donate`   		  = "'. $settings['donate'] .'", ' . "\n";
	$query   .= '`enableDNSresolving` = "'. $settings['enableDNSresolving'] .'" ' . "\n";   
	$query   .= 'where id = 1;' . "\n";   

	/* set log file */
	foreach($settings as $key=>$setting) {
		$log .= " ". $key . ": " . $setting . "<br>";
	}
    
    /* execute query */
    if (!$database->executeQuery($query)) {
        return false;
        updateLogTable ('Failed to update settings', $log, 2);
    }
    else { 
    	updateLogTable ('Settings updated', $log, 1);
        return true;  
    }
}



/**
 * Get Domain settings for authentication
 */
function getADSettings()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
	
	/* first update request */
	$query    = 'select * from `settingsDomain` limit 1;';
	$settings = $database->getArray($query); 
  		  
	/* return settings */
	if($settings) {
		return($settings[0]);
	}
	else {
		return false;
	}
}


/**
 * Get Domain settings for authentication
 */
function updateADsettings($ad)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
	
    /* set query and update */
    $query    = 'update `settingsDomain` set '. "\n";
    $query   .= '`domain_controllers` = "'. $ad['domain_controllers'] .'", `base_dn` = "'. $ad['base_dn'] .'", `account_suffix` = "'. $ad['account_suffix'] .'", '. "\n";
    $query   .= '`use_ssl` = "'. $ad['use_ssl'] .'", `use_tls` = "'. $ad['use_tls'] .'", `ad_port` = "'. $ad['ad_port'] .'"; '. "\n";
    
    if(!$database->executeQuery($query)) {
    	return false;
    }
    else {
    	return true;
    }
}


/**
 * Check if subnet is admin-locked
 */
function isSubnetWriteProtected($subnetId)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'select `adminLock` from subnets where id = '. $subnetId .';';
    $lock 	  = $database->getArray($query); 
  
	/* return true if locked */
	if($lock[0]['adminLock'] == 1) {
		return true;
	}
	else {
		return false;
	}
}


/**
 * Get all tables
 */
function getAllTables()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'show tables;';
    $tables	  = $database->getArray($query); 
  
	/* return all tables */
	return $tables;
}


/**
 * Check if specified table exists
 */
function tableExists($table)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
    
    /* first update request */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "'. $table .'";';
    $count	  = $database->getArray($query); 
  
	/* return true if it exists */
	if($count[0]['count'] == 1) {
		return true;
	}
	else {
		return false;
	}
}


/**
 * describe specific table
 */
function fieldExists($table, $fieldName)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `'. $table .'` `'. $fieldName .'`;';
    $count	  = $database->getArray($query); 
  
	/* return true if it exists */
	if(sizeof($count) == 0) {
		return false;
	}
	else {
		return true;
	}
}


/**
 * upgrade database
 */
function upgradeDatabase($version)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('<div class="error">Connect Error (' . $database->connect_errno . '): '. $database->connect_error). "</div>";
	}
    
    /* import querries from upgrade file */
    $query    = file_get_contents("../../db/UPDATE-v". $version. ".sql");
    
    /* execute */
    try {
    	$database->executeMultipleQuerries( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	updateLogTable ('DB update failed', 'DB updated failed with error: '. $error, 2);
    	die('<div class="error">Update error: '. $error .'</div>');
	}
    
    /* return true if we came to here */
    sleep(1);
    updateLogTable ('DB updated', 'DB updated from version '. $version .' to version 0.4', 1);
    return true;
}


/**
 * version check
 */
function getLatestPHPIPAMversion() 
{
	/* fetch page */
	$handle = fopen("http://sourceforge.net/projects/phpipam/", "r");
	while (!feof($handle)) {
	
		$temp = fgets($handle);
	
		if(strpos($temp, 'small title="/current/phpipam')) {
			$temp = explode('"', $temp);
			
			$version = explode("/",$temp[1]);
		
			$version = $version[2];				//phpipam-0.3.tar
			$version = str_replace(array("phpipam-", ".tar"), "", $version);
		}		
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