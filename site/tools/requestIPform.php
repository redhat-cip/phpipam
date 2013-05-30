<!-- header -->
<div class="pHeader"><?php print _('IP request form');?></div>

<!-- content -->
<div class="pContent editIPAddress">

	<form name="requestIP" id="requestIP">

	<table id="requestIP" class="table table-striped table-condensed">

	<tr>
		<td><?php print _('IP address');?> *</td>
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
		<td><?php print _('Description');?></td>
		<td><input type="text" name="description" size="30" placeholder="<?php print _('Enter description');?>"></td>
	</tr>

	<!-- DNS name -->
	<tr>
		<td><?php print _('DNS name');?></td>
		<td><input type="text" name="dns_name" size="30" placeholder="<?php print _('hostname');?>"></td>
	</tr>

	<!-- owner -->
	<tr class="owner">
		<td><?php print _('Owner');?></td>
		<td>	
		<!-- autocomplete -->
		<input type="text" name="owner" id="owner" size="30" placeholder="<?php print _('Owner of IP address');?>"></td>
	</tr>

	<!-- requester -->
	<tr>
		<td><?php print _('Requester');?> *</td>
		<td>
			<input type="text" name="requester" size="30" placeholder="<?php print _('your email address');?>"></textarea>
		</td>
	</tr>

	<!-- comment -->
	<tr>
		<td><?php print _('Additional comment');?></td>
		<td style="padding-right:20px;">
			<textarea name="comment" rows="2" style="width:100%;" placeholder="<?php print _('Enter additional details for request if they are needed');?>"></textarea>
		</td>
	</tr>


	</table>
	</form>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel');?></button>
	<button class="btn btn-small" id="requestIPAddressSubmit"><?php print _('Request IP');?></button>
	<!-- result  -->
	<div id="requestIPresult"></div>
</div>
