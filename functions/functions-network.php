<?php

/**
 * Network functions
 *
 */



/* @common functions ---------------- */


/**
 * Resolve reverse DNS name if blank
 * Return class and name
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
		case "0": return "<i class='icon-red   icon-tag state' rel='tooltip' title='"._("Not in use (Offline)")."'></i>"; break;
		case "1": return " "; 		break;
		case "2": return "<i class='icon-blue  icon-tag state' rel='tooltip' title='"._("Reserved")."'></i>"; break;
		case "3": return "<i class='icon-gray icon-tag state' rel='tooltip'  title='"._("DHCP")."'></i>"; break;
		default: return $state;
	}	
}


/**
 * Verify that switch exists
 */
function verifySwitchByName ($hostname)
{
    global $db;                                                                      # get variables from config file
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
    $query = 'select * from `switches` where `hostname` = "'. $hostname .'";';

    /* execute */
    try { $role = $database->getRow( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();
    
    /* return true */
    return true;
}


/**
 * Verify that switch exists
 */
function verifySwitchById ($id)
{
    global $db;                                                                      # get variables from config file
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
    $query = 'select * from `switches` where `id` = "'. $id .'";';

    /* execute */
    try { $role = $database->getRow( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();
    
    /* return true */
    return true;
}


/**
 * Get Switch details by ID
 */
function getSwitchById ($switchId)
{
    global $db;                                                                      # get variables from config file
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
    $query = 'select * from `switches` where `id` = "'. $switchId .' limit 1";';
    
    /* execute */
    try { $switch = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();
    
    /* return true, else false */
    if (!$switch) 	{ return false; }
    else 			{ return $switch[0]; }
}










/* @VLAN functions ---------------- */


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
	if(empty($vlan)) 			{ return 'ok'; }
	else if(!is_numeric($vlan)) { return _('VLAN must be numeric value!'); }
	else if ($vlan > 4094) 		{ return _('Vlan number can be max 4094'); }
	else 						{ return 'ok'; }
}


/**
 *	get VLAN details by ID
 */
function getVLANbyNumber ($number) 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     
	/* execute query */
	$query = 'select * from `vlans` where `number` = "'. $number .'";';
    
    /* execute */
    try { $vlan = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
   	
   	/* return false if none, else list */
	if(sizeof($vlan) == 0) 	{ return false; }
	else 					{ return $vlan; }
}









/* @VRF functions ---------------- */


/**
 *	get all VRFs
 */
function getAllVRFs () 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     
	/* execute query */
	$query = "select * from `vrf`;";
    
    /* execute */
    try { $vrfs = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
   	
   	/* return false if none, else list */
	if(sizeof($vrfs) == 0) 	{ return false; }
	else 					{ return $vrfs; }
}


/**
 *	get vrf details by id
 */
function getVRFDetailsById ($vrfId)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     
	/* execute query */
	$query = 'select * from `vrf` where `vrfId` = "'. $vrfId .'";';
    
    /* execute */
    try { $vrf = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
   	
   	/* return false if none, else list */
	if(sizeof($vrf) == 0) 	{ return false; }
	else 					{ return $vrf[0]; }
}









/* @section functions ---------------- */


/**
 * Get all sections
 */
function fetchSections ()
{
    global $db;                                                                      # get variables from config file
    /* set query */
    $query 	  = 'select * from `sections` order by `id` asc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $sections = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnets array */
    return($sections);
}


/**
 * Get number of sections
 */
function getNumberOfSections ()
{
    global $db;                                                                      # get variables from config file
    /* set query */
    $query 	  = 'select count(*) as count from `sections`;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $sections = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnets array */
    return($sections[0]['count']);
}


/**
 * Get section details - provide section id
 */
function getSectionDetailsById ($id)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query 	  = 'select * from sections where id = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $subnets = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return section */
    if(sizeof($subnets) > 0)	{ return($subnets[0]); }
}


/**
 * Get section details - provide section name
 */
function getSectionDetailsByName ($name)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query 	  = 'select * from sections where `name` = "'. $name .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $subnets = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnets array */
    return($subnets[0]);
}










/* @subnet functions ---------------- */


/**
 * Get all subnets
 */
function fetchAllSubnets ()
{
    global $db;                                                                      # get variables from config file
    /* set query */
    $query 	  = 'select * from subnets;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $sections = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }  
    $database->close();

    /* return subnets array */
    return($sections);
}


/**
 * Get number of subnets
 */
function getNumberOfSubnets ()
{
    global $db;                                                                      # get variables from config file
    /* set query */
    $query 	  = 'select count(*) as count from subnets;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $subnets = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnets array */
    return($subnets[0]['count']);
}



/**
 * Get all subnets in provided sectionId
 */
function fetchSubnets ($sectionId, $orderType = "subnet", $orderBy = "asc" )
{
    global $db;                                                                      # get variables from config file
    /* check for sorting in settings and override */
    $settings = getAllSettings();
    
    if(isset($settings['subnetOrdering']))	{
	    $sort = explode(",", $settings['subnetOrdering']);
	    $orderType = $sort[0];
	    $orderBy   = $sort[1];
    }

    /* set query, open db connection and fetch results */
    $query 	  = "select * from `subnets` where `sectionId` = '$sectionId' ORDER BY `masterSubnetId`,`$orderType` $orderBy;";
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $subnets = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnets array */
    return($subnets);
}


/**
 * Get all master subnets in provided sectionId
 */
function fetchMasterSubnets ($sectionId)
{
    global $db;                                                                      # get variables from config file
    # set query, open db connection and fetch results 
    $query 	  = 'select * from subnets where sectionId = "'. $sectionId .'" and (`masterSubnetId` = "0" or `masterSubnetId` IS NULL) ORDER BY subnet ASC;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $subnets = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    # return subnets array
    return($subnets);
}


/**
 * Get all slave subnets in provided subnetId
 */
function getAllSlaveSubnetsBySubnetId ($subnetId)
{
    global $db;                                                                      # get variables from config file
    # set query, open db connection and fetch results
    $query 	  = 'select * from subnets where `masterSubnetId` = "'. $subnetId .'" ORDER BY subnet ASC;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $subnets = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    # return subnets array
    return($subnets);
}


/**
 * Get all ip addresses in requested subnet bt provided Id
 */
function getIpAddressesBySubnetId ($subnetId) 
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query       = 'select * from `ipaddresses` where subnetId = "'. $subnetId .'" order by `ip_addr` ASC;';
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    
    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return ip address array */
    return($ipaddresses);       
}


/**
 * Get all ip addresses in requested subnet by provided Id, sort by fieldname and direction!
 */
function getIpAddressesBySubnetIdSort ($subnetId, $fieldName, $direction) 
{
    global $db;                                                                      # get variables from config file  
    /* set query, open db connection and fetch results */
    $query       = 'select * from `ipaddresses` where subnetId = "'. $subnetId .'" order by `'. $fieldName .'` '. $direction .';';
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    
    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return ip address array */
    return($ipaddresses);       
}


/**
 * Get all ip addresses in requested subnet by provided Id, sort by fieldname and direction!
 */
function getIpAddressesBySubnetIdslavesSort ($subnetId, $fieldName = "subnetId", $direction = "asc") 
{
    global $db;                                                                      # get variables from config file
    /* get ALL slave subnets, then remove all subnets and IP addresses */
    global $removeSlaves;
    getAllSlaves ($subnetId);
    $removeSlaves = array_unique($removeSlaves);
    
    /* set query, open db connection and fetch results */
    $query       = 'select * from `ipaddresses` where subnetId = "" ';
    foreach($removeSlaves as $subnetId2) {
    	if($subnetId2 != $subnetId) {					# ignore orphaned
	    $query  .= " or `subnetId` = '$subnetId2' ";
	    }
    }
   
    $query      .= 'order by `'. $fieldName .'` '. $direction .';';
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    
    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return ip address array */
    return($ipaddresses);       
}


/**
 * Get all ip addresses in requested subnet by provided Id for visual display
 */
function getIpAddressesForVisual ($subnetId) 
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query       = 'select * from `ipaddresses` where `subnetId` = "'. $subnetId .'" order by `ip_addr` ASC;';
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    
    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();
    
    /* reformat array */
    foreach($ipaddresses as $ip) {
	    $out[$ip['ip_addr']]['state'] = $ip['state'];
	    $out[$ip['ip_addr']]['id']    = $ip['id'];
    }

    /* return ip address array */
    return($out);       
}



