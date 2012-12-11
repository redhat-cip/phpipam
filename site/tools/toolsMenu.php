<?php

/**
 * Script to display switches
 *
 */

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all unique switches */
$settings = getAllSettings();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);
/* viewer check */
$viewer = isUserViewer();
?>



<h4>Tools</h4>
<ul class="nav nav-tabs nav-stacked nav-tools">
	<li <?php if($_REQUEST['toolsId'] == "ipCalc") print "class='active'"; ?>>
		<a href="tools/ipCalc/"><i class="icon-chevron-right pull-right icon-gray"></i> IP calculator</a>
	</li>
	<li <?php if($_REQUEST['toolsId'] == "instructions") print "class='active'"; ?>>
		<a href="tools/instructions/"><i class="icon-chevron-right pull-right icon-gray"></i> Instructions</a>
	</li>    
	<li <?php if($_REQUEST['toolsId'] == "search") print "class='active'"; ?>>
		<a href="tools/search/"><i class="icon-chevron-right pull-right icon-gray"></i> Search</a>
	</li>
</ul>

<?php # for non-viewers only
if(!$viewer)  {  ?>
<h4>Devices</h4>
<ul class="nav nav-tabs nav-stacked nav-tools">
    <?php # if switch enabled
    if(in_array("switch", $setFields)) {?>    
	<li <?php if($_REQUEST['toolsId'] == "switches") print "class='active'"; ?>>
		<a href="tools/switches/"><i class="icon-chevron-right pull-right icon-gray <?php if($_REQUEST['toolsId'] != "switches") print "icon-white"; ?>"></i> Switches</a>
	</li>
    <?php } ?>
    <?php # if vrf enabled
    if($settings['enableVRF'] == 1) { ?>
	<li <?php if($_REQUEST['toolsId'] == "vrf") print "class='active'"; ?>>
		<a href="tools/vrf/"><i class="icon-chevron-right pull-right icon-gray"></i> VRF</a>
	</li>    
    <?php } ?>
	<li <?php if($_REQUEST['toolsId'] == "hosts") print "class='active'"; ?>>
		<a href="tools/hosts/"><i class="icon-chevron-right pull-right icon-gray"></i> Hosts</a>
	</li>   
	<li <?php if($_REQUEST['toolsId'] == "vlan") print "class='active'"; ?>>
		<a href="tools/vlan/"><i class="icon-chevron-right pull-right icon-gray"></i> VLANs</a>
	</li>   
	<li <?php if($_REQUEST['toolsId'] == "subnets") print "class='active'"; ?>>
		<a href="tools/subnets/"><i class="icon-chevron-right pull-right icon-gray"></i> Subnets</a>
	</li>  
</ul>
<?php } ?>



<h4>User menu</h4>
<ul class="nav nav-tabs nav-stacked nav-tools">
	<li <?php if($_REQUEST['toolsId'] == "userMenu") print "class='active'"; ?>>
		<a href="tools/userMenu/"><i class="icon-chevron-right pull-right icon-gray"></i> My account</a>
	</li>  
</ul>