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
    if (!$userModDetails['real_name']) 																			{ $errors[] = _('Real name field is mandatory!'); }
    # Both passwords must be same
    if ($userModDetails['password1'] != $userModDetails['password2']) 											{ $errors[] = _("Passwords do not match!"); }
    # pass must be at least 8 chars long for non-domain users
    if($userModDetails['domainUser'] != 1 ) { 
    	if ((strlen($userModDetails['password1orig']) < 8 ) && (strlen($userModDetails['password1orig']) != 0)) { $errors[] = _("Password must be at least 8 characters long!"); }
    	else if (($userModDetails['action'] == "add") && (strlen($userModDetails['password1orig']) < 8 )) 		{ $errors[] = _("Password must be at least 8 characters long!"); }
    }
    # email format must be valid
    if (!checkEmail($userModDetails['email'])) 																	{ $errors[] = _("Invalid email address!"); }
    # username must not already exist (if action is add)
    if ($userModDetails['action'] == "add") {
        global $db;																	 				# get variables from config file
        $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 				# open db connection
        
        $query    = 'select * from users where username = "'. $userModDetails['username'] .'";'; 	# set query and fetch results

        /* execute */
        try { $details = $database->getArray( $query ); }
        catch (Exception $e) { 
        	$error =  $e->getMessage(); 
        	die("<div class='alert alert-error'>"._('Error').": $error</div>");
        }

        # user already exists
        if (sizeof($details) != 0) 																				{ $errors[] = _("User")." ".$userModDetails['username']." "._("already exists!"); }
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
		print "<div class='alert alert-error'>"._('Cannot delete user')."!<br><strong>"._('Error')."</strong>: $error</div>";
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

    # replace special chars
    $userModDetails['groups'] = mysqli_real_escape_string($database, $userModDetails['groups']);

    # set query - add or edit user
    if (empty($userModDetails['userId'])) {
    
         # custom fields
        $myFields = getCustomUserFields();
        $myFieldsInsert['query']  = '';
        $myFieldsInsert['values'] = '';
	
        if(sizeof($myFields) > 0) {
			/* set inserts for custom */
			foreach($myFields as $myField) {			
				$myFieldsInsert['query']  .= ', `'. $myField['name'] .'`';
				$myFieldsInsert['values'] .= ", '". $userModDetails[$myField['name']] . "'";
			}
		}
    
        $query  = "insert into users ";
        $query .= "(`username`, `password`, `role`, `real_name`, `email`, `domainUser`,`groups`,`lang` $myFieldsInsert[query]) values "; 
        $query .= "('$userModDetails[username]', '$userModDetails[password1]', '$userModDetails[role]', '$userModDetails[real_name]', '$userModDetails[email]', '$userModDetails[domainUser]','$userModDetails[groups]','$userModDetails[lang]' $myFieldsInsert[values]);";
    }
    else {

        # custom fields
        $myFields = getCustomUserFields();
        $myFieldsInsert['query']  = '';
	
        if(sizeof($myFields) > 0) {
			/* set inserts for custom */
			foreach($myFields as $myField) {			
				$myFieldsInsert['query']  .= ', `'. $myField['name'] .'` = \''.$userModDetails[$myField['name']].'\' ';
			}
		}

        $query  = "update users set "; 
        $query .= "`username` = '$userModDetails[username]', "; 
        if (strlen($userModDetails['password1']) != 0) {
        $query .= "`password` = '$userModDetails[password1]', "; 
        }
        $query .= "`role`     = '$userModDetails[role]', `real_name`= '$userModDetails[real_name]', `email` = '$userModDetails[email]', `domainUser`= '$userModDetails[domainUser]', `lang`= '$userModDetails[lang]', `groups`='".$userModDetails['groups']."' "; 
    	$query .= $myFieldsInsert['query'];  
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
		print "<div class='alert alert-error'>"._("Cannot $userModDetails[action] user")."!<br><strong>"._('Error')."</strong>: $error</div>";
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
    $query .= "`real_name`= '$userModDetails[real_name]', `email` = '$userModDetails[email]', ";
    $query .= "`lang`= '$userModDetails[lang]' ";
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
		print "<div class='alert alert-error'>"._('Cannot update user')."!<br><strong>"._('Error')."</strong>: $error</div>";
		updateLogTable ('User '. $userModDetails['real_name'] . ' selfupdate failed', $log,  2);	# write error log
		return false;
	}
}