/**
 * Count number of ip addresses in provided subnet
 */
function countIpAddressesBySubnetId ($subnetId) 
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query       = 'select count(*) from ipaddresses where subnetId = "'. $subnetId .'" order by subnetId ASC;';
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $count = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
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
    global $db;                                                                      # get variables from config file  
    /* set query, open db connection and fetch results */
    $query         = 'select * from subnets where id = "'. $subnetId .'";';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $SubnetDetails = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnet details - only 1st field! We cannot do getRow because we need associative array */
    if(sizeof($SubnetDetails) > 0)	{ return($SubnetDetails[0]); }
}


/**
 * Get details for requested subnet by ID
 */
function getSubnetDetailsById ($id)
{
    global $db;                                                                      # get variables from config file 
    /* set query, open db connection and fetch results */
    $query         = 'select * from `subnets` where `id` = "'. $id .'";';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $SubnetDetails = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnet details - only 1st field! We cannot do getRow because we need associative array */
    if(sizeof($SubnetDetails) > 0) { return($SubnetDetails[0]); }
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
    if ( IdentifyAddress( $subnet ) == "IPv4") 	{ $type = 0; }
    else 										{ $type = 1; }
    
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
 * Calculate subnet details
 *
 * Calculate subnet details based on input!
 *
 * We must provide used hosts and subnet mask to calculate free hosts, and subnet to identify type
 */
function calculateSubnetDetailsNew ( $subnet, $bitmask, $online, $offline, $reserved, $dhcp )
{
    $details['online']            = $online;		// number of online hosts
    $details['reserved']          = $reserved;		// number of reserved hosts
    $details['offline']           = $offline;		// number of offline hosts
    $details['dhcp']              = $dhcp;   		// number of dhcp hosts 
    
    $details['used']			  = gmp_strval( gmp_add ($online,$reserved) );
    $details['used']			  = gmp_strval( gmp_add ($details['used'],$offline) );
    $details['used']			  = gmp_strval( gmp_add ($details['used'],$dhcp) );
    
    // calculate max hosts
    if ( IdentifyAddress( $subnet ) == "IPv4") 	{ $type = 0; }
    else 										{ $type = 1; }
    
    $details['maxhosts']          = MaxHosts( $bitmask, $type ); 
    
    // calculate free hosts
    $details['freehosts']         = gmp_strval( gmp_sub ($details['maxhosts'] , $details['used']) );

	//reset maxhosts for /31 and /32 subnets
	if (gmp_cmp($details['maxhosts'],1) == -1) {
		$details['maxhosts'] = "1";
	}

    // calculate use percentage
    $details['freehosts_percent'] = round( ( ($details['freehosts'] * 100) / $details['maxhosts']), 2 );
    $details['used_percent'] 	  = round( ( ($details['used'] * 100) / $details['maxhosts']), 2 );
    $details['online_percent'] 	  = round( ( ($details['online'] * 100) / $details['maxhosts']), 2 );
    $details['reserved_percent']  = round( ( ($details['reserved'] * 100) / $details['maxhosts']), 2 );
    $details['offline_percent']   = round( ( ($details['offline'] * 100) / $details['maxhosts']), 2 );
    $details['dhcp_percent']      = round( ( ($details['dhcp'] * 100) / $details['maxhosts']), 2 );
     
    return( $details );
}



/**
 * Check if subnet already exists in section!
 * 
 * Subnet policy:
 *      - inside section subnets cannot overlap!
 *      - same subnet can be configured in different sections
 */
function verifySubnetOverlapping ($sectionId, $subnetNew, $vrfId = 0) 
{
    /* we need to get all subnets in section */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    
    /* first we must get all subnets in section (by sectionId) */
    $querySubnets     = 'select `subnet`,`mask`,`vrfId`,`description` from subnets where sectionId = "'. $sectionId .'";';  

    /* execute */
    try { $allSubnets = $database->getArray( $querySubnets ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }   

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
                                
                /* only check if vrfId's match */
                if($existingSubnet['vrfId'] == $vrfId) {
	                if ( verifyIPv4SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
	                    return _('Subnet overlapps with').' '. $existingSubnet['subnet']." ($existingSubnet[description])";
	                }
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

            /* only check if vrfId's match */
            if($existingSubnet['vrfId'] == $vrfId) {            
        	    if ( verifyIPv6SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
            	    return _('Subnet overlapps with').' '. $existingSubnet['subnet']." ($existingSubnet[description])";
            	}
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
 *		- if vrf is same do checks, otherwise skip
 *		- mastersubnetid we need for new checks to permit overlapping of nested clients
 */
function verifyNestedSubnetOverlapping ($sectionId, $subnetNew, $vrfId, $masterSubnetId = 0) 
{
    /* we need to get all subnets in section */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    
    /* first we must get all subnets in section (by sectionId) */
    $querySubnets     = 'select `id`,`subnet`,`mask`,`description`,`vrfId` from `subnets` where sectionId = "'. $sectionId .'" and `masterSubnetId` != "0" and `masterSubnetId` IS NOT NULL;';  

    /* execute */
    try { $allSubnets = $database->getArray( $querySubnets ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }      

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

                /* only check if vrfId's match */
                if($existingSubnet['vrfId'] == $vrfId) {
                	# check if it is nested properly - inside its own parent, otherwise check for overlapping
                	$allParents = getAllParents ($masterSubnetId);
                	foreach($allParents as $kp=>$p) {
	                	if($existingSubnet['id'] = $kp) {
		                	$ignore = true;
	                	}
                	}
                	if($ignore == false)  {                      
                		if ( verifyIPv4SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
                    		return _('Subnet overlapps with').' '. $existingSubnet['subnet']." ($existingSubnet[description])";
                    	}
                    }
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

            /* only check if vrfId's match */
            if($existingSubnet['vrfId'] == $vrfId) {   
                # check if it is nested properly - inside its own parent, otherwise check for overlapping
                $allParents = getAllParents ($masterSubnetId);
                foreach($allParents as $kp=>$p) {
	               	if($existingSubnet['id'] = $kp) {
		               	$ignore = true;
	               	}
                }
                if($ignore == false)  {                           
        	    	if ( verifyIPv6SubnetOverlapping ($subnetNew, $existingSubnet['subnet']) ) {
            	    	return _('Subnet overlapps with').' '. $existingSubnet['subnet']." ($existingSubnet[description])";
            	    }
            	}
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
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all ip addresses in subnet */
    $query 		  = 'SELECT count(*) from subnets where `masterSubnetId` = "'. $subnetId .'";';    

    /* execute */
    try { $slaveSubnets = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }    
	
	if($slaveSubnets[0]['count(*)']) { return true; }
	else 							 { return false; }
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
    $Net_IPv4 = new Net_IPv4();
       
    /* subnet 2 needs to be parsed to get subnet and broadcast */
    $net1 = $Net_IPv4->parseAddress( $subnet1 );
    $net2 = $Net_IPv4->parseAddress( $subnet2 );

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
        if ( $Net_IPv4->ipInNetwork($nw1, $subnet2) || $Net_IPv4->ipInNetwork($bc1, $subnet2) ) { return true; }
    }
    else
    {
        /* check smaller nw and bc against bigger network */
        if ( $Net_IPv4->ipInNetwork($nw2, $subnet1) || $Net_IPv4->ipInNetwork($bc2, $subnet1) ) { return true; }    
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
    
    $Net_IPv6 = new Net_IPv6();
    
    /* remove netmask from subnet1 */
    $subnet1 = $Net_IPv6->removeNetmaskSpec ($subnet1);
    
    /* verify */
    if ($Net_IPv6->isInNetmask ( $subnet1 , $subnet2 ) ) {
        return true;
    }

    return false;
}


/**
 * Verify that new nested subnet is inside master subnet!
 *
 * $root = root subnet Id
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
    if(isSubnetInsideSubnet ($new, $rootDetails)) 	{ return true; }
    else 											{ return false; }
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
    	$Net_IPv4 = new Net_IPv4();
       
    	/* subnet A needs to be parsed to get subnet and broadcast */
    	$net = $Net_IPv4->parseAddress( $subnetA );

		//both network and broadcast must be inside root subnet!
		if( ($Net_IPv4->ipInNetwork($net->network, $subnetB)) && ($Net_IPv4->ipInNetwork($net->broadcast, $subnetB)) )  { return true; }
		else 																											{ return false; }
	}
	/* IPv6 */
	else {
    	/* IPv6 functions */
    	require_once('PEAR/Net/IPv6.php');
    	$Net_IPv6 = new Net_IPv6();
    	
    	/* remove netmask from subnet1 */
    	$subnetA = $Net_IPv6->removeNetmaskSpec ($subnetA);
    
	    /* verify */
    	if ($Net_IPv6->isInNetmask ( $subnetA, $subnetB ) ) { return true; }
    	else 												{ return false; }
	}
}


/**
 * Check if subnet is admin-locked
 */
function isSubnetWriteProtected($subnetId)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'select `adminLock` from subnets where id = '. $subnetId .';'; 

	/* execute */
    try { $lock = $database->getArray($query); }
    catch (Exception $e) { $error =  $e->getMessage(); }
  
	/* return true if locked */
	if($lock[0]['adminLock'] == 1) 	{ return true; }
	else 							{ return false; }
}


/**
 * truncate subnet
 */
function truncateSubnet($subnetId) 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'delete from `ipaddresses` where `subnetId` = '. $subnetId .';'; 

	/* execute */
    try { $database->executeQuery($query); }
    catch (Exception $e) { 
    	$error =  $e->getMessage(); 
    	die('<div class="alert alert-error">'.$error.'</div>');
    }
  
	/* return true if locked */
	return true;	
}


/**
 * get all Subnets - for hosts export
 */
function getAllSubnetsForExport() 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    /* first update request */
    $query    = 'select `s`.`id`,`subnet`,`mask`,`name`,`se`.`description` as `se_description`,`s`.`description` as `s_description` from `subnets` as `s`,`sections` as `se` where `se`.`id`=`s`.`sectionId` order by `se`.`id` asc;'; 

	/* execute */
    try { $subnets = $database->getArray($query); }
    catch (Exception $e) { 
    	return false;
    }
  
	/* return true if locked */
	return $subnets;	
}



/**
 *	Print dropdown menu for subnets in section!
 */
function printDropdownMenuBySection($sectionId, $subnetMasterId = "0") 
{
		# get all subnets
		$subnets = fetchSubnets ($sectionId);
		
		$html = array();
		
		$rootId = 0;									# root is 0
		
		foreach ( $subnets as $item )
			$children[$item['masterSubnetId']][] = $item;
		
		# loop will be false if the root has no children (i.e., an empty menu!)
		$loop = !empty( $children[$rootId] );
		
		# initializing $parent as the root
		$parent = $rootId;
		$parent_stack = array();
		
		# display selected subnet as opened
		$allParents = getAllParents ($_REQUEST['subnetId']);
		
		# structure
		$html[] = "<select name='masterSubnetId'>";
		# root
		$html[] = "<option disabled>"._("Select Master subnet")."</option>";
		$html[] = "<option value='0'>"._("Root subnet")."</option>";
		
		# return table content (tr and td's)
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $rootId ) ) )
		{
			# repeat 
			$repeat  = str_repeat( " - ", ( count($parent_stack)) );
			# dashes
			if(count($parent_stack) == 0)	{ $dash = ""; }
			else							{ $dash = $repeat; }
							
			# count levels
			$count = count( $parent_stack ) + 1;
			
			
			# print table line
			if(strlen($option['value']['subnet']) > 0) { 
				# selected
				if($option['value']['id'] == $subnetMasterId) 	{ $html[] = "<option value='".$option['value']['id']."' selected='selected'>$repeat ".transform2long($option['value']['subnet'])."/".$option['value']['mask']." (".$option['value']['description'].")</option>"; }
				else 											{ $html[] = "<option value='".$option['value']['id']."'>$repeat ".transform2long($option['value']['subnet'])."/".$option['value']['mask']." (".$option['value']['description'].")</option>"; }
			}
			
			if ( $option === false ) { $parent = array_pop( $parent_stack ); }
			# Has slave subnets
			elseif ( !empty( $children[$option['value']['id']] ) ) {														
				array_push( $parent_stack, $option['value']['masterSubnetId'] );
				$parent = $option['value']['id'];
			}
			# Last items
			else { }
		}
		$html[] = "</select>";
		
		print implode( "\n", $html );
}


