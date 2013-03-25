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
<div class="pHeader"><?php print ucwords($_POST['action']); ?> VLAN</div>


<!-- content -->
<div class="pContent">
	<form id="<?php print $formId; ?>">
	
	<table id="vlanManagementEdit2" class="table table-noborder table-condensed">
	<!-- hostname  -->
	<tr>
		<td>Name</td>
		<td>
			<input type="text" class="name" name="name" placeholder="VLAN name" value="<?php if(isset($vlan['name'])) print $vlan['name']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- number -->
	<tr>
		<td>Number</td>
		<td>
			<input type="text" class="number" name="number" placeholder="VLAN number" value="<?php if(isset($vlan['number'])) print $vlan['number']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Description -->
	<tr>
		<td>Description</td>
		<td>
			<input type="text" class="description" name="description" placeholder="Description" value="<?php if(isset($vlan['description'])) print $vlan['description']; ?>" <?php print $readonly; ?>>
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) { print '<input type="hidden" name="vlanId" value="'. $_POST['vlanId'] .'">'. "\n"; }
			?>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		</td>
	</tr>
	
	<!-- Custom -->
	<?php
	if(sizeof($custom) > 0) {
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
	if($_POST['action'] == "delete")	{ print "<div class='alert alert-warn'><strong>Warning:</strong> removing VLAN will also remove VLAN reference from belonging subnets!</div>"; }
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small <?php if($_POST['action']=="delete") print "btn-danger" ?> vlanManagementEditFromSubnetButton" id="editVLANsubmit"><i class="icon-gray <?php if($_POST['action']=="delete") print "icon-white" ?> icon-ok"></i> <?php print ucwords($_POST['action']); ?> VLAN</button>

	<!-- result -->
	<div class="<?php print $formId; ?>Result"></div>
</div>