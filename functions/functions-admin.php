<?php

/**
 * Admin functions
 *
 */
 



/* @user functions ---------------- */


/**
 * Verify Input on add
 */
function verifyUserModInput ($userModDetails)
{
    # real name must be entered
    if (!$userModDetails['real_name']) 																	{ $errors[] = 'Real name field is mandatory!'; }
    # Both passwords must be same
    if ($userModDetails['password1'] != $userModDetails['password2']) 									{ $errors[] = "Passwords do not match!"; }
    # pass must be at least 8 chars long for non-domain users
    if($userModDetails['domainUser'] != 1 ) { 
    	if ((strlen($userModDetails['password1']) < 8 ) && (strlen($userModDetails['password1']) != 0)) { $errors[] = "Password must be at least 8 characters long!"; }
    	else if (($userModDetails['action'] == "add") && (strlen($userModDetails['password1']) < 8 )) 	{ $errors[] = "Password must be at least 8 characters long!"; }
    }
    # email format must be valid
    if (!checkEmail($userModDetails['email'])) 															{ $errors[] = "Invalid email address!"; }
    # username must not already exist (if action is add)
    if ($userModDetails['action'] == "add") {
        global $db;																	 				# get variables from config file
        $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 				# open db connection
        
        $query    = 'select * from users where username = "'. $userModDetails['username'] .'";'; 	# set query and fetch results
        $details  = $database->getArray($query); 
        # user already exists
        if (sizeof($details) != 0) 																		{ $errors[] = "User $userModDetails[username] already exists!"; }
    }
    # return errors
    return($errors);
}


/**
 * Delete user by ID
 */
function deleteUserById($id, $name = "")
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection
 
    $query    = 'delete from `users` where `id` = "'. $id .'";';						# set query, open db connection and fetch results */

	/* execute */
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { $error =  $e->getMessage(); }

	# ok
	if(!isset($error)) {
        updateLogTable ('User '. $name .' deleted ok', 'User '. $name .' deleted ok', 1);	# write success log
        return true;		
	}
	# problem
	else {
		print "<div class='alert alert-error'>Cannot delete user!<br><strong>Error</strong>: $error</div>";
		updateLogTable ('Cannot delete user '. $name, 'Cannot delete user '. $name , 2);	# write error log
		return false;
	}
}


/**
 * Update user by ID - if id is empty add new user!
 */
function updateUserById ($userModDetails) {

    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);    # open db connection

    # set query - add or edit user
    if (empty($userModDetails['userId'])) {
        $query  = "insert into users ";
        $query .= "(`username`, `password`, `role`, `real_name`, `email`, `domainUser`, `useFullPageWidth`) values "; 
        $query .= "('$userModDetails[username]', '$userModDetails[password1]', '$userModDetails[role]', '$userModDetails[real_name]', '$userModDetails[email]', '$userModDetails[domainUser]', '$userModDetails[useFullPageWidth]' );";
    }
    else {
        $query  = "update users set "; 
        $query .= "`username` = '$userModDetails[username]', "; 
        if (strlen($userModDetails['password1']) != 0) {
        $query .= "`password` = '$userModDetails[password1]', "; 
        }
        $query .= "`role`     = '$userModDetails[role]', `real_name`= '$userModDetails[real_name]', `email` = '$userModDetails[email]', `domainUser`= '$userModDetails[domainUser]', `useFullPageWidth`= '$userModDetails[useFullPageWidth]' "; 
        $query .= "where `id` = '$userModDetails[userId]';";
    }
    
	$log = prepareLogFromArray ($userModDetails);										# prepare log 

	/* execute */
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { $error =  $e->getMessage(); }
	
	# ok
	if(!isset($error)) {
        updateLogTable ('User '. $userModDetails['username'] .' updated ok', $log, 1);	# write success log
        return true;		
	}
	# problem
	else {
		print "<div class='alert alert-error'>Cannot ".$userModDetails['action']." user!<br><strong>Error</strong>: $error</div>";
		updateLogTable ('Cannot modify user '. $userModDetails['username'], $log, 2);	# write error log
		return false;
	}
}


/**
 * User self-update
 */
