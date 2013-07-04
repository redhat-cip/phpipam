<?php

/** 
 * Function to add / edit / delete section
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/* create array of ordering */
foreach($_POST as $key=>$val) {
	// set 0 to NULL
	if($val==0)	$val = 'NULL';
	// remove order-
	$key = str_replace("order-", "", $key);
	//create output array
	$order[$key] = $val;
}

/* do action! */
if (UpdateSectionOrder ($order)) {
    print '<div class="alert alert-success">'._("Section reordering successful").'!</div>';
}

?>