<?php

/**
 * Script to print edit / delete / new IP address
 * 
 * Fetches info from database
 *************************************************/


/* include required scripts */
require_once('../../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* get posted values */
$subnetId= $_REQUEST['subnetId'];
$action  = $_REQUEST['action'];
$id      = $_REQUEST['id'];


/* set subnet -> for adding new only */
$subnet = getSubnetDetailsById($subnetId);


# get IP details
$details = getIpAddrDetailsById ($id);
?>

<!-- header -->
<div class="pHeader"><?php print _('Move IP address to different subnet'); ?></div>

<!-- content -->
<div class="pContent editIPAddress">

	<!-- IP address modify form -->
	<form class="editipaddress" name="editipaddress">
	<!-- edit IP address table -->
	<table id="editipaddress" class="table table-noborder table-condensed">

	<!-- IP address -->
	<tr>
		<td><?php print _('IP address'); ?>
		</td>
		<td>
			<input type="text" name="ip_addr" class="ip_addr" value="<?php print $details['ip_addr']; ?>" size="30" readonly>
    		
   			<input type="hidden" name="action" 	 	value="<?php print $_REQUEST['action']; 	?>">
			<input type="hidden" name="id" 		 	value="<?php print $id; 		?>">
			<input type="hidden" name="subnet"   	value="<?php print $subnet; 	?>">
			<input type="hidden" name="subnetId" 	value="<?php print $subnetId; 	?>">
    	</td>
	</tr>

	<!-- description -->
	<tr>
		<td><?php print _('Description'); ?></td>
		<td>
			<input type="text" name="description" value="<?php if(isset($details['description'])) {print $details['description'];} ?>" readonly>
		</td>
	</tr>


	<!-- DNS name -->
	<?php
	if(!isset($details['dns_name'])) {$details['dns_name'] = "";}
		print '<tr>'. "\n";
		print '	<td>'._('DNS name').'</td>'. "\n";
		print '	<td>'. "\n";
		print ' <input type="text" name="dns_name" size="30" readonly>'. "\n";
		print '	</td>'. "\n";
		print '</tr>'. "\n";
	?>

	<!-- description -->
	<tr>
		<td colspan="2"><hr></td>
	</tr>
	
	<tr>
		<td><?php print _('Select new subnet'); ?>:</td>
		<td>
			<select name="newSubnet">
				<?php
				/* get ALL slave subnets, then remove all subnets and IP addresses */
				global $removeSlaves;
				getAllSlaves ($subnetId);
				$removeSlaves = array_unique($removeSlaves); 
				foreach($removeSlaves as $subnetId) {
					$subnet = getSubnetDetailsById($subnetId);
					print "<option value='$subnet[id]'>$subnet[description] (".Transform2long($subnet['subnet'])."/$subnet[mask])</option>";
				}
				?>
			</select>
		</td>
		
	</tr>
	
</table>	<!-- end edit ip address table -->
</form>		<!-- end IP address edit form -->




</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small" id="editIPAddressSubmit"><?php print _('Move IP address'); ?></button>

	<!-- holder for result -->
	<div class="addnew_check"></div>
</div>
