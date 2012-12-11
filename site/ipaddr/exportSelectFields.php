<!-- header -->
<div class="pHeader">Select fields to export</div>


<!-- content -->
<div class="pContent">

<?php
/* use required functions */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);

/* get all site settings */
$settings = getAllSettings();

/* user details */
$viewer = isUserViewer();

#  XLS export holder
if(!$viewer ) {
		
	# print 
	print '<form id="selectExportFields">';
	
	# table
	print "	<table class='table table-striped table-condensed'>";
	
	# IP addr - mandatory
	print "	<tr>";
	print "	<td>IP address</td>";
	print "	<td><input type='checkbox' name='ip_addr' checked> </td>";
	print "	</tr>";	
	
	# state
	if(in_array('state', $setFields)) 		{ 
	print "	<tr>";
	print "	<td>IP state</td>";
	print "	<td><input type='checkbox' name='state' checked> </td>";
	print "	</tr>";	
	}
	# description - mandatory
	print "	<tr>";
	print "	<td>Description</td>";
	print "	<td><input type='checkbox' name='description' checked> </td>";
	print "	</tr>";
	# hostname - mandatory
	print "	<tr>";
	print "	<td>Hostname</td>";
	print "	<td><input type='checkbox' name='dns_name' checked> </td>";
	print "	</tr>";	
	# mac 
	if(in_array('mac', $setFields)) 		{ 
	print "	<tr>";
	print "	<td>MAC address</td>";
	print "	<td><input type='checkbox' name='mac' checked> </td>";
	print "	</tr>";		 
	}
	# owner
	if(in_array('owner', $setFields)) 		{ 
	print "	<tr>";
	print "	<td>Owner</td>";
	print "	<td><input type='checkbox' name='owner' checked> </td>";
	print "	</tr>";	
	}
	# switch
	if(in_array('switch', $setFields)) 		{ 
	print "	<tr>";
	print "	<td>Switch</td>";
	print "	<td><input type='checkbox' name='switch' checked> </td>";
	print "	</tr>";	
	}	
	# port
	if(in_array('port', $setFields)) 		{ 
	print "	<tr>";
	print "	<td>Port</td>";
	print "	<td><input type='checkbox' name='port' checked> </td>";
	print "	</tr>";	
	}
	# note
	if(in_array('note', $setFields)) 		{ 
	print "	<tr>";
	print "	<td>Note</td>";
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
	}

?>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small" id="exportSubnet"><i class="icon-gray icon-download"></i> Export</button>
</div>