/**
 * Modify lang
 */
function modifyLang ($lang)
{
    global $db;                                                                      # get variables from config file
    
    /* set query based on action */
    if($lang['action'] == "add")		{ $query = "insert into `lang` (`l_code`,`l_name`) values ('$lang[l_code]','$lang[l_name]');"; }
    elseif($lang['action'] == "edit")	{ $query = "update `lang` set `l_code`='$lang[l_code]',`l_name`='$lang[l_name]' where `l_id`='$lang[l_id]'; "; }
    elseif($lang['action'] == "delete")	{ $query = "delete from `lang` where `l_id`='$lang[l_id]'; "; }    
    else								{ return 'false'; }
    
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
    
    return true;
}









/* @group functions ---------------- */


/**
 *	getall groups
 */
function getAllGroups() 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query = "select * from `userGroups` order by `g_name` asc;";
    
  	/* get groups */
    try { $groups = $database->getArray( $query ); }
    catch (Exception $e) { 
     	$error =  $e->getMessage(); 
        die("<div class='alert alert-error'>"._('Error').": $error</div>");
    }
   	
   	/* return false if none, else list */
	if(sizeof($groups) == 0) { return false; }
	else					 { return $groups; }
}


/**
 *	Group details by ID
 */
function getGroupById($id) 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
	$query = "select * from `userGroups` where `g_id`= '$id';";
    
   	/* get group */
    try { $group = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        die("<div class='alert alert-error'>"._('Error').": $error</div>");
    }
   	
   	/* return false if none, else list */
	if(sizeof($group) == 0) { return false; }
	else					{ return $group[0]; }
}
 

/**
 * Parse all user groups
 */
function parseUserGroups($groups)
{
	if(sizeof($groups)>0) {
    	foreach($groups as $g) {
    		$tmp = getGroupById($g);
    		$out[$tmp['g_id']] = $tmp;
    	}
    }
    /* return array of groups */
    return $out;
}


/**
 * Parse all user groups - get only Id's
 */
function parseUserGroupsIds($groups)
{
	if(sizeof($groups) >0) {
	    foreach($groups as $g) {
    		$tmp = getGroupById($g);
    		$out[$tmp['g_id']] = $tmp['g_id'];
    	}
    }
    /* return array of groups */
    return $out;
}



/**
 *	Get users in group
 */
function getUsersInGroup($gid)
{
	# get all users
	$users = getAllUsers();
	
	# check if $gid in array
	foreach($users as $u) {
		$g = json_decode($u['groups'], true);
		$g = parseUserGroups($g);
		
		if(sizeof($g)>0) {
			foreach($g as $gr) {
				if(in_array($gid, $gr)) {
					$out[] = $u['id'];
				}	
			}
		}
	}
	# return
	return $out;
}


/**
 *	Get users not in group
 */
function getUsersNotInGroup($gid)
{
	# get all users
	$users = getAllUsers();
	
	# check if $gid in array
	foreach($users as $u) {
		if($u['role'] != "Administrator") {
			$g = json_decode($u['groups'], true);		
			if(!in_array($gid, $g)) { $out[] = $u['id']; }
		}
	}
	# return
	return $out;
}


/**
 *	Function that returns all sections with selected group partitions
 */
function getSectionPermissionsByGroup ($gid, $name = true)
{
	# get all users
	$sec = fetchSections();
	
	# check if $gid in array
	foreach($sec as $s) {
		$p = json_decode($s['permissions'], true);	
		if(sizeof($p)>0) {
			if($name) {
				if(array_key_exists($gid, $p)) { $out[$s['name']] = $p[$gid]; }
			}
			else {
				if(array_key_exists($gid, $p)) { $out[$s['id']] = $p[$gid]; }
			}
		}
		# no permissions
		else {
			$out[$s['name']] = 0;
		}
	}
	# return
	return $out;
}



/**
 *	Modify group
 */
