<?php

#
# include database functions
#
require( dirname(__FILE__) . '/../config.php' );
require( dirname(__FILE__) . '/../functions/dbfunctions.php' );


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


/**
 * Update log table
 */
function updateLogTable ($command, $details = NULL, $severity = 0)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass']); 
    
    
    /* select database */
    if(!$database->selectDatabase($db['name'])) {
    		return false;
    }
    else {
	    /* Check connection */
	    if (!$database->connect_error) {

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
	    		return false;
			}
			return true;
		}
		else {
		return false;
		}
	}
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
 * Get active users username - from session!
 */
function getActiveUserDetails ()
{
/*     session_start(); */
	if(isset($_SESSION['ipamusername'])) {
    	return getUserDetailsByName ($_SESSION['ipamusername']);
    }
    else {
    	return false;
    }
    session_write_close();
}


/**
 * Get all site settings
 */
function getAllSettings()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
	
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
 * Get Domain settings for authentication
 */
function getADSettings()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
	
    /* first check if table settings exists */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "settingsDomain";';
    $count	  = $database->getArray($query); 
  
	/* return true if it exists */
	if($count[0]['count'] == 1) {

		/* select database */
		$database->selectDatabase($db['name']);
	
	    /* first update request */
	    $query    = 'select * from `settingsDomain` limit 1;';
	    $settings = $database->getArray($query); 
	    
	    /* reformat DC */
  		$dc = str_replace(" ", "", $settings[0]['domain_controllers']);
  		$dcTemp = explode(";", $dc);
  		$settings[0]['domain_controllers'] = $dcTemp;
  		  
		/* return settings */
		return($settings[0]);
	}
	else {
		return false;
	}
}



/**
 * Login authentication
 *
 * First we try to authenticate via local database
 * if it fails we querry the AD, if set in config file
 */
function checkLogin ($username, $md5password, $rawpassword) 
{
    /* get variables from config file */
    global $db;
    
    /* check if user exists in local database */
    $database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $query 		= 'select * from users where username = binary "'. $username .'" and password = BINARY "'. $md5password .'" ;';
    
    /* fetch results */
    $result  	= $database->getArray($query); 

    /* close database connection */
    $database->close();
    
   	/* set log file */
    if (sizeof($result) !=0 ) { 
    	updateLogTable ('User '. $username .' logged in.', "", 0);
    }
    else { 
	    updateLogTable ('User '. $username .' failed to log in.', "", 2);
    }
    
    /* if local failed and AD is set querry AD! */
    if ( (sizeof($result) !=0 ) && ($useDomainAuth == 1) ) {
    
    	$adLogin = checkADLogin ($username, $rawpassword);
    
    	if($adLogin) {
    		$result  = "AD login OK!";
    		updateLogTable ('User '. $username .' logged in via AD.', "", 1);
    	}
    	else {
    		$result  = "AD login failed!";
    		updateLogTable ('User '. $username .' failed to log in via AD.', "", 2);
    	}
    }

    /* return result */
    return($result);
}



/**
 * Check user against AD
 */
function checkADLogin ($username, $password)
{
	/* first checked if it is defined in database - username and ad option */
    /* get variables from config file */
    global $db;
/*     global $ad; */
    
    /* check if user exists in local database */
    $database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $query 		= 'select count(*) as count from users where `username` = binary "'. $username .'" and `domainUser` = "1";';
    
    /* fetch results */
    $result  	= $database->getArray($query); 

    /* close database connection */
    $database->close();


    
    /* if yes try with AD */
    if($result[0]['count'] == 1) {

		//include login script
		include (dirname(__FILE__) . "/adLDAP/src/adLDAP.php");
	
		//open connection
		try {
			//get settings for connection
			$ad = getADSettings();
			
	    	$adldap = new adLDAP(array( 'base_dn'=>$ad['base_dn'], 'account_suffix'=>$ad['account_suffix'], 
	    								'domain_controllers'=>$ad['domain_controllers'], 'use_ssl'=>$ad['use_ssl'],
	    								'use_tls'=> $ad['use_tls'], 'ad_port'=> $ad['ad_port']
	    								));
		}
		catch (adLDAPException $e) {
			die('<div class="error">'. $e .'</div>');
		}

		//user authentication
		$authUser = $adldap->authenticate($username, $password);
		
		if($authUser == true) { 
			return 'ok'; 
			updateLogTable ('User '. $username .' authenticated via AD.', "", 2);
		}
		else { 
			return 'Failed to authenticate user via AD!'; 
			updateLogTable ('User '. $username .' failed to authenticate via AD.', "", 2);
		}
    }
    //user not defined as AD user or user not existing
    else {
    	return false;
    }
}


/**
 * Check if user is admin
 */
function checkAdmin ($die = true) 
{
    /* get variables from config file */
    global $db;
    
    /* first get active username */
    session_start();
    $ipamusername = $_SESSION['ipamusername'];
    session_write_close();
    
    /* set check query and get result */
    $database = new database ($db['host'], $db['user'], $db['pass'], $db['name']);
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
    
    	//update log
    	updateLogTable ('User '. $ipamusername .' tried to access admin page.', "", 2);
    }      
}


/*********************************
	Upgrade check functions
*********************************/


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
 * install databases
 */
function installDatabase($root)
{
    /* get variables from config file */
    global $db;
    $databaseRoot    = new database($db['host'], $root['user'], $root['pass']); 
    
    /* Check connection */
    if ($databaseRoot->connect_error) {
    	die('<div class="error">Connect Error (' . $databaseRoot->connect_errno . '): '. $databaseRoot->connect_error). "</div>";
	}
    
 	/* first create database */
    $query = "create database ". $db['name'] .";";

    /* execute */
    try {
    	$databaseRoot->executeQuery( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="error">'. $error .'</div>');
	} 
    
    /* select database */
	$databaseRoot->selectDatabase($db['name']);

	/* set permissions! */
	$query = 'grant ALL on '. $db['name'] .'.* to '. $db['user'] .'@localhost identified by "'. $db['pass'] .'";';

    /* execute */
    try {
    	$databaseRoot->executeMultipleQuerries( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="error">Cannot set permissions for user '. $db['user'] .': '. $error. '</div>');
	}
    
    /* try importing SCHEMA file */
    $query       = file_get_contents("../../db/SCHEMA.sql");
    
    /* execute */
    try {
    	$databaseRoot->executeMultipleQuerries( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="error">Cannot install sql SCHEMA file: '. $error. '</div>');
	}
	    
    /* return true, if some errors occured script already died! */
    sleep(1);
   	updateLogTable ('Database installed successfully!', "version 0.4 installed", 1);
   	return true;
}

?>