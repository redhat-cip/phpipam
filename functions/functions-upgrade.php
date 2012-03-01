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
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all existing switches */
    $query 	  = 'select distinct(`switch`) from `ipaddresses` where `switch` not like "";';
    $switches = $database->getArray($query); 
    
    /* get all sectionsIds */
    $sections = fetchSections();
    foreach($sections as $section) {
    	$id[] = $section['id'];
    }
    $id = implode(";", $id);
        
    /* import each to database */
    foreach($switches as $switch) {
    	$query 	  = 'insert into `switches` (`hostname`,`sections`) values ("'. $switch['switch'] .'", "'. $id .'");';
    	$database->executeQuery($query);
    }
    
    return true;
}


/**
 * Since 0.6 the VLAN management changed, so if upgrading from old version
 * we must get all existing VLAN numbers and insert it to VLAN table!
 */
function updateVLANsFromOldVersions()
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 
    
    /* get all existing switches */
    $query 	 = 'select distinct(`VLAN`) from `subnets` where `VLAN` not like "0";';
    $vlans   = $database->getArray($query); 
        
    /* import each to database */
    foreach($vlans as $vlan) {
    	$query 	  = 'insert into `vlans` (`number`,`description`) values ("'. $vlan['VLAN'] .'", "Imported VLAN from upgrade to 0.6");';
    	$database->executeQuery($query);
    }
    
    /* link back from subnets to vlanid */
    $query = "select * from `vlans`;";
    $vlans   = $database->getArray($query);
    
    foreach($vlans as $vlan) {
    	# update subnet vlanId
    	$query = 'update `subnets` set `vlanId` = "'. $vlan['vlanId'] .'" where `VLAN` = "'. $vlan['number'] .'" ;';
    	$database->executeQuery($query);
    }    
    
    /* remove VLAN field */
    $query = "Alter table `subnets` drop column `VLAN`;";
    $database->executeQuery($query);
    
    return true;
}


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
 * upgrade database
 */
function upgradeDatabase($version)
{
    /* get variables from config file */
    global $db;
    $database    = new database($db['host'], $db['user'], $db['pass'], $db['name']); 

    /* Check connection */
    if ($database->connect_error) {
    	die('<div class="error">Connect Error (' . $database->connect_errno . '): '. $database->connect_error). "</div>";
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
    	die('<div class="error">Update error: '. $error .'</div>');
	}
    
    /* return true if we came to here */
    sleep(1);
    updateLogTable ('DB updated', 'DB updated from version '. $version .' to version 0.6', 1);
    return true;
}

?>