function selfUpdateUser ($userModDetails)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);    # open db connection   

    /* set query */
    $query  = "update users set ";
    if(strlen($userModDetails['password1']) != 0) {
    $query .= "`password` = '$userModDetails[password1]',";
    }
    $query .= "`real_name`= '$userModDetails[real_name]', `useFullPageWidth`= '$userModDetails[useFullPageWidth]', `email` = '$userModDetails[email]' ";
    $query .= "where `id` = '$userModDetails[userId]';";
    
    /* set log file */
    $log = prepareLogFromArray ($userModDetails);													# prepare log 
                    

	/* execute */
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { $error =  $e->getMessage(); }

	# ok
	if(!isset($error)) {
        updateLogTable ('User '. $userModDetails['real_name'] . ' selfupdate ok', $log, 1);			# write success log
        return true;		
	}
	# problem
	else {
		print "<div class='alert alert-error'>Cannot update user!<br><strong>Error</strong>: $error</div>";
		updateLogTable ('User '. $userModDetails['real_name'] . ' selfupdate failed', $log,  2);	# write error log
		return false;
	}
}










/* @subnet functions ---------------- */


/**
 * Add new subnet
 */
function modifySubnetDetails ($subnetDetails) 
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection   
    
    # set modify subnet details query
    $query = setModifySubnetDetailsQuery ($subnetDetails, $sectionChange);

	$log = prepareLogFromArray ($subnetDetails);																				# prepare log 

    # execute query
    if (!$database->executeMultipleQuerries($query)) {
        updateLogTable ('Subnet ('. $subnetDetails['description'] .') '. $subnetDetails['action'] .' failed', $log, 2);	# write error log
        return false;
    }
    else {
        updateLogTable ('Subnet ('. $subnetDetails['description'] .') '. $subnetDetails['action'] .' ok', $log, 1);		# write success log
        return true;
    }
}


/**
 * Add new subnet - set query
 */
function setModifySubnetDetailsQuery ($subnetDetails)
{
    # add new subnet
    if ($subnetDetails['action'] == "add")
    {
        # remove netmask and calculate decimal values!
        $subnetDetails['subnet_temp'] = explode("/", $subnetDetails['subnet']);
        $subnetDetails['subnet']      = Transform2decimal ($subnetDetails['subnet_temp'][0]);
        $subnetDetails['mask']        = $subnetDetails['subnet_temp'][1];
        
        # custom fields
        $myFields = getCustomSubnetFields();
        $myFieldsInsert['query']  = '';
        $myFieldsInsert['values'] = '';
	
        if(sizeof($myFields) > 0) {
			/* set inserts for custom */
			foreach($myFields as $myField) {			
				$myFieldsInsert['query']  .= ', `'. $myField['name'] .'`';
				$myFieldsInsert['values'] .= ", '". $subnetDetails[$myField['name']] . "'";
			}
		}
        
        $query  = 'insert into subnets '. "\n";
        $query .= '(`subnet`, `mask`, `sectionId`, `description`, `vlanId`, `vrfId`, `masterSubnetId`, `allowRequests`, `showName`, `adminLock` '.$myFieldsInsert['query'].') ' . "\n";
        $query .= 'values (' . "\n";
        $query .= ' "'. $subnetDetails['subnet'] 		 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['mask'] 			 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['sectionId'] 	 .'", ' . "\n"; 
        $query .= ' "'. htmlentities($subnetDetails['description']) .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['vlanId'] 			 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['vrfId'] 		 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['masterSubnetId'] .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['allowRequests']  .'", ' . "\n";
        $query .= ' "'. $subnetDetails['showName']  .'", ' . "\n";
        $query .= ' "'. $subnetDetails['adminLock']  	 .'" ' . "\n";  
        $query .= $myFieldsInsert['values'];
        $query .= ' );';
    }
    # Delete subnet
    else if ($subnetDetails['action'] == "delete")
    {
    	/* get ALL slave subnets, then remove all subnets and IP addresses */
    	global $removeSlaves;
    	getAllSlaves ($subnetDetails['subnetId']);
    	$removeSlaves = array_unique($removeSlaves);
    	    		
    	$query = "";
    	foreach($removeSlaves as $slave) {
	    	$query .= 'delete from `subnets` where `id` = "'. $slave .'"; '."\n";
	    	$query .= 'delete from `ipaddresses` where `subnetId` = "'. $slave .'"; '."\n";	
    	}
    }
    # Edit subnet
    else if ($subnetDetails['action'] == "edit")
    {

        # custom fields
        $myFields = getCustomSubnetFields();
        $myFieldsInsert['query']  = '';
	
        if(sizeof($myFields) > 0) {
			/* set inserts for custom */
			foreach($myFields as $myField) {			
				$myFieldsInsert['query']  .= ', `'. $myField['name'] .'` = "'.$subnetDetails[$myField['name']].'" ';
			}
		}

        $query  = 'update subnets set '. "\n";
        $query .= '`description` 	= "'. htmlentities($subnetDetails['description']) .'", '. "\n";
        if($subnetDetails['sectionId'] != $subnetDetails['sectionIdNew']) {
        $query .= '`sectionId`      = "'. $subnetDetails['sectionIdNew'] 	.'", '. "\n";
        }
        $query .= '`vlanId`        	= "'. $subnetDetails['vlanId'] 			.'", '. "\n";
        $query .= '`vrfId`        	= "'. $subnetDetails['vrfId'] 			.'", '. "\n";
        $query .= '`masterSubnetId` = "'. $subnetDetails['masterSubnetId'] 	.'", '. "\n";
        $query .= '`allowRequests`  = "'. $subnetDetails['allowRequests'] 	.'", '. "\n";
        $query .= '`showName`   	= "'. $subnetDetails['showName'] 		.'", '. "\n";
        $query .= '`adminLock` 		= "'. $subnetDetails['adminLock'] 		.'"  '. "\n";
        $query .= $myFieldsInsert['query'];
        $query .= 'where id      	= "'. $subnetDetails['subnetId'] .'"; '."\n";
    
        # if section changes
        if($subnetDetails['sectionId'] != $subnetDetails['sectionIdNew']) {
	        # add querry to change slaves!
	        global $removeSlaves;
	        getAllSlaves ($subnetDetails['subnetId']);
	        $removeSlaves = array_unique($removeSlaves);
    	    		
	        foreach($removeSlaves as $slave) {
    			if($subnetDetails['subnetId'] != $slave) {
	    			$query .= 'update `subnets` set `sectionId` = "'. $subnetDetails['sectionIdNew'] .'" where `id` = "'.$slave.'"; '."\n";
	    		}
	    	}
        }
    }
    # Something is not right!
    else {
	    
    }
    
    # return query
    return $query;
}


