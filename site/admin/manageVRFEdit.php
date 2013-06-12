<?php

/**
 *	Print all available VRFs and configurations
 ************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get post */
$vrfPost = $_POST;

/* get all available VRFs */
$vrf = getVRFDetailsById ($vrfPost['vrfId']);

/* disable edit on delete */
if ($_POST['action'] == "delete") 	{ $readonly = "readonly"; }
else 								{ $readonly = ""; }
?>


<!-- header -->
<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('VRF'); ?></div>

<!-- content -->
<div class="pContent">

	<form id="vrfManagementEdit">
	<table id="vrfManagementEdit2" class="table table-noborder table-condensed">

	<!-- hostname  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" class="name" name="name" placeholder="<?php print _('VRF name'); ?>" value="<?php if(isset($vrf['name'])) print $vrf['name']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>
	<!-- IP address -->
	<tr>
		<td><?php print _('RD'); ?></td>
		<td>
			<input type="text" class="rd" name="rd" placeholder="<?php print _('Route distinguisher'); ?>" value="<?php if(isset($vrf['rd'])) print $vrf['rd']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Vendor -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) { print '<input type="hidden" name="vrfId" value="'. $_POST['vrfId'] .'">'. "\n";}
			?>		
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">			
			<input type="text" class="description" name="description" placeholder="<?php print _('Description'); ?>" value="<?php if(isset($vrf['description'])) print $vrf['description']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>
	
	</table>
	</form>

	<?php
	//print delete warning
	if($_POST['action'] == "delete")	{ print "<div class='alert alert-warn'><strong>Warning:</strong> removing VRF will also remove VRF reference from belonging subnets!</div>"; }
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editVRF"><i class="icon-white <?php if($_POST['action']=="add") { print "icon-plus"; } else if ($_POST['action']=="delete") { print "icon-trash"; } else { print "icon-ok"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>
	<!-- result -->
	<div class="vrfManagementEditResult"></div>
</div>