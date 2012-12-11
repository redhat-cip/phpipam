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
  	ini_set('display_errors', 1);
    error_reporting(E_ERROR | E_WARNING);
}
else{
    ini_set('display_errors', 1); 
    error_reporting(E_ALL ^ E_NOTICE);
}


/**
 * Update log table
 */
function updateLogTable ($command, $details = NULL, $severity = 0)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass']); 
    
    
    /* select database */
    try {
    	$database->selectDatabase($db['name']);
    }
    catch (Exception $e) {
    	return false;
    	die();
	}
	
    /* Check connection */
	if (!$database->connect_error) {

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
    		return false;
		}
		return true;
	}
	else {
		return false;
	}
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
    global $db;                                                                      # get variables from config file
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
    global $db;                                                                      # get variables from config file
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
    global $db;                                                                      # get variables from config file
    
    /* check if user exists in local database */
    $database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $query 		= 'select * from `users` where `username` = binary "'. $username .'" and `password` = BINARY "'. $md5password .'" and `domainUser` = "0" limit 1;';
    
    /* fetch results */
    $result  	= $database->getArray($query); 

    /* close database connection */
    $database->close();
    
   	/* locally registered */
    if (sizeof($result) !=0 ) 	{ 

    	/* start session and set variables */
    	session_start();
    	$_SESSION['ipamusername'] = $username;
    	session_write_close();
    	
    	# print success
    	print('<div class="alert alert-success">Login successful!</div>');	
    	# write log file
    	updateLogTable ('User '. $username .' logged in.', "", 0); 
    }
    /* locally failed, try domain */
    else {
    	/* fetch settings */
    	$settings = getAllSettings();  
    	
    	/* if local failed and AD/OpenLDAP is selected try to authenticate */
    	if ( $settings['domainAuth'] != "0") {
    		
    		/* verify that user is in database! */
    		$database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    		$query 		= 'select count(*) as count from `users` where `username` = binary "'. $username .'" and `domainUser` = "1" limit 1;';
    		
    		/* fetch results */
    		$result  	= $database->getArray($query); 
    		/* close database connection */
    		$database->close();
    		
    		if($result[0]['count'] == "1") {

				/* check if user exist in database and has domain user flag */		
				$authAD = checkADLogin ($username, $rawpassword);
		
				if($authAD == "ok") {
	    			/* start session and set variables */
	    			session_start();
	    			$_SESSION['ipamusername'] = $username;
	    			session_write_close();
	    		
	    			# print success
	    			if($settings['domainAuth'] == "1") {
		    			print('<div class="alert alert-success">AD login successful!</div>');	
		    			updateLogTable ('User '. $username .' logged in.', "", 0); 	
		    		}
		    		else {
		    			print('<div class="alert alert-success">LDAP login successful!</div>');	
		    			updateLogTable ('User '. $username .' logged in.', "", 0); 			    	
		    		}
		    	}
		    	# failed to connect
		    	else if ($authAD == 'Failed to connect to AD!') {
					# print error
					if($settings['domainAuth'] == "1") {
					    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Failed to connect to AD server!</div>');	
					    updateLogTable ('Failed to connect to AD!', "", 2); 	
					}
					else {
				    	print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Failed to connect to LDAP server!</div>');	
				    	updateLogTable ('Failed to connect to LDAP!', "", 2); 						
				    }
				}
				# failed to authenticate
				else if ($authAD == 'Failed to authenticate user via AD!') {
					# print error
					if($settings['domainAuth'] == "1") {
					    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Failed to authenticate user against AD!</div>');	
					    updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 1); 	
					}
					else {
				    	print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Failed to authenticate user against LDAP!</div>');	
				    	updateLogTable ('User '. $username .' failed to authenticate against LDAP.', "", 1); 					
				    }
				}
				# wrong user/pass
				else {
					# print error
					if($settings['domainAuth'] == "1") {
					    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Wrong username or password!</div>');
					    updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 1); 
					}
					else {
				    	print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Wrong username or password!</div>');
				    	updateLogTable ('User '. $username .' failed to authenticate against LDAP.', "", 1); 					
				    }
				}
			}
			# user not in db
			else {
				# print error
				if($settings['domainAuth'] == "1") {
				    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Wrong username or password!</div>');
				    updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 1); 
				}
				else {
				    print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Wrong username or password!</div>');
				    updateLogTable ('User '. $username .' failed to authenticate against LDAP.', "", 1); 					
				}				
			}
    	}
    	/* only local set, print error! */
    	else {
    		# print error
			print('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>Failed to log in!</div>');	
			# write log file
	    	updateLogTable ('User '. $username .' failed to log in.', "", 2);
    	}   
    }
}



