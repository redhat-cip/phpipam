<?php

/**
 * Common phpIPAM functions
 *
 * Common functions that are used
 * in phpipam. 
 *
 */



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



/* @user based functions ---------- */

/**
 * Functions to check if user is authenticated properly for ajax-loaded pages
 *
 */
function isUserAuthenticated() 
{
    /* open session and get username / pass */
	if (!isset($_SESSION)) {  session_start(); }
    /* redirect if not authenticated */
    if (empty($_SESSION['ipamusername'])) {
    	if($_SERVER['SERVER_PORT'] == "443") { $url = "https://".$_SERVER['SERVER_NAME'].BASE; }
    	else								 { $url = "http://".$_SERVER['SERVER_NAME'].BASE; }
    	# die
    	die('<div class="error">Please <a href="'.$url.'login">login/</a> first!</div>');
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
	if (!isset($_SESSION)) { session_start(); }
    /* redirect if not authenticated */
    if (empty($_SESSION['ipamusername'])) {
    
    	if($_SERVER['SERVER_PORT'] == "443") { $url = "https://".$_SERVER['SERVER_NAME'].BASE; }
    	else								 { $url = "http://".$_SERVER['SERVER_NAME'].BASE; }
    	# redirect
    	header("Location:".$url."login/");    
    }
    /* close session */
    session_write_close();    
}


/**
 * Check if user is admin
 */
function checkAdmin ($die = true, $startSession = true) 
{
    global $db;                                                                      # get variables from config file
    
    /* first get active username */
    if(!isset($_SESSION)) { session_start(); }
    $ipamusername = $_SESSION['ipamusername'];
    session_write_close();
    
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);

    /* Check connection */
    if ($database->connect_error) {
    	if($_SERVER['SERVER_PORT'] == "443") { $url = "https://".$_SERVER['SERVER_NAME']; }
    	else								 { $url = "http://".$_SERVER['SERVER_NAME']; }
    	# redirect
    	header("Location:".$url."login/");  
	}

	/* set query if database exists! */
    $query = 'select role from users where username = "'. $ipamusername .'";';
    
    /* fetch role */
    try { $role = $database->getRow( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        die ("<div class='alert alert-error'>Error: $error</div>");
    } 

    /* close database connection */
    $database->close();
    
    /* return true if admin, else false */
    if ($role[0] == "Administrator") {
        return true;
    }
    else {
    	//die
    	if($die == true) { die('<div class="alert alert-error">Not admin!</div>'); }
    	//return false if called
    	else 			{ return false; }
    }
         
}


/**
 * Get active users username - from session!
 */
function getActiveUserDetails ()
{
/*     session_start(); */
	if (!isset($_SESSION)) { session_start(); }

	if(isset($_SESSION['ipamusername'])) {
    	return getUserDetailsByName ($_SESSION['ipamusername']);
    }
    session_write_close();
}


/**
 * Get all users
 */
function getAllUsers ()
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from users order by `role` asc, `real_name` asc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
	   
    /* return results */
    return($details);
}


/**
 * Get number of  users
 */
function getNumberOfUsers ()
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select count(*) as count from users order by id desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    }  
	   
    /* return results */
    return($details[0]['count']);
}


/**
 * Get all admin users
 */
function getAllAdminUsers ()
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from users where `role` = "Administrator" order by id desc;';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    }  
	   
    /* return results */
    return($details);
}


/**
 * Get user details by ID
 */
function getUserDetailsById ($id)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from users where id = "'. $id .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
    
    /* return results */
    return($details[0]);
}


/**
 * Get user details by name
 */
