<?php

/**
 * Script to save AD settings
 ***********************************************/

require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get posted values */
$ad = $_POST;

/* Update settings! */
if(!updateADsettings($ad)) {
	print '<div class="error">Failed to update AD settings!</div>';
}
else {
	print '<div class="success">AD settings updated!</div>';
}


?>