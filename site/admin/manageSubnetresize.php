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
?>


<!-- header -->
<div class="pHeader">Resize subnet</div>


<!-- content -->
<div class="pContent">

	<form id="subnetResize">
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
        <td style="vertical-align:middle">
	        / <input type="text" class="input-mini" name="newMask">
	        <input type="hidden" name="subnetId" value="<?php print $_POST['subnetId']; ?>">
        </td>
    </tr>
        
    </table>
    </form> 

    <!-- warning -->
    <div class="alert alert-warn">
    You can change subnet size by specifying new mask (bigger or smaller). Please note:
    <ul>
    	<li>If subnet has hosts outside of resized subnet resizing will not be possible</li>
    	<li>If strict mode is enabled check will be made to ensure it is still inside master subnet</li>
    </ul>
    </div>

</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopup2">Cancel</button>
	<button class="btn btn-small" id="subnetResizeSubmit"><i class="icon-gray icon-ok"></i> Resize subnet</button>

	<div class="subnetResizeResult"></div>
</div>