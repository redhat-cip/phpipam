<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get AD settings */
$adSettings = getADSettings();
?>


<h3>AD domain authentication settings</h3>
Here you can set parameters for connectiong to AD domain controller for authenticating users. phpIPAM uses <a href="http://adldap.sourceforge.net/">adLADP</a> to authenticate users. If you need additional settings please take a look at functions/adLDAP or check online documentation!
<br><br>
<h3>Instructions</h3>
First create new user under user management with <u>same username as on AD</u> and set usertype to domain user. Also set proper permissions (Administrator, Opreator, Viewer) for this user.
<br><br>

<h3>adLDAP Settings</h3>


<!-- check for ldap support in php! -->
<?php
/* Available extensions */
$availableExt = get_loaded_extensions();
/* check if ldap exists */
if (!in_array("ldap", $availableExt)) {
	print '<div class="error">ldap support not enabled in php!</div>';
}

?>

<div class="normalTable ad">
<form id="ad">
<table class="normalTable ad">

<!-- DC -->
<tr>
	<td>Domain controllers</td>
	<td class="info">
		<input type="text" name="domain_controllers" value="<?php print $adSettings['domain_controllers']; ?>">
		<br>
		Enter domain controllers, separated by ; (default: dc1.domain.local;cd2.domain.local)
	</td>
</tr>

<!-- BasedN -->
<tr>
	<td>Base DN</td>
	<td class="base_dn info"> 
		<input type="text" name="base_dn" value="<?php print $adSettings['base_dn']; ?>">		
		<br>
		Enter base DN for LDAP (default: CN=Users,CN=Company,DC=domain,DC=local)<br>
		If this is set to null then adLDAP will attempt to obtain this automatically from the rootDSE
	</td>
</tr>

<!-- Account suffix -->
<tr>
	<td>Account suffix</td>
	<td class="info">
		<input type="text" name="account_suffix" value="<?php print $adSettings['account_suffix']; ?>">			
		<br>
		The account suffix for your domain (default: @domain.local)
	</td>
</tr>

<!-- SSL -->
<tr>
	<td>Use SSL</td>
	<td class="info">
		<select name="use_ssl">
			<option value="0" <?php if($adSettings['use_ssl'] == 0) { print 'selected'; } ?>>false</option>
			<option value="1" <?php if($adSettings['use_ssl'] == 1) { print 'selected'; } ?>>true</option>
		</select>
		<br>
		Use SSL (LDAPS), your server needs to be setup (default: false), please see<bR>
    	<a href="http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl">http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl</a>
	</td>
</tr>

<!-- TLS -->
<tr>
	<td>Use TLS</td>
	<td class="info">
		<select name="use_tls">
			<option value="0" <?php if($adSettings['use_tls'] == 0) { print 'selected'; } ?>>false</option>
			<option value="1" <?php if($adSettings['use_tls'] == 1) { print 'selected'; } ?>>true</option>
		</select>
		<br>
		If you wish to use TLS you should ensure that useSSL is set to false and vice-versa (default: false)
	</td>
</tr>


<!-- AD port -->
<tr>
	<td>AD port</td>
	<td class="port info">
		<input type="text" name="ad_port" value="<?php print $adSettings['ad_port']; ?>">				
		<br>
		The default port for LDAP non-SSL connections (default: 389)
	</td>
</tr>

<!-- submit -->
<tr class="th">
	<td></td>
	<td>
		<input type="button" id="checkAD" value="Test settings">
		<input type="submit" value="Save settings">
	</td>
</tr>

<!-- result -->
<tr class="th">
	<td></td>
	<td>
		<div class="manageADresult"></div>
	</td>
</tr>

</table>
</form>
</div>