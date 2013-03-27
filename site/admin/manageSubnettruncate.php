<?php

/*
 * Print resize subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user has write permissions for subnet */
$subnetPerm = checkSubnetPermission ($_REQUEST['subnetId']);
if($subnetPerm != "2") 	{ die('<div class="alert alert-error">You do not have permissions to resize subnet!</div>'); }


/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);

# get all IP addresses
$ip_addr = getIpAddressesBySubnetId ($_POST['subnetId']) ;
?>


<!-- header -->
<div class="pHeader">Truncate subnet</div>


<!-- content -->
<div class="pContent">

	<table class="table table-noborder table-condensed">

    <!-- subnet -->
    <tr>
        <td class="middle">Subnet</td>
        <td><?php print transform2long($subnet['subnet'])."/$subnet[mask] ($subnet[description])"; ?></td>
    </tr>

    <!-- Mask -->
    <tr>
        <td class="middle">Number of IP addresses</td>
        <td><?php print sizeof($ip_addr); ?></td>
    </tr>
        
    </table>

    <!-- warning -->
    <div class="alert alert-warn">
    Truncating network will remove all IP addresses, that belong to selected subnet!
    </div>

</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopup2">Cancel</button>
	<button class="btn btn-small btn-danger" id="subnetTruncateSubmit" data-subnetId='<?php print $_POST['subnetId']; ?>'><i class="icon-white icon-trash"></i> Truncate subnet</button>

	<div class="subnetTruncateResult"></div>
</div>