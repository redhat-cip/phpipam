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
    <th colspan="2"></th>
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
	
	# edit, delete
	print "	<td>";
	print "		<button class='btn btn-small editUser' data-userid='$user[id]' data-action='edit'  ><i class='icon-gray icon-pencil'></i> Edit</button>";
	print "		<button class='btn btn-small editUser' data-userid='$user[id]' data-action='delete'><i class='icon-gray icon-remove'></i> Delete</button>";
	print "	<td>";
	
	print '</tr>' . "\n";
}

?>

</table>