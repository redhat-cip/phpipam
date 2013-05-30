<?php

/**
 * Script to print add / edit / delete group
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get lang details */
$lang = getLangById ($_POST['langid']);
?>


<!-- header -->
<div class="pHeader">
<?php
/**
 * If action is not set get it form post variable!
 */
if($_POST['action'] == "edit")  		{ print _('Edit language'); }
elseif($_POST['action'] == "delete") 	{ print _('Delete language'); }
else {
	/* Set dummy data  */
	$lang['l_code'] = '';
	$lang['l_name']  = '';
	
	print _('Add new language');
}
?>
</div>

<!-- content -->
<div class="pContent">

	<form id="langEdit" name="langEdit">
	<table class="table table-noborder table-condensed">

	<!-- name -->
	<tr>
	    <td><?php print _('Language code'); ?></td> 
	    <td><input type="text" name="l_code" value="<?php print $lang['l_code']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>></td>
    </tr>

    <!-- description -->
    <tr>
    	<td><?php print _('Language name'); ?></td> 
    	<td>
    		<input type="text" name="l_name" value="<?php print $lang['l_name']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>>

    		<input type="hidden" name="l_id" value="<?php print $_POST['langid']; ?>">
    		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
    	</td>   
    </tr>

</table>
</form>

</div>




<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="langEditSubmit"><i class="icon-white <?php if($_POST['action']=="add") { print "icon-plus"; } else if ($_POST['action']=="delete") { print "icon-trash"; } else { print "icon-ok"; } ?>"></i> <?php print ucwords(_($_POST['action'])); ?></button>

	<!-- Result -->
	<div class="langEditResult"></div>
</div>