/**
 * Print subnets structure
 */
function printAdminSubnets( $subnets, $actions = true, $vrf = "0" )
{
		$html = array();
		
		$rootId = 0;									# root is 0

		if(sizeof($subnets) > 1) {
		foreach ( $subnets as $item ) {
			$children[$item['masterSubnetId']][] = $item;
		}
		}
		
		# loop will be false if the root has no children (i.e., an empty menu!)
		$loop = !empty( $children[$rootId] );
		
		# initializing $parent as the root
		$parent = $rootId;
		$parent_stack = array();
		
		# display selected subnet as opened
		if(isset($_REQUEST['subnetId']))
		$allParents = getAllParents ($_REQUEST['subnetId']);
		
		# return table content (tr and td's)
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $rootId ) ) )
		{
			# repeat 
			$repeat  = str_repeat( " - ", ( count($parent_stack)) );
			# dashes
			if(count($parent_stack) == 0)	{ $dash = ""; }
			else							{ $dash = "-"; }

			if(count($parent_stack) == 0) {
				$margin = "0px";
				$padding = "0px";
			}
			else {
				# padding
				$padding = "10px";			

				# margin
				$margin  = (count($parent_stack) * 10) -10;
				$margin  = $margin *2;
				$margin  = $margin."px";				
			}
							
			# count levels
			$count = count( $parent_stack ) + 1;
			
			# get subnet details
				# get VLAN
				$vlan = subnetGetVLANdetailsById($option['value']['vlanId']);
				$vlan = $vlan['number'];
				if(empty($vlan) || $vlan == 0) 	{ $vlan = ""; }			# no VLAN

				# description
				if(strlen($option['value']['description']) == 0) 	{ $description = "/"; }													# no description
				else 												{ $description = $option['value']['description']; }						# description		
				
				# requests
				if($option['value']['allowRequests'] == 1) 			{ $requests = "enabled"; }												# requests enabled
				else 												{ $requests = ""; }														# request disabled				

				# check if it is locked for writing
				if($option['value']['adminLock'] == "1") 			{ $locked   = "<i class='icon-gray icon-lock' rel='tooltip' title='subnet is locked for writing for non-admins!'></i>"; } 	# locked
				else 												{ $locked   = ""; }																											# unlocked
			
				#vrf
				if($vrf == "1") {
					# get VRF details
					if(($option['value']['vrfId'] != 0) && ($option['value']['vrfId'] != "NULL") ) {
						$vrfTmp = getVRFDetailsById ($option['value']['vrfId']);
						$vrfText = $vrfTmp['name'];
					}
					else {
						$vrfText = "";
					}
				}				
			
			# print table line
			if(strlen($option['value']['subnet']) > 0) { 
				$html[] = "<tr>";
				$html[] = "	<td class='level$count'><span class='structure' style='padding-left:$padding; margin-left:$margin;'></span><a href='subnets/".$option['value']['sectionId']."/".$option['value']['id']."/'>  ".transform2long($option['value']['subnet']) ."/".$option['value']['mask']."</a></td>";
				$html[] = "	<td class='level$count'><span class='structure' style='padding-left:$padding; margin-left:$margin;'></span> $description</td>";
				$html[] = "	<td>$vlan</td>";
				#vrf
				if($vrf == "1") {
				$html[] = "	<td>$vrfText</td>";
				}
				$html[] = "	<td>$requests</td>";
				$html[] = "	<td>$locked</td>";
				if($actions) {
				$html[] = "	<td>";
				$html[] = "		<button class='btn btn-small editSubnet' data-action='edit'   data-subnetid='".$option['value']['id']."'  data-sectionid='".$option['value']['sectionId']."'><i class='icon-gray icon-edit'></i> Edit</button>";
				$html[] = "		<button class='btn btn-small editSubnet' data-action='delete' data-subnetid='".$option['value']['id']."'  data-sectionid='".$option['value']['sectionId']."'><i class='icon-gray icon-remove'></i> Delete</button>";
				$html[] = "	</td>";
				}
				$html[] = "</tr>";
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
		return implode( "\n", $html );
}











/* @section functions ---------------- */


/**
 * Update section
 */
function UpdateSection ($update) 
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection  
    
    if (!$update['name']) 	{ die('<div class="alert alert-error">Name is mandatory!</div>'); }	# section name is mandatory

    $query = setUpdateSectionQuery ($update);										# set update section query

	$log = prepareLogFromArray ($update);											# prepare log

    # delete and edit requires multiquery
    if ( ( $update['action'] == "delete") || ( $update['action'] == "edit") )
    {
        if (!$result  = $database->executeMultipleQuerries($query)) {
            updateLogTable ('Section ' . $update['action'] .' failed ('. $update['name'] . ')', $log, 2);	# write error log
            die('<div class="alert alert-error">Cannot '. $update['action'] .' all entries!</div>');
        }
        else {
            updateLogTable ('Section '. $update['name'] . ' ' . $update['action'] .' ok', $log, 1);			# write success log
            return true;
        }
    }
    # add is single querry
    else 
    {
        if (!$result  = $database->executeQuery($query)) {
            updateLogTable ('Adding section '. $update['name'] .'failed', $log, 2);							# write error log
            die('<div class="alert alert-error">Cannot update database!</div>');
        }
        else {
            updateLogTable ('Section '. $update['name'] .' added succesfully', $log, 1);					# write success log
            return true;
        }
    }
}


