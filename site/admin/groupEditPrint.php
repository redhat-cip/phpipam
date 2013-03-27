<?php

/**
 * Script to print add / edit / delete group
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all settings */
$settings = getAllSettings();
?>


<!-- header -->
<div class="pHeader">
<?php
/**
 * If action is not set get it form post variable!
 */
if($_POST['action'] != "add") {
    
    //fetch all group details
    $group = getGroupById($_POST['id']);
    
    print ucwords($_POST['action']) .'  group '. $group['g_name'];
}
else {
	/* Set dummy data  */
	$group['g_name'] = '';
	$group['g_description']  = '';
	
	print 'Add new group';
}
?>
</div>


<!-- content -->
<div class="pContent">

	<form id="groupEdit" name="groupEdit">
	<table class="groupEdit table table-noborder table-condensed">

	<!-- name -->
	<tr>
	    <td>Group name</td> 
	    <td><input type="text" name="g_name" value="<?php print $group['g_name']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>></td>
       	<td class="info">Enter group name</td>
    </tr>

    <!-- description -->
    <tr>
    	<td>Description</td> 
    	<td>
    		<input type="text" name="g_desc" value="<?php print $group['g_desc']; ?>" <?php if($_POST['action'] == "delete") print "readonly"; ?>>

    		<input type="hidden" name="g_id" value="<?php print $_POST['id']; ?>">
    		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
    	</td>   
    	<td class="info">Enter description</td>
    </tr>

</table>
</form>

</div>




<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small <?php if($_POST['action']=="delete") { print "btn-danger"; } else { print "btn-success"; } ?>" id="editGroupSubmit"><i class="icon-white <?php if($_POST['action']=="add") { print "icon-plus"; } else if ($_POST['action']=="delete") { print "icon-trash"; } else { print "icon-ok"; } ?>"></i> <?php print ucwords($_POST['action']); ?> group</button>

	<!-- Result -->
	<div class="groupEditResult"></div>
</div>