function modifyGroup($g)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);    
    
    # set query
    if($g['action'] == "add") 			{ $query = "insert into `userGroups` (`g_name`,`g_desc`) values ('$g[g_name]','$g[g_desc]'); "; }
    else if($g['action'] == "edit")		{ $query = "update `userGroups` set `g_name`='$g[g_name]', `g_desc`='$g[g_desc]' where `g_id` = '$g[g_id]';"; }
    else if($g['action'] == "delete")	{ $query = "delete from `userGroups` where `g_id` = '$g[g_id]';"; }
    else								{ return false; }

	# execute
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { $error =  $e->getMessage(); }

    # set log file
    $log = prepareLogFromArray ($g);													# prepare log 
	
	# ok
	if(!isset($error)) {
        updateLogTable ("Group $g[action] success", $log, 0);	# write success log
        return true;		
	}
	# problem
	else {
		print "<div class='alert alert-error'>"._("Cannot $userModDetails[action] user")."!<br><strong>"._('Error')."</strong>: $error</div>";
		updateLogTable ("Group $g[action] error", $log, 2);	# write error log
		return false;
	}    
    
}


/**
 *	Delete all users from group
 */
function deleteUsersFromGroup($gid)
{
	# get all users
	$users = getAllUsers();
	
	# check if $gid in array
	foreach($users as $u) {
		$g = json_decode($u['groups'], true);
		$go = $g;
		$g = parseUserGroups($g);
		
		if(sizeof($g)>0) {
			foreach($g as $gr) {
				if(in_array($gid, $gr)) {
					unset($go[$gid]);
					$ng = json_encode($go);
					updateUserGroups($u['id'],$ng);
				}	
			}
		}
	}
	# return
	return $out;

}


/**
 *	Delete all users from group
 */
function deleteGroupFromSections($gid)
{
	# get all users
	$sections = fetchSections();
	
	# check if $gid in array
	foreach($sections as $s) {
		$g = json_decode($s['permissions'], true);
		
		if(sizeof($g)>0) {
			if(array_key_exists($gid, $g)) {
				unset($g[$gid]);
				$ng = json_encode($g);
				updateSectionGroups($s['id'],$ng);
			}	
		}
	}
	# return
	return $out;

}



/**
 *	Add user to group
 */
function addUserToGroup($gid, $uid)
{
	# get old groups
	$user = getUserDetailsById($uid);
	
	# append new group
	$g = json_decode($user['groups'], true);
	$g[$gid] = $gid;
	$g = json_encode($g);
	
	# update
	if(!updateUserGroups($uid, $g)) { return false; }
	else							{ return true; }
}


/**
 *	Remove user from group
 */
function removeUserFromGroup($gid, $uid)
{
	# get old groups
	$user = getUserDetailsById($uid);
	
	# append new group
	$g = json_decode($user['groups'], true);
	unset($g[$gid]);
	$g = json_encode($g);
	
	# update
	if(!updateUserGroups($uid, $g)) { return false; }
	else							{ return true; }
}


/**
 *	Update users's group
 */
function updateUserGroups($uid, $groups)
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection   

    # replace special chars
    $groups = mysqli_real_escape_string($database, $groups);

    # set query
    $query = "update `users` set `groups` = '$groups' where `id` = $uid; ";

	# update
    try { $database->executeQuery($query); }
    catch (Exception $e) { 
    	print "<div class='alert alert-error'>"._('Error').": $e</div>";
    	return false; 
    }
    
    # ok
    return true;
}


/**
 *	Update section permissions
 */
function updateSectionGroups($sid, $groups)
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection   

    # replace special chars
   	$groups = mysqli_real_escape_string($database, $groups);

    # set query
    $query = "update `sections` set `permissions` = '$groups' where `id` = $sid; ";

	# update
    try { $database->executeQuery($query); }
    catch (Exception $e) { 
    	print "<div class='alert alert-error'>"._('Error').": $e</div>";
    	return false; 
    }
    
    # ok
    return true;
}










/* @subnet functions ---------------- */


/**
 * Add new subnet
 */
