<?php

/*
 * truncate subnet result
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* verify post */
CheckReferrer();

# get all site settings
$settings = getAllSettings();

# truncate network
if(!truncateSubnet($_POST['subnetId'])) {}
else 									{ print "<div class='alert alert-success'>Subnet truncated succesfully!</div>"; }

?>