/**
 * Get VLAN number form Id
 */
function subnetGetVLANdetailsById($vlanId)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'select * from `vlans` where `vlanId` = "'. $vlanId .'";';

    /* execute */
    try { $vlan = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }   
  
	/* return vlan details if exists */
	if(sizeof($vlan) != 0) 	{ return $vlan[0]; }	
	else 					{ return false; }
}


/**
 * Get all VLANS
 */
function getAllVlans($tools = false)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    # custom fields
    $myFields = getCustomVLANFields();     
    $myFieldsInsert['id']  = '';
	
    if(sizeof($myFields) > 0) {
		/* set inserts for custom */
		foreach($myFields as $myField) {			
			$myFieldsInsert['id']  .= ',`vlans`.`'. $myField['name'] .'`';
		}
	}
		
    /* check if it came from tools and use different query! */
    if($tools) 	{ $query = 'SELECT vlans.number,vlans.name,vlans.description,subnets.subnet,subnets.mask,subnets.id'.$myFieldsInsert['id'].' AS subnetId,subnets.sectionId FROM vlans LEFT JOIN subnets ON subnets.vlanId = vlans.vlanId ORDER BY vlans.number ASC;'; }
    else 		{ $query = 'select * from `vlans` order by `number` asc;'; }

    /* execute */
    try { $vlan = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }  
  
	/* return vlan details */
	return $vlan;
}


