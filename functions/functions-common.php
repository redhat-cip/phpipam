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
 * Check if user is admin
 */
function checkAdmin ($die = true, $startSession = true) 
{
    /* get variables from config file */
    global $db;
    
    /* first get active username */
    if(!isset($_SESSION)) {
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










/* @autocomplete functions ---------- */


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








/* @general functions ---------- */


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



?>