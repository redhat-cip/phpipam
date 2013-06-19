<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/

/* not logged in users */
if (isUserAuthenticatedNoAjax()) {
	header("Location: login/");	
}
/* logged in users, non-admins and same verision -> redirect */
else if( (!checkAdmin(false)) && ($settings['version'] == VERSION)) {
	header("Location: login/");
} 
/* logged in users, non-admins and and upgrade needed */
else if( (!checkAdmin(false)) && ($settings['version'] < VERSION) ) {
	print '<h4>phpIPAM upgrade script</h4><hr>';
	print '<div class="alert alert-error">Database needs upgrade. Please contact site administrator (<a href="mailto:'. $settings['siteAdminMail'] .'">'. $settings['siteAdminName'] .'</a>)!</div>';
	print '<a href="login/"><button class="btn btn-small">Login</button></a>';
}
/* admin, but no upgrade needed */
else if ( (checkAdmin(false)) && ($settings['version'] == VERSION) ) {
	print "<h4>Database upgrade script</h4><hr>";
	print "<div class='alert alert-success'>Database seems up to date and doesn't need to be upgraded!</div>";
	print '<a href=""><button class="btn btn-small">Go to dashboard</button></a>';
}
/* version check for admins */
else if($settings['version'] != VERSION) {

	/* new install check! */
	if(!tableExists("ipaddresses")) {
		print '<h4>phpIPAM database installation required</h4><hr>';
		die('<div class="alert alert-warn">phpipam database files not installed. Please click <a href="install/">here</a> to run install script!</div>');
	}
	/* v0.2 check -> settings and requests are added */
	else if( (!tableExists("settings")) || (!tableExists("requests")) ) { $version = "0.2"; }
	/* v0.3 check -> subnets have admin lock and and requests */
	else if ( (!fieldExists("subnets", "allowRequests")) || (!fieldExists("subnets", "adminLock")) ) { $version = "0.3"; }
	/* v0.4 check -> table switches does not exist yet */
	else if (!tableExists("switches")) { $version = "0.4";}
	/* v0.5 check -> table vlans does not exist yet */
	else if (!tableExists("vlans")) { $version = "0.5"; }
	/* v0.6 check -> field strictmode does not exist yet */
	else if (!fieldExists("settings", "strictMode")) { $version = "0.6"; }
	/* v0.7 check -> field settings in settings exist but usergroup not yet */
	else if (!fieldExists("settings", "htmlMail") && !tableExists("userGroups") ) { $version = "0.7"; }
	/* ok, "latest" OLD version */
	else { $version = "0.7"; }

	if($version == "0.2" || $version == "0.3" || $version == "0.4" || $version == "0.5") {
		die("<div class='alert alert-error'>It seems you are using phpipam version ".$version.". This version cannot be upgraded to 0.8, please install at least version 0.6 first!</div>");
	}
 	/* if version is not the latest print warning that it will be upgraded! */
	else if($version != VERSION) { ?>
	
		<script type="text/javascript">		
		$(document).ready(function () {		
			$("div.loading").hide();		
			$("table.dbUpgrade a").click(function() {		
				var div = $(this).attr("id");
				$("table.dbUpgrade tbody.content").not("table.dbUpgrade tbody." + div).hide();	
				$("table.dbUpgrade tbody." + div).show("fast");	
				$("table.dbUpgrade i").removeClass("icon-chevron-down").addClass('icon-chevron-right');	
				$("table.dbUpgrade a#"+div+" i").removeClass("icon-chevron-right").addClass('icon-chevron-down');	
				return false;		
			});		
			$(document).on("click", "input.upgrade", function() {	
				$(this).removeClass("upgrade");		
				$("div.loading").fadeIn("fast");		
				var version = $(this).attr("version");		
				$.post("site/admin/databaseUpgrade.php", {version:version}, function(data) {		
					$("div.upgradeResult").html(data).slideDown("fast");		
					$("div.loading").fadeOut("fast");		
				});		
			});	
			$(document).on("click", "div.error", function() {	
				$(this).stop(true,true).show();		
			});		
		});		
		</script>		
	

		<h3>phpIPAM database upgrade required</h3><hr><br>

		<!-- info -->
		<div class="alert alert-info">Database needs to be upgraded to latest version, it seems you are using phpipam version <?php print $version; ?>!</div>

		<table class="dbUpgrade table table-striped table-top">	
			
		<!-- manual upgrade -->
		<tr>	
			<th><a href="#" id="upgrade"><i class="icon-gray icon-chevron-down"></i> Upgrade phpipam database</th>		
		</tr>		
		<tr>		

		<tbody class="upgrade content">
		<tr>		
		<td>
			Clicking on upgrade button will update database to newest version.
			<div class="alert alert-warn alert-nomargin"><strong>Warning!</strong> Backup database first before attempting to upgrade it! You have been warned.</div>
			<input type="button" class="upgrade btn" version="<?php print $version; ?>" value="Upgrade phpipam database">
			<div class="upgradeResult"></div>			
		</td>		
		</tr>	
		</tbody>		
		
		
		<!-- SQL import instructions -->
		<tr>		
			<th><a href="#" id="sqlUpgrade"><i class="icon-gray icon-chevron-right"></i> sql import instructions</a></th>		
		</tr>			

		<tbody class="sqlUpgrade content" style="display:none;">
		<tr>		
		<td>		
			<pre>/* backup database */		
/usr/bin/mysqldump -u <?php print $db['user'] .' -p'. $db['pass'] .' '. $db['name'] .' > my_backup_dir/phpipam_'. $version .'_migration_backup.db'; ?><br>
/* import upgrade file */		
mysql -u root -p my_root_pass < db/UPDATE-v<?php print $version; ?>.sql</pre>	
		</td>		
		</tr>				
		</tbody>
	
		<!-- Manual instructions -->
		<tr>		
			<th><a href="#" id="manualUpgrade"><i class="icon-gray icon-chevron-right"></i> Manual upgrade instructions</a></th>		
		</tr>		
		
		<tbody class="manualUpgrade content" style="display:none">
		<tr>		
		<td>				
			<pre><?php $file = file_get_contents('db/UPDATE-v'. $version .'.sql'); print_r($file); ?></pre>		
		</td>		
		</tr>
		</tbody>		
	
		</table>
		<?php
	}
	/* Check if all the tables are present! */
	else {
		$tables 	= getAllTables();
		/* required tables */
		$requiredTables = array("instructions", "ipaddresses", "logs", "requests", "sections", "settings", "subnets", "users", "switches", "vlans");

		/* reformat available tables */
		foreach ($tables as $table) {
			$availableTables[] = $table['Tables_in_'. $db['name']];
		}

		/* verify that all required tables are present */
		foreach ($requiredTables as $table) {
			if(!in_array($table, $availableTables)) {
				$missing .= "<br>". $table;
			}
		}

		/* if some are missing die */
		if (strlen($missing) != 0) {
			/* die with error */
    		print('<div class="alert alert-error">Following tables are missing in database:<strong>'. $missing .'</strong></div>');
		}
	
	}
}
else {
	# redirect, all good!
	header("Location: login/");
}
?>