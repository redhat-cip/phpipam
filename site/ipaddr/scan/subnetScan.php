<?php

/*
 * Scan subnet for new hosts
 ***************************/

/* required functions */
require_once('../../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(false);

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm < 2) 	{ die('<div class="alert alert-error">'._('You do not have permissions to modify hosts in this subnet').'!</div>'); }

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all IP addresses
$ip_addr = getIpAddressesBySubnetId ($_POST['subnetId']) ;
?>


<!-- header -->
<div class="pHeader"><?php print _('Scan subnet'); ?></div>


<!-- content -->
<div class="pContent">

	<table class="table table-noborder table-condensed">

    <!-- subnet -->
    <tr>
        <td class="middle"><?php print _('Subnet'); ?></td>
        <td><?php print transform2long($subnet['subnet'])."/$subnet[mask] ($subnet[description])"; ?></td>
    </tr>
    
    <!-- Scan type -->
    <tr>
    	<td><?php print _('Select Scan type'); ?></td>
    	<td>
    		<select name="scanType">
    			<!-- Discovery scans -->
	    		<optgroup label="<?php print _('Discovery scans');?>">
		    		<option value="DiscoveryPing">Ping <?php print _('scan');?></option>
		    		<option value="DiscoveryNmap">NMap <?php print _('scan');?></option>
		    		<option value="DiscoverySnmp">SNMP <?php print _('scan');?></option>
	    		</optgroup>
    			<!-- Status update scans -->
	    		<optgroup label="<?php print _('Status update scans');?>">
		    		<option value="UpdatePing">Ping <?php print _('scan');?></option>
		    		<option value="UpdateNmap">NMap <?php print _('scan');?></option>
	    		</optgroup>

			</select>
    	</td>
    </tr>
        
    </table>

    <!-- warning -->
    <div class="alert alert-warn alert-block" id="alert-scan">
    <?php print _('You can set parameters for scan under functions/scan/config-scan.php'); ?>!<br>
    &middot; <?php print _('Discovery scans discover new hosts');?><br>
    &middot; <?php print _('Status update scans update alive status for whole subnet');?><br>
    </div>
    
    <!-- result -->
	<div id="subnetScanResult"></div>

</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Cancel'); ?></button>
	<button class="btn btn-small btn-success" id="subnetScanSubmit" data-subnetId='<?php print $_POST['subnetId']; ?>'><i class="icon-white icon-star"></i> <?php print _('Scan subnet'); ?></button>

	<div class="subnetTruncateResult"></div>
</div>