/**
 * Get subnets by VLAN id
 */
function getSubnetsByVLANid ($id)
{
    global $db;                                                                      # get variables from config file
    
    /* set query, open db connection and fetch results */
    $query         = 'select * from `subnets` where `vlanId` = "'. $id .'";';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $SubnetDetails = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }  
    $database->close();

    /* return subnet details - only 1st field! We cannot do getRow because we need associative array */
    return($SubnetDetails); 
}


/**
 * Calculate maximum number of IPv4 / IPv6 hosts per subnet
 */
function MaxHosts( $mask, $type = 0 ) 
{
    /* IPv4 address */
    if($type == 0) { 
    	//31 and 31 networks
    	if($mask==31 || $mask == 32) {
	    	return pow(2, (32 - $mask)); 
    	}
    	else {
	    	return pow(2, (32 - $mask)) -2;	
    	} 
    }
     /* IPv6 address */
	else {
    	//31 and 31 networks
    	if($mask==127 || $mask == 128) {
	    	return gmp_strval(gmp_pow(2, 128 - $mask));
    	}
    	else {
	    	return gmp_strval(gmp_sub(gmp_pow(2, 128 - $mask) ,1));
    	}   
    }
}


/**
 *	get all subnets belonging to vrf
 */
function getAllSubnetsInVRF($vrfId)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query = 'select * from `subnets` where `vrfId` = "'. $vrfId .'";';

    /* execute */
    try { $vrf = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }  
   	
   	/* return false if none, else list */
	if(sizeof($vrf) == 0) 	{ return false; }
	else 					{ return $vrf; }
}