function modifySubnetDetails ($subnetDetails, $lastId = false) 
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection   

    # replace special chars
    $subnetDetails['permissions'] = mysqli_real_escape_string($database, $subnetDetails['permissions']);
    $subnetDetails['description'] = mysqli_real_escape_string($database, $subnetDetails['description']); 

    # set modify subnet details query
    $query = setModifySubnetDetailsQuery ($subnetDetails, $sectionChange);

	$log = prepareLogFromArray ($subnetDetails);																				# prepare log 

    # execute query
    try { $updateId=$database->executeMultipleQuerries($query, $lastId); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        updateLogTable ('Subnet ('. $subnetDetails['description'] .') '. $subnetDetails['action'] .' failed', $log, 2);	# write error log
        print "<div class='alert alert-error'>$error</div>";
        return false;
    }
    
    # success
    updateLogTable ('Subnet ('. $subnetDetails['description'] .') '. $subnetDetails['action'] .' ok', $log, 1);		# write success log
    if(!$lastId) { return true; }
    else		 { return $updateId; }
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
        $query .= '(`subnet`, `mask`, `sectionId`, `description`, `vlanId`, `vrfId`, `masterSubnetId`, `allowRequests`, `showName`, `permissions`, `pingSubnet` '.$myFieldsInsert['query'].') ' . "\n";
        $query .= 'values (' . "\n";
        $query .= ' "'. $subnetDetails['subnet'] 		 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['mask'] 			 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['sectionId'] 	 .'", ' . "\n"; 
        $query .= ' "'. htmlentities($subnetDetails['description']) .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['vlanId'] 			 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['vrfId'] 		 .'", ' . "\n"; 
        $query .= ' "'. $subnetDetails['masterSubnetId'] .'", ' . "\n"; 
        $query .= ''. isCheckbox($subnetDetails['allowRequests']) .','."\n";
        $query .= ''. isCheckbox($subnetDetails['showName']) .','."\n";  
        $query .= ' "'. $subnetDetails['permissions'] .'", '."\n"; 
        $query .= ''. isCheckbox($subnetDetails['pingSubnet']) .''."\n";  
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
        $query .= '`allowRequests`  = "'. isCheckbox($subnetDetails['allowRequests']) 	.'", '. "\n";
        $query .= '`showName`   	= "'. isCheckbox($subnetDetails['showName']) 		.'", '. "\n";
        $query .= '`pingSubnet`   	= "'. isCheckbox($subnetDetails['pingSubnet']) 		.'" '. "\n";
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
        
        # if vrf changes
        if($subnetDetails['vrfId'] != $subnetDetails['vrfIdOld']) {
	        # add querry to change vrfId!
	        global $removeSlaves;
	        getAllSlaves ($subnetDetails['subnetId']);
	        $removeSlaves = array_unique($removeSlaves);
    	    		
	        foreach($removeSlaves as $slave) {
	    		$query .= 'update `subnets` set `vrfId` = "'. $subnetDetails['vrfId'] .'" where `id` = "'.$slave.'"; '."\n";
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
 * delete subnet - only single subnet, no child/slave hosts and IP addresses are removed!!!! Beware !!!
 */
function deleteSubnet ($subnetId) 
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection   

    # set modify subnet details query
    $query = "delete from `subnets` where `id` = '$subnetId';";

    # execute query
    if (!$database->executeQuery($query)) {
        updateLogTable ('Subnet delete from split failed', "id:$subnetId", 2);	# write error log
        return false;
    }
    else {
        updateLogTable ('Subnet deleted from split ok', "id: $subnetId", 0);		# write success log
        return true;
    }
}


/**
 * Resize subnet - change mask
 */
function modifySubnetMask ($subnetId, $mask) 
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection   

    # set modify subnet details query
    $query = "update `subnets` set `mask` = '$mask' where `id` = '$subnetId';";

	$log = "subnetId: $subnetId\n New mask: $mask";																				# prepare log 

    # execute query
    if (!$database->executeQuery($query)) {
        updateLogTable ('Subnet resize failed', $log, 2);	# write error log
        return false;
    }
    else {
        updateLogTable ('Subnet resized ok', $log, 1);		# write success log
        return true;
    }
}


/**
 * Print subnets structure
 */
