<?php

/**
 *	Print all available VRFs and configurations
 ************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get post */
$vlanPost = $_POST;

/* get all available VRFs */
$vlan = subnetGetVLANdetailsById($_POST['vlanId']);

/* get custom fields */
$custom = getCustomVLANFields();

if ($_POST['action'] == "delete") 	{ $readonly = "readonly"; }
else 								{ $readonly = ""; }

/* set form name! */
if(isset($_POST['fromSubnet'])) { $formId = "vlanManagementEditFromSubnet"; }
else 							{ $formId = "vlanManagementEdit"; }

?>

<!-- header -->
<div class="pHeader"><?php print ucwords($_POST['action']); ?> <?php print _('VLAN'); ?></div>


<!-- content -->
<div class="pContent">
	<form id="<?php print $formId; ?>">
	
	<table id="vlanManagementEdit2" class="table table-noborder table-condensed">
	<!-- number -->
	<tr>
		<td><?php print _('Number'); ?></td>
		<td>
			<input type="text" class="number" name="number" placeholder="<?php print _('VLAN number'); ?>" value="<?php if(isset($vlan['number'])) print $vlan['number']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- hostname  -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>
			<input type="text" class="name" name="name" placeholder="<?php print _('VLAN name'); ?>" value="<?php if(isset($vlan['name'])) print $vlan['name']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" class="description" name="description" placeholder="<?php print _('Description'); ?>" value="<?php if(isset($vlan['description'])) print $vlan['description']; ?>" <?php print $readonly; ?>>
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) { print '<input type="hidden" name="vlanId" value="'. $_POST['vlanId'] .'">'. "\n"; }
			?>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		</td>
	</tr>
	
	<!-- Custom -->
	<?php
	if(sizeof($custom) > 0) {

		print '<tr>';
		print '	<td colspan="2"><hr></td>';
		print '</tr>';

		foreach($custom as $field) {
		
			# replace spaces
		    $field['nameNew'] = str_replace(" ", "___", $field['name']);
			
			print "<tr>";
			print "	<td>$field[name]</td>";
			print "	<td>";
			print "		<input type='text' name='$field[nameNew]' value='".$vlan[$field['name']]."' $readonly>";
			print "	</td>";
			print "</tr>";
		}
	}
	
	?>

	</table>
	</form>

	<?php
	//print delete warning
	if($_POST['action'] == "delete")	{ print "<div class='alert alert-warn'><strong"._('Warning').':</strong> '._('removing VLAN will also remove VLAN reference from belonging subnets')."!</div>"; }
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?> vlanManagementEditFromSubnetButton" id="editVLANsubmit"><i class="icon-white <?php if($_POST['action']=="add") { print "icon-plus"; } else if ($_POST['action']=="delete") { print "icon-trash"; } else { print "icon-ok"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>

	<!-- result -->
	<div class="<?php print $formId; ?>Result"></div>
</div>