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
<div class="pHeader"><?php print ucwords($_POST['action']); ?> VRF</div>

<!-- content -->
<div class="pContent">

	<form id="vrfManagementEdit">
	<table id="vrfManagementEdit2" class="table table-noborder table-condensed">

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
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) { print '<input type="hidden" name="vrfId" value="'. $_POST['vrfId'] .'">'. "\n";}
			?>		
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">			
			<input type="text" class="description" name="description" placeholder="Description" value="<?php if(isset($vrf['description'])) print $vrf['description']; ?>" <?php print $readonly; ?>>
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
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small" id="editVRF"><i class="icon-gray icon-ok"></i> <?php print ucwords($_POST['action']); ?> VRF</button>
	<!-- result -->
	<div class="vrfManagementEditResult"></div>
</div>