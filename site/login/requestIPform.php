<div id="login">
<form name="requestIP" id="requestIP">

<div class="requestIP">
<table class="requestIP">

<!-- title -->
<tr>
	<legend><?php print _('IP request form'); ?></legend>
</tr>

<!-- die if none available! -->
<?php
# scripts
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

# die if no subnets are available for requests!
if($n == 0) {
	?>
	<tr>
		<td colspan="2"><div class="alert alert-warn" style="white-space:nowrap;"><?php print _('No subnets available for requests'); ?></div></td>
	</tr>
	</table>
	</form>
	</div>

	<!-- back to login page -->
	<div class="iprequest" style="text-align:left">
	<a href="#login" class="backToLogin">
		<i class="icon-arrow-left icon-gray"></i> <?php print _('Back to login'); ?>
	</a>		
	</div>
	<?php
	die();
}
?>

<!-- select section -->
<tr>
	<th><?php print _('Select subnet'); ?> *</th>
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


<!-- description -->
<tr>
	<th><?php print _('Description'); ?></th>
	<td>
		<input type="text" name="description" size="30" placeholder="<?php print _('IP description'); ?>"></td>
</tr>

<!-- DNS name -->
<tr>
	<th><?php print _('Hostname'); ?></th>
	<td>
		<input type="text" name="dns_name" size="30" placeholder="<?php print _('device hostname'); ?>"></td>
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
	print '<th>'._('Owner').'</th>'. "\n";
	print '<td>	'. "\n";
	print '</script> '. "\n";		
	print '<input type="text" name="owner" id="owner" size="30" placeholder="'._('Responsible person').'"></td>'. "\n";
	print '</tr>'. "\n";
}

?>


<!-- requester -->
<tr>
	<th><?php print _('Requester'); ?> *</th>
	<td>
		<input type="text" name="requester" size="30" placeholder="<?php print _('Your email address'); ?>"></textarea>
	</td>
</tr>

<!-- comment -->
<tr>
	<th><?php print _('Additional comment'); ?></th>
	<td class="comment">
		<textarea name="comment" rows="3" placeholder="<?php print _('If there is anything else you want to say about request write it in this box'); ?>!"></textarea>
	</td>
</tr>

<!-- submit -->
<tr>
	<td class="submit"></td>
	<td class="submit">
		<input type="submit" class="btn btn-small pull-right" value="<?php print _('Submit request'); ?>">	</td>
	<td class="submit"></td>
</tr>

</table>
</div>


<div id="requestIPresult"></div>


<!-- back to login page -->
<div class="iprequest" style="text-align:left">
	<a href="login/">
		<i class="icon-arrow-left icon-gray"></i> <?php print _('Back to login'); ?>
	</a>		
</div>

</form>
</div>