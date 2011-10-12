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
?>

<!-- autocomplete -->
<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.14.custom.css" rel="stylesheet">	
<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
<script>
$(function() {
	//get all swiches
	var switches = [
		<?php 
		$allSwitches = getUniqueSwitches ();
		foreach ($allSwitches as $switch) {
			print '"'. $switch['switch'] .'", ';
		}
		?>
	];

	//get all unique users
	var users = [
		<?php 
		$allUsers = getUniqueUsers ();
		foreach ($allUsers as $user) {
			print '"'. $user['owner'] .'", ';
		}
		?>
	];
		
	//autocomplete switches
	$( "#switch" ).autocomplete({ source: switches });
	//autocomplete users
	$( "#owner" ).autocomplete({ source: users });
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
			<img class="addIpAddress" src="css/images/infoAdd.png" title="You can add,edit or delete multiple IP addresses<br>by specifying IP range (e.g. 10.10.0.0-10.10.0.25)">
		</td>
		<td>
			<input type="text" name="ip_addr" value="<?php print $details['ip_addr']; ?>" size="30">
    	</td>
	</tr>

	<!-- description -->
	<tr>
		<td>Description</td>
		<td>
			<input type="text" name="description" value="<?php if(isset($details['description'])) {print $details['description'];} ?>" size="30" 
			<?php if ( $btnName == "Delete" ) { print " readonly";} ?> 
			></td>
	</tr>


	<!-- DNS name -->
	<tr>
		<td>DNS name
			<img class="refreshHostname" src="css/images/refresh.png" title="Click to check for hostname">
		</td>
		<td>
			<input type="text" name="dns_name" value="<?php if(isset($details['dns_name'])) { print $details['dns_name'];} ?>" size="30" 
			<?php if ( $btnName == "Delete" ) { print " readonly "; } ?> 
			></td>
	</tr>


	<!-- owner -->
	<tr>
		<td>Owner</td>
		<td>
			<input type="text" name="owner" id="owner" value="<?php if(isset($details['owner'])) {print $details['owner'];} ?>" size="30" 
			<?php if ( $btnName == "Delete" ) { print " readonly "; } ?> 
			></td>
	</tr>

	<!-- switch / port -->
	<tr>
		<td>Switch / port</td>
		
		
		<td>
			<input type="text" name="switch" id="switch" value="<?php if(isset($details['switch'])) { print $details['switch'];} ?>" size="13" 
			<?php if ( $btnName == "Delete" ) { print " readonly "; } ?> 
			>/
			<input type="text" name="port" value="<?php if(isset($details['port'])) { print $details['port'];} ?>" size="9" 
			<?php if ( $btnName == "Delete" ) { print " readonly "; } ?> 
			></td>
	</tr>

	<!-- note -->
	<tr class="note">
		<td>Note</td>
		<td class="note">
			<textarea name="note" cols="22" rows="2"><?php if(isset($details['note'])) { print $details['note']; } ?></textarea>
		</td>
	</tr>

	<!-- state -->
	<tr>
		<td>State</td>
		<td>
			<select name="state">
				<option value="1" <?php if(isset($details['state'])) { if ($details['state'] == "1") print 'selected'; } ?>>Active</option>
				<option value="2" <?php if(isset($details['state'])) { if ($details['state'] == "2") print 'selected'; }?>>Reserved</option>
				<option value="0" <?php if(isset($details['state'])) { if ($details['state'] == "0") print 'selected'; }?>>Offline</option>
			</select>
		</td>
	</tr>

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
