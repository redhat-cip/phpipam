<!-- header -->
<div class="pHeader">IP request form</div>

<!-- content -->
<div class="pContent editIPAddress">

	<form name="requestIP" id="requestIP">

	<table id="requestIP" class="table table-striped table-condensed">

	<tr>
		<td>IP address *</td>
		<td>
			<?php  
			require_once('../../functions/functions.php');
			# get first IP address
			$first = transform2long (getFirstAvailableIPAddress ($_POST['subnetId']));
			# get subnet details
			$subnet = getSubnetDetailsById($_POST['subnetId']);
			?>
			<input type="text" name="ip_addr" class="ip_addr" size="30" value="<?php print $first; ?>">
			
			<input type="hidden" name="subnetId" value="<?php print $subnet['id']; ?>">
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
		<td>Additional comment</td>
		<td style="padding-right:20px;">
			<textarea name="comment" rows="2" style="width:100%;" placeholder="Enter additional details for request if they are needed"></textarea>
		</td>
	</tr>


	</table>
	</form>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small" id="requestIPAddressSubmit">Request IP</button>
	<!-- result  -->
	<div id="requestIPresult"></div>
</div>
