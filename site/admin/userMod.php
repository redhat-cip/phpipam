<?php

/**
 * Script to edit / add / delete users
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/**
 * First print table of existing users with edit / delete links!
 */
$users = getAllUsers();
?>

<!-- display existing users -->
<h3>User management</h3>
<div class="userPrint normalTable">
<table class="userPrint normalTable">

<!-- Headers -->
<tr class="th">
	<th></th>
    <th>Real Name</th>
    <th>Username</th>
    <th>E-mail</th>
    <th>Role</th>
    <th>Type</th>
    <th colspan=2></th>
</tr>

<?php
/* print existing sections */
foreach ($users as $user)
{

	print '<tr>' . "\n";
	
	//set icon based on normal user or admin
	if($user['role'] == "Administrator") {
		print '	<td><img src="css/images/userVader.png" title="Administrator"></td>'. "\n";
	}
	else {
		print '	<td><img src="css/images/userTrooper.png" title="'. $user['role'] .'"></td>'. "\n";	
	}
	
	print '	<td>' . $user['real_name'] . '</td>'. "\n";
	print '	<td>' . $user['username']  . '</td>'. "\n";
	print '	<td>' . $user['email']     . '</td>'. "\n";
	print '	<td>' . $user['role']      . '</td>'. "\n";
	
	//local or ldap?
	if($user['domainUser'] == 0) {
	print '	<td>Local</td>'. "\n";
	}
	else {
	print '	<td>Domain</td>'. "\n";
	}
	
	print '	<td class="edit"><img src="css/images/edit.png"   class="Edit"   id="' . $user['id'] . '" title="Edit user '. $user['username'] .'"></td>'. "\n";
	print '	<td class="edit"><img src="css/images/deleteIP.png" class="Delete" id="' . $user['id'] . '" title="Delete user '. $user['username'] .'"></td>'. "\n";
	print '</tr>' . "\n";
}

?>

<!-- add new -->
<tr class="add th">
	<td colspan="8" class="info edit">
	<img src="css/images/add.png" class="Add" title="Add new User">
	Add new user
	</td>
</tr>

</table>
</div>

<!-- result holder -->
<div class="userEditLoad"></div>