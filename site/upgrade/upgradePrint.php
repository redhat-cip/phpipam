<!-- upgrade JS -->
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


<!-- title -->
<h3>phpIPAM database upgrade required</h3><hr><br>

<!-- info -->
<div class="alert alert-info">Database needs to be upgraded to latest version v<?php print VERSION; ?>, it seems you are using phpipam version v<?php print $settings['version']; ?>!</div>

<!-- table -->
<table class="dbUpgrade table table-striped table-top">	
	
	<!-- automatic upgrade -->
	<tr>	
		<th><a href="#" id="upgrade"><i class="icon-gray icon-chevron-down"></i> Upgrade phpipam database</th>		
	</tr>		
	<tr>		
	
	<tbody class="upgrade content">
	<tr>		
		<td>
			Clicking on upgrade button will update database to newest version.
			<div class="alert alert-warn alert-nomargin" style='margin:4px;'><strong>Warning!</strong> Backup database first before attempting to upgrade it! You have been warned.</div>
			<input type="button" class="upgrade btn btn-small" version="<?php print $settings['version']; ?>" value="Upgrade phpipam database">
			<div class="upgradeResult"></div>			
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
		<pre><?php
		$dir = "db/";
		$files = scandir($dir);
		foreach($files as $f) {
			//get only UPDATE- for specific version
			if(substr($f, 0, 6) == "UPDATE") {
				$ver = str_replace(".sql", "",substr($f, 8));
				if($ver>$settings['version']) {
					//printout
					print "<br>".file_get_contents("db/$f");
				}
			}
		}
		?></pre>
		</td>		
	</tr>
	</tbody>		

</table>