/**
 * Check user against AD
 */
function checkADLogin ($username, $password)
{
	/* first checked if it is defined in database - username and ad option */
    global $db;                                                                      # get variables from config file
/*     global $ad; */
    
    /* check if user exists in local database */
    $database 	= new database($db['host'], $db['user'], $db['pass'], $db['name']);
    $query 		= 'select count(*) as count from users where `username` = binary "'. $username .'" and `domainUser` = "1";';
    
    /* fetch results */
    $result  	= $database->getArray($query); 

    /* close database connection */
    $database->close();

    /* get All settings */
    $settings = getAllSettings();
    
    /* if yes try with AD */
    if($result[0]['count'] == "1") {
		//include login script
		include (dirname(__FILE__) . "/adLDAP/src/adLDAP.php");
	
		//open connection
		try {
			//get settings for connection
			$ad = getADSettings();
			
			//AD
	    	$adldap = new adLDAP(array( 'base_dn'=>$ad['base_dn'], 'account_suffix'=>$ad['account_suffix'], 
	    								'domain_controllers'=>$ad['domain_controllers'], 'use_ssl'=>$ad['use_ssl'],
	    								'use_tls'=> $ad['use_tls'], 'ad_port'=> $ad['ad_port']
	    								));
	    	
	    	// set OpenLDAP flag
	    	if($settings['domainAuth'] == "2") { $adldap->setUseOpenLDAP(true); }
	    	
		}
		catch (adLDAPException $e) {
			die('<div class="alert alert-error">'. $e .'</div>');
		}

		//user authentication
		$authUser = $adldap->authenticate($username, $password);
		
		if($authUser == true) { 
			updateLogTable ('User '. $username .' authenticated against AD.', "", 0);
			return 'ok'; 
		}
		else { 
			updateLogTable ('User '. $username .' failed to authenticate against AD.', "", 2);
			$err = $adldap->getLastError();
			return 'Failed to authenticate user via AD!'; 
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
    global $db;                                                                      # get variables from config file
    
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
    	if($die == true) { die('<div class="alert alert-error">Not admin!</div>'); }
    	//return false if called
    	else 			 { return false; }
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
    global $db;                                                                      # get variables from config file
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
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);
	}
    
    /* first update request */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "'. $table .'";';
    $count	  = $database->getArray($query); 
  
	/* return true if it exists */
	if($count[0]['count'] == 1)	{ return true; }
	else 						{ return false; }
}


/**
 * describe specific table
 */
function fieldExists($table, $fieldName)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'describe `'. $table .'` `'. $fieldName .'`;';
    $count	  = $database->getArray($query); 
  
	/* return true if it exists */
	if(sizeof($count) == 0) { return false; }
	else 					{ return true; }
}



/**
 * Since 0.5 the switch management changed, so if upgrading from old version
 * we must get all existing switch names and insert it to switch table!
 */
function updateSwitchFromOldVersions() 
{
    global $db;                                                                      # get variables from config file
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
    global $db;                                                                      # get variables from config file
    $databaseRoot    = new database($db['host'], $root['user'], $root['pass']); 
    
    /* Check connection */
    if ($databaseRoot->connect_error) {
    	die('<div class="alert alert-error">Connect Error (' . $databaseRoot->connect_errno . '): '. $databaseRoot->connect_error). "</div>";
	}
    
 	/* first create database */
    $query = "create database ". $db['name'] .";";

    /* execute */
    try {
    	$databaseRoot->executeQuery( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">'. $error .'</div>');
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
    	die('<div class="alert alert-error">Cannot set permissions for user '. $db['user'] .': '. $error. '</div>');
	}
    
    /* try importing SCHEMA file */
    $query       = file_get_contents("../../db/SCHEMA.sql");
    
    /* execute */
    try {
    	$databaseRoot->executeMultipleQuerries( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	die('<div class="alert alert-error">Cannot install sql SCHEMA file: '. $error. '</div>');
	}
	    
    /* return true, if some errors occured script already died! */
    sleep(1);
   	updateLogTable ('Database installed successfully!', "version 0.7 installed", 1);
   	return true;
}

?>