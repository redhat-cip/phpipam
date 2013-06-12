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


<div class="pHeader"><?php print ucwords(_("$_POST[action]")); ?> <?php print _('custom User field'); ?></div>


<div class="pContent">

	<form id="editCustomUserFields">
	<table id="editCustomUserFields" class="table table-noborder">

	<!-- name -->
	<tr>
		<td><?php print _('Name'); ?></td>
		<td>	
			<input type="text" name="name" value="<?php print $_POST['fieldName']; ?>" placeholder="<?php print _('Select field name'); ?>" <?php if($_POST['action'] == "delete") { print 'readonly'; } ?>>
			
			<input type="hidden" name="oldname" value="<?php print $_POST['oldname']; ?>">
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		</td>
	</tr>

	</table>
	</form>	
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Close'); ?></button>
	<button class="btn btn-small <?php if($_POST['action']=="delete") print "btn-danger" ?>" id="editcustomUserSubmit"><i class="icon-gray <?php if($_POST['action']=="delete") print "icon-white" ?> icon-ok"></i> <?php print ucwords(_($_POST['action'])); ?></button>

	<!-- result -->
	<div class="customUserEditResult"></div>
</div>