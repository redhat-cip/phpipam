<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* glue together */
$selected = implode(';', $_POST);

/* write to database */
if(!updateSelectedIPaddrFields($selected)) 	{ print '<div class="alert alert-error alert-absolute">Update failed!</div>'; }
else 										{ print '<div class="alert alert-success alert-absolute">Update successfull!</div>'; }
?>