/**
 * Set Query for update section
 */
function setUpdateSectionQuery ($update) 
{
	# add section
    if ($update['action'] == "add") 
    {
        $query = 'Insert into sections (`name`,`description`) values ("'. $update['name'] .'", "'. $update['description'] .'");';
    }
    # edit section
    else if ($update['action'] == "edit") 
    {
        $section_old = getSectionDetailsById ( $update['id'] );												# Get old section name for update
        # Update section name
        $query   = "update `sections` set `name` = '$update[name]', `description` = '$update[description]' where `id` = '$update[id]';";			
    }
	# delete section
	else if( $update['action'] == "delete" ) 
	{
        /* we must delete many entries - section, all belonging subnets and ip addresses */
        $sectionId = $update['id'];
        
        # delete sections query
		$query  = "delete from `sections` where `id` = '$sectionId';"."\n";
		# delete belonging subnets
		$query .= "delete from `subnets` where `sectionId` = '$sectionId';"."\n";		
		# delete IP addresses query
		$subnets = fetchSubnets ( $sectionId );
		
		if (sizeof($subnets) != 0) {
            foreach ($subnets as $subnet) {
            $query .= "delete from `ipaddresses` where `subnetId` = '$subnet[id]';"."\n";
            }
        }
    }
    
    /* return query */
    return $query;
}










/* @switch functions ---------------- */


/**
 * Update switch details
 */
