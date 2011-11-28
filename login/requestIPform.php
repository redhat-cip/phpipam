<form name="requestIP" id="requestIP">

<table class="requestIP">

<!-- title -->
<tr>
	<th colspan="3" class="bottomBorder">IP request form</th>
</tr>

<!-- select section -->
<tr>
	<td>* Select subnet</td>
	
	<td>
		<select name="subnetId" id="subnetId">
		
		<?php
		require_once('../functions/functions.php'); 
		$subnets = fetchAllSubnets ();
		
		$m = 0;		//needed for first IP address definition
		
		foreach($subnets as $subnet) {
			/* show only subnets that allow IP exporting */
			
			if($subnet['allowRequests'] == 1) {
			
				//first subnet definitions
				if ($m == 0) {
					$firstSubnet = $subnet['id'];
					$m++;
				}
						
				print '<option value="'. $subnet['id'] .'">' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .' ['. $subnet['description'] .']</option>';
			}
		}
		?>

		</select>
		
	</td>
	<td class="info">(required) Please select subnet</td>
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
	<td>* IP address</td>
	<td>
		<input type="text" name="ip_addr" class="ip_addr" size="30" value="">
   	</td>
   	<td class="info">(required) Please enter IP address</td>
</tr>

<!-- description -->
<tr>
	<td>Description</td>
	<td>
		<input type="text" name="description" size="30"></td>
	<td class="info">Description for IP address usage</td>
</tr>

<!-- DNS name -->
<tr>
	<td>DNS name</td>
	<td>
		<input type="text" name="dns_name" size="30"></td>
	<td class="info">device hostname</td>
</tr>

<!-- owner -->
<tr class="owner">
	<td>Owner</td>
	<td>	
		<!-- autocomplete -->
		<link type="text/css" href="../css/ui-darkness/jquery-ui-1.8.14.custom.css" rel="Stylesheet" />	
		<script type="text/javascript" src="../js/jquery-ui-1.8.14.custom.min.js"></script>
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
		
		<input type="text" name="owner" id="owner" size="30"></td>
	<td class="info">Person, responsible for IP address</td>
</tr>

<!-- requester -->
<tr>
	<td>Requester</td>
	<td>
		<input type="text" name="requester" size="30"></textarea>
	</td>
	<td class="info">Please enter your own email address!</td>
</tr>

<!-- comment -->
<tr>
	<td>Additional<br>comment</td>
	<td class="comment">
		<textarea name="comment" rows="2" cols="30"></textarea>
	</td>
	<td class="info">If there is anything else you want to say write it in this box!</td>
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
	<td></td>
	<td colspan="2">
		<div id="requestIPresult"></div>
	</td>
</tr>

<!-- back to login page -->
<tr>
	<td class="submit"></td>
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