function getUserDetailsByName ($username)
{
    global $db;                                                                      # get variables from config file
    /* set query, open db connection and fetch results */
    $query    = 'select * from users where username LIKE BINARY "'. $username .'";';
    $database = new database($db['host'], $db['user'], $db['pass'], $db['name']);  

    /* execute */
    try { $details = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
    
    /* return results */
    return($details[0]);
}









/* @permission functions ---------- */

/**
 *	Check section permissions
 */
function checkSectionPermission ($sectionId)
{
    # open session and get username / pass
	if (!isset($_SESSION)) {  session_start(); }
    # redirect if not authenticated */
    if (empty($_SESSION['ipamusername'])) 	{ return "0"; }
    else									{ $username = $_SESSION['ipamusername']; }
    
	# get all user groups
	$user = getUserDetailsByName ($username);
	$groups = json_decode($user['groups']);
	
	# if user is admin then return 2, otherwise check
	if($user['role'] == "Administrator")	{ return "2"; }
	
	# get section permissions
	$section  = getSectionDetailsById($sectionId);
	$sectionP = json_decode($section['permissions']);
	
	# default permission
	$out = 0;
	
	# for each group check permissions, save highest to $out
	foreach($sectionP as $sk=>$sp) {
		# check each group if user is in it and if so check for permissions for that group
		foreach($groups as $uk=>$up) {
			if($uk == $sk) {
				if($sp > $out) { $out = $sp; }
			}
		}
	}
	# return permission level
	return $out;
}


/**
 *	Check subnet permissions
 */
function checkSubnetPermission ($subnetId)
{
    # open session and get username / pass
	if (!isset($_SESSION)) {  session_start(); }
    # redirect if not authenticated */
    if (empty($_SESSION['ipamusername'])) 	{ return "0"; }
    else									{ $username = $_SESSION['ipamusername']; }
    
	# get all user groups
	$user = getUserDetailsByName ($username);
	$groups = json_decode($user['groups']);
	
	# if user is admin then return 2, otherwise check
	if($user['role'] == "Administrator")	{ return "2"; }

	# get subnet permissions
	$subnet  = getSubnetDetailsById($subnetId);
	$subnetP = json_decode($subnet['permissions']);
	
	# get section permissions
	$section  = getSectionDetailsById($subnet['sectionId']);
	$sectionP = json_decode($section['permissions']);
	
	# default permission
	$out = 0;
	
	# for each group check permissions, save highest to $out
	if(sizeof($sectionP) > 0) {
		foreach($sectionP as $sk=>$sp) {
			# check each group if user is in it and if so check for permissions for that group
			foreach($groups as $uk=>$up) {
				if($uk == $sk) {
					if($sp > $out) { $out = $sp; }
				}
			}
		}
	}
	else {
		$out = "0";
	}
	
	# if section permission == 0 then return 0
	if($out == "0") {
		return "0";
	}
	else {
		$out = "0";
		# ok, user has section access, check also for any higher access from subnet
		if(sizeof($subnetP) > 0) {
			foreach($subnetP as $sk=>$sp) {
				# check each group if user is in it and if so check for permissions for that group
				foreach($groups as $uk=>$up) {
					if($uk == $sk) {
						if($sp > $out) { $out = $sp; }
					}
				}
			}
		}
	}
	
	# return result
	return $out;
}









/* @autocomplete functions ---------- */


/**
 *	Get all users for autocomplete
 */
function getUniqueUsers ()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
    $query    	= 'select distinct owner from ipaddresses;';  
 
    /* execute */
    try { $users = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
    
    /* return result */
    return $users;
}


/**
 *	Get unique hostnames for host search
 */
function getUniqueHosts ()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']);     

	/* execute query */
    $query    	= 'select distinct dns_name from `ipaddresses` order by `dns_name` desc;';  

    /* execute */
    try { $hosts = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    }  
    
    /* return result */
    return $hosts;
}








/* @general functions ---------- */


/**
 * Get all site settings
 */
function getAllSettings()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* first check if table settings exists */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "settings";';

    /* execute */
    try { $count = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
  
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
 * Get SVN version
 */
function getSVNversion() {
	$revision = shell_exec('svnversion');
	if($svnversion == "exported") {$svnversion = "";}
	return $revision;
}


/**
 * validate email
 */
function checkEmail($email) {
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) 	{ return false; }
    else 														{ return true; }
}


/**
 * validate hostname
 */
function validateHostname($hostname)
{
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $hostname) 	//valid chars check
            && preg_match("/^.{1,253}$/", $hostname) 										//overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $hostname)   ); 				//length of each label
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
 * Parse section/subnet permissions
 */
function parsePermissions($perm)
{
	switch($perm) {
		case "0": $r = "No access";		break;
		case "1": $r = "Read";			break;
		case "2": $r = "Read / Write";	break;
		default:  $r = "error";
	}
	return $r;
}