/**
 *	Get top 10 subnets by usage
 */
function getSubnetStatsDashboard($type, $limit = "10")
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    # set limit
    if($limit == "0")	{ $limit = ""; }
    else				{ $limit = "limit $limit"; }
    
	# ipv4 stats
	if($type == "IPv4") 
	{
		$query = "select * from (
				select `id`,`subnet`,cast(`subnet` as UNSIGNED) as cmp,`mask`,IF(char_length(`description`)>0, `description`, 'No description') as description, (
					SELECT COUNT(*) FROM `ipaddresses` as `i` where `i`.`subnetId` = `s`.`id`
				) 
				as `usage` from `subnets` as `s`
				where cast(`subnet` as UNSIGNED) < '4294967295'
				order by `usage` desc $limit
				) as `d` where `d`.`usage` > 0;";	
	}
	# IPv6 stats
	else 
	{
		$query = "select * from (
				select `id`,`subnet`,cast(`subnet` as UNSIGNED) as cmp,`mask`, IF(char_length(`description`)>0, `description`, 'No description') as description, (
					SELECT COUNT(*) FROM `ipaddresses` as `i` where `i`.`subnetId` = `s`.`id`
				) 
				as `usage` from `subnets` as `s`
				where cast(`subnet` as UNSIGNED) > '4294967295'
				order by `usage` desc $limit
				) as `d` where `d`.`usage` > 0;";		
	}

    /* execute */
    try { $stats = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
   	
    /* close database connection */
    $database->close();

    /* return subnets array */
    return($stats);   	
}










/* @IP address functions ---------------- */


/**
 * Get all IP addresses
 */
function fetchAllIPAddresses ($hostnameSort = false)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* set query */
    if(!$hostnameSort) {
    	$query 	  = 'select * from ipaddresses;'; 
    }
    else {
    	$query 	   = 'select * from ipaddresses order by dns_name desc;'; 
    }

    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();

    /* return subnets array */
    return($ipaddresses);
}


/**
 * Get number of IPv4 addresses
 */
function getNuberOfIPv4Addresses ()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* set query */
   	$query 	  = 'select count(cast(`ip_addr` as UNSIGNED)) as count from `ipaddresses` where cast(`ip_addr` as UNSIGNED) < "4294967295";'; 

    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnets array */
    return($ipaddresses[0]['count']);
}


/**
 * Get number of IPv6 addresses
 */
function getNuberOfIPv6Addresses ()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* set query */
   	$query 	  = 'select count(cast(`ip_addr` as UNSIGNED)) as count from `ipaddresses` where cast(`ip_addr` as UNSIGNED) > "4294967295";'; 

    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return subnets array */
    return($ipaddresses[0]['count']);
}


/**
 * Get all IP addresses by hostname
 */
function fetchAllIPAddressesByName ($hostname)
{
    global $db;                                                                      # get variables from config file
    /* set query */
    $query 	  = 'select * from ipaddresses where `dns_name` like "%'. $hostname .'%" order by `dns_name` desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $ipaddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 

    /* close database connection */
    $database->close();

    /* return subnets array */
    return($ipaddresses);
}


/**
 * Get sectionId for requested name - needed for hash page loading
 */
function getSectionIdFromSectionName ($sectionName) 
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query         = 'select id from sections where name = "'. $sectionName .'";';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);
    
    /* execute */
    try { $SubnetDetails = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }     
    $database->close();

    /* return subnet details - only 1st field! We cannot do getRow because we need associative array */
    return($SubnetDetails[0]['id']); 

}


/**
 * Check for duplicates on add
 */
function checkDuplicate ($ip, $subnetId)
{
    global $db;                                                                      # get variables from config file
    /* we need to put IP in decimal format */
    $ip = Transform2decimal ($ip);
    
    /* set query, open db connection and fetch results */
    $query         = 'select * from `ipaddresses` where `ip_addr` = "'. $ip .'" and subnetId = "'. $subnetId .'" ;';
    $database      = new database($db['host'], $db['user'], $db['pass'], $db['name']);

    /* execute */
    try { $unique = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    $database->close();

    /* return false if it exists */
    if (sizeof($unique) != 0 ) 	{ return true; }
    else 						{ return false; }
}


/**
 * Modify ( add / edit / delete ) IP address
 */
function modifyIpAddress ($ip) 
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

    /* escape special characters */
    $ip['description'] 	= mysqli_real_escape_string($database, $ip['description']); 
    $ip['note'] 		= mysqli_real_escape_string($database, $ip['note']); 

    /* set query, open db connection and fetch results */
    $query    = SetInsertQuery($ip);

    /* execute */
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 

    # success
    return true;
}


/**
 * set insert / update / delete query for adding IP address
 * based on provided array
 */
