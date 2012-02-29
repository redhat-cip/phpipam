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

?>



<div class="normalTable vrfManagementEdit2">
<form id="vrfManagementEdit">
<table class="normalTable vrfManagementEdit2">

<tr class="th">
	<th colspan="2"><?php print $_POST['action']; ?> VRF</th>
</tr>

<?php
if ($_POST['action'] == "delete") 	{ $readonly = "readonly"; }
else 								{ $readonly = ""; }
?>

<!-- hostname  -->
<tr>
	<td>Name</td>
	<td>
		<input type="text" class="name" name="name" placeholder="VRF name" value="<?php if(isset($vrf['name'])) print $vrf['name']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- IP address -->
<tr>
	<td>RD</td>
	<td>
		<input type="text" class="rd" name="rd" placeholder="Route distinguisher" value="<?php if(isset($vrf['rd'])) print $vrf['rd']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- Vendor -->
<tr>
	<td>Description</td>
	<td>
		<input type="text" class="description" name="description" placeholder="Description" value="<?php if(isset($vrf['description'])) print $vrf['description']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- submit -->
<tr>
	<td></td>
	<td>
		<?php
		if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
			print '<input type="hidden" name="vrfId" value="'. $_POST['vrfId'] .'">'. "\n";
		}
		?>
		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		<input type="submit" value="<?php print $_POST['action']; ?> VRF">
	</td>
</tr>

<!-- result -->
<tr>
	<td colspan="2">
		<div class="vrfManagementEditResult"></div>
	</td>
</tr>

</table>
</form>
</div>