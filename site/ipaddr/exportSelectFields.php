<?php 
/* use required functions */
require_once('../../functions/functions.php'); 
?>

<!-- header -->
<div class="pHeader"><?php print _("Select fields to export"); ?></div>


<!-- content -->
<div class="pContent">

<?php
/* verify that user is authenticated! */
isUserAuthenticated (false);

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

/* get all site settings */
$settings = getAllSettings();

# permissions
$permission = checkSubnetPermission ($_POST['subnetId']);

if($permission < 1) { die("<div class='alert alert-error'>"._('You cannot access this subnet')."!</div>"); }
		
# print 
print '<form id="selectExportFields">';
	
# table
print "	<table class='table table-striped table-condensed'>";
	
# IP addr - mandatory
print "	<tr>";
print "	<td>"._('IP address')."</td>";
print "	<td><input type='checkbox' name='ip_addr' checked> </td>";
print "	</tr>";	
	
# state
if(in_array('state', $setFields)) 		{ 
print "	<tr>";
print "	<td>"._('IP state')."</td>";
print "	<td><input type='checkbox' name='state' checked> </td>";
print "	</tr>";	
}
# description - mandatory
print "	<tr>";
print "	<td>"._('Description')."</td>";
print "	<td><input type='checkbox' name='description' checked> </td>";
print "	</tr>";
# hostname - mandatory
print "	<tr>";
print "	<td>"._('Hostname')."</td>";
print "	<td><input type='checkbox' name='dns_name' checked> </td>";
print "	</tr>";	
# mac 
if(in_array('mac', $setFields)) 		{ 
print "	<tr>";
print "	<td>"._('MAC address')."</td>";
print "	<td><input type='checkbox' name='mac' checked> </td>";
print "	</tr>";		 
}
# owner
if(in_array('owner', $setFields)) 		{ 
print "	<tr>";
print "	<td>"._('Owner')."</td>";
print "	<td><input type='checkbox' name='owner' checked> </td>";
print "	</tr>";	
}
# switch
if(in_array('switch', $setFields)) 		{ 
print "	<tr>";
print "	<td>"._('Switch')."</td>";
print "	<td><input type='checkbox' name='switch' checked> </td>";
print "	</tr>";	
}	
# port
if(in_array('port', $setFields)) 		{ 
print "	<tr>";
print "	<td>"._('Port')."</td>";
print "	<td><input type='checkbox' name='port' checked> </td>";
print "	</tr>";	
}
# note
if(in_array('note', $setFields)) 		{ 
print "	<tr>";
print "	<td>"._('Note')."</td>";
print "	<td><input type='checkbox' name='note' checked> </td>";
print "	</tr>";		
}
		
#get all custom fields!
$myFields = getCustomIPaddrFields();
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
	print "	<tr>";
	print "	<td>$myField[name]</td>";
	print "	<td><input type='checkbox' name='$myField[name]' checked> </td>";
	print "	</tr>";	
	}
}
		
print '</table>';
print '</form>';

?>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small" id="exportSubnet"><i class="icon-gray icon-download"></i> <?php print _('Export'); ?></button>
</div>