function SetInsertQuery( $ip ) 
{
	/* First we need to get custom fields! */
	$myFields = getCustomIPaddrFields();
	$myFieldsInsert['query']  = '';
	$myFieldsInsert['values'] = '';
	
	if(sizeof($myFields) > 0) {
		/* set inserts for custom */
		foreach($myFields as $myField) {			
			$myFieldsInsert['query']  .= ', `'. $myField['name'] .'`';
			$myFieldsInsert['values'] .= ", '". $ip[$myField['name']] . "'";
		}
	}

	/* insert */
	if( $ip['action'] == "add" ) 
	{
		$query  = "insert into `ipaddresses` ";
		$query .= "(`subnetId`,`description`,`ip_addr`, `dns_name`,`mac`, `owner`, `state`, `switch`, `port`, `note` ". $myFieldsInsert['query'] .") ";
		$query .= "values ";
		$query .= "('". $ip['subnetId'] ."', '". $ip['description'] ."', '". Transform2decimal( $ip['ip_addr'] ) ."', ". "\n"; 
		$query .= " '". $ip['dns_name'] ."', '". $ip['mac'] ."', '". $ip['owner'] ."', '". $ip['state'] ."', ". "\n";
		$query .= " '". $ip['switch'] ."', '". $ip['port'] ."', '". $ip['note'] ."'". $myFieldsInsert['values'] .");";
	}
	/* edit multiple */
	else if( ($ip['action'] == "edit") && ($ip['type'] == "series") ) 
	{
		$query  = "update `ipaddresses` ";
		$query .= "set `ip_addr` = '". Transform2decimal( $ip['ip_addr'] ) ."', ";
		$query .= "`description` = '". $ip['description'] ."', ";
		$query .= "`dns_name` = '". $ip['dns_name'] ."' ,"; 
		$query .= "`mac` = '". $ip['mac'] ."' ,"; 
		$query .= "`owner` = '". $ip['owner'] ."' ,"; 
		$query .= "`state` = '". $ip['state'] ."',";
		$query .= "`switch` = '". $ip['switch'] ."',";
		$query .= "`port` = '". $ip['port'] ."',";
		
		# custom!
		foreach($myFields as $myField) {
		$query .= "`". $myField['name'] ."` = '". $ip[$myField['name']] ."',";
		}
		
		$query .= "`note` = '". $ip['note'] ."' ";
		$query .= "where `subnetId` = '". $ip['subnetId'] ."' and `ip_addr` = '". Transform2decimal( $ip['ip_addr'] ) ."';";	
	}
	/* edit */
	else if( $ip['action'] == "edit" ) 
	{
		$query  = "update ipaddresses ";
		$query .= "set `ip_addr` = '". Transform2decimal( $ip['ip_addr'] ) ."', `description` = '". $ip['description'] ."', `dns_name` = '". $ip['dns_name'] ."' , `mac` = '". $ip['mac'] ."', ". "\n"; 
		
		#custom!
		foreach($myFields as $myField) {
		$query .= "`". $myField['name'] ."` = '". $ip[$myField['name']] ."',";
		}
		
		$query .= "`owner` = '". $ip['owner'] ."' , `state` = '". $ip['state'] ."', `switch` = '". $ip['switch'] ."', ". "\n"; 
		$query .= "`port` = '". $ip['port'] ."', `note` = '". $ip['note'] ."' ";
		$query .= "where `id` = '". $ip['id'] ."';";	
	}
	/* delete multiple */
	else if( ($ip['action'] == "delete") && ($ip['type'] == "series") ) {
		$query = "delete from ipaddresses where `subnetId` = '". $ip['subnetId'] ."' and `ip_addr` = '". Transform2decimal( $ip['ip_addr'] ) ."';";	
	}
	/* delete */
	else if( $ip['action'] == "delete" ) {
		$query = "delete from ipaddresses where `id` = '". $ip['id'] ."';";	
	}
	/* move */
	else if ($ip['action'] == "move") {
		$query = "update `ipaddresses` set `subnetId` = '$ip[newSubnet]' where `id` = '$ip[id]';";
	}
	
	/* return query */	
	return $query;
}


/**
 * Move IP address to new subnet - for subnet splitting
 */
function moveIPAddress ($id, $subnetId) 
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'update `ipaddresses` set `subnetId` = "'.$subnetId.'" where `id` = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);             
	   
	/* execute */
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { $error =  $e->getMessage(); }

	# ok
	if(!isset($error)) {
        updateLogTable ('IP address move ok', "id: $id\nsubnetId: $subnetId", 0);			# write success log
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return true;		
	}
	# problem
	else {
        updateLogTable ('IP address move error', "id: $id\nsubnetId: $subnetId", 2);			# write error log
        return false;	
	}
}


/**
 * Get IP address details
 */
function getIpAddrDetailsById ($id) 
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from `ipaddresses` where `id` = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    
    //we only fetch 1 field
    $details  = $details[0];
	//change IP address formatting to dotted(long)
	$details['ip_addr'] = Transform2long( $details['ip_addr'] ); 
	   
    /* return result */
    return($details);
}



/**
 * verify ip address from edit / add
 * noStrict ignores NW and Broadcast checks
 */