function updateSwitchDetails($switch)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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
    
    # execute query
    $res    = $database->executeQuery($query);  

    /* prepare log */ 
    $log = prepareLogFromArray ($switch);
    
    /* return details */
    if($res) {
        updateLogTable ('Switch ' . $switch['action'] .' success ('. $switch['hostname'] . ')', $log, 0);
    	return true;
    }
    else {
       	updateLogTable ('Switch ' . $switch['action'] .' failed ('. $switch['hostname'] . ')', $log, 2);
    	return false;
    }
}


/**
 * reformat sections for switches!
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
 * Update IP address list when switch hostname changes
 */
function updateIPaddressesOnSwitchChange($old, $new) 
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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











/* @adLDAP functions ---------------- */

/**
 * Get Domain settings for authentication
 */
function getADSettings()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

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
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
	
	/* if OpenLDAP then append BaseDN to account suffix */
	if($ad['type'] == "2") { $ad['account_suffix'] = ",".$ad['base_dn']; }
	
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








/* @VRF functions ---------------- */


/**
 * Update VRF details
 */
function updateVRFDetails($vrf)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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
    
    # execute query
    $res    = $database->executeQuery($query); 

    # if delete also NULL all subnets!
    if($vrf['action'] == 'delete') {
	    $query = "update `subnets` set `vrfId` = NULL where `vrfId` = '$vrf[vrfId]';";
	    /* execute */
	    try { $database->executeQuery( $query ); }
	    catch (Exception $e) {
    		$error =  $e->getMessage();
    		print ('<div class="alert alert-error alert-absolute">'.$error.'</div>');
    	}
    } 

    /* prepare log */ 
    $log = prepareLogFromArray ($vrf);
        
    /* return details */
    if($res) {
    	updateLogTable ('VRF ' . $vrf['action'] .' success ('. $vrf['name'] . ')', $log, 0);
    	return true;
    }
    else {
   		updateLogTable ('VRF ' . $vrf['action'] .' failed ('. $vrf['name'] . ')', $log, 2);
    	return false;
    }
}










/* @VLAN functions ---------------- */


/**
 * Update VLAN details
 */
function updateVLANDetails($vlan)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set querry based on action */
    if($vlan['action'] == "add") {
    
        # custom fields
        $myFields = getCustomVLANFields();
        $myFieldsInsert['query']  = '';
        $myFieldsInsert['values'] = '';
	
        if(sizeof($myFields) > 0) {
			/* set inserts for custom */
			foreach($myFields as $myField) {			
				$myFieldsInsert['query']  .= ', `'. $myField['name'] .'`';
				$myFieldsInsert['values'] .= ", '". $vlan[$myField['name']] . "'";
			}
		}
    
    	$query  = 'insert into `vlans` '. "\n";
    	$query .= '(`name`,`number`,`description` '.$myFieldsInsert['query'].') values '. "\n";
   		$query .= '("'. $vlan['name'] .'", "'. $vlan['number'] .'", "'. $vlan['description'] .'" '. $myFieldsInsert['values'] .' ); '. "\n";

    }
    else if($vlan['action'] == "edit") {
    
        # custom fields
        $myFields = getCustomVLANFields();
        $myFieldsInsert['query']  = '';
	
        if(sizeof($myFields) > 0) {
			/* set inserts for custom */
			foreach($myFields as $myField) {			
				$myFieldsInsert['query']  .= ', `'. $myField['name'] .'` = "'.$vlan[$myField['name']].'" ';
			}
		}
    
    	$query  = 'update `vlans` set '. "\n";    
    	$query .= '`name` = "'. $vlan['name'] .'", `number` = "'. $vlan['number'] .'", `description` = "'. $vlan['description'] .'" '. "\n";   
    	$query .= $myFieldsInsert['query'];  
    	$query .= 'where `vlanId` = "'. $vlan['vlanId'] .'";'. "\n";    
    }
    else if($vlan['action'] == "delete") {
    	$query  = 'delete from `vlans` where `vlanId` = "'. $vlan['vlanId'] .'";'. "\n";
    }
    
    # execute query
    $res    = $database->executeQuery($query); 
    
    # if delete also NULL all subnets!
    if($vlan['action'] == 'delete') {
	    $query = "update `subnets` set `vlanId` = NULL where `vlanId` = '$vlan[vlanId]';";
	    /* execute */
	    try { $database->executeQuery( $query ); }
	    catch (Exception $e) {
    		$error =  $e->getMessage();
    		print ('<div class="alert alert-error alert-absolute">'.$error.'</div>');
    	}
    }
    
    /* prepare log */ 
    $log = prepareLogFromArray ($vlan);
    
    /* return details */
    if($res) {
    	updateLogTable ('VLAN ' . $vlan['action'] .' success ('. $vlan['name'] . ')', $log, 0);
    	return true;
    }
    else {
   		updateLogTable ('VLAN ' . $vlan['action'] .' failed ('. $vlan['name'] . ')', $log, 2);
    	return false;
    }
}










