<?php

/**
 * Script to verify database structure
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


/* title */
print '<h3>Database structure verification</h3>'. "\n";


/* required tables */
$reqTables = array("instructions", "ipaddresses", "logs", "requests", "sections", "settings", "settingsDomain", "subnets", "switches", "users", "vrf");

/* required fields for each table */
$fields['instructions']   = array("instructions");
$fields['ipaddresses'] 	  = array("subnetId", "ip_addr", "description", "dns_name", "mac", "owner", "switch", "port", "owner", "state", "note");
$fields['logs']			  = array("severity", "date", "username", "command", "details");
$fields['requests']		  = array("subnetId", "ip_addr", "description", "dns_name", "owner", "requester", "comment", "processed", "accepted", "adminComment");
$fields['sections']		  = array("name", "description");
$fields['settings']		  = array("siteTitle", "siteAdminName", "siteAdminMail", "siteDomain", "siteURL", "domainAuth", "showTooltips", "enableIPrequests", "enableVRF", "enableDNSresolving", "version", "donate");
$fields['settingsDomain'] = array("account_suffix", "base_dn", "domain_controllers", "use_ssl", "use_tls", "ad_port");
$fields['subnets'] 		  = array("subnet", "mask", "sectionId", "description", "masterSubnetId", "VLAN", "vrfId", "allowRequests", "adminLock");
$fields['switches'] 	  = array("hostname", "ip_addr", "vendor", "model", "version", "description", "sections");
$fields['users'] 	  	  = array("username", "password", "role", "real_name", "email", "domainUser");
$fields['vrf'] 	  	  	  = array("name", "rd", "description");


/**
 * check that each database exist - if it does check also fields
 *		2 errors -> $tableError, $fieldError[table] = field 
 ****************************************************************/
 
foreach($reqTables as $table) {

	//check if table exists
	if(!tableExists($table)) {
		$tableError[] = $table;
	}
	//check for each field
	else {
		foreach($fields[$table] as $field) {
			//if it doesnt exist store error
			if(!fieldExists($table, $field)) {
				$fieldError[$table] = $field; 
			}
		}
	}
}


/* print result */
if( (!isset($tableError)) && (!isset($fieldError)) ) {
	print '<div class="success">All tables and fields are installed properly!</div>'. "\n";
}
else if (isset($tableError)) {
	print '<div class="error" style="text-align:left;">'. "\n";
	print '<b>Missing tables:</b>'. "\n";
	print '<ul>'. "\n";
	
	foreach ($tableError as $table) {
		print '<li>'. $table .'</li>'. "\n";
	}

	print '</ul>'. "\n";	
	print '</div>'. "\n";
}
else if (isset($fieldError)) {
	print '<div class="error" style="text-align:left;">'. "\n";
	print '<b>Missing fields:</b>'. "\n";
	print '<ul>'. "\n";
	
	foreach ($fieldError as $table=>$field) {
		print '<li>Table `'. $table .'`: missing field `'. $field .'`;</li>'. "\n";
	}

	print '</ul>'. "\n";	
	print '</div>'. "\n";
}

?>