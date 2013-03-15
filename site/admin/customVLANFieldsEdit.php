<?php

/** 
 * Edit custom IP field
 ************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* reset field name for add! */
if($_POST['action'] == "add") 	{ $_POST['fieldName'] = ""; }
else 							{ $_POST['oldname'] = $_POST['fieldName'];}
?>


<div class="pHeader"><?php print ucwords($_POST['action']); ?> custom VLAN field</div>


<div class="pContent">

	<form id="editCustomVLANFields">
	<table id="editCustomVLANFields" class="table table-noborder">

	<!-- name -->
	<tr>
		<td>Name</td>
		<td>	
			<input type="text" name="name" value="<?php print $_POST['fieldName']; ?>" placeholder="Select field name" <?php if($_POST['action'] == "delete") { print 'readonly'; } ?>>
			
			<input type="hidden" name="oldname" value="<?php print $_POST['oldname']; ?>">
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		</td>
	</tr>

	</table>
	</form>	
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Close</button>
	<button class="btn btn-small" id="editcustomVLANSubmit"><i class="icon-gray icon-ok"></i> <?php print ucwords($_POST['action']); ?> field</button>

	<!-- result -->
	<div class="customVLANEditResult"></div>
</div>