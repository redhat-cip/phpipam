<?php

/**
 * Script to edit / add / delete users
 *************************************************/


/* verify that user is admin */
checkAdmin();

/**
 * First print table of existing users with edit / delete links!
 */
$users = getAllUsers();

/* get all settings */
$settings = getallSettings();
?>

<!-- display existing users -->
<h4>User management</h4>
<hr><br>

<!-- Add new -->
<button class='btn btn-small editUser' style="margin-bottom:10px;" data-action='add'><i class='icon-gray icon-plus'></i> Create new user</button>


<!-- table -->
<table id="userPrint" class="table table-striped table-top table-auto">

<!-- Headers -->
<tr>
	<th></th>
    <th>Real Name</th>
    <th>Username</th>
    <th>E-mail</th>
    <th>Role</th>
    <th>Type</th>
    <th>Groups</th>
    <th></th>
</tr>

<?php
/* print existing sections */
foreach ($users as $user)
{
	print '<tr>' . "\n";
	
	# set icon based on normal user or admin
	if($user['role'] == "Administrator") 	{ print '	<td><img src="css/images/userVader.png" rel="tooltip" title="Administrator"></td>'. "\n"; }
	else 									{ print '	<td><img src="css/images/userTrooper.png" rel="tooltip" title="'. $user['role'] .'"></td>'. "\n";	}
	
	print '	<td>' . $user['real_name'] . '</td>'. "\n";
	print '	<td>' . $user['username']  . '</td>'. "\n";
	print '	<td>' . $user['email']     . '</td>'. "\n";
	print '	<td>' . $user['role']      . '</td>'. "\n";
	
	# local or ldap?
	if($user['domainUser'] == "0") 			{ print '	<td>Local user</td>'. "\n"; }
	else 									{
		if($settings['domainAuth'] == "2") 	{ print '	<td>LDAP user</td>'. "\n"; }
		else 								{ print '	<td>Domain user</td>'. "\n"; }
	}

	# groups
	if($user['role'] == "Administrator") {
	print '	<td>All groups</td>'. "\n";		
	}
	else {
		$groups = json_decode($user['groups'], true);
		$gr = parseUserGroups($groups);
	
		print '	<td>';
		if(sizeof($gr)>0) {
			foreach($gr as $group) {
				print $group['g_name']."<br>";
			}
		}
		print '	</td>'. "\n";
	}
	
	# edit, delete
	print "	<td class='actions'>";
	print "	<div class='btn-group'>";
	print "		<button class='btn btn-small editUser' data-userid='$user[id]' data-action='edit'  ><i class='icon-gray icon-pencil'></i></button>";
	print "		<button class='btn btn-small editUser' data-userid='$user[id]' data-action='delete'><i class='icon-gray icon-remove'></i></button>";
	print "	</div>";
	print "	</td>";
	
	print '</tr>' . "\n";
}

?>

</table>

<div class="alert alert-info alert-absolute">
Adminstrator users will be able to view and edit all all sections and subnets<br>
Normal users will have premissions set based on group access to sections and subnets
</div>