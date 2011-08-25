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
<h3>Edit / Delete existing users</h3>
<div class="userPrint normalTable">
<table class="userPrint normalTable">

<!-- Headers -->
<tr class="th">
    <th>Real Name</th>
    <th>Username</th>
    <th>E-mail</th>
    <th>Role</th>
    <th colspan=2></th>
</tr>

<?php
/* print existing sections */
foreach ($users as $user)
{
	print '<tr>' . "\n";
	print '	<td>' . $user['real_name'] . '</td>'. "\n";
	print '	<td>' . $user['username']  . '</td>'. "\n";
	print '	<td>' . $user['email']     . '</td>'. "\n";
	print '	<td>' . $user['role']      . '</td>'. "\n";
	print '	<td class="edit"><img src="css/images/edit.png"   class="Edit"   id="' . $user['id'] . '" title="Edit user '. $user['username'] .'"></td>'. "\n";
	print '	<td class="edit"><img src="css/images/deleteIP.png" class="Delete" id="' . $user['id'] . '" title="Delete user '. $user['username'] .'"></td>'. "\n";
	print '</tr>' . "\n";
}
?>

</table>
</div>

<!-- result holder -->
<div class="userEditLoad"></div>