function printAdminSubnets( $subnets, $actions = true, $vrf = "0" )
{
		$html = array();
		
		$rootId = 0;									# root is 0

		if(sizeof($subnets) > 0) {
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
				if(empty($vlan) || $vlan == "0") 	{ $vlan = ""; }			# no VLAN

				# description
				if(strlen($option['value']['description']) == 0) 	{ $description = "/"; }													# no description
				else 												{ $description = $option['value']['description']; }						# description		
				
				# requests
				if($option['value']['allowRequests'] == 1) 			{ $requests = "enabled"; }												# requests enabled
				else 												{ $requests = ""; }														# request disabled				

				# hosts check
				if($option['value']['pingSubnet'] == 1) 			{ $pCheck = "enabled"; }												# ping check enabled
				else 												{ $pCheck = ""; }														# ping check disabled

				#vrf
				if($vrf == "1") {
					# get VRF details
					if(($option['value']['vrfId'] != "0") && ($option['value']['vrfId'] != "NULL") ) {
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
				$html[] = "	<td>$pCheck</td>";
				if($actions) {
				$html[] = "	<td class='actions' style='padding:0px;>";
				$html[] = "	<div class='btn-group'>";
				$html[] = "		<button class='btn btn-small editSubnet'     data-action='edit'   data-subnetid='".$option['value']['id']."'  data-sectionid='".$option['value']['sectionId']."'><i class='icon-gray icon-pencil'></i></button>";
				$html[] = "		<button class='btn btn-small showSubnetPerm' data-action='show'   data-subnetid='".$option['value']['id']."'  data-sectionid='".$option['value']['sectionId']."'><i class='icon-gray icon-tasks'></i></button>";
				$html[] = "		<button class='btn btn-small editSubnet'     data-action='delete' data-subnetid='".$option['value']['id']."'  data-sectionid='".$option['value']['sectionId']."'><i class='icon-gray icon-remove'></i></button>";
				$html[] = "	</div>";
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


/**
 *	Update subnet permissions
 */
function updateSubnetPermissions ($subnet)
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection   

    # replace special chars
    $subnet['permissions'] = mysqli_real_escape_string($database, $subnet['permissions']);

    # set querries for subnet and each slave
    foreach($subnet['slaves'] as $slave) {
    	$query .= "update `subnets` set `permissions` = '$subnet[permissions]' where `id` = $slave;";	    
    }
    
	# execute
    try { $database->executeMultipleQuerries($query); }
    catch (Exception $e) { 
    	$error =  $e->getMessage(); 
    	print('<div class="alert alert-error">'._('Error').': '.$error.'</div>');
    	return false;
    }
  
	/* return true if passed */
	return true;	
}











/* @section functions ---------------- */


/**
 * Update section
 */
function UpdateSection ($update) 
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection  
    
     # replace special chars for permissions
    $update['permissions'] = mysqli_real_escape_string($database, $update['permissions']);   
    $update['description'] = mysqli_real_escape_string($database, $update['description']); 
    $update['name'] 	   = mysqli_real_escape_string($database, $update['name']); 
    
    if (!$update['name']) 	{ die('<div class="alert alert-error">'._('Name is mandatory').'!</div>'); }	# section name is mandatory

    $query = setUpdateSectionQuery ($update);										# set update section query

	$log = prepareLogFromArray ($update);											# prepare log

    # delete and edit requires multiquery
    if ( ( $update['action'] == "delete") || ( $update['action'] == "edit") )
    {
		# execute
		try { $result = $database->executeMultipleQuerries($query); }
		catch (Exception $e) { 
    		$error =  $e->getMessage(); 
            updateLogTable ('Section ' . $update['action'] .' failed ('. $update['name']. ') - '.$error, $log, 2);	# write error log
            print ('<div class="alert alert-error">'.("Cannot $update[action] all entries").' - '.$error.'!</div>');
    		return false;
    	}
    	# success
        updateLogTable ('Section '. $update['name'] . ' ' . $update['action'] .' ok', $log, 1);			# write success log
        return true;
    }
    # add is single querry
    else 
    {
		# execute
		try { $result = $database->executeQuery($query); }
		catch (Exception $e) { 
    		$error =  $e->getMessage(); 
            updateLogTable ('Adding section '. $update['name'] .'failed - '.$error, $log, 2);							# write error log
            die('<div class="alert alert-error">'.('Cannot update database').'!<br>'.$error.'</div>');  
		}
		# success
        updateLogTable ('Section '. $update['name'] .' added succesfully', $log, 1);					# write success log
        return true;
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
        $query = 'Insert into sections (`name`,`description`,`permissions`,`strictMode`,`subnetOrdering`) values ("'.$update['name'].'", "'.$update['description'].'", "'.$update['permissions'].'", "'.$update['strictMode'].'", "'.$update['subnetOrdering'].'");';
    }
    # edit section
    else if ($update['action'] == "edit") 
    {
        $section_old = getSectionDetailsById ( $update['id'] );												# Get old section name for update
        # Update section
        $query   = "update `sections` set `name` = '$update[name]', `description` = '$update[description]', `permissions` = '$update[permissions]', `strictMode`='$update[strictMode]', `subnetOrdering`='$update[subnetOrdering]' where `id` = '$update[id]';";	
        
        # delegate permissions if set
        if($update['delegate'] == 1) {
	        $query .= "update `subnets` set `permissions` = '$update[permissions]' where `sectionId` = '$update[id]';";
        }		
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


/**
 * Update section ordering
 */
function UpdateSectionOrder ($order) 
{
    global $db;                                                                     # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);	# open db connection  

	// set querries for each section
	$query = "";
	foreach($order as $key=>$o) {
		$query .= "update `sections` set `order` = $o where `id` = $key; \n";
	}
	//log
	$log = prepareLogFromArray ($order);
	//execute multiple queries
	try { $result = $database->executeMultipleQuerries($query); }
	catch (Exception $e) { 
		$error =  $e->getMessage(); 
        updateLogTable ('Section reordering failed ('. $update['name']. ') - '.$error, $log, 2);	# write error log
        print ('<div class="alert alert-error">'._("Cannot reorder sections").' - '.$error.'!</div>');
		return false;
	}
	# success
    updateLogTable ('Section reordering ok', $log, 1);			# write success log
    return true;
}


/**
 * Parse section permissions
 */
function parseSectionPermissions($permissions)
{
	# save to array
	$permissions = json_decode($permissions, true);
	
	if(sizeof($permissions)>0) {
    	foreach($permissions as $key=>$p) {
    		$tmp = getGroupById($key);
    		$out[$tmp['g_id']] = $p;
    	}
    }
    /* return array of groups */
    return $out;
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
    	$query .= '(`hostname`,`ip_addr`, `type`, `vendor`,`model`,`version`,`description`,`sections`) values '. "\n";
   		$query .= '("'. $switch['hostname'] .'", "'. $switch['ip_addr'] .'", "'.$switch['type'].'", "'. $switch['vendor'] .'", '. "\n";
   		$query .= ' "'. $switch['model'] .'", "'. $switch['version'] .'", "'. $switch['description'] .'", "'. $switch['sections'] .'" );'. "\n";
    }
    else if($switch['action'] == "edit") {
    	$query  = 'update `switches` set '. "\n";    
    	$query .= '`hostname` = "'. $switch['hostname'] .'", `ip_addr` = "'. $switch['ip_addr'] .'", `type` = "'. $switch['type'] .'", `vendor` = "'. $switch['vendor'] .'", '. "\n";    
    	$query .= '`model` = "'. $switch['model'] .'", `version` = "'. $switch['version'] .'", `description` = "'. $switch['description'] .'", '. "\n";    
    	$query .= '`sections` = "'. $switch['sections'] .'" '. "\n"; 
    	$query .= 'where `id` = "'. $switch['switchId'] .'";'. "\n";    
    }
    else if($switch['action'] == "delete") {
    	$query  = 'delete from `switches` where id = "'. $switch['switchId'] .'";'. "\n";
    }

    /* prepare log */ 
    $log = prepareLogFromArray ($switch);
    
    /* execute */
    try { $res = $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
       	updateLogTable ('Switch ' . $switch['action'] .' failed ('. $switch['hostname'] . ')'.$error, $log, 2);
    	return false;
    } 
    
    /* success */
    updateLogTable ('Switch ' . $switch['action'] .' success ('. $switch['hostname'] . ')', $log, 0);
    return true;
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
     
    /* execute */
    try { $switch = $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    }
    
    /* return success */
    return true;
}


/**
 * get switch type
 */
function getSwitchTypes() 
{
	$res[0] = _("Switch");
	$res[1] = _("Router");
	$res[2] = _("Firewall");
	$res[3] = _("Hub");
	$res[4] = _("Wireless");
	$res[5] = _("Database");
	$res[6] = _("Workstation");
	$res[7] = _("Laptop");
	$res[8] = _("Other");

	return $res;
}


/**
 * Transfor switch type
 */
function TransformSwitchType($type) 
{
	switch($type) {
		case "0":	$res = _("Switch");		break;
		case "1":	$res = _("Router");		break;
		case "2":	$res = _("Firewall");	break;
		case "3":	$res = _("Hub");		break;
		case "4":	$res = _("Wireless");	break;
		case "5":	$res = _("Database");	break;
		case "6":	$res = _("Workstation");break;
		case "7":	$res = _("Other");		break;
	}	
	return $res;
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
 
    /* execute */
    try { $settings = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    }
  		  
	/* return settings */
	return($settings[0]);
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

    /* execute */
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    }
    
    # success
    return true;
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

    /* execute */
    try { $res = $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
   		updateLogTable ('VRF ' . $vrf['action'] .' failed ('. $vrf['name'] . ')'.$error, $log, 2);
    	return false;
    }

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
    updateLogTable ('VRF ' . $vrf['action'] .' success ('. $vrf['name'] . ')', $log, 0);
    return true;
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
    
    /* execute */
    try { $res = $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
   		updateLogTable ('VLAN ' . $vlan['action'] .' failed ('. $vlan['name'] . ')'.$error, $log, 2);
    	return false;
    }
    
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
    
    /* return success */
    updateLogTable ('VLAN ' . $vlan['action'] .' success ('. $vlan['name'] . ')', $log, 0);
    return true;
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
	$query   .= '`enableDNSresolving` = "'. isCheckbox($settings['enableDNSresolving']) .'", ' . "\n";  
	$query   .= '`htmlMail` 		  = "'. isCheckbox($settings['htmlMail']) .'", ' . "\n";  
    $query   .= '`printLimit` 	      = "'. $settings['printLimit'] .'", ' . "\n"; 
    $query   .= '`visualLimit` 	      = "'. $settings['visualLimit'] .'", ' . "\n"; 
    $query   .= '`vlanDuplicate` 	  = "'. isCheckbox($settings['vlanDuplicate']) .'", ' . "\n"; 
    $query   .= '`subnetOrdering` 	  = "'. $settings['subnetOrdering'] .'", ' . "\n"; 
    $query   .= '`pingStatus` 	  	  = "'. $settings['pingStatus'] .'", ' . "\n"; 
    $query   .= '`defaultLang` 	  	  = "'. $settings['defaultLang'] .'" ' . "\n"; 
	$query   .= 'where id = 1;' . "\n"; 

	/* set log file */
	foreach($settings as $key=>$setting) {
		$log .= " ". $key . ": " . $setting . "<br>";
	}
 
 	/* execute */
    try {
    	$database->executeQuery( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	print '<div class="alert alert-error">'._('Update settings error').':<hr>'. $error .'</div>';
    	updateLogTable ('Failed to update settings', $log, 2);
    	return false;
	}
	
	if(!isset($e)) {
    	updateLogTable ('Settings updated', $log, 1);
        return true;
	}
}


/**
 *	Verify checkboxes for saving config
 */
function isCheckbox($checkbox)
{
	if($checkbox == "") { $chkbox = "0"; }
	else 				{ $chkbox = $checkbox; }
	
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
    	die('<div class="alert alert-error alert-absolute">'._('Error').': '. $error .'</div>');
	}
	
	if(!isset($e)) {
		print '<div class="alert alert-success alert-absolute">'._('Replaced').' '. $count .' '._('items successfully').'!</div>';
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
    	return _('Wrong IP address').' - '. $line[0];
    } 
    
    /* check for duplicates */
    if (checkDuplicate ($line[0], $subnetId)) {
    	return _('IP address already exists').' - '. $line[0];
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
	
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnetId'], $res['ip_addr'], $res['description'], $res['dns_name'], $res['switch']);
	unset($res['port'], $res['mac'], $res['owner'], $res['state'], $res['note'], $res['lastSeen'], $res['excludePing']);
	
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnetId'], $res['ip_addr'], $res['description'], $res['dns_name'], $res['switch']);
	unset($res['port'], $res['mac'], $res['owner'], $res['state'], $res['note'], $res['lastSeen'], $res['excludePing']);
	
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
    if($field['action'] == "delete") 		{ $query  = 'ALTER TABLE `ipaddresses` DROP `'. $field['name'] .'`;'; }
    else if ($field['action'] == "edit") 	{ $query  = 'ALTER TABLE `ipaddresses` CHANGE COLUMN `'. $field['oldname'] .'` `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;'; }
    else 									{ $query  = 'ALTER TABLE `ipaddresses` ADD COLUMN `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;'; }
    
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnet'], $res['mask'], $res['sectionId'], $res['description'], $res['masterSubnetId']);
	unset($res['vrfId'], $res['allowRequests'], $res['adminLock'], $res['vlanId'], $res['showName'],$res['permissions']);
	unset($res['pingSubnet']);
	
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['subnet'], $res['mask'], $res['sectionId'], $res['description'], $res['masterSubnetId']);
	unset($res['vrfId'], $res['allowRequests'], $res['adminLock'], $res['vlanId'], $res['showName'],$res['permissions']);
	
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
    if($field['action'] == "delete") 		{ $query  = 'ALTER TABLE `subnets` DROP `'. $field['name'] .'`;'; }
    else if ($field['action'] == "edit") 	{ $query  = 'ALTER TABLE `subnets` CHANGE COLUMN `'. $field['oldname'] .'` `'. $field['name'] .'` VARCHAR(1024) CHARACTER SET utf8 DEFAULT NULL;'; }
    else 									{ $query  = 'ALTER TABLE `subnets` ADD COLUMN `'. $field['name'] .'` VARCHAR(1024) CHARACTER SET utf8 DEFAULT NULL;'; }
    
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
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

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
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
    if($field['action'] == "delete") 		{ $query  = 'ALTER TABLE `vlans` DROP `'. $field['name'] .'`;'; }
    else if ($field['action'] == "edit") 	{ $query  = 'ALTER TABLE `vlans` CHANGE COLUMN `'. $field['oldname'] .'` `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;'; }
    else 									{ $query  = 'ALTER TABLE `vlans` ADD COLUMN `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;'; }
    
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







/* @custom USer fields */


/**
 * Get all custom User fields
 */
function getCustomUserFields()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `users`;';

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['username'], $res['password'], $res['groups'], $res['role'], $res['real_name'], $res['email'], $res['domainUser'], $res['lang']);
	
	return $res;
}


/**
 * Get all custom User fields in number array
 */
function getCustomUserFieldsNumArr()
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `users`;';

    /* execute */
    try { $fields = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>"._('Error').": $error</div>");
        return false;
    } 
  
	/* return Field values only */
	foreach($fields as $field) {
		$res[$field['Field']]['name'] = $field['Field'];
		$res[$field['Field']]['type'] = $field['Type'];
	}
	
	/* unset standard fields */
	unset($res['id'], $res['username'], $res['password'], $res['groups'], $res['role'], $res['real_name'], $res['email'], $res['domainUser']);
	
	/* reindex */
	foreach($res as $line) {
		$out[] = $line['name'];
	}
	
	return $out;
}


/**
 * Update custom user field
 */
function updateCustomUserField($field)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    if($field['action'] == "delete") 		{ $query  = 'ALTER TABLE `users` DROP `'. $field['name'] .'`;'; }
    else if ($field['action'] == "edit") 	{ $query  = 'ALTER TABLE `users` CHANGE COLUMN `'. $field['oldname'] .'` `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;'; }
    else 									{ $query  = 'ALTER TABLE `users` ADD COLUMN `'. $field['name'] .'` VARCHAR(256) CHARACTER SET utf8 DEFAULT NULL;'; }
    
    /* prepare log */ 
    $log = prepareLogFromArray ($field);
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('Custom User Field ' . $field['action'] .' failed ('. $field['name'] . ')', $log, 2);
        return false;
    }
    else {
        updateLogTable ('Custom User Field ' . $field['action'] .' success ('. $field['name'] . ')', $log, 0);
        return true;
    }
}


/**
 * reorder custom VLAN field
 */
function reorderCustomUserField($next, $current)
{
    global $db;                                                                      # get variables from config file
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* update request */
    $query  = 'ALTER TABLE `users` MODIFY COLUMN `'. $current .'` VARCHAR(256) AFTER `'. $next .'`;';
    
    if (!$database->executeQuery($query)) {
        updateLogTable ('Custom User Field reordering failed ('. $next .' was not put before '. $current .')', $log, 2);
        return false;
    }
    else {
	    updateLogTable ('Custom User Field reordering success ('. $next .' put before '. $current .')', $log, 0);
        return true;
    }
}





?>