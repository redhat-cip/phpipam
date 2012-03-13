<form name="requestIP" id="requestIP">

<table class="requestIP">

<!-- title -->
<tr>
	<th colspan="2" class="bottomBorder">IP request form</th>
</tr>


<!-- die if none available! -->
<?php
# scripts
require_once('../functions/functions.php'); 
$subnets = fetchAllSubnets ();

# set first IP address
$firstSubnet = $subnets[0]['id'];

# if we have no available master subnets for IP requests die!
$n = 0;
foreach($subnets as $subnet) {
	if($subnet['allowRequests'] == 1) {
		if(!subnetContainsSlaves($subnet['id'])) {
			$n++;
		}
	}
}

# die!
if($n == 0) {
	print '<tr>'. "\n";
	print '<td colspan="2"><div class="error" style="white-space:nowrap;">No subnets available for requests</div></td>'. "\n";
	print '</tr>'. "\n";
	# back
	print '<tr><td colspan="2"><a href="#login" class="backToLogin"><div class="backToLogin">Back to login</div></a></td></tr>';
	print '</table>'. "\n";
	print '</form>'. "\n";
	die();
}
?>

<!-- select section -->
<tr>
	<td>Select subnet *</td>
	
	<td>
		<select name="subnetId" id="subnetId">
		
		<?php		
		$m = 0;		//needed for first IP address definition
		
		foreach($subnets as $subnet) {
		
			/* show only subnets that allow IP exporting */	
			if($subnet['allowRequests'] == 1) {
			
				/* must not have any nested subnets! */
				if(!subnetContainsSlaves($subnet['id']))
				{	
					print '<option value="'. $subnet['id'] .'">' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>';
				}
			}
		}
		?>

		</select>
		
	</td>
</tr>

 
<!-- IP address -->
<script type="text/javascript">
$(document).ready(function() {
	//autofill first IP address
	var subnetId = <?php print $firstSubnet; ?>;
	//post it via json to requestIPfirstFree.php
	$.post('requestIPfirstFree.php', { subnetId:subnetId}, function(data) {
		$('input.ip_addr').val(data);
	});
});
</script>

<tr>
	<td>IP address *</td>
	<td>
		<input type="text" name="ip_addr" class="ip_addr" size="30" value="">
   	</td>
</tr>

<!-- description -->
<tr>
	<td>Description</td>
	<td>
		<input type="text" name="description" size="30" placeholder="IP description"></td>
</tr>

<!-- DNS name -->
<tr>
	<td>Hostname</td>
	<td>
		<input type="text" name="dns_name" size="30" placeholder="device hostname"></td>
</tr>

<!-- owner -->
<?php 
/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


# owner
if(in_array('owner', $setFields)) {

	print '<tr class="owner">'. "\n";
	print '<td>Owner</td>'. "\n";
	print '<td>	'. "\n";
		# autocomplete
	print '<link type="text/css" href="../css/jquery-ui-1.8.14.custom.css" rel="Stylesheet" />	'. "\n";
	print '<script type="text/javascript" src="../js/jquery-ui-1.8.14.custom.min.js"></script>'. "\n";
	print '<script>'. "\n";
	print '$(function() {'. "\n";
		# get all unique users
	print 'var users = ['. "\n";
				$allUsers = getUniqueUsers ();
				foreach ($allUsers as $user) {
					print '"'. $user['owner'] .'", ';
				}
	print '];'. "\n";
		# autocomplete users
	print '$( "#owner" ).autocomplete({ source: users }); '. "\n";
	print '}); '. "\n";
	print '</script> '. "\n";
		
	print '<input type="text" name="owner" id="owner" size="30" placeholder="Responsible person"></td>'. "\n";
	print '</tr>'. "\n";

}

?>


<!-- requester -->
<tr>
	<td>Requester *</td>
	<td>
		<input type="text" name="requester" size="30" placeholder="You email address"></textarea>
	</td>
</tr>

<!-- comment -->
<tr>
	<td>Additional<br>comment</td>
	<td class="comment">
		<textarea name="comment" rows="2" cols="30" placeholder="If there is anything else you want to say write it in this box!"></textarea>
	</td>
</tr>

<!-- submit -->
<tr>
	<td class="submit"></td>
	<td class="submit">
		<input type="submit" value="Submit request">	</td>
	<td class="submit"></td>
</tr>

<!-- result -->
<tr>
	<td colspan="2">
		<div id="requestIPresult"></div>
	</td>
</tr>

<!-- back to login page -->
<tr>
	<td class="submit">
		<a href="#login" class="backToLogin">
		<div class="backToLogin">
			Back to login
		</div>
		</a>
		
	</td>
	<td class="submit"></td>
</tr>

</table>

</form>