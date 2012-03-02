<?php

/** 
 * Edit custom IP field
 ************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* reset field name for add! */
if($_POST['action'] == "add") {
	$_POST['fieldName'] = "";
}
else {
	$_POST['oldname'] = $_POST['fieldName'];
}
?>


<div class="normalTable editCustomIPFields" style="width:300px;">
<form id="editCustomIPFields">
<table class="normalTable editCustomIPFields">

<!-- name -->
<tr>
	<td>Name</td>
	<td>
		<input type="text" name="name" value="<?php print $_POST['fieldName']; ?>" placeholder="Select field name" <?php if($_POST['action'] == "delete") { print 'readonly'; } ?>>
	</td>
</tr>

<!-- submit -->
<tr class="th">
	<td></td>
	<td>
		<input type="hidden" name="oldname" value="<?php print $_POST['oldname']; ?>">
		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		<input type="submit" value="<?php print ucwords($_POST['action']); ?> field">
	</td>
</tr>

</table>
</form>
</div>

<!-- result -->
<div class="customIPEditResult"></div>