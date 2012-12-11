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

if(sizeof($request) == 0) {
	die("<div class='alert alert-error'>Request does not exist!</div>");
}
?>


<!-- header -->
<div class="pHeader">Manage IP request</div>


<!-- content -->
<div class="pContent">

	<h4>IP address request (#<?php print $request['id'] ?>)</h4>
	
	<!-- IP address request form -->
	<form class="manageRequestEdit" name="manageRequestEdit">
	<!-- edit IP address table -->
	<table id="manageRequestEdit" class="table table-striped table-condensed">
	<!-- Section -->
	<tr>
		<th>Requested subnet</th>
		<td>
			<select name="subnetId">
			<?php
			$subnets = fetchAllSubnets ();
		
			foreach($subnets as $subnet) {
				/* show only subnets that allow IP exporting */
				if($subnet['allowRequests'] == 1) {
					if($request['subnetId'] == $subnet['id'])	{ print '<option value="'. $subnet['id'] .'" selected>' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>'; }
					else 										{ print '<option value="'. $subnet['id'] .'">' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>'; }
				}
			}
			?>
			</select>
		</td>
	</tr>
	<!-- IP address -->
	<tr>
		<th>IP address</th>
		<td>
			<input type="text" name="ip_addr" value="<?php print transform2long($request['ip_addr']); ?>" size="30">
			
			<input type="hidden" name="requestId" value="<?php print $request['id']; ?>">
			<input type="hidden" name="requester" value="<?php print $request['requester']; ?>">
    	</td>
	</tr>
	<!-- description -->
	<tr>
		<th>Description</th>
		<td>
			<input type="text" name="description" value="<?php if(isset($request['description'])) { print $request['description'];} ?>" size="30">
		</td>
	</tr>
	<!-- DNS name -->
	<tr>
		<th>Hostname</th>
		<td>
			<input type="text" name="dns_name" value="<?php if(isset($request['dns_name'])) { print $request['dns_name'];} ?>" size="30">
		</td>
	</tr>
	<!-- owner -->
	<tr>
		<th>Owner</th>
		<td>
			<input type="text" name="owner" id="owner" value="<?php if(isset($request['owner'])) { print $request['owner']; } ?>" size="30">
		</td>
	</tr>
	<!-- switch / port -->
	<tr>
		<th>Switch / port</th>		
		<td>
			<input type="text" name="switch" id="switch" value="<?php if(isset($request['switch'])) { print $request['switch']; } ?>" size="13" 
			<?php if ( isset($btnName)) { if( $btnName == "Delete" ) { print " readonly "; }} ?> 
			>/
			<input type="text" name="port" value="<?php if(isset($request['port'])) { print $request['port']; } ?>" size="9" 
			<?php if ( isset($btnName)) { if ( $btnName == "Delete" ) { print " readonly "; }} ?> 
			>
		</td>
	</tr>
	<!-- requested by -->
	<tr>
		<th>Requester email</th>
		<td><?php if(isset($request['requester'])) { print $request['requester']; } ?></td>
	</tr>
	<!-- comment -->
	<tr>
		<th>Requester comment</th>
		<td><i><?php if(isset($request['comment'])) { if(!empty($request['comment'])) { print '"'. $request['comment'] .'"'; }} ?></i></td>
	</tr>
	<!-- Admin comment -->
	<tr>
		<th>Comment approval/reject:</th>
		<td>
			<textarea name="adminComment" rows="2" cols="30"></textarea>
		</td>
	</tr>
	<!-- state -->
	<tr>
		<th>State</th>
		<td>
			<select name="state">
				<option value="1" <?php if(isset($request['state'])) { if ($request['state'] == "1") { print 'selected'; }} ?>>Active</option>
				<option value="2" <?php if(isset($request['state'])) { if ($request['state'] == "2") { print 'selected'; }} ?>>Reserved</option>
				<option value="0" <?php if(isset($request['state'])) { if ($request['state'] == "0") { print 'selected'; }} ?>>Offline</option>
			</select>
		</td>
	</tr>

	</table>
	</form>	
</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small manageRequest" data-action='reject'><i class="icon-gray icon-remove"></i> Reject</button>
	<button class="btn btn-small manageRequest" data-action='accept'><i class="icon-gray icon-ok"></i> Accept</button>
	
	<!-- result -->
	<div class="manageRequestResult"></div>
</div>