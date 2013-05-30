<?php

/**
 *	Format and submit instructions to database
 **********************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get instructions */
$instructions = $_POST['instructions'];

/* modifications */


/* write changes */
if(!writeInstructions ($instructions)) 	{ die('<div class="alert alert-error alert-absolute">'._('Failed to update instructions').'!</div>'); }
else 									{ die('<div class="alert alert-success alert-absolute">'._('Instructions updated successfully').'!</div>');	}

?>