<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* verify that user is admin */
checkAdmin();

?>


<h4><?php print _('OpenLDAP connection settings'); ?></h4>
<hr><br>

<div class="alert alert-info">
<?php print _('Here you can set parameters for connecting to OpenLDAP for authenticating users. phpIPAM uses'); ?> <a href="http://adldap.sourceforge.net/">adLADP</a> <?php print _('to authenticate users. If you need additional settings please take a look at functions/adLDAP or check online documentation!'); ?>
<hr>
<strong><?php print _('Instructions'); ?></strong><br>
<?php print _('First create new user under user management with <u>same username as on LDAP</u> and set usertype to domain user. Also set proper groups (permissions) for this user.'); ?>
</div>


<!-- check for ldap support in php! -->
<?php
/* Available extensions */
$availableExt = get_loaded_extensions();
/* check if ldap exists */
if (!in_array("ldap", $availableExt)) { print '<div class="alert alert-error"><strong>'._('Warning').':</strong> '._('ldap extension not enabled in php').'!</div>'; }

?>

<form id="ad">
<table id="ad" class="table table-striped table-top">

<!-- DC -->
<tr>
	<td><?php print _('OpenLDAP servers'); ?></td>
	<td>
		<input type="text" name="domain_controllers" value="<?php print $adSettings['domain_controllers']; ?>">
	</td>
	<td class="info"><?php print _('Enter domain controllers, separated by ; (default: dc1.domain.local;cd2.domain.local)'); ?>
	</td>
</tr>

<!-- BasedN -->
<tr>
	<td><?php print _('Base DN'); ?></td>
	<td>
		<input type="text" name="base_dn" value="<?php print $adSettings['base_dn']; ?>">	
		<input type="hidden" name="type" value="2">
	</td>
	<td class="base_dn info"> 
		<?php print _('Enter base DN for LDAP (default: CN=Users,CN=Company,DC=domain,DC=local)'); ?>
	</td>
</tr>


<!-- SSL -->
<tr>
	<td><?php print _('Use SSL'); ?></td>
	<td>
		<select name="use_ssl">
			<option value="0" <?php if($adSettings['use_ssl'] == 0) { print 'selected'; } ?>>false'); ?></option>
			<option value="1" <?php if($adSettings['use_ssl'] == 1) { print 'selected'; } ?>>true'); ?></option>
		</select>
	</td>
	<td class="info">
		<?php print _('Use SSL (LDAPS), your server needs to be setup (default: false), please see'); ?><bR>
    	<a href="http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl">http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl</a>
	</td>
</tr>

<!-- TLS -->
<tr>
	<td><?php print _('Use TLS'); ?></td>
	<td>
		<select name="use_tls">
			<option value="0" <?php if($adSettings['use_tls'] == 0) { print 'selected'; } ?>><?php print _('false'); ?></option>
			<option value="1" <?php if($adSettings['use_tls'] == 1) { print 'selected'; } ?>><?php print _('true'); ?></option>
		</select>
	</td>
	<td class="info">
		<?php print _('If you wish to use TLS you should ensure that useSSL is set to false and vice-versa (default: false)'); ?>
	</td>
</tr>


<!-- AD port -->
<tr>
	<td><?php print _('Server port'); ?></td>
	<td>
		<input type="text" name="ad_port" value="<?php print $adSettings['ad_port']; ?>">	
	</td>
	<td class="port info">
		<?php print _('The default port for LDAP non-SSL connections (default: 389)'); ?>
	</td>
</tr>

<!-- submit -->
<tr class="th">
	<td></td>
	<td>
		<input type="button" class="btn btn-small" id="checkAD" value="<?php print _('Test settings'); ?>">
		<input type="submit" class="btn btn-small" value="<?php print _('Save settings'); ?>">
	</td>
	<td></td>
</tr>

</table>
</form>


<!-- result -->
<div class="manageADresult"></div>
