<?php

/**
 * Script to edit / add / delete groups
 *************************************************/

/* verify that user is admin */
checkAdmin();

/**
 * First print table of existing groups with edit / delete links!
 */
$groups = getAllGroups();

/* get all settings */
$settings = getallSettings();
?>

<!-- display existing groups -->
<h4>Group management</h4>
<hr><br>

<!-- Add new -->
<button class='btn btn-small editGroup' style="margin-bottom:10px;" data-action='add'><i class='icon-gray icon-plus'></i> Create new group</button>


<!-- table -->
<table id="userPrint" class="table table-striped table-top table-auto">

<!-- Headers -->
<tr>
    <th>Group name</th>
    <th>Group description</th>
    <th>Belonging users</th>
    <th>Section permissions</th>
    <th colspan="2"></th>
</tr>

<!-- admins -->
<tr>
	<td>Administrators</td>
	<td>Administrator level users</td>
	<td>
	<?php
	$admins = getAllAdminUsers();
	foreach($admins as $a) {
		print "$a[real_name]<br>";
	}
	?>
	</td>
	<td>All sections : Read / Write</td>
	<td colspan="2"></td>
</tr>

<?php
/* print existing sections */
foreach ($groups as $g)
{
	print '<tr>' . "\n";
	
	print '	<td>' . $g['g_name'] . '</td>'. "\n";
	print '	<td>' . $g['g_desc'] . '</td>'. "\n";
	
	# users in group
	print "	<td>";
	$u = getUsersInGroup($g['g_id']);
	if(sizeof($u)>0) {
		foreach($u as $name) {
			# get details
			$user = getUserDetailsById($name);
			print "$user[real_name]<br>";
		}
	}
	print "</td>";

	
	# section permissions
	print "	<td>";
	$s = getSectionPermissionsByGroup($g['g_id']);
	if(sizeof($s)>0) {
		foreach($s as $sec=>$perm) {
			# reformat permissions
			$perm = parsePermissions($perm);
			print $sec." : ".$perm."<br>";
		}
	}
	print "</td>";
	
	
	# add/remove users
	print "	<td class='actions'>";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-small addToGroup' 		data-groupid='$g[g_id]' data-action='add'    rel='tooltip' title='add users to this group'>   	<i class='icon-gray icon-plus'></i></button>";
	print "		<button class='btn btn-small removeFromGroup' 	data-groupid='$g[g_id]' data-action='remove' rel='tooltip' title='remove users from this group'><i class='icon-gray icon-minus'></i></button>";
	print "	</div>";
	print "</td>";

	# edit, delete	
	print "<td class='actions'>";	
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-small editGroup'  		data-groupid='$g[g_id]' data-action='edit'   rel='tooltip' title='Edit group details'>	<i class='icon-gray icon-pencil'></i></button>";
	print "		<button class='btn btn-small editGroup'  		data-groupid='$g[g_id]' data-action='delete' rel='tooltip' title='Remove group'>		<i class='icon-gray icon-remove'></i></button>";
	print "	</div>";
	print "</td>";
	
	print '</tr>' . "\n";
}

?>

</table>