function VerifyIpAddress( $ip , $subnet , $noStrict = false ) 
{
	/* First identify it */
	$type = IdentifyAddress( $ip );
	$type = IdentifyAddress( $subnet );
	
	/* get mask */
	$mask = explode("/", $subnet);
	
	/* IPv4 verification */
	if ( $type == 'IPv4' )
	{
        require_once 'PEAR/Net/IPv4.php';
        $Net_IPv4 = new Net_IPv4();
        
		// is it valid?
		if (!$Net_IPv4->validateIP($ip)) 										{ $error = _("IP address not valid")."! ($ip)"; }
		// it must be in provided subnet
		else if (!$Net_IPv4->ipInNetwork($ip, $subnet)) 						{ $error = _("IP address not in selected subnet")."! ($ip)"; }
		//ignore  /31 and /32 subnet broadcast and subnet checks!
		else if ($mask[1] == "31" || $mask[1] == "32" || $noStrict == true) 	{ }
		// It cannot be subnet or broadcast
		else {
            $net = $Net_IPv4->parseAddress($subnet);
          
            if ($net->network == $ip) 											{ $error = _("Cannot add subnet as IP address!"); }
            else if ($net->broadcast == $ip) 									{ $error = _("Cannot add broadcast as IP address!"); }
		}
	}
	
	/* IPv6 verification */
	else 
	{
        require_once 'PEAR/Net/IPv6.php';
        $Net_IPv6 = new Net_IPv6();
        
        //remove /xx from subnet
        $subnet_short = $Net_IPv6->removeNetmaskSpec($subnet);
		
		// is it valid?
		if (!$Net_IPv6->checkIPv6($ip)) 										{ $error = _("IP address not valid")."! ($ip)"; }
		// it must be in provided subnet
		else if (!$Net_IPv6->isInNetmask($ip, $subnet)) 						{ $error = _("IP address not in selected subnet")."! ($ip)";}
		//ignore  /127 and /128 subnet broadcast and subnet checks!
		else if ($mask[1] == "127" || $mask[1] == "128" || $noStrict == true) 	{ }
		//it cannot be subnet
		else if ($ip == $subnet_short) 											{ $error = _("Cannot add subnet as IP address!");   }
	}
	
	/* return results */
	if( isset($error) ) { return $error; }
	else 				{ return false; }
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
        $errors[] = _("Invalid CIDR format!");
    }

	/* Identify address type */
	$type = IdentifyAddress( $network );
	
	/* IPv4 verification */
	if ( $type == 'IPv4' )
	{
        require_once 'PEAR/Net/IPv4.php';
        $Net_IPv4 = new Net_IPv4();

        if ($net = $Net_IPv4->parseAddress ($cidr)) {
            //validate IP
            if (!$Net_IPv4->validateIP ($net->ip)) 					{ $errors[] = _("Invalid IP address!"); }
            //network must be same as provided IP address
            else if (($net->network != $net->ip) && ($subnet == 1)) { $errors[] = _("IP address cannot be subnet! (Consider using")." ". $net->network .")"; }
            //validate netmask
            else if (!$Net_IPv4->validateNetmask ($net->netmask)) 	{ $errors[] = _('Invalid netmask').' ' . $net->netmask; }    
        }
        else 														{ $errors[] = _('Invalid CIDR format!'); }
	}	
	/* IPv6 verification */
	else 
	{
        require_once 'PEAR/Net/IPv6.php';
        $Net_IPv6 = new Net_IPv6();

        //validate IPv6
        if (!$Net_IPv6->checkIPv6 ($cidr) ) 						{ $errors[] = _("Invalid IPv6 address!"); }
        else {
            
            //validate subnet
            $subnet = $Net_IPv6->getNetmask($cidr);
            $subnet = $Net_IPv6->compress($subnet);

            $subnetParse = explode("/", $cidr);
            $subnetMask  = $subnetParse[1];
            $subnetNet   = $subnetParse[0];
        
            if ( ($subnetParse[0] != $subnet) && ($subnet == 1) ) 	{ $errors[] = _("IP address cannot be subnet! (Consider using")." ". $subnet ."/". $subnetMask .")"; }
	   }
    }
    
	/* return array of errors */
	return($errors);
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
        $Net_IPv4 = new Net_IPv4();
        
        $net = $Net_IPv4->parseAddress( $ip .'/'. $mask );
        
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
        $Net_IPv6 = new Net_IPv6();  

        $out['network']   = $ip;         // 2a34:120:feel::
        $out['bitmask']   = $mask;         // 48
        $out['netmask']   = $mask;         // 48 - we just duplicate it
        
        //broadcast - we fake it with highest IP in subnet
        $net = $Net_IPv6->parseaddress( $ip .'/'. $mask );
        
        $out['broadcast'] = $net['end'];    // 2a34:120:feel::ffff:ffff:ffff:ffff:ffff      
    }
    
    return( $out );
} 


/**
 * Find unused ip addresses between two provided
 *
 * checkType = NW, bcast and none(normal)
 */
