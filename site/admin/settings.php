<?php

/**
 *	Site settings
 **************************/

/* verify that user is admin */
checkAdmin();

/* fetch all settings */
$settings = getAllSettings();
?>

<!-- title -->
<h4>phpIPAM Server settings</h4>
<hr>

<form name="settings" id="settings">
<table id="settings" class="table table-striped table-condensed table-top">

<!-- site settings -->
<tr class="settings-title">
	<th colspan="3"><h4>Site settings</h4></th>
</tr>

<!-- site title -->
<tr>
	<td>Site title</th>
	<td>
		<input type="text" name="siteTitle" value="<?php print $settings['siteTitle']; ?>">
	</td>
	<td class="info">Set site title</td>
</tr>

<!-- site domain -->
<tr>
	<td>Site domain</td>
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


<!-- Admin settings -->
<tr class="settings-title">
	<th colspan="3"><h4>Admin settings</h4></th>
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



<!-- features -->
<tr class="settings-title">
	<th colspan="3"><h4>Feature settings</h4></th>
</tr>

<!-- Domain auth -->
<tr>
	<td class="title">Auth type</td>
	<td>
		<select name="domainAuth">
			<option value="0" <?php if($settings['domainAuth'] == 0) print 'selected'; ?>>Local authentication only</option>
			<option value="1" <?php if($settings['domainAuth'] == 1) print 'selected'; ?>>AD authentication</option>
			<option value="2" <?php if($settings['domainAuth'] == 2) print 'selected'; ?>>OpenLDAP authentication</option>
		</select>
	</td>
	<td class="info">
		Set authentication type for users. Requires php LDAP support. Set connection settings in admin menu.
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


<!-- Strict mode -->
<tr>
	<td class="title">Strict mode</td>
	<td>
		<input type="checkbox" value="1" name="strictMode" <?php if($settings['strictMode'] == 0) print ''; else print 'checked'; ?>>
	</td>
	<td class="info">
	If strict mode is disabled then no more overlapping subnet checks will be made. Subnets can be nested/created randomly. Anarchy.
	</td>
</tr>

<!-- duplicate VLANs -->
<tr>
	<td class="title">Duplicate VLANs</td>
	<td>
		<input type="checkbox" value="1" name="vlanDuplicate" <?php if($settings['vlanDuplicate'] == 0) print ''; else print 'checked'; ?>>
	</td>
	<td class="info">
	Allow duplicate VLAN numbers.
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

<!-- Output limit -->
<tr>
	<td class="title">IP address print limit</td>
	<td>
		<select name="printLimit" style="width:auto;">
			<?php
			$opts = array(
				"0"=>"Show all",
				"10"=>"10",
				"25"=>"25",
				"50"=>"50",
				"100"=>"100"
			);
			
			foreach($opts as $key=>$line) {
				if($settings['printLimit'] == $key) { print "<option value='$key' selected>$line</option>"; }
				else 								{ print "<option value='$key'>$line</option>"; }
			}
			
			?>
		</select>
	</td>
	<td class="info">
	Number of IP addresses per page
	</td>
</tr>

<!-- Subnet ordering -->
<tr>
	<td class="title">Subnet ordering</td>
	<td>
		<select name="subnetOrdering" style="width:auto;">
			<?php
			$opts = array(
				"subnet,asc"		=> "Subnet, ascending",
				"subnet,desc"		=> "Subnet, descending",
				"description,asc"	=> "Description, ascending",
				"description,desc"	=> "Description, descending",
			);
			
			foreach($opts as $key=>$line) {
				if($settings['subnetOrdering'] == $key) { print "<option value='$key' selected>$line</option>"; }
				else 									{ print "<option value='$key'>$line</option>"; }
			}
			
			?>
		</select>
	</td>
	<td class="info">
	How to order display of subnets
	</td>
</tr>


<!-- Submit -->
<tr class="th">
	<td class="title"></td>
	<td class="submit">
		<input type="submit" class="btn btn-small pull-right" value="Save changes">
	</td>
	<td></td>
</tr>

</table>
</form>

<!-- result holder -->
<div class="settingsEdit"></div>