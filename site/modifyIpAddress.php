<?php

/**
 * Script to print edit / delete / new IP address
 * 
 * Fetches info from database
 *************************************************/


/* include required scripts */
require_once('../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* get posted values */
$subnetId= $_REQUEST['subnetId'];
$action  = $_REQUEST['action'];
$id      = $_REQUEST['id'];


/* set subnet -> for adding new only */
$subnet = getSubnetDetailsById($subnetId);
$subnet2 = $subnet;
$subnet = transform2long($subnet['subnet']) . "/" . $subnet['mask'];

/* verify that subnet is not write-protected */
if( (isSubnetWriteProtected($subnetId)) && !checkAdmin(false)) {
	die('<div class="error lock">This subnet is locked for writing!</div>');
}

/**
 *
 * if action is not add then fetch current details
 *
 * otherwise format the $ip field
 *
 */
if ( $action == "add_ipaddress" ) {	
    $details = array(
    	"ip_addr" => transform2long(getFirstAvailableIPAddress ($subnetId)), 
    	"description" => "", 
    	"dns_name" => "", 
    	"owner"	=> "",
    	"switch" => "",
    	"port"	=> ""
    	 );
}
else {
	$details = getIpAddrDetailsById ($id);
}


/**
 *	Get first available IP address!
 */


/* Set title and button text */
if ($action == "add_ipaddress") {
	$btnName = "Add";
}
else if ($action == "edit_ipaddress") {
	$btnName = "Edit";
}
else {
	$btnName = "Delete";
}


/* get all selected fields for filtering */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


/* get all selected fields */
$myFields = getCustomIPaddrFields();
$myFieldsSize = sizeof($myFields);
?>

<!-- autocomplete -->
<link type="text/css" href="css/jquery-ui-1.8.14.custom.css" rel="stylesheet">	
<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
<script>
$(function() {

	//get all unique users
	var users = [
		<?php 
		$allUsers = getUniqueUsers ();
		foreach ($allUsers as $user) {
			print '"'. $user['owner'] .'", ';
		}
		?>
	];

	//autocomplete users
	$( "#owner" ).autocomplete({ source: users, minLength: 0 }).focus(function(){
	if (this.value == "")
		$(this).trigger('keydown.autocomplete');
	});

});
</script>


<!-- IP address modify form -->
<form class="editipaddress" name="editipaddress">

<!-- edit IP address table -->
<table class="editipaddress">

	<!-- title -->
	<tr>
		<th colspan="2"><h4><?php print $btnName; ?> IP address</h4></th>
	</tr>

	<!-- IP address -->
	<tr>
		<td>IP address 
		</td>
		<td>
			<input type="text" name="ip_addr" class="ip_addr" value="<?php print $details['ip_addr']; ?>" size="30" placeholder="IP address">
    		<img class="addIpAddress" src="css/images/infoAdd.png" title="You can add,edit or delete multiple IP addresses<br>by specifying IP range (e.g. 10.10.0.0-10.10.0.25)">
    	</td>
	</tr>

	<!-- description -->
	<tr>
		<td>Description</td>
		<td>
			<input type="text" name="description" value="<?php if(isset($details['description'])) {print $details['description'];} ?>" size="30" 
			<?php if ( $btnName == "Delete" ) { print " readonly";} ?> 
			placeholder="Description">
		</td>
	</tr>


	<!-- DNS name -->
	<?php

	if(!isset($details['dns_name'])) {$details['dns_name'] = "";}	

		print '<tr>'. "\n";
		print '	<td>DNS name</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="dns_name" placeholder="hostname" value="'. $details['dns_name']. '" size="30">'. "\n";
		print '	<img class="refreshHostname" src="css/images/refresh.png" title="Click to check for hostname">'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	?>


	<!-- MAC address -->
	<?php
	if(in_array('mac', $setFields)) {

		if(!isset($details['mac'])) {$details['mac'] = "";}	

		print '<tr>'. "\n";
		print '	<td>MAC address</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="mac" placeholder="MAC address" value="'. $details['mac']. '" size="30">'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>


	<!-- Owner -->
	<?php
	if(in_array('owner', $setFields)) {

		if(!isset($details['owner'])) {$details['owner'] = "";}	

		print '<tr>'. "\n";
		print '	<td>Owner</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="owner" id="owner" placeholder="IP address owner" value="'. $details['owner']. '" size="30">'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>


	<!-- switch / port -->
	<?php
	
	if(!isset($details['switch']))  {$details['switch'] = "";}	
	if(!isset($details['port'])) 	{$details['port'] = "";}	
	
	# both are active
	if(in_array('switch', $setFields)) {
		print '<tr>'. "\n";
		print '	<td>Switch</td>'. "\n";
		print '	<td>'. "\n";

		print '<select name="switch">'. "\n";
		print '<option disabled>Select Switch:</option>'. "\n";
		print '<option value="" selected>None</option>'. "\n";
		$switches = getAllUniqueSwitches();
		
		foreach($switches as $switch) {
			//if same
			if($switch['hostname'] == $details['switch']) {
				print '<option value="'. $switch['hostname'] .'" selected>'. $switch['hostname'] .'</option>'. "\n";
			}
			else {
				print '<option value="'. $switch['hostname'] .'">'. $switch['hostname'] .'</option>'. "\n";			
			}
		}
		print '</select>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";	
	}
	# Port
	if(in_array('port', $setFields)) {

		if(!isset($details['port'])) {$details['port'] = "";}	

		print '<tr>'. "\n";
		print '	<td>Port</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="port"   id="port"   placeholder="Port"   value="'. $details['port']. '" size="30">'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>


	<!-- note -->
	<?php
	if(in_array('note', $setFields)) {

		if(!isset($details['note'])) {$details['note'] = "";}	

		print '<tr>'. "\n";
		print '	<td>Note</td>'. "\n";
		print '	<td class="note">'. "\n";
		print ' <textarea name="note" cols="23" rows="2" placeholder="Additional notes about IP address">'. $details['note'] . '</textarea>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>


	<!-- state -->
	<?php
	if(in_array('state', $setFields)) {
	

		print '<tr>'. "\n";
		print '	<td>State</td>'. "\n";
		print '	<td>'. "\n";
		print '		<select name="state">'. "\n";
		
		#active, reserved, offline
		print '		<option value="1" '; if(isset($details['state'])) { if ($details['state'] == "1") print 'selected'; } print '>Active</option>'. "\n";
		print '		<option value="2" '; if(isset($details['state'])) { if ($details['state'] == "2") print 'selected'; } print '>Reserved</option>'. "\n";
		print '		<option value="0" '; if(isset($details['state'])) { if ($details['state'] == "0") print 'selected'; } print '>Offline</option>'. "\n";

		print '		</select>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	}
	?>
	
	<!-- Custom fields -->
	<?php
	if(sizeof($myFields) > 0) {
		# all my fields
		foreach($myFields as $myField) {
			print '<tr>'. "\n";
			print '	<td>'. $myField['name'] .'</td>'. "\n";
			print '	<td>'. "\n";
			print ' <input type="text" name="'. $myField['name'] .'" placeholder="'. $myField['name'] .'" value="'. $details[$myField['name']]. '" size="30">'. "\n";
			print '	</td>'. "\n";
			print '</tr>'. "\n";		
		}
	}
	
	?>

	<!-- submit -->
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="action" 	 value="<?php print $btnName; 	?>">
			<input type="hidden" name="id" 		 value="<?php print $id; 		?>">
			<input type="hidden" name="subnet"   value="<?php print $subnet; 	?>">
			<input type="hidden" name="subnetId" value="<?php print $subnetId; 	?>">		
			<input type="submit" 				 value="<?php print $btnName; 	?>">
			<input type="button" value="Cancel" class="cancel">
		</td>
	</tr>	


</table>	<!-- end edit ip address table -->
</form>		<!-- end IP address edit form -->

<!-- holder for result -->
<div class="addnew_check"></div>