/* @menu builder */

/**
 * Build the HTML menu
 *
 * based on http://pastebin.com/GAFvSew4
 */
function get_menu_html( $subnets, $rootId = 0 )
{
		$html = array();
		
		foreach ( $subnets as $item )
			$children[$item['masterSubnetId']][] = $item;
		
		# loop will be false if the root has no children (i.e., an empty menu!)
		$loop = !empty( $children[$rootId] );
		
		# initializing $parent as the root
		$parent = $rootId;
		$parent_stack = array();
		
		# display selected subnet as opened
		if(isset($_REQUEST['subnetId'])) 	{ $allParents = getAllParents ($_REQUEST['subnetId']); }
		else 								{ $allParents = array(); }
		
		# Menu start
		$html[] = '<ul id="subnets">';
		
		while ( $loop && ( ( $option = each( $children[$parent] ) ) || ( $parent > $rootId ) ) )
		{
			# count levels
			$count = count( $parent_stack ) + 1;
			
			# set opened or closed tag for displaying proper folders
			if(in_array($option['value']['id'], $allParents))		{ $open = "open"; }
			else													{ $open = "close"; }
			
			# override if cookie is set
			if(isset($_COOKIE['expandfolders'])) {
				if($_COOKIE['expandfolders'] == "1")					{ $open='open'; }
				else									{ 
					if(in_array($option['value']['id'], $allParents))	{ $open = "open"; }
					else												{ $open = "close"; }
				}
			}
			
			# for active class
			if(isset($_REQUEST['subnetId']) && ($option['value']['id'] == $_REQUEST['subnetId']))	{ $active = "active";	$leafClass=""; }
			else 																					{ $active = ""; 		$leafClass="icon-gray" ;}
			
			# check for permissions if id is provided
			if($option['value']['id'] != "") {
				$sp = checkSubnetPermission ($option['value']['id']);
			}
			
			if ( $option === false )
			{
				$parent = array_pop( $parent_stack );
				
				# HTML for menu item containing childrens (close)
				$html[] = '</ul>';
				$html[] = '</li>';
			}
			# Has children
			elseif ( !empty( $children[$option['value']['id']] ) )
			{
				# if user has access permission
				if($sp != 0) {	
					# print name
					if($option['value']['showName'] == 1) {
						$html[] = '<li class="folder folder-'.$open.' '.$active.'"><i class="icon-gray icon-folder-'.$open.'" rel="tooltip" data-placement="right" title="Subnet contains more subnets.<br>Click on folder to open/close."></i>';
						$html[] = '<a href="subnets/'.$option['value']['sectionId'].'/'.$option['value']['id'].'/" rel="tooltip" data-placement="right" title="'.Transform2long($option['value']['subnet']).'/'.$option['value']['mask'].'">'.$option['value']['description'].'</a>'; 				
					}
					# print subnet
					else {
						$html[] = '<li class="folder folder-'.$open.' '.$active.'""><i class="icon-gray icon-folder-'.$open.'" rel="tooltip" data-placement="right" title="Subnet contains more subnets.<br>Click on folder to open/close."></i>';
						$html[] = '<a href="subnets/'.$option['value']['sectionId'].'/'.$option['value']['id'].'/" rel="tooltip" data-placement="right" title="'.$option['value']['description'].'">'.Transform2long($option['value']['subnet']).'/'.$option['value']['mask'].'</a>'; 										
					}

					# print submenu
					if($open == "open") { $html[] = '<ul class="submenu submenu-'.$open.'">'; }							# show if opened
					else 				{ $html[] = '<ul class="submenu submenu-'.$open.'" style="display:none">'; }	# hide - prevent flickering			
								
					array_push( $parent_stack, $option['value']['masterSubnetId'] );
					$parent = $option['value']['id'];
				}
			}
			# Leaf items (last)
			else
				if($sp != 0) {
					# print name
					if($option['value']['showName'] == 1) {				
						$html[] = '<li class="leaf '.$active.'""><i class="'.$leafClass.' icon-chevron-right"></i>';
						$html[] = '<a href="subnets/'.$option['value']['sectionId'].'/'.$option['value']['id'].'/" rel="tooltip" data-placement="right" title="'.Transform2long($option['value']['subnet']).'/'.$option['value']['mask'].'">'.$option['value']['description'].'</a></li>';
					}
					# print subnet
					else {
						$html[] = '<li class="leaf '.$active.'""><i class="'.$leafClass.' icon-chevron-right"></i>';
						$html[] = '<a href="subnets/'.$option['value']['sectionId'].'/'.$option['value']['id'].'/" rel="tooltip" data-placement="right" title="'.$option['value']['description'].'">'.Transform2long($option['value']['subnet']).'/'.$option['value']['mask'].'</a></li>';					
					}
				}
		}
		
		# Close menu
		$html[] = '</ul>';
		
		return implode( "\n", $html );
}


