<?php

/**
 * Script to confirm / reject IP address request
 ***********************************************/

require_once('../../functions/functions.php'); 
require_once('../../config.php'); 

/* verify that user is admin */
checkAdmin();

/* get posted request id */
$requestId = $_POST['requestId'];

/* fetch request */
$request = getIPrequestById ($requestId);

?>

<!-- autocomplete -->
<link type="text/css" href="css/jquery-ui-1.8.14.custom.css" rel="Stylesheet" />	
<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
<script type="text/javascript">
$(function() {
	//get all swiches
	var switches = [
		<?php 
		$allSwitches = getAllUniqueSwitches ();
		foreach ($allSwitches as $switch) {
			print '"'. $switch['hostname'] .'", ';
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
	$( "#switch" ).autocomplete({ source: switches, minLength: 0 }).focus(function(){
		if (this.value == "") {
			$(this).trigger('keydown.autocomplete');
		}
	});
	//autocomplete users
	$( "#owner" ).autocomplete({ source: users, minLength: 0 }).focus(function(){
	if (this.value == "") {
			$(this).trigger('keydown.autocomplete');
		}
	});
});
</script>


<div class="normalTable manageRequestEdit">

<!-- IP address request form -->
<form class="manageRequestEdit" name="manageRequestEdit">

<!-- edit IP address table -->
<table class="normalTable manageRequestEdit">

	<!-- title -->
	<tr class="th">
		<th colspan="2"><h4>IP address request (#<?php print $request['id'] ?>)</h4></th>
	</tr>
	
	<!-- Section -->
	<tr>
		<td>Requested subnet</td>
	
		<td>
			<select name="subnetId">
		
			<?php
			$subnets = fetchAllSubnets ();
		
			foreach($subnets as $subnet) {
			
				/* show only subnets that allow IP exporting */
				if($subnet['allowRequests'] == 1) {
			
					if($request['subnetId'] == $subnet['id']) {
						print '<option value="'. $subnet['id'] .'" selected>' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>';
					}
					else {
						print '<option value="'. $subnet['id'] .'">' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>';
					}
				}
			}
			?>

			</select>
		
		</td>
	</tr>


	<!-- IP address -->
	<tr>
		<td>IP address</td>
		<td>
			<input type="text" name="ip_addr" value="<?php print transform2long($request['ip_addr']); ?>" size="30">
    	</td>
	</tr>

	<!-- description -->
	<tr>
		<td>Description</td>
		<td>
			<input type="text" name="description" value="<?php if(isset($request['description'])) { print $request['description'];} ?>" size="30"></td>
	</tr>


	<!-- DNS name -->
	<tr>
		<td>DNS name</td>
		<td>
			<input type="text" name="dns_name" value="<?php if(isset($request['dns_name'])) { print $request['dns_name'];} ?>" size="30"></td>
	</tr>


	<!-- owner -->
	<tr>
		<td>Owner</td>
		<td>
			<input type="text" name="owner" id="owner" value="<?php if(isset($request['owner'])) { print $request['owner']; } ?>" size="30"></td>
	</tr>

	<!-- switch / port -->
	<tr>
		<td>Switch / port</td>		
		<td>
			<input type="text" name="switch" id="switch" value="<?php if(isset($request['switch'])) { print $request['switch']; } ?>" size="13" 
			<?php if ( isset($btnName)) { if( $btnName == "Delete" ) { print " readonly "; }} ?> 
			>/
			<input type="text" name="port" value="<?php if(isset($request['port'])) { print $request['port']; } ?>" size="9" 
			<?php if ( isset($btnName)) { if ( $btnName == "Delete" ) { print " readonly "; }} ?> 
			></td>
	</tr>

	<!-- requested by -->
	<tr>
		<td>Requester email</td>
		<td><?php if(isset($request['requester'])) { print $request['requester']; } ?></td>
	</tr>

	<!-- comment -->
	<tr>
		<td>Requester comment</td>
		<td><i><?php if(isset($request['comment'])) { if(!empty($request['comment'])) { print '"'. $request['comment'] .'"'; }} ?></i></td>
	</tr>

	<!-- Admin comment -->
	<tr>
		<td>Comment approval/reject:</td>
		<td>
			<textarea name="adminComment" rows="2" cols="30"></textarea>
		</td>
	</tr>

	<!-- state -->
	<tr>
		<td>State</td>
		<td>
			<select name="state">
				<option value="1" <?php if(isset($request['state'])) { if ($request['state'] == "1") { print 'selected'; }} ?>>Active</option>
				<option value="2" <?php if(isset($request['state'])) { if ($request['state'] == "2") { print 'selected'; }} ?>>Reserved</option>
				<option value="0" <?php if(isset($request['state'])) { if ($request['state'] == "0") { print 'selected'; }} ?>>Offline</option>
			</select>
		</td>
	</tr>

	<!-- submit -->
	<tr class="th">
		<td></td>
		<td>
			<input type="hidden" name="requestId" value="<?php print $request['id']; ?>">
			<input type="hidden" name="requester" value="<?php print $request['requester']; ?>">
			<input type="submit" 				 value="Accept request">
			<input type="button" class="reject"	 value="Reject request" >
		</td>
	</tr>	
	
	<!-- result -->
	<tr>
		<td></td>
		<td>
			<div class="manageRequestResult"></div>
		</td>
	</tr>


</table>
</form>

</div>