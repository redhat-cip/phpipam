<?php

/**
 * Script to verify database structure
 ****************************************/


/* verify that user is admin */
checkAdmin();


/* title */
print '<h4>'._('Database structure verification').'</h4><hr>'. "\n";


/* required tables */
$reqTables = array("instructions", "ipaddresses", "logs", "requests", "sections", "settings", "settingsDomain", "subnets", "switches", "users", "vrf", "vlans");

/* required fields for each table */
$fields['instructions']   = array("instructions");
$fields['ipaddresses'] 	  = array("subnetId", "ip_addr", "description", "dns_name", "mac", "owner", "switch", "port", "owner", "state", "note");
$fields['logs']			  = array("severity", "date", "username", "ipaddr", "command", "details");
$fields['requests']		  = array("subnetId", "ip_addr", "description", "dns_name", "owner", "requester", "comment", "processed", "accepted", "adminComment");
$fields['sections']		  = array("name", "description", "permissions", "strictMode");
$fields['settings']		  = array("siteTitle", "siteAdminName", "siteAdminMail", "siteDomain", "siteURL", "domainAuth", "showTooltips", "enableIPrequests", "enableVRF", "enableDNSresolving", "version", "donate", "IPfilter", "printLimit", "visualLimit", "vlanDuplicate", "htmlMail", "subnetOrdering");
$fields['settingsDomain'] = array("account_suffix", "base_dn", "domain_controllers", "use_ssl", "use_tls", "ad_port");
$fields['subnets'] 		  = array("subnet", "mask", "sectionId", "description", "masterSubnetId", "vrfId", "allowRequests", "vlanId", "showName", "permissions");
$fields['switches'] 	  = array("hostname", "ip_addr", "type", "vendor", "model", "version", "description", "sections");
$fields['users'] 	  	  = array("username", "password", "groups", "role", "real_name", "email", "domainUser", "lang");
$fields['vrf'] 	  	  	  = array("name", "rd", "description");
$fields['vlans']   	  	  = array("vlanId", "name", "number", "description");
$fields['userGroups']     = array("g_id", "g_name", "g_descr");
$fields['lang']     	  = array("l_id", "l_code", "l_name");

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
	print '<div class="alert alert-success alert-absolute">'._('All tables and fields are installed properly').'!</div>'. "\n";
}
else if (isset($tableError)) {
	print '<div class="alert alert-error alert-absolute" style="text-align:left;">'. "\n";
	print '<b>'._('Missing table').'s:</b>'. "\n";
	print '<ul>'. "\n";
	
	foreach ($tableError as $table) {
		print '<li>'. $table .'</li>'. "\n";
	}

	print '</ul>'. "\n";	
	print '</div>'. "\n";
}
else if (isset($fieldError)) {
	print '<div class="alert alert-error alert-absolute" style="text-align:left;">'. "\n";
	print '<b>'._('Missing fields').':</b>'. "\n";
	print '<ul>'. "\n";
	
	foreach ($fieldError as $table=>$field) {
		print '<li>Table `'. $table .'`: missing field `'. $field .'`;</li>'. "\n";
	}

	print '</ul>'. "\n";	
	print '</div>'. "\n";
}

?>