function FindUnusedIpAddresses ($ip1, $ip2, $type, $broadcast = 0, $checkType = "", $mask = false ) 
{     
    /* calculate difference */
    $diff = gmp_strval(gmp_sub($ip2, $ip1));
    
    /* /32 */
    if($mask == "32" && $checkType=="networkempty" && $type=="IPv4") {
	    $result['ip'] 	 = long2ip($ip1);
		$result['hosts'] = "1";
    }
    /* /31 */
    else if($mask == "31" && $type=="IPv4") {
    	if($diff == 1 && $checkType == "networkempty" ) {
    	    $result['ip'] 	 = long2ip($ip1);
    	    $result['hosts'] = "2";		    	
    	}
    	if($diff == 1 && $checkType == "network" ) {
    	    $result['ip'] 	 = long2ip($ip1);
    	    $result['hosts'] = "1";		    	
    	}
    	else if($diff == 1 && $checkType == "" ) {
/*
	    	$result['ip'] 	 = long2ip($ip1);
	    	$result['hosts'] = "";	
*/    	
    	}
    	else if($diff == 1 && $checkType == "broadcast" ) {
	    	$result['ip'] 	 = long2ip($ip2);
	    	$result['hosts'] = "1";	    	
    	}
    	else if($diff == 2 ) {
    	    $result['ip'] 	 = long2ip($ip1);
    	    $result['hosts'] = "2";	
    	}
    }    
    /* /128 */
    else if($mask == "128" && $checkType=="networkempty" && $type=="IPv6") {
	    $result['ip'] 	 = long2ip6($ip1);
		$result['hosts'] = "1";
    }
    /* /127 */
    else if($mask == "127" && $type=="IPv6") {
    	if($diff == 1 && $checkType == "networkempty" ) {
    	    $result['ip'] 	 = long2ip6($ip1);
    	    $result['hosts'] = "2";		    	
    	}
    	if($diff == 1 && $checkType == "network" ) {
    	    $result['ip'] 	 = long2ip6($ip1);
    	    $result['hosts'] = "1";		    	
    	}
    	else if($diff == 1 && $checkType == "" ) {  	
    	}
    	else if($diff == 1 && $checkType == "broadcast" ) {
	    	$result['ip'] 	 = long2ip6($ip2);
	    	$result['hosts'] = "1";	    	
    	}
    	else if($diff == 2 ) {
    	    $result['ip'] 	 = long2ip6($ip1);
    	    $result['hosts'] = "2";	
    	}
    } 
    /* if diff is less than 2 return false */
    else if ( $diff < 2 ) {
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
 * Get first available IP address
 */
function getFirstAvailableIPAddress ($subnetId)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all ip addresses in subnet */
    $query 		 = 'SELECT `ip_addr` from `ipaddresses` where `subnetId` = "'. $subnetId .'" order by `ip_addr` ASC;';    

    /* execute */
    try { $ipAddresses = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 

    /* get subnet */
    $query 	 = 'SELECT `subnet`,`mask` from `subnets` where `id` = "'. $subnetId .'";';    
    $subnet2 = $database->getArray($query); 
    $subnet  = $subnet2[0]['subnet'];
    $mask    = $subnet2[0]['mask'];
    
    /* create array of IP addresses */
    $ipaddressArray[]	  = $subnet;
    foreach($ipAddresses as $ipaddress) {
    	$ipaddressArray[] = $ipaddress['ip_addr'];
    }
    //get array size
    $size = sizeof($ipaddressArray);
    $curr = 0;
    //get type
    $type = IdentifyAddress($subnet);
  
    //if subnet is /32
    if($mask == "32" && $type == "IPv4") {
    	if($size == 1)  { $firstAvailable = $ipaddressArray[0]; }
    	else 			{ $firstAvailable = false; }
    }
    //if subnet /31
    else if($mask == "31" && $type == "IPv4") {
    	if($size == 1)  	 { $firstAvailable = $ipaddressArray[0]; }
    	else if($size == 2)  { 
    		$delta = $ipaddressArray[1] - $ipaddressArray[0];
    		if($delta == 1)  { $firstAvailable = $ipaddressArray[0]; }
    		else			 { $firstAvailable = gmp_strval(gmp_add($ipaddressArray[0], 1)); }
    	}
    	else 				 { $firstAvailable = false; }
    }
    //if subnet is /128
    else if($mask == "128" && $type == "IPv6") {
    	if($size == 1)  { $firstAvailable = $ipaddressArray[0]; }
    	else 			{ $firstAvailable = false; }
    }
    //if subnet /127
    else if($mask == "127" && $type == "IPv6") {
    	if($size == 1)  	 { $firstAvailable = $ipaddressArray[0]; }
    	else if($size == 2)  { 
    		$delta = $ipaddressArray[1] - $ipaddressArray[0];
    		if($delta == 1)  { $firstAvailable = $ipaddressArray[0]; }
    		else			 { $firstAvailable = gmp_strval(gmp_add($ipaddressArray[0], 1)); }
    	}
    	else 				 { $firstAvailable = false; }
    }
    //if size = 0 return subnet +1
    else if($size == 1) {
    	$firstAvailable = gmp_strval(gmp_add($ipaddressArray[0], 1));
    }
    else {
    	//get first change -> delta > 1
    	for($m=1; $m <= $size -1; $m++) {
    		$delta = gmp_strval(gmp_sub($ipaddressArray[$m],$ipaddressArray[$m-1]));
    
    		//compare with previous
    		if ($delta != 1 ) {
    			$firstAvailable = gmp_strval(gmp_add($ipaddressArray[$m-1],1));
    			$m = $size;
    		}
    		else {
    			$firstAvailable = gmp_strval(gmp_add($ipaddressArray[$m],1));	    		
    		}
    	}
    	
    	//if bcast ignore!
        if($type == "IPv4") {
        	require_once 'PEAR/Net/IPv4.php';
            $Net_IPv4 = new Net_IPv4();
            	
            $net = $Net_IPv4->parseAddress(transform2long($subnet)."/".$mask);
	        if ($net->broadcast == transform2long($firstAvailable)) { $firstAvailable = false; }
        }
        else if ($type == "IPv6") {
            require_once 'PEAR/Net/IPv6.php';
            $Net_IPv6 = new Net_IPv6();
        
            $net = $Net_IPv6->parseAddress(transform2long($subnet)."/".$mask);
	        if ($net->broadcast == transform2long($firstAvailable)) { $firstAvailable = false; }	            
        }
        //else return last
        else {
	    	$firstAvailable = gmp_strval(gmp_add($ipaddressArray[$size-1],1));
    	}
    }   
    /* return first available IP address */
    return $firstAvailable;
}


/**
 * Check if hostname is unique
 */
function isHostUnique($host)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  
    $query    = 'select count(*) as cnt from `ipaddresses` where `dns_name` = "'. $host .'";';           

    /* execute */
    try { $res = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    } 
    
    if($res[0]['cnt'] == '0')	{ return true; }
    else						{ return false; }
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


/**
 * Get all avaialble devices
 */
function getIPaddressesBySwitchName ( $name ) 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all vlans, descriptions and subnets */
    $query = 'SELECT * FROM `ipaddresses` where `switch` = "'. $name .'" order by port ASC;';

    /* execute */
    try { $ip = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').":$error</div>");
        return false;
    }   
    
    /* return vlans */
    return $ip;
}










/* @IPcalculations ---------- */

/**
 * Transform IP address from decimal to dotted (167903488 -> 10.2.1.0)
 */
function Transform2long ($ip) 
{
    if (IdentifyAddress($ip) == "IPv4" ) { return(long2ip($ip)); }
    else 								 { return(long2ip6($ip)); }
} 


/**
 * Transform IP address from dotted to decimal (10.2.1.0 -> 167903488)
 */
function Transform2decimal ($ip) 
{
    if (IdentifyAddress($ip) == "IPv4" ) { return( sprintf("%u", ip2long($ip)) ); }
    else 								 { return(ip2long6($ip)); }
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




?>