/**
 *	get whole tree path for subnetId - from slave to parents
 */
function getAllParents ($subnetId) 
{
	$parents = array();
	$root = false;
	
	while($root == false) {
		$subd = getSubnetDetailsById($subnetId);		# get subnet details
		
		if($subd['masterSubnetId'] != 0) {
			array_unshift($parents, $subd['masterSubnetId']);
			$subnetId  = $subd['masterSubnetId'];
		}
		else {
			array_unshift($parents, $subd['masterSubnetId']);
			$root = true;
		}
	}

	return $parents;
}


/**
 *	get whole tree path for subnetId - from parent all slaves
 *
 * 	if multi than create multidimensional array
 */
$removeSlaves = array();

function getAllSlaves ($subnetId, $multi = false) 
{
	global $removeSlaves;
	$end = false;			# breaks while
	
	$removeSlaves[] = $subnetId;		# first

	# db
	global $db;                                                                      # get variables from config file
	$database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
	
	while($end == false) {
		
		/* get all immediate slaves */
		$query = "select * from `subnets` where `masterSubnetId` = '$subnetId' order by `id` asc; ";    
		/* execute query */
		try { $slaves2 = $database->getArray( $query ); }
		catch (Exception $e) { 
        	$error =  $e->getMessage(); 
        	print ("<div class='alert alert-error'>Error: $error</div>");
        	return false;
        }
		
		# we have more slaves
		if(sizeof($slaves2) != 0) {
			# recursive
			foreach($slaves2 as $slave) {
				$removeSlaves[] = $slave['id'];
				getAllSlaves ($slave['id']);
				$end = true;
			}
		}
		# no more slaves
		else {
			$end = true;
		}
	}
}


/**
 *	print breadcrumbs
 */
function printBreadcrumbs ($req)
{
	# subnets
	if($req['page'] == "subnets")	{
		if(isset($req['subnetId'])) {
			# get all parents
			$parents = getAllParents ($req['subnetId']);
			print "<ul class='breadcrumb'>";
			# remove root - 0
			array_shift($parents);
			
			# section details
			if(is_numeric($req['section']))	{ $section = getSectionDetailsById($req['section']); }					# if id is provided
			else							{ $section = getSectionDetailsByName($req['section']); }				# if name is provided
			
			print "	<li><a href='subnets/$section[id]/'>$section[name]</a> <span class='divider'>/</span></li>";	# section name
			
			foreach($parents as $parent) {
			$subnet = getSubnetDetailsById($parent);
			print "	<li><a href='subnets/$section[id]/$parent/'>$subnet[description] (".Transform2long($subnet['subnet']).'/'.$subnet['mask'].")</a> <span class='divider'>/</span></li>";								# subnets in between
			}
			$subnet = getSubnetDetailsById($req['subnetId']);
			print "	<li class='active'>$subnet[description] (".Transform2long($subnet['subnet']).'/'.$subnet['mask'].")</li>";																# active subnet
			print "</ul>";
		}
	}
	# admin
	else if($req['page'] == "admin")
	{
		
	}
	# tools
	else if ($req['page'] == "tools") {
		if(isset($req['tpage'])) {
			print "<ul class='breadcrumb'>";
			print "	<li><a href='tools/'>Tools</a> <span class='divider'>/</span></li>";
			print "	<li class='active'>$req[tpage]></li>";
			print "</ul>";
		}
	}
}




?>