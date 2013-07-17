<?php

/**
 * Script to edit / add / delete APIs and keys
 *************************************************/

/* verify that user is admin */
checkAdmin();

/**
 * First print table of existing groups with edit / delete links!
 */
$apis = getAPIkeys();

/* get all settings */
$settings = getallSettings();
?>

<!-- display existing groups -->
<h4><?php print _('API management'); ?></h4>
<hr><br>

<!-- only IF aPI enabled -->
<?php if($settings['api']==1) { ?>
	<!-- Add new -->
	<button class='btn btn-small editAPI' style="margin-bottom:10px;" data-action='add'><i class='icon-gray icon-plus'></i> <?php print _('Create API key'); ?></button>
	
	
	<!-- table -->
	<table id="userPrint" class="table table-striped table-top table-auto">
	
	<!-- Headers -->
	<tr>
	    <th><?php print _('App id'); ?></th>
	    <th><?php print _('App code'); ?></th>
	    <th><?php print _('App permissions'); ?></th>
	    <th></th>
	</tr>
	
	<?php
	/* print existing APIs */
	if(sizeof($apis)>0) {
		foreach ($apis as $a)
		{
			print '<tr>' . "\n";
			
			print '	<td>' . $a['app_id'] . '</td>'. "\n";
			print '	<td>' . $a['app_code'] . '</td>'. "\n";
			
			# reformat permissions
			if($a['app_permissions']==0)		{ $a['app_permissions'] = _("Disabled"); }
			elseif($a['app_permissions']==1)	{ $a['app_permissions'] = _("Read"); }
			elseif($a['app_permissions']==2)	{ $a['app_permissions'] = _("Read / Write"); }
			
			print '	<td>' . $a['app_permissions'] . '</td>'. "\n";
			
			
			# add/remove APIs
			print "	<td class='actions'>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-small editAPI'  		data-appid='$a[id]' data-action='edit'   rel='tooltip' title='"._('edit app details')."'>	<i class='icon-gray icon-pencil'></i></button>";
			print "		<button class='btn btn-small editAPI'  		data-appid='$a[id]' data-action='delete' rel='tooltip' title='"._('remove app')."'>		<i class='icon-gray icon-remove'></i></button>";
			print "	</div>";
			print "</td>";
			
			print '</tr>' . "\n";
		}
	}
	else {
		print "<tr><td colspan='4'><div class='alert alert-warning alert-nomargin'>"._("No Apps available")."!</div></td></tr>";
	}
	
	?>
	</table>
	
	<?php
	# print error if extensions are not available on server!
	$requiredExt  = array("mcrypt", "curl");
	$availableExt = get_loaded_extensions();
	# check for missing ext
	$missingExt = array();
	foreach ($requiredExt as $extension) {
	    if (!in_array($extension, $availableExt)) {
	        $missingExt[] = $extension;
	    }
	}
	# print warning if missing
	if (sizeof($missingExt) > 0) {
	    print "<div class='alert alert-error'><strong>"._('The following PHP extensions for API server are missing').":</strong><br><hr>";
	    print '<ul>' . "\n";
	    foreach ($missingExt as $missing) {
	        print '<li>'. $missing .'</li>' . "\n";
	    }
	    print '</ul>';
	    print _('Please recompile PHP to include missing extensions for API server') . "\n";
	    print "</div>";
	}
	?>
	<hr>
	
	<h4><?php print _('API documentation'); ?></h4>
	<pre><?php print file_get_contents(dirname(__FILE__) . '/../../api/README'); ?></pre>

<?php } else { ?>
<div class="alert alert-warning"><?php print _('Please enable API module under server management'); ?></div>
<?php } ?>