/* @other functions ---------------- */


/**
 * update site settings
 */
function updateSettings($settings)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'update `settings` set ' . "\n";
    $query   .= '`siteTitle` 		  = "'. $settings['siteTitle'] .'", ' . "\n";
    $query   .= '`siteDomain` 		  = "'. $settings['siteDomain'] .'", ' . "\n";
    $query   .= '`siteURL` 			  = "'. $settings['siteURL'] .'", ' . "\n";
    $query   .= '`siteAdminName` 	  = "'. $settings['siteAdminName'] .'", ' . "\n";
    $query   .= '`siteAdminMail` 	  = "'. $settings['siteAdminMail'] .'", ' . "\n";
	$query   .= '`domainAuth` 		  = "'. isCheckbox($settings['domainAuth']) .'", ' . "\n";
	$query   .= '`showTooltips`		  = "'. isCheckbox($settings['showTooltips']) .'", ' . "\n";
	$query   .= '`enableIPrequests`   = "'. isCheckbox($settings['enableIPrequests']) .'", ' . "\n";
	$query   .= '`enableVRF`   		  = "'. isCheckbox($settings['enableVRF']) .'", ' . "\n";
	$query   .= '`donate`   		  = "'. isCheckbox($settings['donate']) .'", ' . "\n";
	$query   .= '`strictMode`   	  = "'. isCheckbox($settings['strictMode']) .'", ' . "\n";
	$query   .= '`enableDNSresolving` = "'. isCheckbox($settings['enableDNSresolving']) .'", ' . "\n";  
    $query   .= '`printLimit` 	      = "'. $settings['printLimit'] .'", ' . "\n"; 
    $query   .= '`vlanDuplicate` 	  = "'. $settings['vlanDuplicate'] .'" ' . "\n"; 
	$query   .= 'where id = 1;' . "\n";   

	/* set log file */
	foreach($settings as $key=>$setting) {
		$log .= " ". $key . ": " . $setting . "<br>";
	}
    
    # execute query
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
 *	Verify checkboxes for saving config
 */
function isCheckbox($checkbox)
{
	if($checkbox == "") {
		$chkbox = "0";
	}
	else {
		$chkbox = $checkbox;
	}
	
	/* return 0 if not checkbos and same result if checkbox */
	return $chkbox;
}


/**
 * Search and replace fields
 */
function searchAndReplace($query, $post)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* check how many records are in database */
    $query2 = 'select count(*) as count from `ipaddresses` where '. $post['field'] .' like "%'. $post['search'] .'%";';
    $count 	  = $database->getArray($query2); 
    $count 	  = $count[0]['count'];
  
	/* execute */
    try {
    	$database->executeQuery( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error alert-absolute">Replace error: '. $error .'</div>');
	}
	
	if(!isset($e)) {
		print '<div class="alert alert-success alert-absolute">Replaced '. $count .' items successfully!</div>';
	}
}


/**
 *	Write instructions
 */
function writeInstructions ($instructions) 
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	$instructions = $database->real_escape_string($instructions);	//this hides code
	
	# execute query
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
 * CSV import IP address
 *
 *		provided input is CSV line!
 */
