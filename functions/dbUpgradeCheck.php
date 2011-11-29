<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/

/* use required functions */
require_once('config.php');
require_once('functions.php');

/* set latest version */
$latest = "0.5";

/* get all site settings */
$settings = getAllSettings();

/* display only to admin users */
if( (!checkAdmin(false)) && ($settings['version'] != $latest) ) {
	die('<div class="error">Database needs upgrade. Please contact site administrator (<a href="mailto:'. $settings['siteAdminMail'] .'">'. $settings['siteAdminName'] .'</a>)!</div>');
}

/* version check */
if($settings['version'] != $latest) {

	/* new install check! */
	if(!tableExists("ipaddresses")) {
		print '<div class="header">phpIPAM database upgrade script</div>';
		die('<div class="error">phpipam database files not installed. Please click <a href="login">here</a> to run install script!</div>');
	}
	/* v0.2 check -> settings and requests are added */
	else if( (!tableExists("settings")) || (!tableExists("requests")) ) {
		$version = "0.2";
	}
	/* v0.3 check -> subnets have admin lock and and requests */
	else if ( (!fieldExists("subnets", "allowRequests")) || (!fieldExists("subnets", "adminLock")) ) {
		$version = "0.3";	
	}
	/* v0.4 check -> table switches does not exist yet */
	else if (!tableExists("switches")) {
		$version = "0.4";	
	}
	/* ok, latest version */
	else {
		$version = "0.5";
	}

	/* if version is not the latest print warning that it will be upgraded! */
	if($version != $latest) {
	
		//javascript
		print '<script type="text/javascript"> '. "\n";
		print '$(document).ready(function () { '. "\n";
		print '	$("div.loading").hide(); '. "\n";
		print '	$("table.dbUpgrade a").click(function() { '. "\n";
		print '		var div = $(this).attr("id"); '. "\n";
		print '		$("table.dbUpgrade div").not("table.dbUpgrade div." + div).slideUp("fast"); '. "\n";
		print '		$("table.dbUpgrade div." + div).slideToggle("fast"); '. "\n";
		print '		return false; '. "\n";
		print '	}); '. "\n";
		print '	$("input.upgrade").live("click", function() { '. "\n";
		print '		$(this).removeClass("upgrade"); '. "\n";
		print '		$("div.loading").fadeIn("fast"); '. "\n";
		print '		var version = $(this).attr("version"); '. "\n";
		print '		$.post("site/admin/databaseUpgrade.php", {version:version}, function(data) { '. "\n";
		print '			$("div.upgradeResult").html(data).slideDown("fast"); '. "\n";
		print '			$("div.loading").fadeOut("fast"); '. "\n";
		print '		}); '. "\n";
		print '	}); '. "\n";
		print '	$("div.error").live("click", function() { '. "\n";
		print '		$(this).stop(true,true).show(); '. "\n";
		print '	}); '. "\n";
		print '}); '. "\n";
		print '</script> '. "\n";
	
		//spinner
		print '<div class="loading">Loading...<br><img src="css/images/ajax-loader.gif"></div>'. "\n";

		//header
		print '<div class="header">phpIPAM database upgrade script</div>';

		print '<div class="normalTable" style="width:80%;margin:auto;margin-top:20px">'. "\n";
		print '<table class="normalTable dbUpgrade">'. "\n";
	
		//title
		print '<tr>'. "\n";
		print '	<th>Database needs to be upgraded to latest version, it seems you are using phpipam version '. $version .'!</th>';
		print '</tr>'. "\n";
	
		//upgrade button
		print '<tr>'. "\n";
		print '	<td class="title"><a href="#" id="upgrade">&middot; Upgrade phpipam database</td>'. "\n";
		print '</tr>'. "\n";
		print '<tr>'. "\n";

		print '<tr class="th">'. "\n";
		print '<td>'. "\n";
		print '	<div class="upgrade">'. "\n";
		
		print '	Clicking on upgrade button will update database to newest version.<br>'. "\n";	
		
		print '<input type="button" class="upgrade" version="'. $version .'" value="Upgrade phpipam database">';
		print '<div class="upgradeResult"></div>'. "\n";
		
		print '	</div>'. "\n";
		print '</td>'. "\n";
		print '</tr>'. "\n";	
		
		
		//SQL import instructions
		print '<tr>'. "\n";
		print '	<td class="title"><a href="#" id="sqlUpgrade">&middot; sql import instructions</a></td>'. "\n";
		print '</tr>'. "\n";	

		print '<tr class="th">'. "\n";
		print '<td>'. "\n";
		print '	<div class="sqlUpgrade">'. "\n";
		print '	<pre>'. "\n";
		
		print '/* backup database */'. "\n";
		print '/usr/bin/mysqldump -u '. $db['user'] .' -p'. $db['pass'] .' '. $db['name'] .' > my_backup_dir/phpipam_'. $version .'_migration_backup.db'. "<br>\n";
		print '/* import upgrade file */'. "\n";
		print 'mysql -u root -p my_root_pass < db/UPDATE-v'. $version .'.sql';		
		
		print '	</pre>'. "\n";;
		print '	</div>'. "\n";
		print '</td>'. "\n";
		print '</tr>'. "\n";		
			
	
		//Manual instructions
		print '<tr>'. "\n";
		print '	<td class="title"><a href="#" id="manualUpgrade">&middot; Manual upgrade instructions</a></td>'. "\n";
		print '</tr>'. "\n";
		
		print '<tr class="th">'. "\n";
		print '<td>'. "\n";
		print '	<div class="manualUpgrade">'. "\n";
		print '	<pre>'. "\n";
	
		$file = file_get_contents('db/UPDATE-v'. $version .'.sql');
		print_r($file);
	
		print ' </pre>'. "\n";;
		print '	</div>'. "\n";
		print '</td>'. "\n";
		print '</tr>'. "\n";
	
		//end of table
		print '</table>'. "\n";
		print '</div>';
		die();
	}
	/* Check if all the tables are present! */
	else {
		$tables 	= getAllTables();

		/* required tables */
		$requiredTables = array("instructions", "ipaddresses", "logs", "requests", "sections", "settings", "subnets", "users", "switches");

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

			/* HMTL frame */
			print '<html>' . "\n";
			print '<head>' . "\n";
			print '	<title>IPAM error</title>' . "\n";
			print '	<link rel="stylesheet" type="text/css" href="'. $locationPrefix .'css/style.css">' . "\n";
			print '</head>' . "\n";
			print '</html>' . "\n";

			/* die with error */
    		die('<div class="error extError"><img src="'. $locationPrefix .'css/images/error.png"><h3>Following tables are missing in database:</h3><br>'. $missing .'<br><br></div>');
		}
	
	}

}
?>