<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* verify that user is admin */
checkAdmin();

?>


<h4>Active Directory connection settings</h4>
<hr><br>

<div class="alert alert-info">
Here you can set parameters for connecting to AD for authenticating users. phpIPAM uses <a href="http://adldap.sourceforge.net/">adLADP</a> to authenticate users. If you need additional settings please take a look at functions/adLDAP or check online documentation!
<hr>
<strong>Instructions</strong><br>
First create new user under user management with <u>same username as on AD</u> and set usertype to domain user. Also set proper permissions (Administrator, Opreator, Viewer) for this user.
</div>


<!-- check for ldap support in php! -->
<?php
/* Available extensions */
$availableExt = get_loaded_extensions();
/* check if ldap exists */
if (!in_array("ldap", $availableExt)) { print '<div class="alert alert-error"><strong>Warning:</strong> ldap extension not enabled in php!</div>'; }

?>

<form id="ad">
<table id="ad" class="table table-striped table-top table-hover">

<!-- DC -->
<tr>
	<td>Domain controllers</td>
	<td>
		<input type="text" name="domain_controllers" value="<?php print $adSettings['domain_controllers']; ?>">
		<input type="hidden" name="type" value="1">
	</td>
	<td class="info">Enter domain controllers, separated by ; (default: dc1.domain.local;cd2.domain.local)
	</td>
</tr>

<!-- BasedN -->
<tr>
	<td>Base DN</td>
	<td>
		<input type="text" name="base_dn" value="<?php print $adSettings['base_dn']; ?>">		
	</td>
	<td class="base_dn info"> 
		Enter base DN for LDAP (default: CN=Users,CN=Company,DC=domain,DC=local)<br>
		If this is set to null then adLDAP will attempt to obtain this automatically from the rootDSE
	</td>
</tr>

<!-- Account suffix -->
<tr>
	<td>Account suffix</td>
	<td>
		<input type="text" name="account_suffix" value="<?php print $adSettings['account_suffix']; ?>">			
	</td>
	<td class="info">
		The account suffix for your domain (default: @domain.local)
	</td>
</tr>

<!-- SSL -->
<tr>
	<td>Use SSL</td>
	<td>
		<select name="use_ssl">
			<option value="0" <?php if($adSettings['use_ssl'] == 0) { print 'selected'; } ?>>false</option>
			<option value="1" <?php if($adSettings['use_ssl'] == 1) { print 'selected'; } ?>>true</option>
		</select>
	</td>
	<td class="info">
		Use SSL (LDAPS), your server needs to be setup (default: false), please see<bR>
    	<a href="http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl">http://adldap.sourceforge.net/wiki/doku.php?id=ldap_over_ssl</a>
	</td>
</tr>

<!-- TLS -->
<tr>
	<td>Use TLS</td>
	<td>
		<select name="use_tls">
			<option value="0" <?php if($adSettings['use_tls'] == 0) { print 'selected'; } ?>>false</option>
			<option value="1" <?php if($adSettings['use_tls'] == 1) { print 'selected'; } ?>>true</option>
		</select>
	</td>
	<td class="info">
		If you wish to use TLS you should ensure that useSSL is set to false and vice-versa (default: false)
	</td>
</tr>


<!-- AD port -->
<tr>
	<td>AD port</td>
	<td>
		<input type="text" name="ad_port" value="<?php print $adSettings['ad_port']; ?>">	
	</td>
	<td class="port info">
		The default port for LDAP non-SSL connections (default: 389)
	</td>
</tr>

<!-- submit -->
<tr class="th">
	<td></td>
	<td>
		<input type="button" class="btn btn-small" id="checkAD" value="Test settings">
		<input type="submit" class="btn btn-small" value="Save settings">
	</td>
	<td></td>
</tr>

</table>
</form>


<!-- result -->
<div class="manageADresult"></div>