function importCSVline ($line, $subnetId)
{
	/* array */
	$line = explode(",", $line);

    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get subnet details by Id */
    $subnetDetails = getSubnetDetailsById ($subnetId);
    $subnet = Transform2long($subnetDetails['subnet']) . "/" . $subnetDetails['mask'];
   
    /* verify! */
    if (VerifyIpAddress( $line[0] , $subnet )) {
    	return 'Wrong IP address - '. $line[0];
    } 
    
    /* check for duplicates */
    if (checkDuplicate ($line[0], $subnetId)) {
    	return 'IP address already exists - '. $line[0];
    }
    
    /* reformat state */
    switch($line[5]) {
    	case "Active": 		$line[5] = "1";	break;
    	case "active": 		$line[5] = "1";	break;
    	case "Reserved": 	$line[5] = "2";	break;
    	case "reserved": 	$line[5] = "2";	break;
    	case "Offline": 	$line[5] = "0";	break;
    	case "offline": 	$line[5] = "0";	break;
    }
    
    /* reformat switch! */
    $switch = getSwitchDetailsByHostname($line[7]);
    
    /* get custom fields */
    $myFields = getCustomIPaddrFields();
    if(sizeof($myFields) > 0) {
    	$import['fieldName']  = "";
    	$import['fieldValue'] = "";
    	$m = 9;
    	foreach($myFields as $field) {
	    	$import['fieldName']  .= ",`$field[name]`";
	    	$import['fieldValue'] .= ",'$line[$m]'";
	    	$m++;
    	}
    }
	
	/* all ok, set query */
	$query  = "insert into ipaddresses ";
	$query .= "(`subnetId`, `ip_addr`, `state`, `description`, `dns_name`, `mac`, `owner`, `switch`, `port`, `note` $import[fieldName] ) ";
	$query .= "values ";
	$query .= "('$subnetId','".Transform2decimal( $line[0] )."', '$line[1]','$line[2]','$line[3]','$line[4]','$line[5]','$line[6]','$switch[id]','$line[8]' $import[fieldValue]);";
	
	/* set log details */
	$log = prepareLogFromArray ($line);

	/* execute */
    try {
    	$database->executeQuery( $query );
    }
    catch (Exception $e) {
    	$error = $e->getMessage();
	}
	
	if(!isset($e)) {
        updateLogTable ('CSV import of IP address '. $line[1] .' succeeded', $log, 0);
		return true;
	}
	else {
        updateLogTable ('CSV import of IP address '. $line[1] .' failed', $log, 2);
        return $error;		
	}
}









/* @filter functions ---------------- */


/**
 * Get all fields in IP addresses
 */
function getIPaddrFields()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `ipaddresses`;';
    $fields	  = $database->getArray($query); 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']] = $field['Field'];
	}
	
	return $res;
}


/**
 * Get selected IP fields
 */
function getSelectedIPaddrFields()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'select IPfilter from `settings`;';
    $fields	  = $database->getArray($query); 
	
	return $fields[0]['IPfilter'];
}


/**
 * Set selected IP fields
 */
function updateSelectedIPaddrFields($fields)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'update `settings` set `IPfilter` = "'. $fields .'";';
	
    # execute query
    if (!$database->executeQuery($query)) {
        updateLogTable ('Failed to change IP field filter', $fields,  2);
        return false;
    }
    else {
        updateLogTable ('IP field filter change success', $fields, 1);
        return true;
    }
}









/* @custom IP address fields */


/**
 * Get all fields in IP addresses
 */
function getCustomIPaddrFields()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `ipaddresses`;';
    $fields	  = $database->getArray($query); 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnetId'], $res['ip_addr'], $res['description'], $res['dns_name'], $res['switch']);
	unset($res['port'], $res['mac'], $res['owner'], $res['state'], $res['note']);
	
	return $res;
}


/**
 * Get all fields in IP addresses in number array
 */
function getCustomIPaddrFieldsNumArr()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `ipaddresses`;';
    $fields	  = $database->getArray($query); 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnetId'], $res['ip_addr'], $res['description'], $res['dns_name'], $res['switch']);
	unset($res['port'], $res['mac'], $res['owner'], $res['state'], $res['note']);
	
	/* reindex */
	foreach($res as $line) {
		$out[] = $line['name'];
	}
	
	return $out;
}


/**
 *Update custom field
 */
function updateCustomIPField($field)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    if($field['action'] == "delete") {
    	$query  = 'ALTER TABLE `ipaddresses` DROP `'. $field['name'] .'`;';
    }
    else if ($field['action'] == "edit") {
    	$query  = 'ALTER TABLE `ipaddresses` CHANGE COLUMN `'. $field['oldname'] .'` `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;';
    }
    else {
    	$query  = 'ALTER TABLE `ipaddresses` ADD COLUMN `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;';
    }
    
    /* prepare log */ 
    $log = prepareLogFromArray ($field);
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('CustomIPField ' . $field['action'] .' failed ('. $field['name'] . ')', $log, 2);
        return false;
    }
    else {
        updateLogTable ('CustomIPField ' . $field['action'] .' success ('. $field['name'] . ')', $log, 0);
        return true;
    }
}


/**
 * reorder custom field
 */
function reorderCustomIPField($next, $current)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    $query  = 'ALTER TABLE `ipaddresses` MODIFY COLUMN `'. $current .'` VARCHAR(256) AFTER `'. $next .'`;';
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('CustomIPField reordering failed ('. $next .' was not put before '. $current .')', $log, 2);
        return false;
    }
    else {
	    updateLogTable ('CustomIPField reordering success ('. $next .' put before '. $current .')', $log, 0);
        return true;
    }
}










