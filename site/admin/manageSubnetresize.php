<?php

/*
 * Print resize subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* verify post */
CheckReferrer();

# get subnet details
$subnet = getSubnetDetailsById ($_POST['subnetId']);
?>


<!-- header -->
<div class="pHeader">Resize subnet</div>


<!-- content -->
<div class="pContent">

	<form id="subnetSplit">
	<table class="table table-noborder table-condensed">

    <!-- subnet -->
    <tr>
        <td class="middle">Subnet</td>
        <td><?php print transform2long($subnet['subnet']) . " ($subnet[description])"; ?></td>
    </tr>

    <!-- Mask -->
    <tr>
        <td class="middle">Current mask</td>
        <td><?php print "/".$subnet['mask']; ?></td>
    </tr>

    <!-- new Mask -->
    <tr>
        <td class="middle">New mask</td>
        <td>
	        <input type="text" class="input-mini" name="newMask">
        </td>
    </tr>
        
    </table>
    </form> 

    <!-- warning -->
    <div class="alert alert-warn">
    
    </div>

</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopup2">Cancel</button>
	<button class="btn btn-small editSubnetSubmit"><i class="icon-gray icon-ok"></i> Resize subnet</button>

	<div class="subnetSplitResult"></div>
</div>