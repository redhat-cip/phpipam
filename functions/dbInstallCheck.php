<script type="text/javascript">
$(document).ready(function () {
	$('div.loading').hide();
	$('table.dbUpgrade a').click(function() {
		var div = $(this).attr('id');
		$('table.dbUpgrade div').not('table.dbUpgrade div.' + div).slideUp('fast');
		$('table.dbUpgrade div.' + div).slideToggle('fast');
		return false;
	});
	$('input.upgrade').live('click', function() {
		$('div.loading').fadeIn('fast');
		var postData = $('#install').serialize();
		$.post('../site/admin/databaseInstall.php', postData, function(data) {
			$('div.upgradeResult').html(data).slideDown('fast');
			$('div.loading').fadeOut('fast');
		});
	});
	$('div.error').live('click', function() {
		$(this).stop(true,true).show();
	});
});
</script>

<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/

/* use required functions */
require_once('../config.php');
require_once('loginFunctions.php');

/* set latest version */
$latest = "0.4";

/* get all site settings */
$settings = getAllSettings();

/* version check */
if($settings['version'] != $latest) {

	/* new install check! */
	if(!tableExists("ipaddresses")) {
	
		//spinner
		print '<div class="loading">Loading...<br><img src="../css/images/ajax-loader.gif"></div>'. "\n";

		//header
		print '<div class="header">phpIPAM database install script</div>';

		print '<div class="normalTable" style="width:80%;margin:auto;margin-top:20px">'. "\n";
		print '<table class="normalTable dbUpgrade">'. "\n";
	
		//title
		print '<tr>'. "\n";
		print '	<th>phpIPAM database installation</th>';
		print '</tr>'. "\n";
	
		//install button
		print '<tr>'. "\n";
		print '	<td class="title"><a href="#" id="upgrade">&middot; Install phpipam database</td>'. "\n";
		print '</tr>'. "\n";
		print '<tr>'. "\n";

		print '<tr class="th">'. "\n";
		print '<td>'. "\n";
		print '	<div class="upgrade">'. "\n";
		

		print '	Clicking on install button will install required database files. Please fill in following database connection details:'. "\n";
			
		//We need the following details -> mysql root pass
		print '<form id="install">'. "\n";
		print '<br><input type="text" name="mysqlrootuser"  value="root">MySQL username (user with full permissions to MySQL database)<br>'. "\n";
		print '<input type="password" name="mysqlrootpass">MySQL (root) password<br>'. "\n";
		print '<input type="text" name="mysqllocation" 	value="'. $db['host'] .'" disabled>MySQL database location *<br>'. "\n";
		print '<input type="text" name="mysqltable" 	value="'. $db['name'] .'" disabled>Database name*<br>'. "\n";
		print '<span style="padding-left:20px;p">* Please change database name and location by modifying config.php file!</span>';
			
		print '</form>'. "\n";
			
		
		print '<input type="button" class="upgrade" version="0" value="Install phpipam database">';
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
		
		print '/* import upgrade file */'. "\n";
		print 'mysql -u root -p my_root_pass < db/SCHEMA.sql';		
		
		print '	</pre>'. "\n";;
		print '	</div>'. "\n";
		print '</td>'. "\n";
		print '</tr>'. "\n";		
			
	
		//Manual instructions
		print '<tr>'. "\n";
		print '	<td class="title"><a href="#" id="manualUpgrade">&middot; Manual install instructions</a></td>'. "\n";
		print '</tr>'. "\n";
		
		print '<tr class="th">'. "\n";
		print '<td>'. "\n";
		print '	<div class="manualUpgrade">'. "\n";
		print '	<pre>'. "\n";
	
		$file = file_get_contents("../db/SCHEMA.sql");
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
		$requiredTables = array("instructions", "ipaddresses", "logs", "requests", "sections", "settings", "subnets", "users");

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