<?php

/**
 * Check for fresh installation
 ****************************************************/

# show only errors
error_reporting(E_ERROR);


if(!tableExists("ipaddresses")) { ?>
	
		<!-- javascript -->
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
				$("div.loading").fadeIn("fast"); 
				var postData = $("#install").serialize(); 
				$.post("site/admin/databaseInstall.php", postData, function(data) { 
					$("div.upgradeResult").html(data).slideDown("fast"); 
					$("div.loading").fadeOut("fast"); 
				}); 
			}); 
			$(document).on("click", "div.error", function() { 
				$(this).stop(true,true).show(); 
			}); 
		}); 
		</script> 
	
		<!-- title -->
		<h4>phpIPAM database installation</h4>
		<hr><br>
		

		<table class="dbUpgrade table table-striped table-top">
		
		<!-- install -->
		<tr>
			<th><a href="#" id="upgrade"><i class="icon-gray icon-chevron-down"></i> Install phpipam database</th>
		</tr>
		<tr>

		<tbody class="upgrade content">
		<tr>
			<td>
			<div class="alert alert-info">Clicking on install button will install required database files. Please fill in following database connection details:</div>
			<form id="install">
				<input type="text"     style='margin-bottom:5px;' name="mysqlrootuser"  value="root"> MySQL username (user with permissions to create new MySQL database)<br>
				<input type="password" style='margin-bottom:5px;' name="mysqlrootpass"> MySQL password<br>
				<input type="text"     style='margin-bottom:5px;' name="mysqllocation" 	value="<?php print $db['host']; ?>" disabled> MySQL database location *<br>
				<input type="text"     style='margin-bottom:5px;' name="mysqltable" 	value="<?php print $db['name']; ?>" disabled> Database name*<br>
				<span style="color:gray;"> * Please change database name and location by modifying config.php file!</span><br>
				<input type="button" class="upgrade btn btn-small" version="0" value="Install phpipam database">
			</form>
		
			<div class="upgradeResult"></div>
		</td>
		</tr>	
		</tbody>
		
		
		<!-- SQL import instructions -->
		<tr>
			<th><a href="#" id="sqlUpgrade"><i class="icon-gray icon-chevron-right"></i> MySQL import instructions</a></th>
		</tr>	

		<tbody style="display:none;" class="sqlUpgrade content">
		<tr>
		<td>
			<div class="sqlUpgrade">
			<pre>/* import upgrade file */
mysql -u root -p my_root_pass < db/SCHEMA.sql</pre>
			</div>
		</td>
		</tr>
		</tbody>
			
	
		<!-- Manual instructions -->
		<tr>
			<th><a href="#" id="manualUpgrade"><i class="icon-gray icon-chevron-right"></i> Manual install instructions</a></th>
		</tr>
		
		<tbody style="display:none;" class="manualUpgrade content">
		<tr>
		<td>
			<div class="manualUpgrade">
			<pre><?php $file = file_get_contents("db/SCHEMA.sql"); print_r($file); ?></pre>
			</div>
		</td>
		</tr>
		</tbody>
	
		</table>
<?php
}
else {
	# already installed
	header("Location: /");
}
?>