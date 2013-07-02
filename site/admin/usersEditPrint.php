<?php

/**
 * Script to print add / edit / delete users
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();

/* get custom fields */
$custom = getCustomUserFields();

/* get languages */
$langs = getLanguages ();
?>


<!-- header -->
<div class="pHeader">
<?php
/**
 * If action is not set get it form post variable!
 */
if (!$action) {
    $action = $_POST['action'];
    $id     = $_POST['id'];
    
    //fetch all requested userdetails
    $user = getUserDetailsById($id);
    
    if(!empty($user['real_name'])) 	{ print _("$action user").' '. $user['real_name']; }
    else 							{ print _('Add new user'); }
}
else {
	/* Set dummy data  */
	$user['real_name'] = '';
	$user['username']  = '';
	$user['email']     = '';
	$user['password']  = '';
	
	print _('Add new user');
}

# set default language
if(isset($settings['defaultLang']) && !is_null($settings['defaultLang']) && $action=="add" ) {
	$user['lang']=$settings['defaultLang'];
}
?>
</div>


<!-- content -->
<div class="pContent">

	<form id="usersEdit" name="usersEdit">
	<table class="usersEdit table table-noborder table-condensed">

	<!-- real name -->
	<tr>
	    <td><?php print _('Real name'); ?></td> 
	    <td><input type="text" name="real_name" value="<?php print $user['real_name']; ?>"></td>
       	<td class="info"><?php print _('Enter users real name'); ?></td>
    </tr>

    <!-- username -->
    <tr>
    	<td><?php print _('Username'); ?></td> 
    	<td><input type="text" name="username" value="<?php print $user['username']; ?>" <?php if($action == "edit" || $action == "delete") print 'readonly'; ?>></td>   
    	<td class="info"><?php print _('Enter username'); ?></td>
    </tr>

    <!-- email -->
    <tr>
    	<td><?php print _('e-mail'); ?></td> 
    	<td><input type="text" name="email" value="<?php print $user['email']; ?>"></td>
    	<td class="info"><?php print _('Enter users email address'); ?></td>
    </tr>

<!-- type -->
<?php
/* if domainauth is not enabled default to local user */
if($settings['domainAuth'] == 0) {
	if($_POST['action'] == "add") 	{ print '<input type="hidden" name="domainUser" value="0">'. "\n"; }
	else 							{ print '<input type="hidden" name="domainUser" value="'. $user['domainUser'] .'">'. "\n"; }
}
else {

	print '<tr>'. "\n";
    print '	<td>'._('User Type').'</td> '. "\n";
    print '	<td>'. "\n";
    print '	<select name="domainUser" id="domainUser">'. "\n";
    print '	<option value="0" '. "\n";
    		if ($user['domainUser'] == "0") print "selected"; 
    print '	>'._('Local user').'</option>'. "\n";
    print '	<option value="1" '. "\n"; 
    		if ($user['domainUser'] == "1") print "selected"; 
    print '	>'._('Domain user').'</option> '. "\n";
    print '	</select>'. "\n";
    print '	</td> '. "\n";
    print '	<td class="info">'._('Set user type').''. "\n";
    print '	<ul>'. "\n";
    print '		<li>'._('Local authenticates here').'</li>'. "\n";
    print '		<li>'._('Domain authenticates on AD, but still needs to be setup here for permissions etc.').'</li>'. "\n";
    print '	</ul>'. "\n";
    print '	</td>  '. "\n";
	print '</tr>'. "\n";

}
	if ($user['domainUser'] == "1") { $disabled = "disabled"; }
	else 							{ $disabled = ""; }
