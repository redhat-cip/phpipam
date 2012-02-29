<?php
require_once('../../functions/functions.php');
?>



<form name="requestIP" id="requestIP">

<table class="requestIP">

<!-- title -->
<tr>
	<th colspan="3" class="bottomBorder" style="text-align:left;padding-left:0px;padding-bottom:10px;">IP request form</th>
</tr>


<tr>
	<td>IP address *</td>
	<td>
		<?php  
		# get first IP address
		$first = transform2long (getFirstAvailableIPAddress ($_POST['subnetId']));
		# get subnet details
		$subnet = getSubnetDetailsById($_POST['subnetId']);
		?>
		<input type="text" name="ip_addr" class="ip_addr" size="30" value="<?php print $first; ?>">
	</td>
</tr>

<!-- description -->
<tr>
	<td>Description</td>
	<td><input type="text" name="description" size="30" placeholder="Enter description"></td>
</tr>

<!-- DNS name -->
<tr>
	<td>DNS name</td>
	<td><input type="text" name="dns_name" size="30" placeholder="hostname"></td>
</tr>

<!-- owner -->
<tr class="owner">
	<td>Owner</td>
	<td>	
		<!-- autocomplete -->
		<link type="text/css" href="../../css/jquery-ui-1.8.14.custom.css" rel="Stylesheet" />	
		<script type="text/javascript" src="../../js/jquery-ui-1.8.14.custom.min.js"></script>
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
		$( "#owner" ).autocomplete({ source: users });
		});
		</script>
		
		<input type="text" name="owner" id="owner" size="30" placeholder="Owner of IP address"></td>
</tr>

<!-- requester -->
<tr>
	<td>Requester *</td>
	<td>
		<input type="text" name="requester" size="30" placeholder="your email address"></textarea>
	</td>
</tr>

<!-- comment -->
<tr>
	<td>Additional<br>comment</td>
	<td class="comment">
		<textarea name="comment" rows="2" cols="30" placeholder="Enter additional details for request if they are needed"></textarea>
	</td>
</tr>

<!-- submit -->
<tr>
	<td class="submit">
	
		<!-- hidden subnet id -->
		<?php print '<input type="hidden" name="subnetId" value="'. $subnet['id'] .'">'; ?>
	
		<input type="submit" value="Submit request">	</td>
	<td class="submit"></td>
</tr>

<!-- result -->
<tr>
	<td colspan="2">
		<div id="requestIPresult"></div>
	</td>
</tr>


</table>

</form>