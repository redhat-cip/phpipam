<?php

/**
 * Script to add users to group
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();

# get group details
$group = getGroupById($_POST['g_id']);

# not in group
$missing = getUsersNotInGroup($_POST['g_id']);
?>


<!-- header -->
<div class="pHeader">Add users to group <?php print $group['g_name'] ?></div>


<!-- content -->
<div class="pContent">

	<?php if(sizeof($missing) > 0) { ?>

	<form id="groupAddUsers" name="groupAddUsers">
	<table class="groupEdit table table-condensed table-hover table-top">
	
	<tr>
		<th>
			<input type="hidden" name="gid" value="<?php print $_POST['g_id']; ?>">
		</th>
		<th>Name</th>
		<th>Username</th>
		<th>Email</th>
	</tr>

	<?php
	foreach($missing as $m) {
		# get details
		$u = getUserDetailsById($m);
		
		print "<tr>";
		
		print "	<td>";
		print "	<input type='checkbox' name='user$u[id]'>";
		print "	</td>";
		
		print "	<td>$u[real_name]</td>";
		print "	<td>$u[username]</td>";
		print "	<td>$u[email]</td>";
		
		print "</tr>";	
	}
	?>

    </table>
    </form>
    
    <?php } else { print "<div class='alert alert-info'>No available users to add to group</div>"; } ?>


</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<?php if(sizeof($missing) > 0) { ?>
	<button class="btn btn-small btn-success" id="groupAddUsersSubmit"><i class="icon-white icon-ok"></i> Add selected users</button>
	<?php } ?>
	<!-- Result -->
	<div class="groupAddUsersResult"></div>
</div>