?>

	<!-- Language -->
	<tr>
		<td><?php print _('Language'); ?></td>
		<td>
			<select name="lang">
				<?php
				foreach($langs as $lang) {
					if($lang['l_id']==$user['lang'])	{ print "<option value='$lang[l_id]' selected>$lang[l_name] ($lang[l_code])</option>"; }
					else								{ print "<option value='$lang[l_id]'		 >$lang[l_name] ($lang[l_code])</option>"; }
				}
				?>
			</select>
		</td>
		<td class="info"><?php print _('Select language'); ?></td>
	</tr>

    <!-- password -->
    <tr class="password">
    	<td><?php print _('Password'); ?></td> 
    	<td><input type="password" class="userPass" name="password1" <?php print $disabled; ?>></td>
    	<td class="info"><?php print _("User's password"); ?> (<a href="#" id="randomPass"><?php print _('click to generate random'); ?>!</a>)</td>
    </tr>

    <!-- password repeat -->
    <tr class="password">
    	<td><?php print _('Password'); ?></td> 
    	<td><input type="password" class="userPass" name="password2" <?php print $disabled; ?>></td>   
    	<td class="info"><?php print _('Re-type password'); ?></td>
    </tr>

    <!-- send notification mail -->
    <tr>
    	<td><?php print _('Notification'); ?></td> 
    	<td><input type="checkbox" name="notifyUser" <?php if($action == "add") { print 'checked'; } else if($action == "delete") { print 'disabled="disabled"';} ?>></td>   
    	<td class="info"><?php print _('Send notification email to user with account details'); ?></td>
    </tr>

    <!-- role -->
    <tr>
    	<td><?php print _('User role'); ?></td> 
    	<td>
        <select name="role">
            <option value="Administrator"   <?php if ($user['role'] == "Administrator") print "selected"; ?>><?php print _('Administrator'); ?></option>
            <option value="User" 			<?php if ($user['role'] == "User" || $_POST['action'] == "add") print "selected"; ?>><?php print _('Normal User'); ?></option>
        </select>
        
        
        <input type="hidden" name="userId" value="<?php if(isset($user['id'])) { print $user['id']; } ?>">
        <input type="hidden" name="action" value="<?php print $action; ?>">
        
        </td> 
        <td class="info"><?php print _('Select user role'); ?>
	    	<ul>
		    	<li><?php print _('Administrator is almighty'); ?></li>
		    	<li><?php print _('Users have access defined based on groups'); ?></li>
		    </ul>
		</td>  
	</tr>
	
	<!-- groups -->
	<tr>
		<td><?php print _('Groups'); ?></td>
		<td class="groups">
		<?php
		$groups = getAllGroups();		# all groups
		$ugroups = json_decode($user['groups'], true);
		$ugroups = parseUserGroupsIds($ugroups);
		
		if(sizeof($groups) > 0) {
			foreach($groups as $g) {
				# empty fix
				if(sizeof($ugroups) > 0) {	
					if(in_array($g['g_id'], $ugroups)) 	{ print "<input type='checkbox' name='group$g[g_id]' checked>$g[g_name]<br>"; }
					else 								{ print "<input type='checkbox' name='group$g[g_id]'>$g[g_name]<br>"; }
				}
				else {
														{ print "<input type='checkbox' name='group$g[g_id]'>$g[g_name]<br>"; }
				}
			}
		}
		else {
			print _("No groups configured");
		}
		
		?>
		</td>
		<td class="info"><?php print _('Select to which groups the user belongs to'); ?></td>
	</tr>

	<!-- Custom -->
	<?php
	if(sizeof($custom) > 0) {
		print '<tr>';
		print '	<td colspan="3"><hr></td>';
		print '</tr>';
		
		foreach($custom as $field) {
		
			# replace spaces
		    $field['nameNew'] = str_replace(" ", "___", $field['name']);
			
			print "<tr>";
			print "	<td>$field[name]</td>";
			print "	<td colspan='2'>";
			print "		<input type='text' class='input-xlarge' name='$field[nameNew]' value='".$user[$field['name']]."' $readonly>";
			print "	</td>";
			print "</tr>";
		}
	}
	
	?>

	
</table>
</form>

</div>




<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editUserSubmit"><i class="icon-white <?php if($_POST['action']=="add") { print "icon-plus"; } else if ($_POST['action']=="delete") { print "icon-trash"; } else { print "icon-ok"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>

	<!-- Result -->
	<div class="usersEditResult"></div>
</div>
