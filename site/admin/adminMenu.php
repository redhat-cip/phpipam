<?php
/*
 * Print Admin menu pn left if user is admin
 *************************************************/

/* verify that user is admin */
checkAdmin();

/* get all site settings */
$settings = getAllSettings();
?>


<h4>Server management</h4>
<ul class="nav nav-tabs nav-stacked nav-admin">
	<li <?php if($_REQUEST['adminId'] == "settings") print "class='active'"; ?>>
		<a href="administration/settings/"><i class="icon-chevron-right pull-right icon-gray"></i> Server management</a>
	</li>
	<li <?php if($_REQUEST['adminId'] == "userMod") print "class='active'"; ?>>
		<a href="administration/userMod/"><i class="icon-chevron-right pull-right icon-gray"></i> User management</a>
	</li>
    <?php # show AD conection settings if enabled in config!
    if($settings['domainAuth'] == 1) { ?>
	<li <?php if($_REQUEST['adminId'] == "manageAD") print "class='active'"; ?>>
		<a href="administration/manageAD/"><i class="icon-chevron-right pull-right icon-gray"></i> AD connection settings</a>
	</li>
	<?php } ?>
    <?php # show OpenLDAP connection settings if enabled in config!
    if($settings['domainAuth'] == 2) { ?>
	<li <?php if($_REQUEST['adminId'] == "manageAD") print "class='active'"; ?>>
		<a href="administration/manageAD/"><i class="icon-chevron-right pull-right icon-gray"></i> OpenLDAP connection settings</a>
	</li>
	<?php } ?>
	<li <?php if($_REQUEST['adminId'] == "instructions") print "class='active'"; ?>>
		<a href="administration/instructions/"><i class="icon-chevron-right pull-right icon-gray"></i> Edit instructions</a>
	</li>	
	<li <?php if($_REQUEST['adminId'] == "logs") print "class='active'"; ?>>
		<a href="administration/logs/"><i class="icon-chevron-right pull-right icon-gray"></i> Log files</a>
	</li>	
</ul>



<h4>IP related management</h4>
<ul class="nav nav-tabs nav-stacked nav-admin">
	<li <?php if($_REQUEST['adminId'] == "manageSection") print "class='active'"; ?>>
		<a href="administration/manageSection/"><i class="icon-chevron-right pull-right icon-gray"></i> Section management</a>
	</li>
	<li <?php if($_REQUEST['adminId'] == "manageSubnet") print "class='active'"; ?>>
		<a href="administration/manageSubnet/"><i class="icon-chevron-right pull-right icon-gray"></i> Subnet management</a>
	</li>
	<li <?php if($_REQUEST['adminId'] == "manageSwitches") print "class='active'"; ?>>
		<a href="administration/manageSwitches/"><i class="icon-chevron-right pull-right icon-gray"></i> Switch management</a>
	</li>
	<li <?php if($_REQUEST['adminId'] == "manageVLANs") print "class='active'"; ?>>
		<a href="administration/manageVLANs/"><i class="icon-chevron-right pull-right icon-gray"></i> VLAN management</a>
	</li>
    <?php # show IP request link if enabled in config file!
    if($settings['enableVRF'] == 1) {  ?>
    <li <?php if($_REQUEST['adminId'] == "manageVRF") print "class='active'"; ?>>
		<a href="administration/manageVRF/"><i class="icon-chevron-right pull-right icon-gray"></i> VRF management</a>
	</li>    
    <?php } ?>
    <li <?php if($_REQUEST['adminId'] == "ripeImport") print "class='active'"; ?>>
		<a href="administration/ripeImport/"><i class="icon-chevron-right pull-right icon-gray"></i> RIPE import</a>
	</li>    
    <?php # show IP request link if enabled in config file!  */
    if($settings['enableIPrequests'] == 1) { ?>
    <li <?php if($_REQUEST['adminId'] == "manageRequests") print "class='active'"; ?>>
		<a href="administration/manageRequests/"><i class="icon-chevron-right pull-right icon-gray"></i> IP requests <?php if(($requestNum = countRequestedIPaddresses()) != 0) { print "<span class='ipreqMenu'>$requestNum</span>";} ?></a>
	</li> 
    <?php } ?>
    <li <?php if($_REQUEST['adminId'] == "filterIPFields") print "class='active'"; ?>>
		<a href="administration/filterIPFields/"><i class="icon-chevron-right pull-right icon-gray"></i> Filter IP fields</a>
	</li> 
    <li <?php if($_REQUEST['adminId'] == "customIPFields") print "class='active'"; ?>>
		<a href="administration/customIPFields/"><i class="icon-chevron-right pull-right icon-gray"></i> Custom fields</a>
	</li> 
</ul>


<h4>Other</h4>
<ul class="nav nav-tabs nav-stacked nav-admin">
	<li <?php if($_REQUEST['adminId'] == "versionCheck") print "class='active'"; ?>>
		<a href="administration/versionCheck/"><i class="icon-chevron-right pull-right icon-gray"></i> Version check</a>
	</li>
	<li <?php if($_REQUEST['adminId'] == "verifyDatabase") print "class='active'"; ?>>
		<a href="administration/verifyDatabase/"><i class="icon-chevron-right pull-right icon-gray"></i> Verify database</a>
	</li>
	<li <?php if($_REQUEST['adminId'] == "replaceFields") print "class='active'"; ?>>
		<a href="administration/replaceFields/"><i class="icon-chevron-right pull-right icon-gray"></i> Replace fields</a>
	</li>
	<li <?php if($_REQUEST['adminId'] == "export") print "class='active'"; ?>>
		<a href="administration/export/"><i class="icon-chevron-right pull-right icon-gray"></i> Export database</a>
	</li>
</ul>