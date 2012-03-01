<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* glue together */
$selected = implode(';', $_POST);

/* write to database */
if(!updateSelectedIPaddrFields($selected)) {
	print '<div class="error">Update failed!</div>';
}
else {
	print '<div class="success">Update successfull!</div>';
}
?>