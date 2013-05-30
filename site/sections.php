<!--[if lt IE 9]>
<style type="text/css">
.tooltipBottom,
.tooltipLeft,
.tooltipTop,
.tooltipTopDonate,
.tooltip,
.tooltipRightSubnets { 
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e61d2429', endColorstr='#b3293339',GradientType=0 );
}
.tooltipBottom {
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e61d2429', endColorstr='#b3293339',GradientType=0 );
}
</style>
<![endif]-->


<?php

/**
 * Script to print sections and admin link on top of page
 ********************************************************/

/* use scripts, but only if requested through post! */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once( dirname(__FILE__) . '/../functions/functions.php' );
}

/* verify that user is authenticated! */
isUserAuthenticated ();

/* fetch result */
$sections = fetchSections ();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

?>

<!-- Section nabvigation -->
<div class="navbar">
<div class="navbar-inner">


	<table class="nav">
	<tr>
		<td>
		
		<!-- hide when too small -->
		<div class="nav-collapse">
		<!-- sections -->
		<ul class="nav nav-tabs sections">
			<?php
			# if section is not set
			if(!isset($_REQUEST['section'])) { $_REQUEST['section'] = ""; }
			
			foreach($sections as $section) {
				# check permissions for user
				$perm = checkSectionPermission ($section['id']);
				if($perm == "1" || $perm == "2") {
					if( ($section['name'] == $_REQUEST['section']) || ($section['id'] == $_REQUEST['section']) ) 	{ print "<li class='active'>"; }
					else 																							{ print "<li>"; }
				
					print "	<a href='subnets/$section[id]/' rel='tooltip' data-placement='bottom' title='"._('Show all subnets in section')." $section[name]'>$section[name]</a>";
					print "</li>";
				}
			}
			?>
		</ul>		
		</div>
	</td>

	<!-- instructions -->	
	<td class="fixed instr">	
		<ul class="nav nav-tabs">
			<li>
				<a href="tools/instructions/" rel='tooltip' data-placement='bottom' title="<?php print _('Show IP addressing guide'); ?>"><img src="css/images/info.png" style="width:20px;"></a>
			</li>
		</ul>
	</td>

	<!-- Tools -->
	<td class="fixed">
	    <ul class="nav nav-tabs pull-right">
	    	<li class="dropdown">
	    		<a class="dropdown-toggle topmenulink" data-toggle="dropdown" href="" rel='tooltip' data-placement='bottom' title='<?php print _('Show tools menu'); ?>'><i class="icon-wrench icon-white"></i> <?php print _('Tools'); ?> <b class="caret"></b></a>
	    		<ul class="dropdown-menu tools">
	    			<!-- public -->
	    			<li class="nav-header"><?php print _('Available IPAM tools'); ?> </li>
	    			<!-- private -->
	    			<?php
	    				# if adminId is not set
	    				if(!isset($_REQUEST['toolsId'])) { $_REQUEST['toolsId'] = ""; }
			    		
			    		print "	<li "; if($_REQUEST['toolsId'] == "ipCalc") 	print "class='active'"; print "><a href='tools/ipCalc/'>"._('IP calculator')."</a></li>"; 
				    	print "	<li "; if($_REQUEST['toolsId'] == "devices") 	print "class='active'"; print "><a href='tools/devices/'>"._('Devices')."</a></li>";
				    	if($settings['enableVRF'] == 1) {									# print VRFs if enabled
				    	print "	<li "; if($_REQUEST['toolsId'] == "vrf") 		print "class='active'"; print "><a href='tools/vrf/'>"._('VRFs')."</a></li>"; 
					    }
				    	print "	<li "; if($_REQUEST['toolsId'] == "vlan") 		print "class='active'"; print "><a href='tools/vlan/'>"._('VLANs')."</a></li>"; 	
				    	print "	<li "; if($_REQUEST['toolsId'] == "subnets") 	print "class='active'"; print "><a href='tools/subnets/'>"._('Subnets')."</a></li>"; 
				    	print "	<li "; if($_REQUEST['toolsId'] == "search") 	print "class='active'"; print "><a href='tools/search/'>"._('Search')."</a></li>"; 
				    	print "	<li class='divider'></li>";
				    	print "	<li><a href='tools/'>"._('Show all tools')."</a></li>";	
	
	    			?>
	    		</ul>
	    	</li>
	    </ul>
	</td>
	
	    <?php
	    # print admin menu if admin user and don't die!
		if(checkAdmin(false)) {
			# if adminId is not set
			if(!isset($_REQUEST['adminId'])) { $_REQUEST['adminId'] = ""; }
		
			print '<td class="fixed">';
			print "<ul class='nav nav-tabs' style='margin-right:0px;'>";
			print "	<li class='dropdown'>";
			# title
			print "	<a class='dropdown-toggle btn-danger' data-toggle='dropdown' href='administration/' id='admin' rel='tooltip' data-placement='bottom' title='"._('Show Administration menu')."'><i class='icon-cog icon-white'></i> "._('Administration')." <b class='caret'></b></a>";
			# dropdown
			print "		<ul class='dropdown-menu admin'>";
			
			# show IP request link if enabled in config file!
			if($settings['enableIPrequests'] == 1) {    
				$requestNum = countRequestedIPaddresses();
				if($requestNum != 0) {
					print "<li class='nav-header'>IP address requests</li>";
					print "<li "; if($_REQUEST['adminId'] == "manageRequests") print "class='active'"; print "><a href='administration/manageRequests/'>"._('IP requests')." ($requestNum)</a></li>";
					print "<li class='divider'></li>";
				}
			}
			print "		<li class='nav-header'>"._('Server management')."</li>";
			print "		<li "; if($_REQUEST['adminId'] == "manageRequests") print "class='active'"; print "><a href='administration/settings/'>"._('IPAM settings')."</a></li>";
			print "		<li "; if($_REQUEST['adminId'] == "users") 			print "class='active'"; print "><a href='administration/users/'>"._('Users')."</a></li>";
			print "		<li "; if($_REQUEST['adminId'] == "groups") 		print "class='active'"; print "><a href='administration/groups/'>"._('Groups')."</a></li>";
			print "		<li "; if($_REQUEST['adminId'] == "logs") 			print "class='active'"; print "><a href='administration/logs/'>"._('Log files')."</a></li>";

			print "		<li class='divider'></li>";
			print "		<li class='nav-header'>"._('IP related settings')."</li>";
			print "		<li "; if($_REQUEST['adminId'] == "manageSection") 	print "class='active'"; print "><a href='administration/manageSection/'>"._('Sections')."</a></li>";
			print "		<li "; if($_REQUEST['adminId'] == "manageSubnet") 	print "class='active'"; print "><a href='administration/manageSubnet/'>"._('Subnets')."</a></li>";
			print "		<li "; if($_REQUEST['adminId'] == "manageDevices") 	print "class='active'"; print "><a href='administration/manageDevices/'>"._('Devices')."</a></li>";
			print "		<li "; if($_REQUEST['adminId'] == "manageVLANs") 	print "class='active'"; print "><a href='administration/manageVLANs/'>"._('VLANs')."</a></li>";
			# vrf if enabled
			if($settings['enableVRF'] == 1) { 
			print "		<li "; if($_REQUEST['adminId'] == "manageVRF") 		print "class='active'"; print "><a href='administration/manageVRF/'>"._('VRF')."</a></li>";
			}
			print "		<li class='divider'></li>";
			print "		<li><a href='administration/'>"._('Show all settings')."</a></li>";		
			print "		</ul>";
			
			print "	</li>";
			print "</ul>";
			print "</td>";
		}
	    
	    ?>

	</tr>
	</table>
				    
</div>
</div>

<?php



?>