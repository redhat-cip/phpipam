<?php

/**
 *
 * Usermenu - user can change password and email
 *
 */
 
/* verify that user is authenticated! */
isUserAuthenticated ();

/* get username */
$ipamusername = getActiveUserDetails ();

/* get languages */
$langs = getLanguages ();

/* print hello */
print "<h4>$ipamusername[real_name], "._('here you can change your account details').":</h4>";
print "<hr><br>";

?>



<form id="userModSelf">
<table id="userModSelf" class="table table-striped table-condensed">

<!-- real name -->
<tr>
    <td><?php print _('Real name'); ?></td> 
    <td>
        <input type="text" name="real_name" value="<?php print $ipamusername['real_name']; ?>">
    </td>
    <td class="info"><?php print _('Display name'); ?></td>
</tr>

<!-- username -->
<tr>
    <td><?php print _('E-mail'); ?></td> 
    <td>
        <input type="text" name="email" value="<?php print $ipamusername['email']; ?>">
    </td>
    <td class="info"><?php print _('Email address'); ?></td>
</tr>

<?php
# show pass only to local users!
if($ipamusername['domainUser'] == "0") {
?>
<!-- password -->
<tr>
    <td><?php print _('Password'); ?></td> 
    <td>
        <input type="password" class="userPass" name="password1">
    </td style="white-space:nowrap">   
    <td class="info"><?php print _('Password'); ?> <button id="randomPassSelf" class="btn btn-small"><i class="icon-gray icon-random"></i> <?php print _('Random'); ?></button><span id="userRandomPass" style="padding-left:15px;"></span></td>
</tr>

<!-- password repeat -->
<tr>
    <td><?php print _('Password'); ?> (<?php print _('repeat'); ?>)</td> 
    <td>
        <input type="password" class="userPass" name="password2">
    </td>   
    <td class="info"><?php print _('Re-type password'); ?></td>
</tr>
<?php } ?>

<!-- select language -->
<tr>
	<td><?php print _('Language'); ?></td>
	<td>
		<select name="lang">
			<?php
			foreach($langs as $lang) {
				if($lang['l_id']==$ipamusername['lang'])	{ print "<option value='$lang[l_id]' selected>$lang[l_name]</option>"; }
				else										{ print "<option value='$lang[l_id]'>$lang[l_name]</option>"; }
			}
			?>
		</select>
	</td>
	<td class="info"><?php print _('Select language'); ?></td>
</tr>

<!-- Submit and hidden values -->
<tr class="th">
    <td></td> 
    <td class="submit">
        <input type="hidden" name="userId"     value="<?php print $ipamusername['id']; ?>">
        <input type="submit" class="btn btn-small" value="<?php print _('Save changes'); ?>">
    </td>   
    <td></td>
</tr>

</table>
</form>


<!-- result -->
<div class="userModSelfResult"></div>