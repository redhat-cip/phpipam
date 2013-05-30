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
?>



<h4><?php print _('Tools'); ?></h4>
<ul class="nav nav-tabs nav-stacked nav-tools">
	<li <?php if($_REQUEST['toolsId'] == "ipCalc") print "class='active'"; ?>>
		<a href="tools/ipCalc/"><i class="icon-chevron-right pull-right icon-gray"></i> <?php print _('IP calculator'); ?></a>
	</li>
	<li <?php if($_REQUEST['toolsId'] == "instructions") print "class='active'"; ?>>
		<a href="tools/instructions/"><i class="icon-chevron-right pull-right icon-gray"></i> <?php print _('Instructions'); ?></a>
	</li>    
	<li <?php if($_REQUEST['toolsId'] == "search") print "class='active'"; ?>>
		<a href="tools/search/"><i class="icon-chevron-right pull-right icon-gray"></i> <?php print _('Search'); ?></a>
	</li>
</ul>

<h4><?php print _('Devices'); ?></h4>
<ul class="nav nav-tabs nav-stacked nav-tools">    
	<li <?php if($_REQUEST['toolsId'] == "devices") print "class='active'"; ?>>
		<a href="tools/devices/"><i class="icon-chevron-right pull-right icon-gray "></i> <?php print _('Devices'); ?></a>
	</li>
    <?php # if vrf enabled
    if($settings['enableVRF'] == 1) { ?>
	<li <?php if($_REQUEST['toolsId'] == "vrf") print "class='active'"; ?>>
		<a href="tools/vrf/"><i class="icon-chevron-right pull-right icon-gray"></i> <?php print _('VRF'); ?></a>
	</li>    
    <?php } ?> 
	<li <?php if($_REQUEST['toolsId'] == "vlan") print "class='active'"; ?>>
		<a href="tools/vlan/"><i class="icon-chevron-right pull-right icon-gray"></i> <?php print _('VLANs'); ?></a>
	</li>   
	<li <?php if($_REQUEST['toolsId'] == "subnets") print "class='active'"; ?>>
		<a href="tools/subnets/"><i class="icon-chevron-right pull-right icon-gray"></i> <?php print _('Subnets'); ?></a>
	</li>  
</ul>



<h4><?php print _('User menu'); ?></h4>
<ul class="nav nav-tabs nav-stacked nav-tools">
	<li <?php if($_REQUEST['toolsId'] == "userMenu") print "class='active'"; ?>>
		<a href="tools/userMenu/"><i class="icon-chevron-right pull-right icon-gray"></i> <?php print _('My account'); ?></a>
	</li>  
</ul>