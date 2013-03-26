<?php

/**
 * Functions for upgrade and verification checks
 *
 */


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

    /* execute */
    try { $switches = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
    
    /* get all sectionsIds */
    $sections = fetchSections();
    foreach($sections as $section) {
    	$id[] = $section['id'];
    }
    $id = implode(";", $id);
        
    /* import each to database */
    foreach($switches as $switch) {
    	$query 	  = 'insert into `switches` (`hostname`,`sections`) values ("'. $switch['switch'] .'", "'. $id .'");';
    	
    	/* execute */
    	try { $database->executeQuery( $query ); }
    	catch (Exception $e) { 
        	$error =  $e->getMessage(); 
        	print ("<div class='alert alert-error'>Error: $error</div>");
        } 
    }
    
    return true;
}


/**
 * Since 0.6 the VLAN management changed, so if upgrading from old version
 * we must get all existing VLAN numbers and insert it to VLAN table!
 */
function updateVLANsFromOldVersions()
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all existing switches */
    $query 	 = 'select distinct(`VLAN`) from `subnets` where `VLAN` not like "0";';

    /* execute */
    try { $vlans = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
        
    /* import each to database */
    foreach($vlans as $vlan) {
    	$query 	  = 'insert into `vlans` (`number`,`description`) values ("'. $vlan['VLAN'] .'", "Imported VLAN from upgrade to 0.6");';
    	$database->executeQuery($query);

    	/* execute */
    	try { $database->executeQuery( $query ); }
    	catch (Exception $e) { 
    	    $error =  $e->getMessage(); 
    	    print ("<div class='alert alert-error'>Error: $error</div>");
    	 } 
    }
    
    /* link back from subnets to vlanid */
    $query = "select * from `vlans`;";
    $vlans   = $database->getArray($query);
    
    foreach($vlans as $vlan) {
    	# update subnet vlanId
    	$query = 'update `subnets` set `vlanId` = "'. $vlan['vlanId'] .'" where `VLAN` = "'. $vlan['number'] .'" ;';
    	/* execute */
    	try { $database->executeQuery( $query ); }
    	catch (Exception $e) { 
    	    $error =  $e->getMessage(); 
    	    print ("<div class='alert alert-error'>Error: $error</div>");
    	 }
    }    
    
    /* remove VLAN field */
    $query = "Alter table `subnets` drop column `VLAN`;";
    /* execute */
    try { $database->executeQuery( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
    }
    
    return true;
}


/**
 * Since 0.7 the switches are not linked with hostnames but with Id's!
 */
function updateSwitchFromOldVersionsToId() 
{
    global $db;                                                                      # get variables from config file
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all existing switches */
    $query 	  = 'select `id`,`hostname` from `switches`;';

    /* execute */
    try { $switches = $database->getArray( $query ); }
    catch (Exception $e) { 
        $error =  $e->getMessage(); 
        print ("<div class='alert alert-error'>Error: $error</div>");
        return false;
    } 
        
    /* change name to id to database */
    foreach($switches as $switch) {
    	$query 	  = 'update `ipaddresses` set `switch` = "'.$switch['id'].'" where `switch` ="'.$switch['hostname'].'" ;';
    	/* execute */
    	try {
    		$database->executeQuery( $query );
    	}
    	catch (Exception $e) {
    		$error =  $e->getMessage();
    		print('<div class="alert alert-error">Failed to update switch ip address associations for switch '.$switch['hostname'].': '. $error .'</div>');
    	}
    }
    /* remove remaining non-numeric values */
    $query = "update `ipaddresses` set `switch` = '' WHERE `switch` REGEXP '[^0-9]';";
    /* execute */
    try {
    	$database->executeQuery( $query );
    }    
    catch (Exception $e) {
    	$error =  $e->getMessage();
    	print('<div class="alert alert-error">Failed to remove orphaned switches from IP address list!: '. $error .'</div>');
    }    
    
    return true;
}


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
    
    /* import querries from upgrade file */
    $query    = file_get_contents("../../db/UPDATE-v". $version. ".sql");
    
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
    updateLogTable ('DB updated', 'DB updated from version '. $version .' to version 0.8', 1);
    return true;
}

?>