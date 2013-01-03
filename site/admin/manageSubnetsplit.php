<?php

/*
 * Print split subnet
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
<div class="pHeader">Split subnet</div>


<!-- content -->
<div class="pContent">

	<form id="subnetSplit">
	<table class="editSubnetDetails table table-noborder table-condensed">

    <!-- subnet details -->
    <tr>
        <td class="middle">Subnet</td>
        <td>
        </td>
    </tr>
    
    </table>
    </form> 

</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopup2">Cancel</button>
	<button class="btn btn-small editSubnetSubmit"><i class="icon-gray icon-ok"></i> Split subnet</button>

	<div class="subnetSplitResult"></div>
</div>