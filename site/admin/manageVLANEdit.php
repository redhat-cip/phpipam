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

?>



<div class="normalTable vlanManagementEdit2">
<form id="vlanManagementEdit">
<table class="normalTable vlanManagementEdit2">

<tr class="th">
	<th colspan="2"><?php print ucwords($_POST['action']); ?> VLAN</th>
</tr>

<?php
if ($_POST['action'] == "delete") 	{ $readonly = "readonly"; }
else 								{ $readonly = ""; }
?>

<!-- hostname  -->
<tr>
	<td style="width:20px;">Name</td>
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

<!-- Vendor -->
<tr>
	<td>Description</td>
	<td>
		<input type="text" class="description" name="description" placeholder="Description" value="<?php if(isset($vlan['description'])) print $vlan['description']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- submit -->
<tr>
	<td></td>
	<td>
		<?php
		if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
			print '<input type="hidden" name="vlanId" value="'. $_POST['vlanId'] .'">'. "\n";
		}
		?>
		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		<input type="submit" value="<?php print ucwords($_POST['action']); ?> VLAN">
	</td>
</tr>

<!-- result -->
<tr>
	<td colspan="2">
		<div class="vlanManagementEditResult"></div>
	</td>
</tr>

</table>
</form>
</div>