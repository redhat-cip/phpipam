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
        $query .= '(`username`, `password`, `role`, `real_name`, `email`, `domainUser`, `useFullPageWidth`) '. "\n"; 
        $query .= 'values '. "\n"; 
        $query .= '("'. $userModDetails['username'] .'", "'. $userModDetails['password1'] .'", "'. $userModDetails['role'] .'", '. "\n";
        $query .= ' "'. $userModDetails['real_name'] .'", "'. $userModDetails['email'] .'", "'. $userModDetails['domainUser'] .'", "'. $userModDetails['useFullPageWidth'] .'" );';
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
        $query .= '`domainUser`= "'. $userModDetails['domainUser'] .'", '. "\n"; 
        $query .= '`useFullPageWidth`= "'. $userModDetails['useFullPageWidth'] .'" '. "\n"; 
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
    $query .= '`useFullPageWidth`= "'. $userModDetails['useFullPageWidth'] .'", ' . "\n";
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










/* @subnet functions ---------------- */


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
        $query .= '(`subnet`, `mask`, `sectionId`, `description`, `vlanId`, `vrfId`, `masterSubnetId`, `allowRequests`, `adminLock`) ' . "\n";
        $query .= 'values (' . "\n";
        $query .= ' "'. $subnetDetails['subnet'] 		 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['mask'] 			 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['sectionId'] 	 .'", ' . "\n"; 
        $query .= ' "'. htmlentities($subnetDetails['description']) .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['vlanId'] 			 .'", ' . "\n"; 
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
        $query .= '`vlanId`        	= "'. $subnetDetails['vlanId'] 			.'", '. "\n";
        $query .= '`vrfId`        	= "'. $subnetDetails['vrfId'] 			.'", '. "\n";
        $query .= '`masterSubnetId` = "'. $subnetDetails['masterSubnetId'] 	.'", '. "\n";
        $query .= '`allowRequests`  = "'. $subnetDetails['allowRequests'] 	.'", '. "\n";
        $query .= '`adminLock` 		= "'. $subnetDetails['adminLock'] 		.'"  '. "\n";
        $query .= 'where id      	= "'. $subnetDetails['subnetId'] .'";';
    }
    
    /* return query */
    return $query;
}










/* @section functions ---------------- */


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
            updateLogTable ('Section ' . $update['action'] .' failed ('. $update['name'] . ')', $log, 2);
            die('<div class="error">Cannot '. $update['action'] .' all entries!</div>');
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










/* @switch functions ---------------- */


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











/* @adLDAP functions ---------------- */

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








/* @VRF functions ---------------- */


/**
 * Update VRF details
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
    $res    = $database->executeQuery($query);  

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
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* set querry based on action */
    if($vlan['action'] == "add") {
    	$query  = 'insert into `vlans` '. "\n";
    	$query .= '(`name`,`number`,`description`) values '. "\n";
   		$query .= '("'. $vlan['name'] .'", "'. $vlan['number'] .'", "'. $vlan['description'] .'" ); '. "\n";
    }
    else if($vlan['action'] == "edit") {
    	$query  = 'update `vlans` set '. "\n";    
    	$query .= '`name` = "'. $vlan['name'] .'", `number` = "'. $vlan['number'] .'", `description` = "'. $vlan['description'] .'" '. "\n";     
    	$query .= 'where `vlanId` = "'. $vlan['vlanId'] .'";'. "\n";    
    }
    else if($vlan['action'] == "delete") {
    	$query  = 'delete from `vlans` where `vlanId` = "'. $vlan['vlanId'] .'";'. "\n";
    }
    
    /* execute query */
    $res    = $database->executeQuery($query); 
    
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
	$query   .= '`domainAuth` 		  = "'. isCheckbox($settings['domainAuth']) .'", ' . "\n";
	$query   .= '`showTooltips`		  = "'. isCheckbox($settings['showTooltips']) .'", ' . "\n";
	$query   .= '`enableIPrequests`   = "'. isCheckbox($settings['enableIPrequests']) .'", ' . "\n";
	$query   .= '`enableVRF`   		  = "'. isCheckbox($settings['enableVRF']) .'", ' . "\n";
	$query   .= '`donate`   		  = "'. isCheckbox($settings['donate']) .'", ' . "\n";
	$query   .= '`enableDNSresolving` = "'. isCheckbox($settings['enableDNSresolving']) .'" ' . "\n";   
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
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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
    	die('<div class="error">Replace error: '. $error .'</div>');
	}
	
	if(!isset($e)) {
		print '<div class="success">Replaced '. $count .' items successfully!</div>';
	}
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









/* @filter functions ---------------- */


/**
 * Get all fields in IP addresses
 */
function getIPaddrFields()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'update `settings` set `IPfilter` = "'. $fields .'";';
	
    /* execute query */
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
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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
 *Update custom field
 */
function updateCustomIPField($field)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
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
        updateLogTable ('CustomIPField ' . $field['action'] .' success ('. $field['name'] . ')', $log, 0);
        return false;
    }
    else {
        updateLogTable ('CustomIPField ' . $field['action'] .' failed ('. $field['name'] . ')', $log, 2);
        return true;
    }
}



?>