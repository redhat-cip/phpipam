<?php

/**
 * Functions for upgrade and verification checks
 *
 */


/**
 * add http to siteURL by default
 */
function addHTTP() 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
	$query = "UPDATE `settings` SET `siteURL` = IFNULL(CONCAT('http://',`siteURL`), 'http://');";

    /* execute */
    try { $database->executeQuery( $query ); }    
    catch (Exception $e) {}   
}



/**
 * Get all tables
 */
function getAllTables()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* first update request */
    $query    = 'show tables;';

    /* execute */
    try { $tables = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
  
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
    	if($quit)   { die('Connect Error (' . $database->connect_errno . '): '. $database->connect_error);}
    	else		{ return false; }
	}
    
    /* first update request */
    $query    = 'SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = "'. $db['name'] .'" AND table_name = "'. $table .'";';
    
	/* execute */
    try { $count = $database->getArray($query); }
    catch (Exception $e) { $error =  $e->getMessage(); } 
  
    /* die if error */
    if(isset($error)) 				{ return false; }
    else {
		/* return true if it exists */
		if($count[0]['count'] == 1) { return true; }
		else 						{ return false; }  
    }
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

    /* execute */
    try { $count = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
  
	/* return true if it exists */
	if(sizeof($count) == 0) { return false; }
	else 					{ return true; }
}


/**
 * upgrade database
 */
function upgradeDatabase($version)
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('<div class="alert alert-error">Connect Error (' . $database->connect_errno . '): '. $database->connect_error). "</div>";
	}
	
	/* get all upgrade files */
	$dir = "../db/";
	$dir = dirname(__FILE__) . '/../db/';

	$files = scandir($dir);
	foreach($files as $f) {
		//get only UPGRADE- for specific version
		if(substr($f, 0, 6) == "UPDATE") {
			$ver = str_replace(".sql", "",substr($f, 8));
			if($ver>$version) {
				//printout
				$query .= file_get_contents($dir.$f);
			}
		}
	}
	
    /* execute */
    try {
    	$database->executeMultipleQuerries( $query );
    }
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	updateLogTable ('DB update failed', 'DB updated failed with error: '. $error, 2);
    	die('<div class="alert alert-error">Update error: '. $error .'</div>');
	}
    
    /* return true if we came to here */
    sleep(1);
    updateLogTable ('DB updated', 'DB updated from version '. $version .' to version '.VERSION, 1);
    return true;
}

?>