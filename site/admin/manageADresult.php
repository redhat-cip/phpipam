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
if(!updateADsettings($ad)) 	{ print '<div class="alert alert-error alert-absolute">'._('Failed to update AD settings').'!</div>'; }
else 						{ print '<div class="alert alert-success alert-absolute">'._('AD settings updated').'!</div>'; }


?>