/* @custom subnet fields */


/**
 * Get all custom subnet fields
 */
function getCustomSubnetFields()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `subnets`;';
    $fields	  = $database->getArray($query); 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnet'], $res['mask'], $res['sectionId'], $res['description'], $res['masterSubnetId']);
	unset($res['vrfId'], $res['allowRequests'], $res['adminLock'], $res['vlanId'], $res['showName']);
	
	return $res;
}


/**
 * Get all custom subnet fields in number array
 */
function getCustomSubnetsFieldsNumArr()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `subnets`;';
    $fields	  = $database->getArray($query); 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnet'], $res['mask'], $res['sectionId'], $res['description'], $res['masterSubnetId']);
	unset($res['vrfId'], $res['allowRequests'], $res['adminLock'], $res['vlanId'], $res['showName']);
	
	/* reindex */
	foreach($res as $line) {
		$out[] = $line['name'];
	}
	
	return $out;
}


/**
 * Update custom subnet field
 */
function updateCustomSubnetField($field)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    if($field['action'] == "delete") {
    	$query  = 'ALTER TABLE `subnets` DROP `'. $field['name'] .'`;';
    }
    else if ($field['action'] == "edit") {
    	$query  = 'ALTER TABLE `subnets` CHANGE COLUMN `'. $field['oldname'] .'` `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;';
    }
    else {
    	$query  = 'ALTER TABLE `subnets` ADD COLUMN `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;';
    }
    
    /* prepare log */ 
    $log = prepareLogFromArray ($field);
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('Custom Subnet Field ' . $field['action'] .' failed ('. $field['name'] . ')', $log, 2);
        return false;
    }
    else {
        updateLogTable ('Custom Subnet Field ' . $field['action'] .' success ('. $field['name'] . ')', $log, 0);
        return true;
    }
}


/**
 * reorder custom field
 */
function reorderCustomSubnetField($next, $current)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    $query  = 'ALTER TABLE `subnets` MODIFY COLUMN `'. $current .'` VARCHAR(256) AFTER `'. $next .'`;';
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('Custom Subnet Field reordering failed ('. $next .' was not put before '. $current .')', $log, 2);
        return false;
    }
    else {
	    updateLogTable ('Custom Subnet Field reordering success ('. $next .' put before '. $current .')', $log, 0);
        return true;
    }
}











/* @custom VLAN fields */


/**
 * Get all custom VLAN fields
 */
function getCustomVLANFields()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `vlans`;';
    $fields	  = $database->getArray($query); 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['vlanId'], $res['name'], $res['number'], $res['description']);
	
	return $res;
}


/**
 * Get all custom VLAN fields in number array
 */
function getCustomVLANFieldsNumArr()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `vlans`;';
    $fields	  = $database->getArray($query); 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['vlanId'], $res['name'], $res['number'], $res['description']);
	
	/* reindex */
	foreach($res as $line) {
		$out[] = $line['name'];
	}
	
	return $out;
}


/**
 * Update custom VLAN field
 */
function updateCustomVLANField($field)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    if($field['action'] == "delete") {
    	$query  = 'ALTER TABLE `vlans` DROP `'. $field['name'] .'`;';
    }
    else if ($field['action'] == "edit") {
    	$query  = 'ALTER TABLE `vlans` CHANGE COLUMN `'. $field['oldname'] .'` `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;';
    }
    else {
    	$query  = 'ALTER TABLE `vlans` ADD COLUMN `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;';
    }
    
    /* prepare log */ 
    $log = prepareLogFromArray ($field);
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('Custom VLAN Field ' . $field['action'] .' failed ('. $field['name'] . ')', $log, 2);
        return false;
    }
    else {
        updateLogTable ('Custom VLAN Field ' . $field['action'] .' success ('. $field['name'] . ')', $log, 0);
        return true;
    }
}


/**
 * reorder custom VLAN field
 */
function reorderCustomVLANField($next, $current)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    $query  = 'ALTER TABLE `vlans` MODIFY COLUMN `'. $current .'` VARCHAR(256) AFTER `'. $next .'`;';
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('Custom VLAN Field reordering failed ('. $next .' was not put before '. $current .')', $log, 2);
        return false;
    }
    else {
	    updateLogTable ('Custom VLAN Field reordering success ('. $next .' put before '. $current .')', $log, 0);
        return true;
    }
}




?>