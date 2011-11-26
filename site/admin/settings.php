<?php

/**
 *	Site settings
 **************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* fetch all settings */
$settings = getAllSettings();
?>

<!-- title -->
<h3>phpIPAM Server settings</h3>
<form name="settings" id="settings">
<!-- settings talbe -->
<div class="normalTable">
<table class="normalTable settings">

<!-- site settings -->
<tr>
	<th colspan="3">Site settings</th>
</tr>

<!-- site title -->
<tr>
	<td class="title">Site title</th>
	<td>
		<input type="text" size="50"name="siteTitle" value="<?php print $settings['siteTitle']; ?>">
	</td>
	<td class="info">Set site title</td>
</tr>

<!-- site domain -->
<tr>
	<td class="title">Site domain</td>
	<td>
		<input type="text" size="50"name="siteDomain" value="<?php print $settings['siteDomain']; ?>">
	</td>
	<td class="info">Set domain for sending mail notifications</td>
</tr>

<!-- site URL -->
<tr>
	<td class="title">Site URL</td>
	<td>
		<input type="text" size="50"name="siteURL" value="<?php print $settings['siteURL']; ?>">
	</td>
	<td class="info">Set site URL</td>
</tr>

<!-- space holder -->    
<tr class="th">
    <td>&nbsp;</td>
</tr>

<!-- Admin settings -->
<tr>
	<th colspan="3">Admin settings</th>
</tr>

<!-- Admin name -->
<tr>
	<td class="title">Admin name</td>
	<td>
		<input type="text" size="50"name="siteAdminName" value="<?php print $settings['siteAdminName']; ?>">
	</td>
	<td class="info">
		Set administrator name to display when sending mails and for contact info.
	</td>
</tr>

<!-- Admin mail -->
<tr>
	<td class="title">Admin mail</td>
	<td>
		<input type="text" size="50"name="siteAdminMail" value="<?php print $settings['siteAdminMail']; ?>">
	</td>
	<td class="info">
		Set administrator e-mail to display when sending mails and for contact info.
	</td>
</tr>

<!-- space holder -->    
<tr class="th">
    <td>&nbsp;</td>
</tr>

<!-- features -->
<tr>
	<th colspan="3">Feature settings</th>
</tr>

<!-- Domain auth -->
<tr>
	<td class="title">Domain auth</td>
	<td>
		<input type="checkbox" value="1" name="domainAuth" <?php if($settings['domainAuth'] == 1) print 'checked'; ?>>
	</td>
	<td class="info">
		Use domain authentication for users. Requires php LDAP support.<br>
		Set connection settings in admin menu.<br>
	</td>
</tr>

<!-- Tooltips -->
<tr>
	<td class="title">Tooltips</td>
	<td>
		<input type="checkbox" value="1" name="showTooltips" <?php if($settings['showTooltips'] == 1) print 'checked'; ?>>
	</td>
	<td class="info">
		Enable or disable tooltips.
	</td>
</tr>

<!-- IP requests -->
<tr>
	<td class="title">IP request module</td>
	<td>
		<input type="checkbox" value="1" name="enableIPrequests" <?php if($settings['enableIPrequests'] == 1) print 'checked'; ?>>
	</td>
	<td class="info">
		Enable or disable IP request module.
	</td>
</tr>

<!-- VRF -->
<tr>
	<td class="title">Enable VRF support</td>
	<td>
		<input type="checkbox" value="1" name="enableVRF" <?php if($settings['enableVRF'] == 1) print 'checked'; ?>>
	</td>
	<td class="info">
		Enable or disable VRF module.
	</td>
</tr>

<!-- DNS resolving -->
<tr>
	<td class="title">Resolve DNS names</td>
	<td>
		<input type="checkbox" value="1" name="enableDNSresolving" <?php if($settings['enableDNSresolving'] == 1) print 'checked'; ?>>
	</td>
	<td class="info">
	Check reverse dns lookups for IP addresses that do not have hostname in database. (Activating this feature can significantly increase ip address pages loading time!)
	</td>
</tr>

<!-- Disable donation field -->
<tr>
	<td class="title">Hide donation button</td>
	<td>
		<input type="checkbox" value="1" name="donate" <?php if($settings['donate'] == 1) print 'checked'; ?>>
	</td>
	<td class="info">
	Hide donation button.
	</td>
</tr>

<!-- space holder -->    
<tr class="th">
    <td>&nbsp;</td>
</tr>

<!-- Submit -->
<tr class="th">
	<td class="title"></td>
	<td class="submit">
		<input type="submit" value="Save changes">
	</td>
	<td class="info">Save changes</td>
</tr>

</table>
</form>

<!-- result holder -->
<div class="settingsEdit"></div>

</div>