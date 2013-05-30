<?php

/*
 * Print edit subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* verify post */
CheckReferrer();

/* get all groups */
$groups = getAllGroups();

/* get subnet details */
$subnet = getSubnetDetailsById($_POST['subnetId']);
?>



<!-- header -->
<div class="pHeader"><?php print _('Manage subnet permissions'); ?></div>

<!-- content -->
<div class="pContent">

	<?php print _('Manage permissions for subnet'); ?> <?php print transform2long($subnet['subnet'])."/".$subnet['mask']." ($subnet[description])"; ?>
	<hr>

	<form id="editSubnetPermissions">
	<table class="editSubnetPermissions table table-noborder table-condensed table-hover">

	<?php
	# parse permissions
	if(strlen($subnet['permissions'])>1) {
		$permissons = parseSectionPermissions($subnet['permissions']);
	}
	else {
		$permissons = "";
	}

	# print each group
	foreach($groups as $g) {
		print "<tr>";
		print "	<td>$g[g_name]</td>";
		print "	<td>";
			
		print "<label class='checkbox inline noborder'>";			

		print "	<input type='radio' name='group$g[g_id]' value='0' checked> na";
		if($permissons[$g['g_id']] == "1")	{ print " <input type='radio' name='group$g[g_id]' value='1' checked> ro"; }			
		else								{ print " <input type='radio' name='group$g[g_id]' value='1'> ro"; }	
		if($permissons[$g['g_id']] == "2")	{ print " <input type='radio' name='group$g[g_id]' value='2' checked> rw"; }			
		else								{ print " <input type='radio' name='group$g[g_id]' value='2'> rw"; }			
		print "</label>";

		# hidden
		print "<input type='hidden' name='subnetId' value='$_POST[subnetId]'>";
		
		print "	</td>";
		print "</tr>";
	}
	?>
     
    </table>
    </form> 
    
    <?php
    # print warning if slaves exist
    if(subnetContainsSlaves($_POST['subnetId'])) { print "<div class='alert alert-warning'>"._('Permissions for all nested subnets will be overridden')."!</div>"; }
    ?>
    
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small btn-success editSubnetPermissionsSubmit"><i class="icon-white icon-ok"></i> <?php print _('Set permissions'); ?></button>

	<div class="editSubnetPermissionsResult"></div>
</div>